
<?php
    require_once __DIR__ . '/app/Core/Autoloader.php';
    require_once __DIR__ . '/app/config/database.php';

    use app\Core\Autoloader;
    $autoloader = new Autoloader();
    $autoloader->register();

    $dbConnection = getConnection();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>    
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form class="login-form" action="/login" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>
        <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div>
    </div>
</body>
</html>