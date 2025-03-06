<?php
session_start();

require_once('includes/loader.php');
require_once('includes/db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rebootpass.php');
    exit();
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$email) {
    $_SESSION['error'] = "Please enter a valid email address.";
    header('Location: rebootpass.php');
    exit();
}

try {
    $conn = getConnection();
    
    // Check if email exists in database
    $query = "SELECT id, username FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, [$email]);
    
    if (!$result || pg_num_rows($result) === 0) {
        // Don't reveal if email exists or not for security
        $_SESSION['success'] = "If your email is registered, you will receive reset instructions shortly.";
        header('Location: login.php');
        exit();
    }
    
    $user = pg_fetch_assoc($result);
    
    // Generate unique token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store reset token in database
    $query = "INSERT INTO password_resets (user_id, token, expires_at) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, [$user['id'], $token, $expires]);
    
    if (!$result) {
        throw new Exception("Failed to create password reset token");
    }
    
    // Send email with reset link
    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
    $to = $email;
    $subject = "Password Reset Request";
    $message = "Hello " . htmlspecialchars($user['username']) . ",\n\n";
    $message .= "You have requested to reset your password. Click the link below to proceed:\n\n";
    $message .= $resetLink . "\n\n";
    $message .= "This link will expire in 1 hour.\n\n";
    $message .= "If you did not request this reset, please ignore this email.\n\n";
    $message .= "Best regards,\nMy Website Team";
    $headers = "From: noreply@mywebsite.com";
    
    if (mail($to, $subject, $message, $headers)) {
        $_SESSION['success'] = "Reset instructions have been sent to your email.";
        header('Location: login.php');
    } else {
        throw new Exception("Failed to send reset email");
    }
    
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header('Location: rebootpass.php');
} finally {
    if (isset($conn)) {
        pg_close($conn);
    }
}
exit();
?>
