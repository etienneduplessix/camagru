<?php
ob_start();
session_start();
require_once('includes/loader.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Gallery</title>
    <link rel="stylesheet" href="includes/css/gallery_style.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="/" class="logo">Camagru</a>
            <nav>
                <a href="index.php">Editor</a>
                <a href="galerie.php">Gallery</a>
                <?php if (isset($_SESSION['user']['id'])): ?>
                    <a href="user_management.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <h1 style="text-align: center; margin-bottom: 30px;">Gallery</h1>
        <div class="gallery" id="imageContainer">
        </div>
        <div class="pagination" id="pagination">
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. All rights reserved.</p>
    </footer>

    <script src="script2.js" defer></script>
</body>
</html>
<?php ob_end_flush(); ?>
