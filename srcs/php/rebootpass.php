<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Website - Home</title>
  <link rel="stylesheet" href="assets/css/style.css">
<header>
    <div class="logo">My Website</div>
    <nav>
    <a href="index.php">Home</a>
    <a href="usrman.php">User Management</a>
    <a href="galerie.php">galerie</a>
    <a href="logout.php">Logout</a> 
    </nav>
</header>
</head>
<body>
<div class="forgot-container">
    <h1>Forgot Password</h1>
    <div id="error-message" class="error-message" style="display: none;"></div>
    <form id="forgot-form">
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</div>

<script>
document.getElementById("forgot-form").addEventListener("submit", function(event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    
    fetch("forgot_password.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(email)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            alert(data.message);
        }
        console.log("jeher")
    });
});
</script>

<footer>
    &copy; 2025 My Websit
  </footer>
</body>
</html>