<nav id="navbar" class="customer-navbar flex items-center text-secondary bg-transparent w-full py-2 px-5 fixed top-0 z-50">
    <div class="flex w-full justify-between items-center">
        <a id="navbar-title" href="/index.php" class="font-satisfy font-extrabold text-xl text-nowrap transition-opacity hover:opacity-80 focus:outline-none focus-visible:underline">J.M. Apilado Resort</a>
        <button id="customer-menu-toggle" class="customer-menu-toggle" type="button" aria-expanded="false" aria-controls="customer-nav-links">
            <span class="sr-only">Toggle navigation</span>
            <span></span><span></span><span></span>
        </button>
        <ul id="customer-nav-links" class="customer-nav-links flex flex-row gap-5 items-center">
            <a href="../showcase.php" class="navbar-item customer-nav-link font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">SHOWCASE</p>
                </li>
            </a>
            <a href="../index.php#our-location" class="navbar-item customer-nav-link font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">OUR LOCATION</p>
                </li>
            </a>
            <a href="../index.php#packages" class="navbar-item customer-nav-link font-bold text-sm p-3 rounded-md text-center">
                <li class="flex flex-row items-center gap-2"> 
                    <p class="navbar-item-text text-nowrap">PACKAGES</p>
                </li>
            </a>
            <a href="/bookings.php" class="navbar-item customer-nav-link font-bold text-sm p-3 rounded-md text-center">
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
// Toggle the customer navigation menu
(() => {
    const toggle = document.getElementById('customer-menu-toggle');
    const links = document.getElementById('customer-nav-links');
    if (!toggle || !links) return;

    toggle.addEventListener('click', () => {
        const isOpen = links.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    links.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        links.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    }));
})();
</script>



