


<?php
require_once('includes/loader.php');

$currentPage = Utils::currentPage();

// Generate token only if one doesn't already exist
if (!isset($_SESSION['sessiontoken'])) {
    $_SESSION['sessiontoken'] = md5(uniqid(rand(), true));
}
$token = $_SESSION['sessiontoken'];

// If already logged in, redirect to index.php
if (Auth::isLoggedIn()) {
    header("Location: /index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Website - Home</title>
  <link rel="stylesheet" href="includes/css/style.css">
  <link rel="icon" type="image/x-icon" href="/favicon.ico">
<header>
    <div class="logo">My Website</div>
    <nav>
    <a href="index.php">Home</a>
    <a href="user_management.php">User Management</a>
    <a href="galerie.php">galerie</a>
    <a href="logout.php">Logout</a> 
    </nav>
</header>
</head>
<body>
<div class="login-container">
    <h1>Login</h1>
    <div id="error-message" class="error-message" style="display: none;"></div>


    <form class="auth-form" id="login-form" method="POST" action="/login.php">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <!-- Username: 3-20 characters, only letters, numbers, and underscores -->
        <input type="text" name="username" id="username" placeholder="Username" required
               pattern="^[a-zA-Z0-9_]{3,20}$"
               title="Username must be 3-20 characters long and contain only letters, numbers, and underscores.">

        <!-- Password: Minimum 8 characters, at least 1 uppercase, 1 lowercase, 1 number -->
        <input type="password" name="password" id="password" placeholder="Password" required
               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$"
               title="Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, and a number.">

        <button type="submit" name="login">Sign In</button>
    </form>

    <div class="form-footer">
        <li><a href="<?php echo'register.php'; ?>">Register</a></li>
        <li><a href="<?php echo'rebootpass.php'; ?>">forgoten password</a></li>
    </div>
</div>

<script>
document.getElementById("login-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent page reload

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
            console.log("✅ Login Successful! Redirecting...");
            window.location.replace(data.redirect); // Redirect to index.php
        } else {
            document.getElementById("error-message").innerHTML = data.error;
            document.getElementById("error-message").style.display = "block";
        }
    })
    .catch(error => {
        console.error("❌ Error:", error);
        document.getElementById("error-message").innerHTML = "An unexpected error occurred.";
        document.getElementById("error-message").style.display = "block";
    });
});

</script>
<footer>
    &copy; 2025 My Websit
  </footer>
</body>
</html>