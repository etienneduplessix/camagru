<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Website - Home</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="script2.js" defer></script> <!-- Fixed filename and added 'defer' -->
<header>
    <div class="logo">My Website</div>
    <nav>
    <a href="index.php">Home</a>
    <a href="usrman.php">User Management</a>
    <a href="logout.php">Logout</a> 
    </nav>
</header>
</head>
<body>


<style>
    /* Global styling */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }

    /* Gallery container using CSS Grid */
    #imageContainer {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      padding: 20px;
    }

    /* Wrapper for image and like button */
    .wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

      .wrapper img {
    width: 100%;
    height: 200px; /* Fixed height for uniform image size */
    object-fit: cover; /* Ensures the image covers the container without distortion */
    border: 2px solid black; /* Nice black border */
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
  }

  /* Optional hover effect */
  .wrapper img:hover {
    transform: scale(1.03);
  }

    /* Like button styling */
    .like-button {
      position: absolute;
      top: 100px;
      left: 100px;
      background-color: rgba(255, 255, 255, 0.8);
      border: none;
      border-radius: 50%;
      padding: 8px;
      cursor: pointer;
      font-size: 16px;
    }

    /* Styling for the liked state */
    .like-button.liked {
      background-color: red;
      color: white;
    }
  </style>
<body>
  <div id="imageContainer">
    <!-- Gallery images will be loaded here -->
  </div>
<footer>
    &copy; 2025 My Website
</footer>

</body>
</html>
<?php ob_end_flush(); ?>
