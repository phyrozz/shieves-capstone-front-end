<?php
include "../conn.php";

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract the booking ID and the new status ID from the POST data
    $bookingId = $_POST["booking_id"];
    $newStatusId = $_POST["new_status_id"];

    // Prepare and execute the UPDATE query to update the status of the booking
    $updateStmt = $conn->prepare("UPDATE bookings SET status_id = ? WHERE id = ?");
    $updateStmt->bind_param("ii", $newStatusId, $bookingId);
    
    if ($updateStmt->execute()) {
        // If the query is successful, return a success message
        echo json_encode(["success" => true, "message" => "Status updated successfully"]);
    } else {
        // If there's an error, return an error message
        echo json_encode(["success" => false, "message" => "Failed to update status"]);
    }

    // Close the prepared statement and the database connection
    $updateStmt->close();
    $conn->close();
} else {
    // If the request method is not POST, return an error message
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
