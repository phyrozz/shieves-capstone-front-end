<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<div class="form-container">

        <form action="" method="post">
            <h3>Register Here</h3>
            <input type="text" name="name" require placeholder="enter your name">
            <input type="email" name="email" require placeholder="enter your email">
            <input type="password" name="password" require placeholder="enter your password">
            <input type="password" name="cpassword" require placeholder="confirm your password">
            <select name="user_type">
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
            <input type="submit" name="submit" value="Register now" class="form-btn">
            <p>You already have an account <a href=login.php>Login now</p>
        </form>
    </div>

</body>
</html>