<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="tailwind.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <title>Museo de San Pedro</title>    
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    <div class="flex flex-col h-screen justify-center items-center">
        <p class="header-text font-bold text-lg text-slate-100">Welcome to</p>
        <h1 class="font-satisfy mt-2 header-text font-extrabold text-6xl text-center text-slate-100">Museo de San Pedro</h1>
        <p id="scroll-down-text" class="absolute m-auto bottom-5 text-slate-100">Scroll down to get started</p>
        <div id="header-bg" class="absolute -z-50 bg-[url(/assets/bg.jpg)] filter brightness-50 bg-cover mask h-screen w-full"></div>
    </div>

    <div class="flex md:flex-row flex-col justify-center flex-wrap bg-gradient-to-br from-slate-950 to-violet-950 gap-5 p-5">
        <div class="m-10 flex flex-col gap-10 items-center">
            <div class="text-white md:mx-40 mx-0 md:text-left text-center">
                <h1 class="font-satisfy text-6xl font-bold mb-10">Where to find?</h1>
                <p class="text-sm font-bold mb-5">Museo De San Pedro is located at #122 Magsasaysay, San Pedro City, Laguna.</p>
                <p class="text-sm">It's only a 17-minute drive from HBC San Pedro. You can also take a jeepney ride on the "estrella trip" route, heading to Magsasay (beside HBC). Advise the driver to drop you off at "Museo De San Pedro" in front of Villa Consolascion. Alternatively, use the Waze Mobile App to locate us.</p>
                <div class="rounded-2xl overflow-hidden my-10 shadow-2xl shadow-slate-300">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d941.3497232969933!2d121.03387312189574!3d14.338998936361174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d6e6c433d123%3A0x4be58cfd35018b40!2sMuseo%20de%20San%20Pedro!5e1!3m2!1sen!2sph!4v1714867684158!5m2!1sen!2sph" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            
            <div class="w-full text-center mt-5 mb-0">
                <h1 class="font-satisfy text-6xl font-bold text-slate-50">All In Packages</h1>
            </div>
            <div class="flex flex-row gap-3 flex-wrap justify-center cursor-default">
                <!-- Package containers -->
                <div class="package-container md:w-96 w-full bg-gradient-to-tr from-violet-950 to-indigo-900 p-10 flex flex-col gap-5 rounded-lg shadow-xl text-center text-white hover:shadow-2xl hover:shadow-slate-300 transition-shadow">
                    <!-- Package name, pax, and price -->
                    <div class="flex flex-col gap-1">
                        <p class="font-black text-4xl">PHP 50,000</p>
                        <p class="font-extrabold text-xs">(50 pax)</p>
                    </div>
                    <!-- Food caterings, decorations, and venue -->
                    <div class="flex flex-row flex-wrap gap-5 justify-between">
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Food Catering</h1>
                            <ul class="text-sm">
                                <li>4 Main Course</li>
                                <li>Unli Rice</li>
                                <li>Unli Iced Tea/Water</li>
                                <li>Dessert</li>
                                <li>Waiters</li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Decorations</h1>
                            <ul class="text-sm">
                                <li>Theme of your choice set-up</li>
                                <li>Celebrant's chair</li>
                                <li>Flower/Balloon Arrangement</li>
                                <li>Tables and chairs set-up</li>
                                <li>Cake Table set-up</li>
                                <li>Back Drop set-up</li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Venue</h1>
                            <ul class="text-sm">
                                <li>Function Area</li>
                                <li>Swimming Pool</li>
                                <li>Basic Sound System</li>
                                <li>3 Air-conditioned System</li>
                                <li>Videoke Room</li>
                                <li>1 Kubo</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Package containers -->
                <div class="package-container md:w-96 w-full bg-gradient-to-tr from-violet-950 to-indigo-900 p-10 flex flex-col gap-5 rounded-lg shadow-xl text-center text-white hover:shadow-2xl hover:shadow-slate-300 transition-shadow">
                    <!-- Package name, pax, and price -->
                    <div class="flex flex-col gap-1">
                        <p class="font-black text-4xl">PHP 60,000</p>
                        <p class="font-extrabold text-xs">(75 pax)</p>
                    </div>
                    <!-- Food caterings, decorations, and venue -->
                    <div class="flex flex-row flex-wrap gap-5 justify-between">
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Food Catering</h1>
                            <ul class="text-sm">
                                <li>4 Main Course</li>
                                <li>Unli Rice</li>
                                <li>Unli Iced Tea/Water</li>
                                <li>Dessert</li>
                                <li>Waiters</li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Decorations</h1>
                            <ul class="text-sm">
                                <li>Theme of your choice set-up</li>
                                <li>Celebrant's chair</li>
                                <li>Flower/Balloon Arrangement</li>
                                <li>Tables and chairs set-up</li>
                                <li>Cake Table set-up</li>
                                <li>Back Drop set-up</li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Venue</h1>
                            <ul class="text-sm">
                                <li>Function Area</li>
                                <li>Swimming Pool</li>
                                <li>Basic Sound System</li>
                                <li>3 Air-conditioned System</li>
                                <li>Videoke Room</li>
                                <li>1 Kubo</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="package-container md:w-96 w-full bg-gradient-to-tr from-violet-950 to-indigo-900 p-10 flex flex-col gap-5 rounded-lg shadow-xl text-center text-white hover:shadow-2xl hover:shadow-slate-300 transition-shadow">
                    <!-- Package name, pax, and price -->
                    <div class="flex flex-col gap-1">
                        <p class="font-black text-4xl">PHP 70,000</p>
                        <p class="font-extrabold text-xs">(100 pax)</p>
                    </div>
                    <!-- Food caterings, decorations, and venue -->
                    <div class="flex flex-row flex-wrap gap-5 justify-between">
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Food Catering</h1>
                            <ul class="text-sm">
                                <li>4 Main Course</li>
                                <li>Unli Rice</li>
                                <li>Unli Iced Tea/Water</li>
                                <li>Dessert</li>
                                <li>Waiters</li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Decorations</h1>
                            <ul class="text-sm">
                                <li>Theme of your choice set-up</li>
                                <li>Celebrant's chair</li>
                                <li>Flower/Balloon Arrangement</li>
                                <li>Tables and chairs set-up</li>
                                <li>Cake Table set-up</li>
                                <li>Back Drop set-up</li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 m-auto">
                            <h1 class="font-satisfy text-2xl">Venue</h1>
                            <ul class="text-sm">
                                <li>Function Area</li>
                                <li>Swimming Pool</li>
                                <li>Basic Sound System</li>
                                <li>3 Air-conditioned System</li>
                                <li>Videoke Room</li>
                                <li>1 Kubo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-0 w-full">
                <p class="text-white text-center">Ready to dig in? <a class="hover:underline font-bold" href="./bookings.php">Book now</a> online!</p>
                <p class="text-white text-center">or you can contact us at <a class="hover:underline font-bold" href="mailto:info@museodesanpedro.com">info@museodesanpedro.com</a></p>
            </div>
        </div>
    </div>
    <script>
        // GSAP animation events
        document.addEventListener("DOMContentLoaded", () => {
            gsap.from(".header-text", { opacity: 0, y: 75, duration: 0.50, ease: "easeOut" });
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
        document.querySelectorAll('.home-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                onHomeItemHoverIn(item);
            });
        });
        document.querySelectorAll('.home-item').forEach(item => {
            item.addEventListener('mouseleave', () => {
                onHomeItemHoverOut(item);
            });
        });
        document.querySelectorAll('.package-container').forEach(item => {
            item.addEventListener('mouseenter', () => {
                onPackageItemHoverIn(item);
            });
        });
        document.querySelectorAll('.package-container').forEach(item => {
            item.addEventListener('mouseleave', () => {
                onPackageItemHoverOut(item);
            });
        });


        function bounceNavbarItem(target) {
            var tl = new TimelineMax({ paused: true });
            tl.to(target, { y: -20, duration: 0.1, ease: "easeIn" })
            .to(target, { y: 0, duration: 0.1, ease: "easeOut" });

            if (!tl.isActive()) {
                tl.play(0);
            }
        }

        function onHomeItemHoverIn(target) {
            var tl = new TimelineMax({ paused: true });
            tl.to(target, { scale: 1.05, duration: 0.1, ease: "easeIn" });

            if (!tl.isActive()) {
                tl.play(0);
            }
        }

        function onHomeItemHoverOut(target) {
            var tl = new TimelineMax({ paused: true });
            tl.to(target, { scale: 1, duration: 0.25, ease: "easeOut" });

            if (!tl.isActive()) {
                tl.play(0);
            }
        }

        function onPackageItemHoverIn(target) {
            var tl = new TimelineMax({ paused: true });
            tl.to(target, { scale: 1.05, duration: 0.1, ease: "easeIn" });

            if (!tl.isActive()) {
                tl.play(0);
            }
        };

        function onPackageItemHoverOut(target) {
            var tl = new TimelineMax({ paused: true });
            tl.to(target, { scale: 1, duration: 0.25, ease: "easeOut" });

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

            console.log(currentScrollPos)
            if (currentScrollPos > 350) {
                gsap.to("#header-bg", 0.50, {filter: "brightness(50%) blur(8px)"});
                scrollDownText.style.display = "none";
                
            } else {
                gsap.to("#header-bg", 0.50, {filter: "brightness(50%) blur(0px)"});
                scrollDownText.style.display = "block";
            }

            prevScrollpos = currentScrollPos;
        };
    </script>
</body>
</html>