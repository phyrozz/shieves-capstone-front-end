<nav id="navbar" class="flex flex-nowrap gap-8 bg-slate-300 flex-row w-full py-2 px-5 items-center fixed justify-center md:justify-between top-0 z-50 transition-all">
    <h1 class="font-satisfy font-extrabold text-xl md:block hidden">Museo de San Pedro</h1>
    <ul class="flex flex-row gap-5 items-center">
        <a href="/index.php" class="navbar-item hover:bg-slate-400 font-bold text-sm p-3 rounded-md transition-all text-center">
            <li class="flex flex-row items-center gap-2"> 
                <lord-icon
                    src="https://cdn.lordicon.com/wmwqvixz.json"
                    trigger="hover"
                    style="width:1.5em;height:1.5em">
                </lord-icon>
                <p class="navbar-item-text">HOME</p>
            </li>
        </a>
        <a href="/bookings.php" class="navbar-item hover:bg-slate-400 font-bold text-sm p-3 rounded-md transition-all text-center">
            <li class="flex flex-row items-center gap-2"> 
                <lord-icon
                    src="https://cdn.lordicon.com/wmlleaaf.json"
                    trigger="hover"
                    style="width:1.5em;height:1.5em">
                </lord-icon>
                <p class="navbar-item-text">BOOK NOW</p>
            </li>
        </a>
        <a id="login-btn" href="admin/login.php" class="hover:bg-slate-400 font-semibold p-3 rounded-md transition-all text-center group">
            <li class="flex flex-row items-center gap-2">
                <lord-icon
                    id="login-btn-icon"
                    src="https://cdn.lordicon.com/hrjifpbq.json"
                    trigger="hover"
                    style="width:2em;height:2em">
                </lord-icon>
                <p id="login-btn-text" class="group-hover:block hidden">LOG IN</p>
            </li>
        </a>
    </ul>
</nav>