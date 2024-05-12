<?php

$host="localhost";
$user="shieves";
$password="Msdpfree123@";
$db="id22151551_user";

session_start();

$data=mysqli_connect($host,$user,$password,$db);
if($data===false)
{
    die("connection error");
}

if($_SERVER["REQUEST_METHOD"]=="POST")
{
    $username=$_POST["username"];
    $password=$_POST["password"];

    $sql= "SELECT * FROM login WHERE username= '".$username."' AND password= '".$password."' ";

    $result=mysqli_query($data, $sql);

    $row=mysqli_fetch_array($result);

    if($row["usertype"]=="user")
    {
        $_SESSION ["username"]=$username;
        
        header("location:user_home.php");
    }

    elseif($row["usertype"]=="admin")
    {
        $_SESSION ["username"]=$username;
        
        header("location:admin_home.php");
    }

    else
    {
        echo "username or password incorrect";
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museo de San Pedro Admin</title>
    
    <!-- custom css file link -->
    <link rel="stylesheet" href="style.css">
    

     <center>

          <h1>Welcome to Museo de San Pedro!</h1>
          <br><br><br><br>
          <div style="background-color: lightblue; width: 500px;">
          <br><br>

          <form action="#" method="POST">

     <div>
         <label>Username</label>
         <input type="text" name="username" required>
     </div> 
     <br><br>
     
     <div>
         <label>Password</label>
         <input type="text" name="password" required>
     </div> 
     <br><br>
     
     <div>

         <input type="submit" value="login" required>
     </div>
    <br><br>
    <p>Go Back to <a href= "admin_host.php" class="btn">Admin Page</a></p>
     </center>

</head>
<body>

    

</body>
</html>