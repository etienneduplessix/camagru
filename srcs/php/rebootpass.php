<?php
session_start();
require_once('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("📩 Received POST request for password reset");
    
    // Use HTTPS in production
    $apiUrl = 'http://php_web/forgot_password.php'; // Docker service name for API
    
    $data = [
        'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL)
    ];
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        error_log("⚠️ Invalid email, redirecting.");
        $_SESSION['errors'] = "Please enter a valid email address.";
        header('Location: /rebootpass.php');
        exit;
    }
    
    error_log("🚀 Sending request to API: " . $apiUrl);
    
    // Set up a proper JSON request
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
        $httpCode = isset($http_response_header[0]) ? $http_response_header[0] : "Unknown";
        
        error_log("📩 API Response: " . ($result ?: "No response"));
        error_log("🔢 HTTP Code: " . $httpCode);
        
        if (isset($response['success'])) {
            $_SESSION['success'] = $response['success'];
            error_log("✅ Password reset request successful, redirecting.");
            header('Location: /login.php');
            exit;
        } else if (isset($response['error'])) {
            $_SESSION['errors'] = $response['error'];
        } else {
            $_SESSION['errors'] = "An unexpected error occurred.";
        }
    } catch (Exception $e) {
        error_log("❌ Exception caught: " . $e->getMessage());
        $_SESSION['errors'] = "Password reset failed: Unable to process your request.";
    }
    
    error_log("❌ Redirecting to /rebootpass.php due to errors.");
    header('Location: /rebootpass.php');
    exit;
}

error_log("👀 Showing password reset form.");
showPasswordResetForm();

function showPasswordResetForm() {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Camagru</title>
    <link rel="stylesheet" href="includes/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Reset Password</h1>
            <p>Enter your email to receive reset instructions</p>
            
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="error-message">
                    <?= htmlspecialchars($_SESSION['errors']) ?>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <form class="auth-form" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            
            <div class="form-footer">
                <p>Remembered your password? <a href="/login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
?>