<?php
require '../../vendor/autoload.php';
include "../../conn.php";

use Ramsey\Uuid\Uuid;

// Get environment variables
$env = parse_ini_file('../../.env');

$apiKey = $env['XENDIT_API_KEY'];
$url = 'https://api.xendit.co/v2/invoices';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data with proper sanitation of inputs
        $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $phone_number = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_STRING);
        $dateRange = filter_input(INPUT_POST, "checkinout", FILTER_SANITIZE_STRING);
        $package = filter_input(INPUT_POST, "package", FILTER_SANITIZE_STRING);
        $amount = filter_input(INPUT_POST, "amount", FILTER_VALIDATE_FLOAT);
        $packagename = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);

        // Validate required fields
        if (!$name || !$email || !$phone_number || !$dateRange || !$package || !$amount) {
            throw new Exception("Invalid input");
        }

        // Split the date range into check-in and check-out dates
        $dates = explode(" to ", $dateRange);

        // Handle cases where only one date is provided
        if (count($dates) == 1) {
            $checkin = $dates[0];
            $checkout = $dates[0]; // Same day for check-in and check-out
        } else {
            $checkin = $dates[0];
            $checkout = $dates[1];
        }

        // Calculate the invoice duration in seconds
        $checkinTimestamp = strtotime($checkin);
        $currentTimestamp = time();
        $invoiceDuration = $checkinTimestamp - $currentTimestamp;

        // Ensure the invoice duration is a positive number
        if ($invoiceDuration < 0) {
            throw new Exception("Check-in date must be in the future.");
        }

        // Ensure the email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address");
        }

        // Generate a UUID for the external_id and booking_id
        $externalId = Uuid::uuid4()->toString();
        $bookingId = Uuid::uuid4()->toString();

        // Description for the invoice
        $description = "Booking for " . $name . " - " . $packagename;

        // Insert the booking into the database
        $stmt = $conn->prepare("INSERT INTO bookings (id, email, name, phone_number, time_in, time_out, package_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
        }

        $stmt->bind_param("ssssssi", $bookingId, $email, $name, $phone_number, $checkin, $checkout, $package);

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute the SQL statement: " . $stmt->error);
        }

        $stmt->close();

        // The data to be sent in the POST request
        $data = [
            "external_id" => $externalId,
            "description" => $description,
            "amount" => $amount,
            "invoice_duration" => $invoiceDuration,
            "currency" => "PHP",
            "reminder_time" => 1,
            "success_redirect_url" => "http://localhost/bookings/success.php?booking_id=" . $bookingId,
            "metadata" => [
                "booking_id" => $bookingId
            ]
        ];

        // Send the invoice creation request to the API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Error creating invoice: ' . curl_error($ch));
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        if (isset($responseData['id']) && isset($responseData['invoice_url'])) {
            $invoiceId = $responseData['id'];
            $invoiceUrl = $responseData['invoice_url'];

            // Update the booking with the invoice ID
            $stmt = $conn->prepare("UPDATE bookings SET invoice_id = ? WHERE id = ?");

            if ($stmt === false) {
                throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
            }

            $stmt->bind_param("ss", $invoiceId, $bookingId);

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute the SQL statement: " . $stmt->error);
            }

            $stmt->close();

            // Send a JSON response with the invoice URL and invoice ID
            echo json_encode([
                "status" => "success",
                "message" => "Booking successful",
                "invoice_url" => $invoiceUrl,
                "invoice_id" => $invoiceId
            ]);
        } else {
            throw new Exception("Failed to create invoice: " . $responseData['message']);
        }

        curl_close($ch);
    }
} catch (Exception $e) {
    error_log($e->getMessage());

    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
