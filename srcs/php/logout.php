<?php
session_abort();

?>

<?php
$currentPage = Utils::currentPage();

  if (Auth::isLoggedIn()) {
    logOut();
    redirectTo(ROOT_DIR.'index.php');
  }
  else {
      redirectTo(ROOT_DIR.'index.php');
  }
?>