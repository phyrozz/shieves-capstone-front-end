<?php
include "conn.php";

function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = validate_input($_POST['full_name']);
    $address = validate_input($_POST['address']);
    $contact_number = validate_input($_POST['contact_number']);
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);

    // Validate Full Name
    if (empty($full_name) || !preg_match("/^[a-zA-Z ]*$/", $full_name)) {
        $errors[] = "Full Name is required and should only contain letters and spaces.";
    }

    // Validate Address
    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    // Validate Contact Number
    if (empty($contact_number) || !preg_match("/^[0-9]{10,15}$/", $contact_number)) {
        $errors[] = "Contact Number is required and should contain 10 to 15 digits.";
    }

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }

    // Validate Password
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $stmt = $conn->prepare("INSERT INTO registered_users (full_name, address, contact_number, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $address, $contact_number, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Signup successful!";
            header("Location: bookings.php");
            exit(); 
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <div class="container">
        <h2>Registration Form</h2>
        <form action="api/signup_process.php" method="post">
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name ?? '') ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($address ?? '') ?>" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($contact_number ?? '') ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Sign Up">
        </form>
    </div>
</body>
</html>