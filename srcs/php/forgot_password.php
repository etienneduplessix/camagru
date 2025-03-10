
<?php
session_start();
header('Content-Type: application/json');

require_once('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(["error" => "Please enter a valid email address."]);
    exit();
}

try {
    $conn = getConnection();

    // Check if user exists
    $query = "SELECT id FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, [$email]);

    if (!$result || pg_num_rows($result) === 0) {
        echo json_encode(["success" => "If your email is registered, you will receive reset instructions."]);
        exit();
    }

    $user = pg_fetch_assoc($result);
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store reset token inside the `users` table
    $query = "UPDATE users SET password_reset_token = $1, token_expiry = $2 WHERE id = $3";
    $updateResult = pg_query_params($conn, $query, [$token, $expires, $user['id']]);

    if (!$updateResult) {
        error_log("❌ Failed to update token: " . pg_last_error($conn));
        echo json_encode(["error" => "Database error: Unable to store reset token."]);
        exit();
    }
        if ($insertResult) {
            sendResetEmail($email, $token);
        } else {
            // Log the error but don't expose it to the user
            error_log("Failed to insert token: " . pg_last_error($conn));
        }
    }

    /**
     * Send a password reset email
     * 
     * @param string $email The recipient's email address
     * @param string $token The reset token
     * @return bool Whether the email was sent successfully
     */
    function sendResetEmail($email, $token) {
        // Use HTTPS in production
        $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/rebootpass.php?token=" . urlencode($token);
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Password Reset Request</title>
        </head>
        <body>
            <h2>Password Reset Request</h2>
            <p>Click the link below to reset your password:</p>
            <p><a href='" . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . "' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none;'>Reset Password</a></p>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p>" . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . "</p>
            <p>This link will expire in 1 hour.</p>
        </body>
        </html>";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Camagru <noreply@camagru.com>'
        ];
        
        return mail($email, "Password Reset Request", $message, implode("\r\n", $headers));
    }

    exit();
</php>