<?php
session_start();
require_once 'includes/db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $conn = getConnection();
    
    $result = pg_query_params($conn, 
        'UPDATE users 
         SET is_verified = true, 
             verification_token = NULL, 
             verified_at = CURRENT_TIMESTAMP 
         WHERE verification_token = $1 
         RETURNING id',
        [$token]
    );
    
    if (pg_num_rows($result) > 0) {
        $_SESSION['success'] = 'Your account has been verified! You can now login.';
    } else {
        $_SESSION['error'] = 'Invalid or expired verification token.';
    }
    
    header('Location: /login');
    exit();
}

$_SESSION['error'] = 'Invalid verification request';
header('Location: /login');
exit();
?>