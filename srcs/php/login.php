<?php
define('ROOT_DIR', '');
require_once(ROOT_DIR.'includes/loader.php');
require_once(ROOT_DIR.'includes/partials/header.php');
session_start();
// If already logged in, redirect to index.php
if (isLoggedIn()) {
    header("Location: /index.php");
    exit();
}

showLoginForm();

function showLoginForm(){ ?>
  <div class="login-container">
      <h1>Login</h1>
      <div id="error-message" class="error-message" style="display: none;"></div>

      <form class="auth-form" id="login-form">
          <input type="email" name="email" id="email" placeholder="Email" required>
          <input type="password" name="password" id="password" placeholder="Password" required>
          <button type="submit" name="login">Sign In</button>
      </form>

      <div class="form-footer">
          <div class="forgot-password">
              <a href="#">Forgot password?</a>
          </div>
          <li><a href="<?php echo ROOT_DIR.'register.php'; ?>">Register</a></li>
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
                window.location.replace(data.redirect); // Force reload to index.php
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

<?php } ?>

<?php require_once(ROOT_DIR.'includes/partials/footer.php'); ?>
