<?php
include "../conn.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION["username"] = $admin['username'];
        header("location: admin_dashboard.php");
        exit();
    }

    $_SESSION["error"] = "Incorrect username or password. Please try again.";
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>J.M. Apilado Resort - Admin Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../tailwind.css">
  <link rel="stylesheet" href="../css/theme.css">
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-secondary text-primary select-none">
  <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-5 py-10 sm:px-8">
    <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-secondary)] via-[var(--color-secondary)] to-[var(--color-primary)] opacity-70"></div>
    <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-primary opacity-40 blur-3xl"></div>
    <div class="absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-tertiary opacity-20 blur-3xl"></div>

    <section id="login-container" class="relative grid w-full max-w-4xl overflow-hidden rounded-3xl bg-secondary shadow-2xl md:grid-cols-5">
      <div class="hidden flex-col justify-between bg-tertiary p-10 text-secondary md:col-span-2 md:flex">
        <div>
          <p class="font-satisfy text-3xl">J.M. Apilado Resort</p>
          <div class="mt-10 h-px w-16 bg-accent"></div>
          <h1 class="mt-6 text-3xl font-bold leading-tight">Welcome back,<br>administrator.</h1>
          <p class="mt-4 text-sm leading-6 text-secondary opacity-80">Sign in to manage bookings, client details, and resort reports.</p>
        </div>
        <p class="text-xs tracking-widest text-secondary opacity-60">ADMIN PORTAL</p>
      </div>

      <div class="p-8 sm:p-12 md:col-span-3">
        <div class="mb-9">
          <p class="font-satisfy text-3xl md:hidden">J.M. Apilado Resort</p>
          <p class="mt-2 text-xs font-bold uppercase tracking-[0.2em] opacity-60">Secure access</p>
          <h2 class="mt-2 text-3xl font-black">Admin login</h2>
          <p class="mt-2 text-sm leading-6 opacity-75">Enter your credentials to continue to the dashboard.</p>
        </div>

        <form class="flex flex-col gap-5" method="POST">
          <label class="flex flex-col gap-2">
            <span class="text-xs font-bold tracking-wider">USERNAME</span>
            <input type="text" name="username" autocomplete="username" class="rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" maxlength="255" required autofocus>
          </label>
          <label class="flex flex-col gap-2">
            <span class="text-xs font-bold tracking-wider">PASSWORD</span>
            <input type="password" name="password" autocomplete="current-password" class="rounded-xl border border-[var(--color-primary-shadow)] bg-white px-4 py-3 text-primary outline-none transition focus:border-[var(--color-tertiary)] focus:ring-2 focus:ring-[var(--color-accent)]" minlength="8" maxlength="255" required>
          </label>
          <button id="login-btn" type="submit" class="mt-2 flex items-center justify-center gap-2 rounded-xl bg-tertiary px-5 py-3 text-sm font-bold tracking-wider text-secondary transition hover:scale-[1.01] hover:bg-primary-shadow focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] disabled:cursor-not-allowed disabled:opacity-70">
            <span id="login-label">LOG IN TO DASHBOARD</span>
            <svg id="login-spinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          </button>
        </form>
      </div>
    </section>
  </main>

  <script>
    document.querySelector('form').addEventListener('submit', function() {
      document.getElementById('login-label').textContent = 'SIGNING IN...';
      document.getElementById('login-spinner').classList.remove('hidden');
      document.getElementById('login-btn').disabled = true;
    });

    gsap.from('#login-container', { opacity: 0, y: 24, duration: 0.45, ease: 'power2.out' });

    <?php if (isset($_SESSION['error'])): ?>
      Swal.fire({ title: 'Error', text: <?= json_encode($_SESSION['error']) ?>, icon: 'warning' });
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>
</html>
