<?php
session_start();
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Check if the user is logged in and get the user ID
if (Auth::isLoggedIn()) {
    echo json_encode([
        'success' => true,
        'user_id' => Auth::getUserId(),
        'username' => Auth::currentUser()
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
}
?>
