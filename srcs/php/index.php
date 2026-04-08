<?php
session_start();
require_once('includes/loader.php');

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
    <title>Camagru - Editor</title>
    <link rel="stylesheet" href="includes/css/index_style.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="/" class="logo">Camagru</a>
            <nav>
                <a href="index.php">Editor</a>
                <a href="galerie.php">Gallery</a>
                <a href="user_management.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="editor-container">
            <div class="editor-main">
                <div class="preview-area" id="previewArea">
                    <video id="video" autoplay playsinline></video>
                    <img id="preview" src="" alt="" style="display: none;">
                    <canvas id="canvas" style="display: none;"></canvas>
                </div>

                <div class="overlay-select">
                    <img src="png/cat.png" class="overlay-option" data-overlay="cat" alt="Cat">
                    <img src="png/sun.png" class="overlay-option" data-overlay="sun" alt="Sun">
                    <img src="png/flower.png" class="overlay-option" data-overlay="flower" alt="Flower">
                </div>

                <div class="controls">
                    <button class="btn btn-primary" id="startCamera">Start Camera</button>
                    <label class="btn btn-success">
                        Upload Photo
                        <input type="file" id="fileInput" accept="image/*" style="display: none;">
                    </label>
                    <button class="btn btn-success" id="sendToDB" disabled>Capture</button>
                </div>
            </div>

            <div class="editor-sidebar">
                <h3 class="sidebar-title">My Images</h3>
                <div class="thumbnails" id="thumbnails">
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. All rights reserved.</p>
    </footer>

    <script src="script.js" defer></script>
</body>
</html>
