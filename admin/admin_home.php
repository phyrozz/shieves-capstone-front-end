<?php
session_start();


if(!isset ($_SESSION["username"]))
{
    header("location: admin_home.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Homepage</title>
</head>
<body>
    <h1>ADMIN PANEL</h1>
    <a href="logout.php">"Logout"</a>
</body>
</html>
