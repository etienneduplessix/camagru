<?php
session_start();

require_once('includes/loader.php');
require_once('includes/db.php');

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
  <title>My Website - Home</title>
  <link rel="stylesheet" href="includes/css/style.css">
  <script src="script.js" defer></script> <!-- Fixed filename and added 'defer' -->
<header>
    <div class="logo">My Website</div>
    <nav>
    <a href="index.php">Home</a>
    <a href="user_management.php">User Management</a>
    <a href="galerie.php">Galerie</a>
    <a href="logout.php">Logout</a> 
    </nav>
</header>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Create Account</h1>

            <div id="error-message" class="error-message" style="display: none;"></div>
            <div id="success-message" class="success-message" style="display: none;"></div>
            <form class="auth-form" method="POST" action="/register.php">
                <!-- Username: 3-20 characters, only letters, numbers, and underscores -->
                <input type="text" name="username" placeholder="Username" required
                    pattern="^[a-zA-Z0-9_]{3,20}$"
                    title="Username must be 3-20 characters long and contain only letters, numbers, and underscores.">
                
                <!-- Email: Standard email format -->
                <input type="email" name="email" placeholder="Email" required
                    pattern="^\S+@\S+\.\S+$"
                    title="Please enter a valid email address.">

                <!-- Password: At least 8 characters, one uppercase, one lowercase, one number, and one special character -->
                <input type="password" id="password" name="password" placeholder="Password" required
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                    title="Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.">

                <!-- Confirm Password: Must match the first password field -->
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

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
<footer>
    &copy; 2025 My Websit
  </footer>
</body>
</html>

<?php } ?>