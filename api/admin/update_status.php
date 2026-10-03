<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function respond(int $statusCode, array $payload): void {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

if (!isset($_SESSION['username'])) {
    respond(401, ['success' => false, 'message' => 'Please sign in as an administrator.']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Invalid request method.']);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    respond(400, ['success' => false, 'message' => 'Invalid request data.']);
}

$bookingId = $payload['booking_id'] ?? null;
$newStatusId = filter_var($payload['new_status_id'] ?? null, FILTER_VALIDATE_INT);
$statusType = $payload['status_type'] ?? 'booking';
if ((!is_string($bookingId) && !is_int($bookingId)) || trim((string)$bookingId) === '' || $newStatusId === false || $newStatusId < 1) {
    respond(400, ['success' => false, 'message' => 'A booking and valid status are required.']);
}

$statusConfig = [
    'booking' => ['table' => 'statuses', 'column' => 'status_id'],
    'payment' => ['table' => 'payment_statuses', 'column' => 'payment_status_id'],
];
if (!is_string($statusType) || !isset($statusConfig[$statusType])) {
    respond(400, ['success' => false, 'message' => 'Invalid status type.']);
}

require_once __DIR__ . '/../../conn.php';

try {
    $statusTable = $statusConfig[$statusType]['table'];
    $statusColumn = $statusConfig[$statusType]['column'];
    $statusStmt = $conn->prepare("SELECT id FROM {$statusTable} WHERE id = ?");
    $statusStmt->bind_param('i', $newStatusId);
    $statusStmt->execute();
    $validStatus = $statusStmt->get_result()->num_rows > 0;
    $statusStmt->close();
    if (!$validStatus) {
        respond(400, ['success' => false, 'message' => 'That status is not available.']);
    }

    $bookingStmt = $conn->prepare('SELECT id FROM bookings WHERE id = ? LIMIT 1');
    $bookingStmt->bind_param('s', $bookingId);
    $bookingStmt->execute();
    $bookingExists = $bookingStmt->get_result()->num_rows > 0;
    $bookingStmt->close();
    if (!$bookingExists) {
        respond(404, ['success' => false, 'message' => 'Booking not found.']);
    }

    $updateStmt = $conn->prepare("UPDATE bookings SET {$statusColumn} = ? WHERE id = ?");
    $updateStmt->bind_param('is', $newStatusId, $bookingId);
    if (!$updateStmt->execute()) {
        respond(500, ['success' => false, 'message' => 'The status could not be saved.']);
    }
    $updateStmt->close();
    $conn->close();
    respond(200, ['success' => true, 'message' => 'Status updated successfully.']);
} catch (Throwable $exception) {
    error_log('Admin status update: ' . $exception->getMessage());
    respond(500, ['success' => false, 'message' => 'The status could not be saved.']);
}
