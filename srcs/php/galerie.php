<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Website - Home</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="includes/css/gallery_style.css">
  <script src="script2.js" defer></script>
</head>
<body>
  <header>
    <div class="logo">My Website</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="user_management.php">User Management</a>
      <a href="galerie.php">Galerie</a>
      <a href="logout.php">Logout</a> 
    </nav>
  </header>

  <div class="gallery" id="imageContainer">
    <!-- Gallery images will be loaded here -->
  </div>

  <footer>
    &copy; 2025 My Website
  </footer>
</body>
</html>
<?php ob_end_flush(); ?>
