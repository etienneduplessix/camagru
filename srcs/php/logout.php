<?php
session_start(); // Start the session

require_once('includes/loader.php');

if (Auth::isLoggedIn()) {
    // Clear session variables
    $_SESSION = [];

    // Destroy session on the server
    session_destroy();

    // Clear the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Clear any authentication cookies (if used)
    setcookie("auth_token", "", time() - 3600, "/"); // Adjust cookie name as needed

    // Redirect to homepage
    header("Location: /index.php");
    exit();
} else {
    // Redirect to homepage if not logged in
    header("Location: /index.php");
    exit();
}
?>
