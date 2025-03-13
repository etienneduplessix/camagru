<?php
// Start the session if not already started
session_start();

// Include necessary files
require_once('includes/loader.php');

// Log the logout attempt (helpful for debugging)
error_log("Logout attempt initiated. User status: " . (Auth::isLoggedIn() ? "Logged in" : "Not logged in"));

// Always perform logout actions regardless of login status
// Clear session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Clear any authentication cookies (if used)
setcookie("auth_token", "", time() - 3600, "/"); 

// Make sure there's no whitespace or output before this point
header("Location: /index.php");
exit();
?>