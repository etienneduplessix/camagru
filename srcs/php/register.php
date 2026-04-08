<?php
session_start();

require_once('includes/loader.php');
require_once('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiUrl = 'http://php_web/register_api.php';

    $data = [
        'username' => trim($_POST['username'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? ''
    ];

    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        $_SESSION['errors'] = "All fields are required.";
        header('Location: /register');
        exit;
    }

    $postData = http_build_query($data);

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                         "Content-Length: " . strlen($postData) . "\r\n",
            'content' => $postData,
            'timeout' => 10
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($apiUrl, false, $context);

    $httpCode = isset($http_response_header[0]) ? $http_response_header[0] : "Unknown";

    if ($result === FALSE) {
        $_SESSION['errors'] = "Registration failed: Unable to contact the API.";
    } elseif (strpos($httpCode, "200") === false) {
        $_SESSION['errors'] = "Registration failed: " . ($result ?: "Unexpected error.");
    } else {
        $_SESSION['success'] = "Registration successful! Please check your email.";
        header('Location: /login');
        exit;
    }

    header('Location: /register');
    exit;
}

showRegistrationForm();

function showRegistrationForm() {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Register</title>
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
            <h1>Create Account</h1>

            <?php if (isset($_SESSION['errors'])): ?>
                <div id="error-message" class="error-message show"><?php echo htmlspecialchars($_SESSION['errors']); unset($_SESSION['errors']); ?></div>
            <?php endif; ?>

            <form id="register-form" method="POST" action="/register.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Choose a username" required
                        pattern="^[a-zA-Z0-9_]{3,20}$"
                        title="Username must be 3-20 characters long and contain only letters, numbers, and underscores.">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required
                        title="Please enter a valid email address.">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                        title="Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>

                <button type="submit">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById("register-form").addEventListener("submit", function(event) {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var errorMessage = document.getElementById("error-message");

            if (password !== confirmPassword) {
                event.preventDefault();
                errorMessage.textContent = "Passwords do not match!";
                errorMessage.classList.add('show');
            }
        });
    </script>
</body>
</html>
<?php } ?>
