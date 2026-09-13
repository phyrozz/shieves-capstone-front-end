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
    <link rel="stylesheet" href="css/theme.css">
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
<body class="min-h-screen bg-secondary text-primary select-none">
    <?php include "./components/navbar.php"; ?>
    <main class="relative min-h-screen overflow-hidden px-5 pb-12 pt-28 sm:px-8 md:pt-32">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-[var(--color-secondary)] via-[var(--color-secondary)] to-[var(--color-primary)] opacity-70"></div>
        <div class="absolute -left-28 top-32 -z-10 h-80 w-80 rounded-full bg-primary opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-20 -z-10 h-96 w-96 rounded-full bg-tertiary opacity-20 blur-3xl"></div>

        <div id="booking-form-container" class="mx-auto grid w-full max-w-6xl overflow-hidden rounded-3xl bg-secondary shadow-2xl lg:grid-cols-5">
            <aside class="flex flex-col justify-between bg-tertiary p-8 text-secondary sm:p-10 lg:col-span-2">
                <div>
                    <p class="font-satisfy text-4xl">Your resort escape</p>
                    <div class="mt-8 h-px w-16 bg-accent"></div>
                    <h1 class="mt-6 text-3xl font-bold leading-tight">Make your next celebration memorable.</h1>
                    <p class="mt-4 text-sm leading-6 text-secondary opacity-80">Reserve your event date in a few simple steps. We’ll prepare an invoice for your booking after submission.</p>
                </div>
                <div class="mt-10 grid gap-3 text-sm sm:grid-cols-3 lg:grid-cols-1">
                    <p><span class="mr-2 text-accent">01</span>Choose a package</p>
                    <p><span class="mr-2 text-accent">02</span>Select your dates</p>
                    <p><span class="mr-2 text-accent">03</span>Receive your invoice</p>
                </div>
            </aside>

            <section class="p-7 sm:p-10 lg:col-span-3">
                <div class="mb-8">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] opacity-60">Online reservation</p>
                    <h2 class="mt-2 font-satisfy text-5xl font-bold">Book an event</h2>
                    <p class="mt-2 text-sm opacity-75">Tell us a little about your preferred stay.</p>
                </div>
                <form id="booking-form" method="POST" class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-xs font-bold tracking-wider">FULL NAME</label>
                            <input type="text" id="name" name="name" autocomplete="name" class="form-input w-full rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" required>
                        </div>
                        <div>
                            <label for="email" class="mb-2 block text-xs font-bold tracking-wider">EMAIL ADDRESS</label>
                            <input type="email" id="email" name="email" autocomplete="email" class="form-input w-full rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" required>
                        </div>
                        <div>
                            <label for="phonenumber" class="mb-2 block text-xs font-bold tracking-wider">MOBILE NUMBER</label>
                            <input type="tel" id="phonenumber" name="phonenumber" autocomplete="tel" pattern="^09\d{9}$" title="Use an 11-digit mobile number beginning with 09" placeholder="09XXXXXXXXX" class="form-input w-full rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" required>
                        </div>
                        <div>
                            <label for="package" class="mb-2 block text-xs font-bold tracking-wider">CHOOSE A PACKAGE</label>
                            <select id="package" name="package" class="form-input w-full rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" required>
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
                        <div class="sm:col-span-2">
                            <label for="checkinout" class="mb-2 block text-xs font-bold tracking-wider">EVENT DATE RANGE</label>
                            <input id="checkinout" name="checkinout" type="text" placeholder="Select your preferred dates" class="form-input w-full rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" required>
                        </div>
                        <div class="flex flex-col gap-3 border-t border-[var(--color-primary-shadow)] pt-5 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs leading-5 opacity-70">You’ll receive a downloadable invoice to present at the resort.</p>
                            <button id="booknow" type="button" class="rounded-xl bg-tertiary px-6 py-3 text-xs font-bold tracking-wider text-secondary transition hover:scale-[1.01] hover:bg-primary-shadow focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]">GENERATE BOOKING INVOICE</button>
                        </div>
                </form>
            </section>
        </div>
    </main>
    <script>
        // validate name field (must only accept letters and spaces)
        document.getElementById("name").addEventListener("input", function() {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });

        // change navbar text color to secondary when the page has loaded
        document.getElementById("navbar").classList.remove("bg-transparent");
        document.getElementById("navbar").classList.add("bg-tertiary");
        document.getElementById("navbar").classList.add("shadow-lg");

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

            // trim whitespace from name, email, phone number
            formData.set('name', formData.get('name').trim());
            formData.set('email', formData.get('email').trim());
            formData.set('phonenumber', formData.get('phonenumber').trim());

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
                        text: 'An invoice will be generated for you. Please check downloaded invoice and present it to the receptionist to verify your booking.',
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
