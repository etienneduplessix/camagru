<?php
session_start();
header('Content-Type: application/json'); // Set response type to JSON
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Email and password are required."]);
    exit();
}

$conn = getConnection();

// Check if statement already exists to avoid duplicate `pg_prepare()`
$queryName = "check_user_query";
@pg_prepare($conn, $queryName, "SELECT id, password_hash, is_verified FROM users WHERE email = $1");

$result = pg_execute($conn, $queryName, [$email]);

if ($result && pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);

    // If user is not verified, return an error
    if ($user['is_verified'] === 'f') {
        http_response_code(403); // Forbidden
        echo json_encode(["error" => "Please verify your profile before logging in."]);
        pg_close($conn);
        exit();
    }

    // Verify the password
    if (password_verify($password, $user['password_hash'])) {
        // Secure session handling
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        http_response_code(200); // OK
        echo json_encode(["success" => "Login successful.", "redirect" => "/index.php"]);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(["error" => "Invalid email or password."]);
    }
} else {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Invalid email or password."]);
}

pg_close($conn);
exit();
?>
