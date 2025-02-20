<?php
ob_start();
define('ROOT_DIR', '');
// Imports and initializations
require_once(ROOT_DIR . 'includes/models.php');
require_once(ROOT_DIR . 'includes/functions.php');

// Get the current page; if you refactored this into a Utils class, you can use that method.
$currentPage = Utils::currentPage();

// If the user is not logged in and is not on a login or register page, redirect them.
if (
    !Auth::isLoggedIn() &&
    !preg_match('/login(?:\.php)?$/', $currentPage) &&
    !preg_match('/register(?:\.php)?$/', $currentPage)
) {
    header("Location: " . ROOT_DIR . "login.php");
    exit;
}
?>
