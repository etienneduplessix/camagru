<?php
session_start();
require_once('includes/loader.php');
require_once('includes/db.php');

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Both password fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        try {
            $db = getConnection();
            
            // Verify token and get user
            $query = "SELECT id FROM users WHERE password_reset_token = $1 AND token_expiry > NOW()";
            $result = pg_query_params($db, $query, [$token]);
            
            if (!$result || pg_num_rows($result) === 0) {
                $error = "Invalid or expired reset link";
            } else {
                $userId = pg_fetch_result($result, 0, 'id');
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Update password and clear reset token
                $updateQuery = "UPDATE users SET password_hash = $1, password_reset_token = NULL, token_expiry = NULL WHERE id = $2";
                $result = pg_query_params($db, $updateQuery, [$hashedPassword, $userId]);
                
                if (!$result) {
                    throw new Exception("Failed to update password");
                }
                
                $success = "Password has been reset successfully. You can now login with your new password.";
            }
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $error = "An error occurred while resetting your password";
        } finally {
            if (isset($db)) {
                pg_close($db);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="includes/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Reset Password</h1>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
                <div class="form-footer">
                    <p><a href="/login.php">Return to Login</a></p>
                </div>
            <?php else: ?>
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="auth-button">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
