<nav id="navbar" class="flex items-center text-secondary bg-transparent w-full py-2 px-5 fixed top-0 z-50">
    <div class="flex justify-start items-center flex-1">
        <ul class="flex flex-row gap-5 items-center">
            <h1 id="navbar-title" class="font-satisfy font-extrabold text-xl md:block hidden text-nowrap cursor-default">J.M. Apilado Resort</h1>
            <a href="/index.php" class="navbar-item font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">HOME</p>
                </li>
            </a>
            <a href="../showcase.php" class="navbar-item font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">SHOWCASE</p>
                </li>
            </a>
            <a href="../index.php#our-location" class="navbar-item font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">OUR LOCATION</p>
                </li>
            </a>
            <a href="../index.php#packages" class="navbar-item font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">PACKAGES</p>
                </li>
            </a>
            <a href="/bookings.php" class="navbar-item font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">BOOK NOW</p>
                </li>
            </a>
        </ul>
    </div>

    <!-- <div class="flex justify-end items-center flex-1">
        <a id="login-btn" href="login.php" class="hover:bg-slate-900 font-bold text-sm p-3 rounded-md  text-center">
            <li class="flex flex-row items-center gap-2">
                <lord-icon
                    id="login-btn-icon"
                    src="https://cdn.lordicon.com/hrjifpbq.json"
                    trigger="hover"
                    colors="primary:#ffffff"
                    style="width:1.5em;height:1.5em">
                </lord-icon>
                <p id="login-btn-text" class="text-nowrap">LOG IN</p>
            </li>
        </a>
    </div> -->
</nav>



<script>
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
</script>