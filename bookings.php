<?php
if (isset($_POST["submit"])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "museodesanpedro";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = $_POST["name"];
    $email = $_POST["email"];
    $checkin = $_POST["checkin"];
    $checkout = $_POST["checkout"];

    $stmt = $conn->prepare("INSERT INTO bookings (email, name, time_in, time_out) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $checkin, $checkout);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="tailwind.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
</head>
<body class="bg-gray-100">
    <?php include "./components/navbar.php"; ?>
    <div class="container mx-auto mt-28">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="py-4 px-6">
                <h2 class="text-2xl font-bold text-gray-800">Book a Event</h2>
                <form method="POST" class="mt-4">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
                        <input type="text" id="name" name="name" class="form-input w-full">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                        <input type="email" id="email" name="email" class="form-input w-full">
                    </div>
                    <div class="mb-4">
                        <label for="checkin" class="block text-gray-700 font-semibold mb-2">Check-in Date</label>
                        <input type="date" id="checkin" name="checkin" class="form-input w-full">
                    </div>
                    <div class="mb-6">
                        <label for="checkout" class="block text-gray-700 font-semibold mb-2">Check-out Date</label>
                        <input type="date" id="checkout" name="checkout" class="form-input w-full">
                    </div>
                    <button type="submit" name="submit" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-200">Book Now</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("login-btn").addEventListener("mouseenter", () => {
            var tl = new TimelineMax({ paused: true });
            tl.from("#login-btn-icon", {x: 50, duration: 0.20 })
            tl.from("#login-btn-text", { opacity: 0, x: 20, duration: 0.25 });

            if (!tl.isActive()) {
                tl.play(0);
            }
        });
        document.querySelectorAll('.navbar-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                bounceNavbarItem(item);
            });
        });

        function bounceNavbarItem(target) {
            var tl = new TimelineMax({ paused: true });
            tl.to(target, { y: -20, duration: 0.1, ease: "easeIn" })
            .to(target, { y: 0, duration: 0.1, ease: "easeOut" });

            if (!tl.isActive()) {
                tl.play(0);
            }
        };

        let prevScrollpos = window.pageYOffset;
        window.onscroll = function () {
            let currentScrollPos = window.pageYOffset;
            let scrollDownText = document.getElementById("scroll-down-text");
            let navbar = document.getElementById("navbar");

            if (prevScrollpos > currentScrollPos) {
                navbar.style.top = "0";
            } else {
                navbar.style.top = "-75px";
            }

            prevScrollpos = currentScrollPos;
        };
    </script>
</body>
</html>
