<?php
session_start();

define('ROOT_DIR', '');
require_once(ROOT_DIR . 'includes/loader.php');
require_once(ROOT_DIR . 'includes/db.php');
require_once(ROOT_DIR . 'includes/partials/header.php');

error_log("🔍 Entered register.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("📩 Received POST request");

    $apiUrl = 'http://php_web/register_api.php'; // Use Docker service name

    $data = [
        'username' => trim($_POST['username'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? ''
    ];

    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        error_log("⚠️ Missing form fields, redirecting.");
        $_SESSION['errors'] = "All fields are required.";
        header('Location: /register');
        exit;
    }

    error_log("🚀 Sending request to API: " . $apiUrl);
    error_log("📝 Data being sent: " . print_r($data, true));

    // Convert data into a query string
    $postData = http_build_query($data);

    // Set HTTP request options
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                         "Content-Length: " . strlen($postData) . "\r\n",
            'content' => $postData,
            'timeout' => 10 // Timeout after 10 seconds
        ]
    ];

    // Create stream context
    $context = stream_context_create($options);
    
    // Send request and get response
    $result = @file_get_contents($apiUrl, false, $context);

    // Get HTTP response code
    $httpCode = isset($http_response_header[0]) ? $http_response_header[0] : "Unknown";

    error_log("📩 API Response: " . ($result ?: "No response"));
    error_log("🔢 HTTP Code: " . $httpCode);

    if ($result === FALSE) {
        $_SESSION['errors'] = "Registration failed: Unable to contact the API.";
    } elseif (strpos($httpCode, "200") === false) {
        $_SESSION['errors'] = "Registration failed: " . ($result ?: "Unexpected error.");
    } else {
        $_SESSION['success'] = "Registration successful! Please check your email.";
        error_log("✅ Registration success, redirecting to login.");
        header('Location: /login');
        exit;
    }

    error_log("❌ Redirecting to /register due to errors.");
    header('Location: /register');
    exit;
}

error_log("👀 Showing registration form.");
showRegistrationForm();
?>


<?php
function showRegistrationForm() { ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Camagru</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>

    <div class="auth-container">
        <div class="auth-box">
            <h1>Create Account</h1>

            <div id="error-message" class="error-message" style="display: none;"></div>
            <div id="success-message" class="success-message" style="display: none;"></div>

            <form class="auth-form" method="POST" action="/register.php" onsubmit="return validatePassword()">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="password" id="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Register</button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a href="/login.php">Login here</a></p>
            </div>
        </div>
    </div>

    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var errorMessage = document.getElementById("error-message");

            if (password !== confirmPassword) {
                errorMessage.innerHTML = "Passwords do not match!";
                errorMessage.style.display = "block";
                return false;
            }
            errorMessage.style.display = "none";
            return true;
        }
    </script>

</body>
</html>

<?php } ?>

<?php require_once(ROOT_DIR . 'includes/partials/footer.php'); ?>
