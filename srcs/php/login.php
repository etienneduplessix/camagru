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

require_once('includes/partials/header.php');
?>

<div class="login-container">
    <h1>Login</h1>
    <div id="error-message" class="error-message" style="display: none;"></div>

    <form class="auth-form" id="login-form">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="email" name="email" id="email" placeholder="Email" required>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <button type="submit" name="login">Sign In</button>
    </form>

    <div class="form-footer">
        <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div>
        <li><a href="<?php echo ROOT_DIR . 'register.php'; ?>">Register</a></li>
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

<?php require_once('includes/partials/footer.php'); ?>
