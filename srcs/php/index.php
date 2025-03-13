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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Website - Home</title>
    <link rel="stylesheet" href="includes/css/index_style.css">
    <script src="script.js" defer></script>
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
    <div class="main-content">

            <div class="controls">
                <button class="btn btn-primary" id="startCamera">Start Camera</button>
                <label class="btn btn-success">
                    Upload Photo
                    <input type="file" id="fileInput" accept="image/*" style="display: none;">
                </label>
                <button class="btn btn-success" id="sendToDB">Send to DB</button>
            </div>
        <main class="content">
            <div class="editor">
                <div class="preview">
                    <div class="preview-area" id="previewArea">
                        <video id="video" autoplay playsinline style="width: 100%; height: auto;"></video>
                        <img id="preview" src="" alt="" style="width: 100%; height: auto; display: none;">
                        <canvas id="canvas" style="display: none;"></canvas>
                    </div>
                </div>
            </div>
            <div class="tata">
                <div class="button-group">
                    <button class="btn btn-success" id="cat">Cat</button>
                    <button class="btn btn-success" id="sun">Sun</button>
                    <button class="btn btn-success" id="flower">Flower</button>
                </div>
            </div>
        </main>
        <aside class="sidebar" id="sidebar-container">
            <!-- Sidebar content goes here -->
        </aside>
    </div>
    <footer>
        &copy; 2025 My Website
    </footer>
</body>
</html>
