<?php
// config.php
define('ROOT_DIR', '');
define('SITE_URL', 'http://localhost:8080'); // Adjust port if different
define('MIN_PASSWORD_LENGTH', 8);
define('EMAIL_FROM', 'esusagence@gmail.com');

// For development/testing only
define('DEV_MODE', true);
define('SMTP_HOST', 'mailhog'); // Docker service name for MailHog
define('SMTP_PORT', 1025);      // Default MailHog SMTP port

// register.php
require_once(ROOT_DIR.'includes/loader.php');
require_once(ROOT_DIR.'includes/partials/header.php');
require_once(ROOT_DIR.'includes/db.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class RegistrationHandler {
    private $conn;
    private $errors = [];
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }
    
    public function handleRegistration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        try {
            $this->validateInput();
            
            if (!empty($this->errors)) {
                $this->setSessionError();
                return;
            }
            
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $username = $this->generateUniqueUsername($email);
            $password = $_POST['password'];
            
            if ($this->isEmailRegistered($email)) {
                throw new Exception('Email already registered');
            }
            
            $userId = $this->createUser($username, $email, $password);
            if (!$userId) {
                throw new Exception('Registration failed');
            }
            
            $verificationToken = $this->generateVerificationToken($userId);
            if (!$this->sendVerificationEmail($email, $verificationToken)) {
                error_log("Failed to send verification email to: $email");
            }
            
            $_SESSION['success'] = 'Registration successful! Please check your email to verify your account.';
            header('Location: /login');
            exit();
            
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->setSessionError();
        }
    }
    
    private function validateInput() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Invalid email format';
        }
        
        if (strlen($password) < MIN_PASSWORD_LENGTH) {
            $this->errors[] = 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $this->errors[] = 'Password must contain at least one number';
        }
        
        if ($password !== $confirmPassword) {
            $this->errors[] = 'Passwords do not match';
        }
    }
    
    private function generateUniqueUsername($email) {
        $username = strtolower(explode('@', $email)[0]);
        $baseUsername = $username;
        $counter = 1;
        
        while ($this->isUsernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    private function isUsernameExists($username) {
        $stmt = pg_prepare($this->conn, "check_username", 'SELECT id FROM users WHERE username = $1');
        $result = pg_execute($this->conn, "check_username", [$username]);
        return pg_num_rows($result) > 0;
    }
    
    private function isEmailRegistered($email) {
        $stmt = pg_prepare($this->conn, "check_email", 'SELECT id FROM users WHERE email = $1');
        $result = pg_execute($this->conn, "check_email", [$email]);
        return pg_num_rows($result) > 0;
    }
    
    private function createUser($username, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        $stmt = pg_prepare($this->conn, "create_user", 
            'INSERT INTO users (username, email, password_hash, created_at, is_verified) 
             VALUES ($1, $2, $3, CURRENT_TIMESTAMP, false) RETURNING id'
        );
        
        $result = pg_execute($this->conn, "create_user", [$username, $email, $passwordHash]);
        
        if ($result === false) {
            return false;
        }
        
        $row = pg_fetch_assoc($result);
        return $row['id'];
    }
    
    private function generateVerificationToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = pg_prepare($this->conn, "create_token",
            'INSERT INTO verification_tokens (user_id, token, expires_at) 
             VALUES ($1, $2, $3)'
        );
        
        pg_execute($this->conn, "create_token", [$userId, $token, $expiresAt]);
        return $token;
    }
    
    private function sendVerificationEmail($email, $token) {
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'esusagence@gmail.com';
            $mail->Password = 'ezsetfubktuskwew';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom(EMAIL_FROM);
            $mail->addAddress($email);
            $mail->Subject = 'Verify Your Account';
            $mail->isHTML(true);
            
            $verificationUrl = SITE_URL . '/verify?token=' . urlencode($token);
            $mail->Body = "Please click the following link to verify your account:<br>
                         <a href='$verificationUrl'>$verificationUrl</a>";
            
            return $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function setSessionError() {
        $_SESSION['error'] = implode('<br>', $this->errors);
        header('Location: /register');
        exit();
    }
}

$handler = new RegistrationHandler(getConnection());
$handler->handleRegistration();
showRegisterForm();

?>


<?php
function showRegisterForm() {?>
<div class="login-container">
    <h1>Register</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php 
            echo htmlspecialchars($_SESSION['error']); 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <form class="login-form" action="/register" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <small class="password-requirements">
                Password must be at least 8 characters long and contain:
                <ul>
                    <li>At least one uppercase letter</li>
                    <li>At least one number</li>
                </ul>
            </small>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <div class="login-link">
        <a href="/login">Already have an account? Login</a>
    </div>
</div>
<?php 
}
?>


