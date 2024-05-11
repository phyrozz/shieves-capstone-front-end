<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="tailwind.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
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
    <div class="flex md:flex-row flex-col justify-center flex-wrap bg-slate-800 gap-5 p-5">
        <a class="home-item" href="/bookings.php">
            <div class="md:w-80 w-full rounded-lg shadow-2xl shadow-slate-900 overflow-hidden">
                <div class="w-full h-full overflow-clip">
                    <img class="w-full h-80 object-cover" src="assets/booking.jpg" alt="Booking Link Image">
                </div>
                <div class="p-5 bg-slate-100 relative">
                    <lord-icon
                        class="absolute right-4 bottom-1/2 translate-y-1/2"
                        src="https://cdn.lordicon.com/wmlleaaf.json"
                        trigger="hover"
                        style="width:50px;height:50px">
                    </lord-icon> 
                    <h2 class="font-bold text-2xl">Book Now</h2>
                    <p>Reserve a booking online!</p>
                </div>
            </div>
        </a>
        <a class="home-item" href="/about-us.php">
            <div class="md:w-80 w-full rounded-lg shadow-2xl shadow-slate-900 overflow-hidden">
                <div class="w-full h-full overflow-clip">
                    <img class="object-cover w-full h-80" src="assets/about-us.jpg" alt="Booking Link Image">
                </div>
                <div class="p-5 bg-slate-100 relative">
                    <lord-icon
                        class="absolute right-4 bottom-1/2 translate-y-1/2"
                        src="https://cdn.lordicon.com/jnzhohhs.json"
                        trigger="hover"
                        style="width:50px;height:50px">
                    </lord-icon> 
                    <h2 class="font-bold text-2xl">About Us</h2>
                    <p>Know more about our resort.</p>
                </div>
            </div>
        </a>
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