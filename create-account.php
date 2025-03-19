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
    <link rel="stylesheet" href="../tailwind.css">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
</head>
<body>
    <div class="w-screen h-screen bg-gradient-to-br from-slate-950 to-violet-950 flex justify-center items-center text-slate-50">
        <div id="signup-container" class="w-96 bg-slate-950 flex flex-col items-start rounded-lg shadow-2xl shadow-slate-950 px-8 py-10">
            <h1 class="font-black text-3xl font-satisfy">Create New Account</h1>
            <form class="flex flex-col w-full py-5" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <?php if (!empty($errors)): ?>
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="w-full flex flex-col">
                    <span class="text-xs font-bold tracking-wider text-slate-400">FULL NAME</span>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name ?? '') ?>" class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all text-white" required>
                </div>

                <div class="w-full flex flex-col">
                    <span class="text-xs font-bold tracking-wider text-slate-400">ADDRESS</span>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($address ?? '') ?>" class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all text-white" required>
                </div>

                <div class="w-full flex flex-col">
                    <span class="text-xs font-bold tracking-wider text-slate-400">CONTACT NUMBER</span>
                    <input
                        type="text"
                        id="contact_number" 
                        name="contact_number" 
                        value="<?= htmlspecialchars($contact_number ?? '') ?>" 
                        class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all text-white" 
                        pattern="09[0-9]{9}" 
                        title="Invalid phone number." 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" 
                        required
                    >
                </div>

                <div class="w-full flex flex-col">
                    <span class="text-xs font-bold tracking-wider text-slate-400">EMAIL</span>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all text-white" required>
                </div>

                <div class="w-full flex flex-col">
                    <span class="text-xs font-bold tracking-wider text-slate-400">PASSWORD</span>
                    <input type="password" id="password" name="password" class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all text-white" required>
                </div>

                <div class="w-full flex flex-row justify-end items-center">
                    <input id="signup-btn" type="submit" class="text-xs font-bold tracking-wider bg-gray-800 px-5 py-2 rounded-md hover:bg-gray-900 cursor-pointer transition-all" value="SIGN UP">
                </div>
            </form>
        </div>
    </div>

    <script>
        gsap.from("#signup-container", { scale: 0, duration: 0.25, ease: "easeInOut" });
    </script>
</body>
</html>
