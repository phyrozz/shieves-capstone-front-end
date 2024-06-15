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
    <script>
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
    </script>
</nav>