<?php
session_start();
header('Content-Type: application/json');
require_once('includes/db.php'); // Ensure this file correctly connects to PostgreSQL

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

// Read input data
$data = json_decode(file_get_contents("php://input"), true);
$token = trim($data['token'] ?? '');
$newPassword = trim($data['newpassword'] ?? '');

// Validate input
if (empty($token) || empty($newPassword)) {
    echo json_encode(["error" => "Token and new password are required."]);
    exit();
}

try {
    $conn = getConnection(); // Get PostgreSQL connection

    // Check if the token exists and is still valid
    $query = "SELECT id, token_expiry FROM users WHERE password_reset_token = $1";
    $result = pg_query_params($conn, $query, [$token]);

    if (!$result || pg_num_rows($result) === 0) {
        echo json_encode(["error" => "Invalid or expired token."]);
        exit();
    }

    $user = pg_fetch_assoc($result);
    
    // Check if token has expired
    if (strtotime($user['token_expiry']) < time()) {
        echo json_encode(["error" => "This reset token has expired."]);
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in the database
    $updateQuery = "UPDATE users SET password_hash = $1, password_reset_token = NULL, token_expiry = NULL WHERE id = $2";
    $updateResult = pg_query_params($conn, $updateQuery, [$hashedPassword, $user['id']]);

    if (!$updateResult) {
        echo json_encode(["error" => "Failed to update password."]);
        exit();
    }

    echo json_encode(["success" => "Password reset successfully!"]);
} catch (Exception $e) {
    error_log("❌ Error resetting password: " . $e->getMessage());
    echo json_encode(["error" => "An unexpected error occurred."]);
}
?>