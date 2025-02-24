<?php
session_start();
if (!isset($_SESSION['user']['id'])) {
    header("Location: /login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Website - Home</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="srcipt.js"></script>
</head>
<body>
  <header>
    <div class="logo">My Website</div>
    <nav>
      <a href="#">Home</a>
      <a href="#">usrman</a> 
      <a href="#">Logout</a>

    </nav>
  </header>
  
  <div class="main-content">
    <div class="container">
      <div class="controls">
        <button class="btn btn-primary" id="startCamera">Start Camera</button>
        <label class="btn btn-success">
          Upload Photo
          <input type="file" id="fileInput" accept="image/*" style="display: none;">
        </label>
        <button class="btn btn-success" id="sendToDB">Send to DB</button>
      </div>
  
      <div class="editor">
        <div class="preview">
          <div class="preview-area" id="previewArea">
            <video id="video" autoplay playsinline></video>
            <img id="preview" src="" alt="">
            <canvas id="canvas"></canvas>
          </div>
        </div>
      </div>
    </div>
  
    <aside class="sidebar" id="sidebar-container">
    </aside>
  </div>
  
  <footer>
    &copy; 2025 My Website
  </footer>
</body>
</html>
