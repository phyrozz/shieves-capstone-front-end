<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="tailwind.css">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/swiper.css">
    <link rel="stylesheet" href="node_modules/swiper/swiper-bundle.min.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="node_modules/swiper/swiper-bundle.min.js"></script>
    <title>J.M. Apilado Resort - Showcase</title>
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    <div class="flex flex-col h-[75vh] justify-center items-center text-secondary cursor-default select-none">
        <h1 class="text-7xl font-satisfy font-bold text-center w-full p-10">Showcase</h1>
        <p class="font-bold uppercase tracking-wider">About our Resort</p>
        <div id="header-bg" class="absolute -z-50 bg-[url(/assets/showcase-header.jpg)] filter brightness-50 bg-cover mask h-screen w-full"></div>
    </div>

    <div class="flex md:flex-row flex-col justify-center flex-wrap bg-gradient-to-br bg-secondary gap-5 select-none">
        <!-- Showcase component -->
        <div class="grid grid-cols-2 pt-40 pb-20 md:px-48 px-10 gap-12 justify-center items-center">
            <div class="w-full md:col-span-1 col-span-2 text-primary md:text-end text-center leading-3">
                <h1 class="text-6xl font-satisfy font-bold mb-8">A Resort Like No Other...</h1>
                <p class="text-sm text-pretty mb-3">Welcome to the ultimate getaway at our serene resort, where we offer a variety of packages to suit every occasion and preference. Nestled in a picturesque location, our resort provides the perfect backdrop for your dream wedding. Exchange vows amidst stunning natural beauty, with customized wedding packages designed to create unforgettable memories.</p>
            </div>
            <div class="w-full h-[500px] md:col-span-1 col-span-2 shadow-2xl shadow-black overflow-hidden">
                <img class="w-full h-full object-cover" src="/assets/showcase-7.jpg" />
            </div>
        </div>

        <div class="grid grid-cols-2 pt-40 pb-20 md:px-48 px-10 gap-12 justify-center items-center">
            <div class="w-full h-[500px] md:col-span-1 col-span-2 md:order-1 order-2 shadow-2xl shadow-black overflow-hidden">
                <img class="w-full h-full object-cover" src="/assets/showcase-9.jpeg" />
            </div>
            <div class="w-full md:col-span-1 col-span-2 md:order-2 order-1 text-primary md:text-left text-center leading-3">
                <h1 class="text-5xl font-satisfy font-bold mb-8">Romantic Nights Under the Stars</h1>
                <p class="text-sm text-pretty mb-3">For those seeking relaxation and a touch of romance, our night swimming experience offers a tranquil escape under the stars, with beautifully lit pools and a serene ambiance. Our overnight packages provide a comfortable and luxurious stay, allowing you to unwind and recharge in our elegantly appointed rooms.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 pt-40 pb-20 md:px-48 px-10 gap-12 justify-center items-center">
            <div class="w-full md:col-span-1 col-span-2 text-primary md:text-end text-center leading-3">
                <h1 class="text-5xl font-satisfy font-bold mb-8">Indulge in World-Class Amenities</h1>
                <p class="text-sm text-pretty mb-3">In addition to our specialized packages, our resort features an array of amenities to enhance your stay. Enjoy delectable cuisine at our on-site restaurant, indulge in a rejuvenating spa treatment, or explore the nearby attractions for a taste of local culture.</p>
            </div>
            <div class="w-full h-[500px] md:col-span-1 col-span-2 shadow-2xl shadow-black overflow-hidden">
                <img class="w-full h-full object-cover" src="/assets/showcase-2.jpg" />
            </div>
        </div>

        <div class="grid grid-cols-2 pt-40 pb-20 md:px-48 px-10 gap-12 justify-center items-center">
            <div class="w-full h-[500px] md:col-span-1 col-span-2 md:order-1 order-2 shadow-2xl shadow-black overflow-hidden">
                <img class="w-full h-full object-cover" src="/assets/showcase-4.jpg" />
            </div>
            <div class="w-full md:col-span-1 col-span-2 md:order-2 order-1 text-primary md:text-left text-center leading-3">
                <h1 class="text-5xl font-satisfy font-bold mb-8">Crafted for Unforgettable Experiences</h1>
                <p class="text-sm text-pretty mb-3">Whether you're planning a grand celebration or a peaceful retreat, our dedicated staff is here to ensure that your experience is nothing short of exceptional. Discover the perfect blend of luxury and tranquility at our resort, where every moment is crafted to provide you with a memorable and relaxing stay.</p>
            </div>
        </div>
        

        <div class="flex flex-col gap-10 items-center bg-tertiary w-full pb-20">
            <div class="text-secondary flex flex-col gap-0 w-full mt-12">
                <p class="text-center">Ready to stay in? <a class="hover:underline font-bold" href="./bookings.php">Book now</a> online!</p>
                <p class="text-center">or you can contact us at <a class="hover:underline font-bold" href="mailto:info@museodesanpedro.com">info@museodesanpedro.com</a></p>
                <p class="mt-5 text-center">Are you an admin? <a class="hover:underline font-bold" href="admin/login.php">Log in here</a></p>
            </div>
        </div>
    </div>
    <script>
        let navbar = document.getElementById("navbar");
        let navbarTitle = document.getElementById("navbar-title");

        // GSAP animation events
        document.addEventListener("DOMContentLoaded", () => {

            // For the showcase component
            const swiper = new Swiper('.swiper-container', {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
            });

            navbarTitle.style.display = "none";
            navbar.classList.remove("bg-primary");
            navbar.classList.remove("shadow-lg");
            navbar.classList.add("bg-transparent");

            gsap.from(".header-text", { opacity: 0, duration: 0.50, ease: "easeOut" });
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

            if (pageYOffset < 400) {
                navbarTitle.style.display = "none";
                navbar.classList.remove("bg-tertiary");
                // navbar.classList.remove("shadow-2xl");
                navbar.classList.add("bg-transparent");
                navbar.classList.add("text-secondary");
                navbar.classList.remove("text-primary");
            } else {
                navbarTitle.style.display = "block";
                navbar.classList.add("bg-tertiary");
                // navbar.classList.add("shadow-2xl");
                navbar.classList.remove("bg-transparent");
            }

            if (window.innerWidth < 768) {
                if (prevScrollpos > currentScrollPos) {
                    navbar.style.top = "0";
                } else {
                    navbar.style.top = "-100px";
                }

                prevScrollpos = currentScrollPos;
            }
            
        };
    </script>
</body>
</html>