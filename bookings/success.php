<?php
session_start();

require '../vendor/autoload.php';
include "../conn.php";

if (isset($_GET['booking_id'])) {
    $env = parse_ini_file('../.env');

    $bookingId = $_GET['booking_id'];

    $stmt = $conn->prepare("SELECT invoice_id FROM bookings WHERE id = ?");
    if ($stmt === false) {
        error_log("Failed to prepare the SQL statement: " . $conn->error);
        echo json_encode(["status" => "error", "message" => "Failed to prepare the SQL statement"]);
        exit;
    }

    $stmt->bind_param("i", $bookingId);
    if (!$stmt->execute()) {
        error_log("Failed to execute the SQL statement: " . $stmt->error);
        echo json_encode(["status" => "error", "message" => "Failed to execute the SQL statement"]);
        exit;
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $invoiceId = $row['invoice_id'];    
    $stmt->close();

    if (!$invoiceId) {
        echo json_encode(["status" => "error", "message" => "Cannot process request"]);
        exit;
    }

    // Fetch the booking ID from Xendit invoice metadata
    $apiKey = $env['XENDIT_API_KEY'];
    $url = 'https://api.xendit.co/v2/invoices/' . $invoiceId;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $apiKey
    ]);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Error fetching invoice: ' . curl_error($ch));
        echo json_encode(["status" => "error", "message" => "Error fetching invoice"]);
        exit;
    }

    $responseData = json_decode($response, true);

    if (isset($responseData['metadata']['booking_id'])) {
        $bookingId = $responseData['metadata']['booking_id'];

        // Update booking status in the database
        $stmt = $conn->prepare("UPDATE bookings SET payment_status_id = 2 WHERE id = ?");
        if ($stmt === false) {
            error_log("Failed to prepare the SQL statement: " . $conn->error);
            echo json_encode(["status" => "error", "message" => "Failed to prepare the SQL statement"]);
            exit;
        }

        $stmt->bind_param("i", $bookingId);
        if (!$stmt->execute()) {
            error_log("Failed to execute the SQL statement: " . $stmt->error);
            echo json_encode(["status" => "error", "message" => "Failed to execute the SQL statement"]);
            exit;
        }

        $stmt->close();

        // Set success message in session
        $_SESSION['success_message'] = "Payment has been processed successfully! Please proceed to our resort to verify your payment.";
        
        // Redirect to bookings.php
        header("Location: ../bookings.php");
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid invoice metadata"]);
    }

    curl_close($ch);
} else {
    echo json_encode(["status" => "error", "message" => "Cannot process request"]);
}
?>
