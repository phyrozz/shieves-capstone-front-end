<?php
include "../conn.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("INSERT INTO registered_users (full_name, address, contact_number, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $address, $contact_number, $email, $password);

    if ($stmt->execute()) {
        echo "Signup successful!";
        header("Location: ../bookings.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>