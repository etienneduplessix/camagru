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
    <title>Camagru - Profile</title>
    <link rel="stylesheet" href="includes/css/style.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            padding: 40px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
    </style>
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
        <div class="profile-container">
            <h1>Edit Profile</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message show"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message show"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <form id="profile-form" method="POST" action="update_profile.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                        value="<?php echo htmlspecialchars($_SESSION['user']['username'] ?? ''); ?>"
                        pattern="^[a-zA-Z0-9_]{3,20}$"
                        title="Username must be 3-20 characters long and contain only letters, numbers, and underscores."
                        required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password (optional)</label>
                    <input type="password" id="new_password" name="new_password"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$"
                        title="Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, and a number.">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. All rights reserved.</p>
    </footer>
</body>
</html>
