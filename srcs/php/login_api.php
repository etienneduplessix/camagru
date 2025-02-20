<?php
ob_start();
session_start(); // Start the session to properly set session cookies
header('Content-Type: application/json');
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request method."]);
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required."]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email format."]);
    exit();
}

$conn = getConnection();

// Use a parameterized query to safely fetch the user
$query = "SELECT id, password_hash, is_verified FROM users WHERE email = $1";
$result = pg_query_params($conn, $query, [$email]);

if ($result && pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);

    // If user is not verified, return an error
    if ($user['is_verified'] === 'f') {
        http_response_code(403);
        echo json_encode(["error" => "Please verify your profile before logging in."]);
        pg_close($conn);
        exit();
    }

    // Verify the password
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'email' => $email,
        ];
        session_write_close(); // Ensure session data is saved

        http_response_code(200);
        ob_end_clean(); // Clear buffer before outputting JSON
        echo json_encode(["success" => "Login successful.", "redirect" => "/index.php"]);
    } else {
        http_response_code(401);
        ob_end_clean();
        echo json_encode(["error" => "Invalid email or password."]);
    }
} else {
    http_response_code(401);
    ob_end_clean();
    echo json_encode(["error" => "Invalid email or password."]);
}

pg_close($conn);
exit();
?>
