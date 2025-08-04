<?php
include "conn.php";

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST")
{
  $email=$_POST["email"];
  $password=$_POST["password"];

  $sql= "SELECT * FROM registered_users WHERE email= '".$email."' AND password= '".$password."' ";

  $result=mysqli_query($conn, $sql);

  $row=mysqli_fetch_array($result);

  if($row)
  {
    $_SESSION ["email"]=$email;
    
    header("location: bookings.php");
    exit();
  }

  else
  {
    $_SESSION["error"] = "Incorrect email or password. Please try again.";
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>J.M. Apilado Resort - Customer Login</title>
  <link rel="stylesheet" href="../tailwind.css">
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="w-screen h-screen bg-gradient-to-br from-slate-950 to-violet-950 flex justify-center items-center text-slate-50">
    <div id="login-container" class="w-96 bg-slate-950 flex flex-col items-start rounded-lg shadow-2xl shadow-slate-950 px-8 py-10">
      <h1 class="font-black text-3xl">Log in</h1>
      <form class="flex flex-col w-full py-5" method="POST">
        <div class="w-full flex flex-col">
          <span class="text-xs font-bold tracking-wider text-slate-400">EMAIL</span>
          <input type="email" name="email" class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all" maxlength="255" required />
        </div>
        <div class="w-full flex flex-col">
          <span class="text-xs font-bold tracking-wider text-slate-400">PASSWORD</span>
          <input type="password" name="password" class="mt-1 mb-5 p-2 bg-gray-800 rounded-md transition-all" minlength="8" maxlength="255" required />
        </div>
        <div class="w-full flex flex-row justify-end items-center">
          <input id="login-btn" type="submit" class="text-xs font-bold tracking-wider bg-gray-800 px-5 py-2 rounded-md hover:bg-gray-900 cursor-pointer transition-all" value="LOGIN" />
        </div>
      </form>
    </div>
  </div>

  <script>
    gsap.from("#login-container", { scale: 0, duration: 0.25, ease: "easeInOut" });

    <?php if (isset($_SESSION['error'])): ?>
      Swal.fire({
        title: 'Error',
        text: '<?php echo $_SESSION['error']; ?>',
        icon: 'warning',
      });
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </script>
</body>
</html>