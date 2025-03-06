<?php
ob_start(); // Start output buffering
session_start();
require_once('includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    ob_end_clean(); // Clear the buffer
    header("Location: /login.php");
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean(); // Clear the buffer
    header("Location: /user_management.php");
    exit();
}

// Get and sanitize input
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate input
if (empty($username) || empty($email) || empty($current_password)) {
    $_SESSION['error'] = "Username, email, and current password are required.";
    ob_end_clean(); // Clear the buffer
    header("Location: /user_management.php");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    ob_end_clean(); // Clear the buffer
    header("Location: /user_management.php");
    exit();
}

// Validate new password if provided
if (!empty($new_password)) {
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "New password must be at least 8 characters long.";
        ob_end_clean(); // Clear the buffer
        header("Location: /user_management.php");
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
        ob_end_clean(); // Clear the buffer
        header("Location: /user_management.php");
        exit();
    }
}

try {
    $conn = getConnection();
    
    // First, verify current password
    $query = "SELECT password_hash FROM users WHERE id = $1";
    $result = pg_query_params($conn, $query, [$_SESSION['user']['id']]);
    
    if (!$result || pg_num_rows($result) === 0) {
        throw new Exception("User not found.");
    }
    
    $user = pg_fetch_assoc($result);
    
    if (!password_verify($current_password, $user['password_hash'])) {
        $_SESSION['error'] = "Current password is incorrect.";
        ob_end_clean(); // Clear the buffer
        header("Location: /user_management.php");
        exit();
    }
    
    // Check if username is already taken by another user
    $query = "SELECT id FROM users WHERE username = $1 AND id != $2";
    $result = pg_query_params($conn, $query, [$username, $_SESSION['user']['id']]);
    
    if ($result && pg_num_rows($result) > 0) {
        $_SESSION['error'] = "Username is already taken.";
        ob_end_clean(); // Clear the buffer
        header("Location: /user_management.php");
        exit();
    }
    
    // Check if email is already taken by another user
    $query = "SELECT id FROM users WHERE email = $1 AND id != $2";
    $result = pg_query_params($conn, $query, [$email, $_SESSION['user']['id']]);
    
    if ($result && pg_num_rows($result) > 0) {
        $_SESSION['error'] = "Email is already registered.";
        ob_end_clean(); // Clear the buffer
        header("Location: /user_management.php");
        exit();
    }
    
    // Prepare update query
    if (!empty($new_password)) {
        // Update with new password
        $query = "UPDATE users SET username = $1, email = $2, password_hash = $3 WHERE id = $4";
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $result = pg_query_params($conn, $query, [$username, $email, $password_hash, $_SESSION['user']['id']]);
    } else {
        // Update without changing password
        $query = "UPDATE users SET username = $1, email = $2 WHERE id = $3";
        $result = pg_query_params($conn, $query, [$username, $email, $_SESSION['user']['id']]);
    }
    
    if (!$result) {
        throw new Exception("Failed to update profile.");
    }
    
    // Update session data
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email'] = $email;
    
    $_SESSION['success'] = "Profile updated successfully!";
    ob_end_clean(); // Clear the buffer
    header("Location: /user_management.php");
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    ob_end_clean(); // Clear the buffer
    header("Location: /user_management.php");
    exit();
} finally {
    if (isset($conn)) {
        pg_close($conn);
    }
    ob_end_clean(); // Clear the buffer
}
?> 