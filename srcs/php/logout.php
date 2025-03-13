<?php
// Start or resume the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
require_once('includes/loader.php');

// Log the logout attempt
error_log("Logout attempt initiated. User status: " . (Auth::isLoggedIn() ? "Logged in" : "Not logged in"));

// Unset all session variables
$_SESSION = [];

// Get session parameters before destroying it
$params = session_get_cookie_params();

// Destroy the session
if (session_id() !== "") {
    session_destroy(); // Destroys session file
}

// Remove session cookie
setcookie(
    session_name(), // Gets session name
    '', 
    time() - 42000, // Expire in the past
    $params["path"], 
    $params["domain"], 
    $params["secure"], 
    $params["httponly"]
);

// Remove authentication token cookie (if used)
setcookie("auth_token", "", time() - 3600, "/");

// Unset $_COOKIE global to prevent reuse
unset($_COOKIE[session_name()]);
unset($_COOKIE['auth_token']);

// Log the logout success
error_log("Logout successful. Session fully destroyed.");

// Redirect to homepage
header("Location: /index.php");
exit();
?>
