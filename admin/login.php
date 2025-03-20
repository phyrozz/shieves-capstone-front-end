<?php
include "../conn.php";

session_start();

if($_SERVER["REQUEST_METHOD"]=="POST")
{
  $username=$_POST["username"];
  $password=$_POST["password"];

  $sql= "SELECT * FROM admins WHERE username= '".$username."' AND password= '".$password."' ";

  $result=mysqli_query($conn, $sql);

  $row=mysqli_fetch_array($result);

  if($row)
  {
    $_SESSION ["username"]=$username;
    
    header("location:booked_client.php");
    exit();
  }

  else
  {
    $_SESSION["error"] = "Incorrect username or password. Please try again.";
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
  <title>Museo de San Pedro - Admin Login</title>
  <link rel="stylesheet" href="../tailwind.css">
  <link rel="stylesheet" href="../css/theme.css">
  <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="w-screen h-screen bg-gradient-to-br bg-secondary flex justify-center items-center text-primary">
    <div id="login-container" class="w-96 bg-slate-950 flex flex-col items-start rounded-lg shadow-2xl bg-primary px-8 py-10">
      <h1 class="font-black text-3xl">Log in as Admin</h1>
      <form class="flex flex-col w-full py-5" method="POST">
        <div class="w-full flex flex-col">
          <span class="text-xs font-bold tracking-wider text-primary">USER NAME</span>
          <input type="text" name="username" class="mt-1 mb-5 p-2 rounded-md transition-all" maxlength="255" required />
        </div>
        <div class="w-full flex flex-col">
          <span class="text-xs font-bold tracking-wider text-primary">PASSWORD</span>
          <input type="password" name="password" class="mt-1 mb-5 p-2 rounded-md transition-all" minlength="8" maxlength="255" required />
        </div>
        <div class="w-full flex flex-row justify-end items-center">
          <input id="login-btn" type="submit" class="text-xs font-bold tracking-wider px-5 py-2 border border-[--color-text-primary] bg-transparent hover:bg-[--color-text-primary] hover:text-[--color-text-secondary] rounded-md cursor-pointer transition-all" value="LOGIN" />
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