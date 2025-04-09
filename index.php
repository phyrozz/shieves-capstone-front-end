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
    <title>Museo de San Pedro</title>
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    <div class="flex flex-col h-[75vh] justify-center items-center text-secondary cursor-default">
        <p class="font-satisfy md:text-2xl text-lg">Welcome to</p>
        <h1 class="md:text-6xl text-4xl font-satisfy text-center text-pretty font-bold header-text">Museo de San Pedro</h1>
        <div id="header-bg" class="absolute -z-50 bg-[url(/assets/bg.jpg)] filter brightness-50 bg-cover mask h-screen w-full"></div>
    </div>

    <div class="flex md:flex-row flex-col justify-center flex-wrap bg-gradient-to-br bg-secondary gap-5">
        <!-- Showcase component -->
        <div id="showcase" class="grid grid-cols-2 pt-40 pb-20 md:px-48 px-10 gap-12 justify-center items-center">
            <div class="w-full md:col-span-1 col-span-2 text-primary md:text-end text-center leading-3">
                <h1 class="text-6xl font-satisfy font-bold mb-8">A Resort Like No Other...</h1>
                <p class="text-sm text-pretty mb-3">Welcome to the <u><b>ultimate getaway</b></u> at our serene resort, where we offer a variety of packages to suit every occasion and preference. Nestled in a picturesque location, our resort provides the perfect backdrop for your dream wedding. Exchange vows amidst stunning natural beauty, with customized wedding packages designed to create unforgettable memories. For those seeking relaxation and a touch of romance, our night swimming experience offers a tranquil escape under the stars, with beautifully lit pools and a serene ambiance. Our overnight packages provide a comfortable and luxurious stay, allowing you to unwind and recharge in our elegantly appointed rooms.</p>
                <p class="text-sm text-pretty">In addition to our specialized packages, our resort features an array of amenities to enhance your stay. Enjoy delectable cuisine at our on-site restaurant, indulge in a rejuvenating spa treatment, or explore the nearby attractions for a taste of local culture. Whether you're planning a grand celebration or a peaceful retreat, our dedicated staff is here to ensure that your experience is nothing short of exceptional. Discover the perfect blend of luxury and tranquility at our resort, where every moment is crafted to provide you with a memorable and relaxing stay.</p>
            </div>
            <div class="w-full h-[500px] cursor-pointer md:col-span-1 col-span-2 transition-all md:hover:scale-105 md:hover:rotate-3 shadow-2xl hover:shadow-slate-300 shadow-black">
                <div class="swiper-container w-full h-full relative overflow-hidden">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide"><img class="w-full h-full object-cover" src="assets/showcase-1.jpg" alt="Image 1"></div>
                        <div class="swiper-slide"><img class="w-full h-full object-cover" src="assets/showcase-2.jpg" alt="Image 2"></div>
                        <div class="swiper-slide"><img class="w-full h-full object-cover" src="assets/showcase-3.jpg" alt="Image 3"></div>
                        <div class="swiper-slide"><img class="w-full h-full object-cover" src="assets/showcase-4.jpg" alt="Image 4"></div>
                        <div class="swiper-slide"><img class="w-full h-full object-cover" src="assets/showcase-5.jpg" alt="Image 5"></div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
        

        <div id="our-location" class="m-10 mt-20 flex flex-col gap-10 items-center">
            <div class="text-primary md:mx-40 mx-0 md:text-left text-center">
                <h1 class="font-satisfy text-6xl font-bold mb-10">Where to find?</h1>
                <p class="text-sm font-bold mb-5">Museo De San Pedro is located at #122 Magsasaysay, San Pedro City, Laguna.</p>
                <p class="text-sm">It's only a 17-minute drive from HBC San Pedro. You can also take a jeepney ride on the "estrella trip" route, heading to Magsasay (beside HBC). Advise the driver to drop you off at "Museo De San Pedro" in front of Villa Consolascion. Alternatively, use the Waze Mobile App to locate us.</p>
                <div class="rounded-2xl overflow-hidden my-10 shadow-2xl shadow-black">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d941.3497232969933!2d121.03387312189574!3d14.338998936361174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d6e6c433d123%3A0x4be58cfd35018b40!2sMuseo%20de%20San%20Pedro!5e1!3m2!1sen!2sph!4v1714867684158!5m2!1sen!2sph" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            
            <div id="packages" class="w-full text-center mt-20 mb-0">
                <h1 class="font-satisfy text-6xl font-bold text-primary">All In Packages</h1>
            </div>
            <div class="flex flex-row gap-3 flex-wrap justify-center cursor-default">
                <!-- Package containers -->
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
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
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
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
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
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

            <div class="w-full text-center mt-20 mb-0">
                <h1 class="font-satisfy text-5xl text-primary">or book with our other <b><u>Packages!</u></b></h1>
            </div>
            <div class="flex flex-row gap-3 flex-wrap justify-center cursor-default">
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
                    <div class="flex flex-col gap-1">
                        <p class="font-satisfy text-3xl font-bold">Wedding Package</p>
                        <p class="font-black text-4xl">PHP 50,000</p>
                        <p class="font-extrabold text-xs">(50 pax)</p>
                    </div>
                </div>
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
                    <div class="flex flex-col gap-1">
                        <p class="font-satisfy text-3xl font-bold">Daytime Swimming</p>
                        <p class="font-black text-4xl">PHP 7,000</p>
                        <p class="font-extrabold text-xs">(25 pax)</p>
                    </div>
                </div>
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
                    <div class="flex flex-col gap-1">
                        <p class="font-satisfy text-3xl font-bold">Overnight Swimming</p>
                        <p class="font-black text-4xl">PHP 8,500</p>
                        <p class="font-extrabold text-xs">(20 pax)</p>
                    </div>
                </div>
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
                    <div class="flex flex-col gap-1">
                        <p class="font-satisfy text-3xl font-bold">Swimming</p>
                        <p class="font-black text-4xl">PHP 14,500</p>
                        <p class="font-extrabold text-xs">(22 hrs)</p>
                    </div>
                </div>
                <div class="package-container md:w-96 w-full bg-primary p-10 flex flex-col gap-5 rounded-lg text-center text-primary shadow-lg shadow-black transition-shadow">
                    <div class="flex flex-col gap-1">
                        <p class="font-satisfy text-3xl font-bold">Events Venue (Function and Pool Area)</p>
                        <p class="font-black text-4xl">PHP 20,000</p>
                        <p class="font-extrabold text-xs">(8 hrs - 50/75/100 pax)</p>
                    </div>
                </div>
            </div>

            <div class="text-primary flex flex-col gap-0 w-full mt-10">
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