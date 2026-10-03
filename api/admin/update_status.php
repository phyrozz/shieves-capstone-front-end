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
$newPaymentStatusId = filter_var($payload['new_payment_status_id'] ?? null, FILTER_VALIDATE_INT);
$statusType = $payload['status_type'] ?? 'booking';
if ((!is_string($bookingId) && !is_int($bookingId)) || trim((string)$bookingId) === '' || $newStatusId === false || $newStatusId < 1
    || ($statusType === 'both' && ($newPaymentStatusId === false || $newPaymentStatusId < 1))) {
    respond(400, ['success' => false, 'message' => 'A booking and valid status are required.']);
}

$statusConfig = [
    'booking' => ['table' => 'statuses', 'column' => 'status_id'],
    'payment' => ['table' => 'payment_statuses', 'column' => 'payment_status_id'],
    'both' => ['table' => 'statuses', 'column' => 'status_id'],
];
if (!is_string($statusType) || !isset($statusConfig[$statusType])) {
    respond(400, ['success' => false, 'message' => 'Invalid status type.']);
}

require_once __DIR__ . '/../../conn.php';

try {
    $statusTable = $statusConfig[$statusType]['table'];
    $statusStmt = $conn->prepare("SELECT name FROM {$statusTable} WHERE id = ?");
    $statusStmt->bind_param('i', $newStatusId);
    $statusStmt->execute();
    $selectedStatus = $statusStmt->get_result()->fetch_assoc();
    $statusStmt->close();
    if (!$selectedStatus) {
        respond(400, ['success' => false, 'message' => 'That status is not available.']);
    }

    if ($statusType === 'both') {
        $paymentCheckStmt = $conn->prepare('SELECT id FROM payment_statuses WHERE id = ?');
        $paymentCheckStmt->bind_param('i', $newPaymentStatusId);
        $paymentCheckStmt->execute();
        $paymentStatusExists = $paymentCheckStmt->get_result()->num_rows > 0;
        $paymentCheckStmt->close();
        if (!$paymentStatusExists) {
            respond(400, ['success' => false, 'message' => 'That payment status is not available.']);
        }
    }

    $isBookingCancellation = in_array($statusType, ['booking', 'both'], true)
        && in_array(strtolower(trim($selectedStatus['name'])), ['cancelled', 'canceled'], true);
    $effectivePaymentStatusId = $statusType === 'both' ? $newPaymentStatusId : null;
    if ($isBookingCancellation) {
        $paymentStatusStmt = $conn->prepare("SELECT id FROM payment_statuses WHERE LOWER(TRIM(name)) IN ('cancelled', 'canceled') ORDER BY id LIMIT 1");
        $paymentStatusStmt->execute();
        $cancelledPaymentStatus = $paymentStatusStmt->get_result()->fetch_assoc();
        $paymentStatusStmt->close();
        if (!$cancelledPaymentStatus) {
            respond(409, ['success' => false, 'message' => 'A cancelled payment status is not configured. No status was changed.']);
        }
        $effectivePaymentStatusId = (int)$cancelledPaymentStatus['id'];
    }

    $bookingStmt = $conn->prepare('SELECT id FROM bookings WHERE id = ? LIMIT 1');
    $bookingStmt->bind_param('s', $bookingId);
    $bookingStmt->execute();
    $bookingExists = $bookingStmt->get_result()->num_rows > 0;
    $bookingStmt->close();
    if (!$bookingExists) {
        respond(404, ['success' => false, 'message' => 'Booking not found.']);
    }

    if (!$conn->begin_transaction()) {
        throw new RuntimeException('Could not start the status update transaction.');
    }
    if ($statusType === 'both' || $isBookingCancellation) {
        $updateStmt = $conn->prepare('UPDATE bookings SET status_id = ?, payment_status_id = ? WHERE id = ?');
        $updateStmt->bind_param('iis', $newStatusId, $effectivePaymentStatusId, $bookingId);
    } else {
        $statusColumn = $statusConfig[$statusType]['column'];
        $updateStmt = $conn->prepare("UPDATE bookings SET {$statusColumn} = ? WHERE id = ?");
        $updateStmt->bind_param('is', $newStatusId, $bookingId);
    }
    if (!$updateStmt->execute()) {
        throw new RuntimeException('Status update query failed.');
    }
    $updateStmt->close();
    if (!$conn->commit()) {
        throw new RuntimeException('Could not commit the status update transaction.');
    }
    $conn->close();
    respond(200, [
        'success' => true,
        'message' => ($statusType === 'both' || $isBookingCancellation) ? 'Booking and payment statuses updated.' : 'Status updated successfully.',
        'booking_status_id' => in_array($statusType, ['booking', 'both'], true) ? $newStatusId : null,
        'payment_status_id' => ($statusType === 'both' || $isBookingCancellation) ? $effectivePaymentStatusId : null,
    ]);
} catch (Throwable $exception) {
    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->rollback();
        } catch (Throwable $rollbackException) {
            error_log('Admin status rollback: ' . $rollbackException->getMessage());
        }
        $conn->close();
    }
    error_log('Admin status update: ' . $exception->getMessage());
    respond(500, ['success' => false, 'message' => 'The status could not be saved.']);
}
