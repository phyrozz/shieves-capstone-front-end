<?php
include "./conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone_number = $_POST["phonenumber"];

    $checkin = $_POST["checkin"];
    $checkout = $_POST["checkout"];
    $package = $_POST["packagename"];
    $stmt = $conn->prepare("INSERT INTO bookings (email, name, phone_number, time_in, time_out, package_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $email, $name, $phone_number, $checkin, $checkout, $package);
    $stmt->execute();

    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Retrieve all package names
$packageStmt = $conn->prepare("SELECT id AS package_id, name, price FROM packages");
$packageStmt->execute();
$packagesResult = $packageStmt->get_result();
$packageStmt->close();
$conn->close();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    <div class="h-screen w-full bg-gradient-to-br from-slate-950 to-violet-950 flex justify-center items-center">
        <div id="booking-form-container" class="container">
            <div class="max-w-md mx-auto bg-gradient-to-t from-slate-300 to-slate-200 rounded-lg shadow-2xl overflow-hidden">
                <div class="py-4 px-6">
                    <h2 class="text-3xl font-bold text-gray-800 font-satisfy text-center p-3">Book an Event</h2>
                    <form id="booking-form" method="POST" class="mt-4">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-bold text-sm pb-2 tracking-wide">NAME</label>
                            <input type="text" id="name" name="name" class="form-input w-full p-2 rounded-md shadow-lg">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-bold text-sm pb-2 tracking-wide">EMAIL</label>
                            <input type="email" id="email" name="email" class="form-input w-full p-2 rounded-md shadow-lg">
                        </div>
                        <div class="mb-4">
                            <label for="phonenumber" class="block text-gray-700 font-bold text-sm pb-2 tracking-wide">MOBILE NUMBER</label>
                            <input type="text" id="phonenumber" name="phonenumber" pattern="09\d{2}-\d{3}-\d{4}" class="form-input w-full p-2 rounded-md shadow-lg">
                        </div>
                        <div class="mb-4">
                            <label for="packagename" class="block text-gray-700 font-bold text-sm pb-2 tracking-wide">CHOOSE A PACKAGE</label>
                            <select type="text" id="packagename" name="packagename" class="form-input w-full p-2 rounded-md shadow-lg">
                                <?php
                                while ($package = $packagesResult->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($package['package_id']) . "'>" . htmlspecialchars($package['name']) . " - PHP " . number_format(htmlspecialchars($package['price'])) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="checkin" class="block text-gray-700 font-bold text-sm pb-2 tracking-wide">CHECK-IN DATE</label>
                            <input type="date" id="checkin" name="checkin" min class="form-input w-full p-2 rounded-md shadow-lg">
                        </div>
                        <div class="mb-6">
                            <label for="checkout" class="block text-gray-700 font-bold text-sm pb-2 tracking-wide">CHECK-OUT DATE</label>
                            <input type="date" id="checkout" name="checkout" class="form-input w-full p-2 rounded-md shadow-lg">
                        </div>
                        <div class="flex w-full justify-end items-center">
                            <button id="booknow" type="button" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">Book Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        gsap.from("#booking-form-container", { scale: 0, duration: 0.25, ease: "easeInOut" });

        // Add dynamic min and max date values for the check in and out date pickers
        const checkinInput = document.getElementById('checkin');
        const checkoutInput = document.getElementById('checkout');

        // Set the minimum check-in date to today
        const today = new Date().toISOString().split('T')[0];
        checkinInput.min = today;

        // Event listener to update the checkout date based on the check-in date
        checkinInput.addEventListener('change', function() {
            const checkinDate = new Date(this.value);
            checkinDate.setDate(checkinDate.getDate() + 1); // Minimum checkout is the day after check-in

            const minCheckoutDate = checkinDate.toISOString().split('T')[0];
            checkoutInput.min = minCheckoutDate;
            checkoutInput.value = ''; // Reset the checkout date if check-in date changes

            // Optionally, set a max checkout date (e.g., max 30 days after check-in)
            checkinDate.setDate(checkinDate.getDate() + 29); // 30 days total including the first day
            const maxCheckoutDate = checkinDate.toISOString().split('T')[0];
            checkoutInput.max = maxCheckoutDate;
        });


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

            const button = document.getElementById("booknow");

        button.addEventListener("click", (event) => {
            event.preventDefault()
            Swal.fire({
        title: "Do you wish to proceed?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        reverseButtons: true
        }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
            title: "Booked",
            text: "You have successfully booked",
            icon: "success"
            }).then(() => {
                document.getElementById("booking-form").submit();
            });
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            Swal.fire({
            title: "Cancelled",
            text: "You have cancelled your book",
            icon: "error"
            });
        }
        });
        });    

        
    </script>
</body>
</html>
