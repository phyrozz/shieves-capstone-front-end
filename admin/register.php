<?php
// register_admin.php
session_start();
require_once "../conn.php"; // expects $conn = new mysqli(...)

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        // Basic validation
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if ($username === '') $errors[] = "Username is required.";
        if ($password === '') $errors[] = "Password is required.";
        if ($password !== $confirm) $errors[] = "Passwords do not match.";
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";

        if (!$errors) {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Username is already taken.";
            } else {
                // Hash and insert
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
                $ins->bind_param("ss", $username, $hash);

                if ($ins->execute()) {
                    $success = "Admin registered successfully.";
                    // Reset form fields
                    $username = "";
                } else {
                    $errors[] = "Something went wrong. Please try again.";
                }
                $ins->close();
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Tailwind CSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="bg-white shadow rounded-2xl p-6">
      <h1 class="text-2xl font-semibold mb-4">Register Admin</h1>

      <?php if ($success): ?>
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-sm">
          <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm">
          <ul class="list-disc list-inside space-y-1">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-4" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">

        <div>
          <label class="block text-sm mb-1" for="username">Username</label>
          <input
            id="username"
            name="username"
            type="text"
            required
            value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>"
            class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="admin_username"
          >
        </div>

        <div>
          <label class="block text-sm mb-1" for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            required
            minlength="8"
            class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="At least 8 characters"
          >
        </div>

        <div>
          <label class="block text-sm mb-1" for="confirm">Confirm Password</label>
          <input
            id="confirm"
            name="confirm"
            type="password"
            required
            class="w-full rounded-xl border border-gray-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Retype password"
          >
        </div>

        <button
          type="submit"
          class="w-full rounded-xl bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700"
        >
          Register
        </button>
      </form>
    </div>

    <p class="text-center text-xs text-gray-500 mt-3">
      Tip: Add a UNIQUE index on <code>username</code> to enforce uniqueness.
    </p>
  </div>
</body>
</html>
