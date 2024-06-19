<nav id="navbar" class="flex flex-nowrap gap-8 bg-slate-300 flex-row w-full py-2 px-5 items-center sticky justify-center md:justify-between top-0 z-50 transition-all">
    <h1 class="font-satisfy font-extrabold text-xl md:block hidden">Museo de San Pedro</h1>
    <ul class="flex flex-row gap-5 items-center">
        <a href="admin_home.php" class="navbar-item hover:bg-slate-400 font-bold text-sm p-3 rounded-md transition-all text-center">
            <li class="flex flex-row items-center gap-2"> 
                <lord-icon
                    src="https://cdn.lordicon.com/wmwqvixz.json"
                    trigger="hover"
                    style="width:1.5em;height:1.5em">
                </lord-icon>
                <p class="navbar-item-text">HOME</p>
            </li>
        </a>
        <a href="#" class="navbar-item hover:bg-slate-400 font-bold text-sm p-3 rounded-md transition-all text-center">
            <li class="flex flex-row items-center gap-2"> 
                <lord-icon
                    src="https://cdn.lordicon.com/hrjifpbq.json"
                    trigger="hover"
                    style="width:1.5em;height:1.5em">
                </lord-icon>
                <p class="navbar-item-text">CUSTOMERS</p>
            </li>
        </a>
        <a href="#" class="navbar-item hover:bg-slate-400 font-bold text-sm p-3 rounded-md transition-all text-center">
            <li class="flex flex-row items-center gap-2"> 
                <lord-icon
                    src="https://cdn.lordicon.com/gjjvytyq.json"
                    trigger="hover"
                    style="width:1.5em;height:1.5em">
                </lord-icon>
                <p class="navbar-item-text">INVOICES</p>
            </li>
        </a>
        <a href="../admin/logout.php" class="navbar-item bg-rose-700 hover:bg-rose-800 font-bold text-sm ml-5 p-3 rounded-md transition-all text-center text-nowrap">
            <li class="flex flex-row items-center gap-2"> 
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="1.25em" height="1.25em"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z"/></svg>
                <p class="navbar-item-text text-white">LOG OUT</p>
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