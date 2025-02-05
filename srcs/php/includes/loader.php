<?php

ob_start();

# Imports and initializations
require_once(ROOT_DIR.'includes/models.php');
require_once(ROOT_DIR.'includes/functions.php');



if (!isLoggedIn() && 
    !preg_match('/login(.php)?/', currentPage()) && 
    !preg_match('/register(.php)?/', currentPage())) {
    redirectTo(ROOT_DIR.'login.php');
}
?>
