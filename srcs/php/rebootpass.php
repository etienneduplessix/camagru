<?php
session_start();
require_once('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiUrl = 'http://php_web/forgot_password.php';
    
    $data = [
        'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL)
    ];
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors'] = "Please enter a valid email address.";
        header('Location: /rebootpass.php');
        exit;
    }
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($data),
            'timeout' => 10
        ]
    ];
    
    $context = stream_context_create($options);
    
    try {
        $result = file_get_contents($apiUrl, false, $context);
        
        if ($result === FALSE) {
            throw new Exception("Failed to contact the API");
        }
        
        $response = json_decode($result, true);
        
        if (isset($response['success'])) {
            $_SESSION['success'] = $response['success'];
            header('Location: /login.php');
            exit;
        } else if (isset($response['error'])) {
            $_SESSION['errors'] = $response['error'];
        } else {
            $_SESSION['errors'] = "An unexpected error occurred.";
        }
    } catch (Exception $e) {
        $_SESSION['errors'] = "Password reset failed: Unable to process your request.";
    }
    
    header('Location: /rebootpass.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Reset Password</title>
    <link rel="stylesheet" href="includes/css/style.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="/" class="logo">Camagru</a>
            <nav>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>Reset Password</h1>
            <p style="text-align: center; margin-bottom: 20px;">Enter your email to receive reset instructions</p>
            
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="error-message show"><?php echo htmlspecialchars($_SESSION['errors']); unset($_SESSION['errors']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message show"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="/rebootpass.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <button type="submit">Send Reset Link</button>
            </form>
            
            <div class="form-footer">
                <p>Remembered your password? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. All rights reserved.</p>
    </footer>
</body>
</html>
