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
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
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
        <main class="content">
            <h1>User Management</h1>
            <div class="profile-form-container">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php 
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo htmlspecialchars($_SESSION['success']);
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <form id="profile-form" class="profile-form" method="POST" action="update_profile.php">
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
                            pattern="^\S+@\S+\.\S+$"
                            title="Please enter a valid email address."
                            required>
                    </div>

                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password"
                            required>
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

                    <button type="submit" class="submit-btn">Update Profile</button>
                </form> 
            </div>
        </main>
    </div>
    <footer>
        &copy; 2025 My Website
    </footer>
</body>
</html>
