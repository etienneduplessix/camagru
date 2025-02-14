<?php
session_start();
require_once 'includes/db.php';

$conn = getConnection2();

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = trim($_GET['token']);

    // Prepare the SQL query to update is_verified
    $query = "UPDATE users 
              SET is_verified = TRUE, 
                  verification_token = NULL 
              WHERE verification_token = $1 
                AND is_verified = FALSE 
              RETURNING id";

    $stmt = pg_prepare($conn, "verify_user", $query);
    $result = pg_execute($conn, "verify_user", [$token]);

    if ($result && pg_num_rows($result) > 0) {
        $_SESSION['success'] = 'Your account has been verified! You can now login.';
    } else {
        $_SESSION['error'] = 'Invalid or expired verification token.';
    }

    pg_close($conn);
    header('Location: /login.php');
    exit();
}

// If no token is provided
$_SESSION['error'] = 'Invalid verification request.';
header('Location: /login.php');
exit();
?>
