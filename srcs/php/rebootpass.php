<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - My Website</title>
    <link rel="stylesheet" href="includes/css/auth.css">
</head>
<body class="auth-page">
    <header class="auth-header">
        <div class="logo">My Website</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-container">
            <div class="auth-box">
                <h1>Reset Password</h1>
                <p class="auth-subtitle">Enter your email to receive reset instructions</p>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="error-message">
                        <?php 
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success-message">
                        <?php 
                            echo htmlspecialchars($_SESSION['success']);
                            unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <form id="reset-form" class="auth-form" method="POST" action="forgot_password.php">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            <span class="input-icon">📧</span>
                        </div>
                    </div>

                    <button type="submit" class="auth-button">
                        <span class="button-text">Send Reset Link</span>
                        <span class="button-loader" style="display: none;">Loading...</span>
                    </button>
                </form>

                <div class="auth-links">
                    <a href="login.php" class="auth-link">Back to Login</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="auth-footer">
        &copy; 2025 My Website
    </footer>

    <script>
    document.getElementById('reset-form').addEventListener('submit', function(event) {
        const button = this.querySelector('button[type="submit"]');
        const buttonText = button.querySelector('.button-text');
        const buttonLoader = button.querySelector('.button-loader');

        // Show loading state
        button.disabled = true;
        buttonText.style.display = 'none';
        buttonLoader.style.display = 'inline';
    });
    </script>
</body>
</html>