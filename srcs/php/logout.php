<?php
session_start(); // Start the session

require_once('includes/loader.php');

if (Auth::isLoggedIn()) {
    Auth::logOut(); // Clear session variables
    session_destroy(); // Destroy the session
    header("Location: /index.php"); // Redirect to homepage
    exit();
} else {
    header("Location: /index.php"); // Redirect to homepage if not logged in
    exit();
}
?>