<?php session_start(); ?>

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
    <link rel="stylesheet" type="text/css" href="/node_modules/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/node_modules/axios/dist/axios.min.js"></script>
    <script src="/node_modules/flatpickr/dist/flatpickr.min.js"></script>
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    <div class="h-screen w-full bg-gradient-to-br from-slate-950 to-violet-950 flex justify-center items-center">
        <div id="booking-form-container" class="container">
            <div class="max-w-md mx-auto bg-slate-950 rounded-lg shadow-2xl overflow-hidden">
                <div class="py-4 px-6">
                    <h2 class="text-3xl font-bold text-white font-satisfy text-center p-3">Book an Event</h2>
                    <form id="booking-form" method="POST" class="mt-4">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-300 font-bold text-sm pb-2 tracking-wide">NAME</label>
                            <input type="text" id="name" name="name" class="form-input w-full p-2 bg-gray-800 text-white rounded-md shadow-lg" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-300 font-bold text-sm pb-2 tracking-wide">EMAIL</label>
                            <input type="email" id="email" name="email" class="form-input w-full p-2 bg-gray-800 text-white rounded-md shadow-lg">
                        </div>
                        <div class="mb-4">
                            <label for="phonenumber" class="block text-gray-300 font-bold text-sm pb-2 tracking-wide">MOBILE NUMBER</label>
                            <input type="text" id="phonenumber" name="phonenumber" pattern="^09\d{9}$" title="Invalid mobile number" class="form-input w-full p-2 bg-gray-800 text-white rounded-md shadow-lg" required>
                        </div>
                        <div class="mb-4">
                            <label for="package" class="block text-gray-300 font-bold text-sm pb-2 tracking-wide">CHOOSE A PACKAGE</label>
                            <select type="text" id="package" name="package" class="form-input w-full p-2 bg-gray-800 text-white rounded-md shadow-lg" required>
                                <?php
                                include "./conn.php";
                                // Retrieve all package names
                                $packageStmt = $conn->prepare("SELECT id AS package_id, name, price FROM packages");
                                $packageStmt->execute();
                                $packagesResult = $packageStmt->get_result();
                                $packageStmt->close();
                                $conn->close();

                                while ($package = $packagesResult->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($package['package_id']) . "' data-name='" . htmlspecialchars($package['name']) . "' data-price='" . htmlspecialchars($package['price']) . "'>" . htmlspecialchars($package['name']) . " - PHP " . number_format(htmlspecialchars($package['price'])) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="checkinout" class="block text-gray-300 font-bold text-sm pb-2 tracking-wide">CHECK-IN & OUT</label>
                            <input id="checkinout" name="checkinout" type="text" class="form-input w-full p-2 bg-gray-800 text-white rounded-md shadow-lg" required />
                        </div>
                        
                        <div class="flex w-full justify-end items-center">
                            <button id="booknow" type="button" class="text-xs font-bold tracking-wider bg-gray-800 px-5 py-2 rounded-md hover:bg-gray-900 cursor-pointer transition-all text-white">BOOK NOW</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        gsap.from("#booking-form-container", { scale: 0, duration: 0.25, ease: "easeInOut" });

        flatpickr('#checkinout', {
            mode: "range",
            minDate: new Date().fp_incr(1),
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            maxDate: new Date().fp_incr(90),
        });

        document.getElementById("booknow").addEventListener("click", (event) => {
            event.preventDefault();

            // Validate the form
            const form = document.getElementById("booking-form");
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Get selected package details
            const packageSelect = document.getElementById('package');
            const selectedOption = packageSelect.options[packageSelect.selectedIndex];
            const packageName = selectedOption.getAttribute('data-name');
            const packagePrice = selectedOption.getAttribute('data-price');

            // Submit form data to generate invoice
            const formData = new FormData(form);
            formData.append('package_name', packageName);
            formData.append('package_price', packagePrice);

            axios.post('api/bookings/generate_invoice.php', formData, { responseType: 'blob' })
            .then(response => {
                if (response.status === 'error') {
                    Swal.fire({
                        title: "Duplicate Booking",
                        text: response.data.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                    return;
                } else {
                    Swal.fire({
                        title: 'Booking Successful!',
                        text: 'An invoice will be generated for you. Please download and present it to the receptionist to verify your booking.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }

                // Generate invoice if no error
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Invoice.pdf');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            })
            .catch(error => {
                Swal.fire({
                    title: "Error",
                    text: "There was an error generating your invoice. Please try again later.",
                    icon: "error"
                });
            });
        });

        // document.getElementById("booknow").addEventListener("click", (event) => {
        //     event.preventDefault();

        //     // Validate the form
        //     const form = document.getElementById("booking-form");
        //     if (!form.checkValidity()) {
        //         form.reportValidity();
        //         return;
        //     }

        //     // Get selected package details
        //     const packageSelect = document.getElementById('package');
        //     const selectedOption = packageSelect.options[packageSelect.selectedIndex];
        //     const packageName = selectedOption.getAttribute('data-name');
        //     const packagePrice = selectedOption.getAttribute('data-price');

        //     // Show confirmation dialog
        //     Swal.fire({
        //         title: "Do you wish to proceed?",
        //         text: "You will be redirected to another page for online payment. Make sure to prepare your payment before proceeding.",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Yes",
        //         cancelButtonText: "No",
        //         reverseButtons: true
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             const formData = new FormData(form);
        //             formData.append('description', packageName);
        //             formData.append('amount', packagePrice);

        //             axios.post('api/bookings/submit_booking.php', formData)
        //                 .then(response => {
        //                     if (response.data.status === 'success') {
        //                         // Redirect to the invoice URL
        //                         window.location.href = response.data.invoice_url;
        //                     } else {
        //                         Swal.fire({
        //                             title: "Error",
        //                             text: response.data.message,
        //                             icon: "error"
        //                         });
        //                     }
        //                 })
        //                 .catch(error => {
        //                     Swal.fire({
        //                         title: "Error",
        //                         text: "There was an error submitting your booking. Please try again.",
        //                         icon: "error"
        //                     });
        //                 });
        //         } else if (result.dismiss === Swal.DismissReason.cancel) {
        //             Swal.fire({
        //                 title: "Cancelled",
        //                 text: "You have cancelled your book",
        //                 icon: "error"
        //             });
        //         }
        //     })
        // });

        // Check for success message in session and display SweetAlert
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({
                title: 'Payment Successful!',
                text: '" . $_SESSION['success_message'] . "',
                icon: 'success',
                confirmButtonText: 'OK'
            });";
            unset($_SESSION['success_message']); // Clear success message after displaying
        }
        ?>
    </script>
</body>
</html>
