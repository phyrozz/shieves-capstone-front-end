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
        // Retrieve form data
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone_number = $_POST["phonenumber"];
        $checkin = $_POST["checkin"];
        $checkout = $_POST["checkout"];
        $package = $_POST["packagename"];
        $description = "Booking for " . $name;
        $amount = $_POST["amount"];

        // Generate a UUID for the external_id
        $externalId = Uuid::uuid4()->toString();

        // The data to be sent in the POST request
        $data = [
            "external_id" => $externalId,
            "description" => $description,
            "amount" => $amount,
            "invoice_duration" => 172800,
            "currency" => "PHP",
            "reminder_time" => 1
        ];

        // Send the invoice creation request to the API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . $apiKey
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

            $stmt = $conn->prepare("INSERT INTO bookings (email, name, phone_number, time_in, time_out, package_id, invoice_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt === false) {
                throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
            }

            $stmt->bind_param("sssssis", $email, $name, $phone_number, $checkin, $checkout, $package, $invoiceId);

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute the SQL statement: " . $stmt->error);
            }

            $stmt->close();

            // Send a JSON response with the invoice URL
            echo json_encode(["status" => "success", "message" => "Booking successful", "invoice_url" => $invoiceUrl]);
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
