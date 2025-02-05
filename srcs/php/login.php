<?php
define('ROOT_DIR', '');
require_once(ROOT_DIR.'includes/loader.php');
require_once(ROOT_DIR.'includes/partials/header.php');
?>

<?php
if (isLoggedIn()) {
    redirectTo(ROOT_DIR.'.');
}
else {
  if (isset($_POST['login'])){
    checkSessionToken($_REQUEST['tokenfield'], $_SESSION['sessiontoken'], '/login.php');
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    var_dump($username);
    var_dump($password);
    logIn($username,$password);
  }
  else {
    newSessionToken();
    showLoginForm();
  }
}
?>

<?php
function showLoginForm(){ ?>
  <div class="login-container">
      <h1>Login</h1>
      <?php if (isset($_SESSION['error'])): ?>
          <div class="error-message">
              <?php 
               echo htmlspecialchars($_SESSION['error']);
               unset($_SESSION['error']);
              ?>
          </div>
      <?php endif; ?>
      <form class="login-form" action="/login" method="POST">
          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <?php if (isset($_SESSION['sessiontoken'])): ?>
              <input type="hidden" name="tokenfield" value="<?php echo htmlspecialchars($_SESSION['sessiontoken']); ?>">
          <?php endif; ?>
          <button type="submit">Sign In</button>
      </form>
      <div class="form-footer">
          <div class="forgot-password">
              <a href="#">Forgot password?</a>
          </div>
            <li><a href="<?php echo ROOT_DIR.'register.php';?>" >register</a></li>
      </div>
  </div>
<?php } ?>


<?php require_once(ROOT_DIR.'includes/partials/footer.php'); ?>
