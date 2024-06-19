<?php
include "../../conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // For some reason, this line makes php read JSON post requests
    // Deleting this line will make booking_id and new_status_id null
    $_POST = json_decode(file_get_contents("php://input"), true);

    // Extract the booking ID and the new status ID from the POST data
    $bookingId = $_POST["booking_id"];
    $newStatusId = $_POST["new_status_id"];

    // Prepare and execute the UPDATE query to update the status of the booking
    $stmt = $conn->prepare("UPDATE bookings SET status_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $newStatusId, $bookingId);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status"]);
    }

    $stmt->close();
    $conn->close();
} else {
    // If the request method is not POST, return an error message
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
