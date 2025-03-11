<?php
// Start session (if needed)
session_start();

// Check if token exists in the URL (for password reset)
if (!isset($_GET['token'])) {
    die("Invalid request. Token is missing.");
}
$token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Website - Reset Password</title>
    <link rel="stylesheet" href="includes/css/style.css">
    <script defer>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("reset-password-form");
            const errorMessage = document.getElementById("error-message");

            form.addEventListener("submit", async function (event) {
                event.preventDefault(); // Prevent default form submission

                const newPassword = document.getElementById("newpassword").value;
                const confirmPassword = document.getElementById("newpasswordconf").value;
                const token = "<?php echo $token; ?>"; // Inject PHP token into JavaScript

                if (newPassword !== confirmPassword) {
                    errorMessage.textContent = "Passwords do not match!";
                    errorMessage.style.display = "block";
                    return;
                }

                try {
                    const response = await fetch("reboot_pass_api.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            token: token,
                            newpassword: newPassword
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert("Password reset successfully!");
                        window.location.href = "login.php"; // Redirect to login page
                    } else {
                        errorMessage.textContent = data.error || "An error occurred.";
                        errorMessage.style.display = "block";
                    }
                } catch (error) {
                    console.error("Error:", error);
                    errorMessage.textContent = "Failed to connect to the server.";
                    errorMessage.style.display = "block";
                }
            });
        });
    </script>
</head>
<body>

<header>
    <div class="logo">My Website</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="usrman.php">User Management</a>
        <a href="galerie.php">Galerie</a>
        <a href="logout.php">Logout</a> 
    </nav>
</header>

<div class="login-container">
    <h1>Reset Password</h1>
    <div id="error-message" class="error-message" style="display: none;"></div>

    <form class="auth-form" id="reset-password-form">
        <input type="password" name="newpassword" id="newpassword" placeholder="New Password" required>
        <input type="password" name="newpasswordconf" id="newpasswordconf" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</div>

<footer>
    &copy; 2025 My Website
</footer>

</body>
</html>
