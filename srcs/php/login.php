<?php
require_once('includes/loader.php');

$currentPage = Utils::currentPage();

if (!isset($_SESSION['sessiontoken'])) {
    $_SESSION['sessiontoken'] = md5(uniqid(rand(), true));
}
$token = $_SESSION['sessiontoken'];

if (Auth::isLoggedIn()) {
    header("Location: /index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Login</title>
    <link rel="stylesheet" href="includes/css/style.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="/" class="logo">Camagru</a>
            <nav>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>Welcome Back</h1>
            <div id="error-message" class="error-message"></div>

            <form id="login-form" method="POST" action="/login.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your username" required
                           pattern="^[a-zA-Z0-9_]{3,20}$"
                           title="Username must be 3-20 characters long and contain only letters, numbers, and underscores.">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$"
                           title="Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, and a number.">
                </div>

                <button type="submit" name="login">Sign In</button>
            </form>

            <div class="form-footer">
                <p><a href="register.php">Create an account</a></p>
                <p><a href="rebootpass.php">Forgot your password?</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById("login-form").addEventListener("submit", function(event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("/login_api.php", {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.replace(data.redirect);
                } else {
                    document.getElementById("error-message").textContent = data.error;
                    document.getElementById("error-message").classList.add('show');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("error-message").textContent = "An unexpected error occurred.";
                document.getElementById("error-message").classList.add('show');
            });
        });
    </script>
</body>
</html>
