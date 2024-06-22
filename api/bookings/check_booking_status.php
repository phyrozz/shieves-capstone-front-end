<?php
require '../../vendor/autoload.php';
include "../../conn.php";

// Get environment variables
$env = parse_ini_file('../../.env');
$apiKey = $env['XENDIT_API_KEY'];

if (isset($_GET['invoice_id'])) {
    $invoiceId = $_GET['invoice_id'];
    $url = "https://api.xendit.co/v2/invoices/$invoiceId";

    // Initialize cURL session
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . $apiKey,
    ]);

    // Execute cURL session
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo json_encode(['status' => 'error', 'message' => curl_error($ch)]);
        exit();
    }

    // Decode the JSON response
    $responseData = json_decode($response, true);
    curl_close($ch);

    // Check if the invoice is paid
    if (isset($responseData['status']) && $responseData['status'] == 'PAID') {
        // Update the payment_status_id in the bookings table
        $stmt = $conn->prepare("UPDATE bookings SET payment_status_id = ? WHERE invoice_id = ?");
        $paymentStatusId = 1; // Assuming 2 represents 'PAID'
        $stmt->bind_param("is", $paymentStatusId, $invoiceId);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => 'Payment successful']);
    } else {
        echo json_encode(['status' => 'pending', 'message' => 'Payment is still pending']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invoice ID is required']);
}
