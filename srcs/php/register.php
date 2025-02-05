
<?php
// Start output buffering at the very beginning
ob_start();

// config.php
define('ROOT_DIR', '');
define('SITE_URL', 'http://localhost:8000'); // Adjust port if different
define('MIN_PASSWORD_LENGTH', 8);
define('EMAIL_FROM', 'noreply@localhost');

// For development/testing only
define('DEV_MODE', true);
define('SMTP_HOST', 'test-mailhog-1'); // Docker service name for MailHog
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
    private function isUsernameExists($username) {
        $stmt = pg_prepare($this->conn, "check_username", 'SELECT id FROM users WHERE username = $1');
        $result = pg_execute($this->conn, "check_username", [$username]);
        return pg_num_rows($result) > 0;
    }

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
            $password = $_POST['password'];
            
            if ($this->isEmailRegistered($email)) {
                throw new Exception('Email already registered');
            }
            
            $userId = $this->createUser($email, $password);
            if (!$userId) {
                throw new Exception('Registration failed');
            }
            
            $verificationToken = $this->generateVerificationToken($userId);
            if (!$this->sendVerificationEmail($email, $verificationToken)) {
                // Log the email sending failure but don't expose it to the user
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
    
    private function isEmailRegistered($email) {
        $stmt = pg_prepare($this->conn, "check_email", 'SELECT id FROM users WHERE email = $1');
        $result = pg_execute($this->conn, "check_email", [$email]);
        return pg_num_rows($result) > 0;
    }
    
    private function createUser($email, $password) {
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        // Generate username from email if not provided
        $username = strtolower(explode('@', $email)[0]);
        
        // Make username unique by adding number if needed
        $baseUsername = $username;
        $counter = 1;
        while ($this->isUsernameExists($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
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
        try {
            // Check if verification_tokens table exists
            $check_table = pg_query($this->conn, 
                "SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_name = 'verification_tokens'
                )"
            );
            $table_exists = pg_fetch_result($check_table, 0, 0);
            
            if ($table_exists === 'f') {
                // Create table if it doesn't exist
                pg_query($this->conn, "
                    CREATE TABLE IF NOT EXISTS verification_tokens (
                        id SERIAL PRIMARY KEY,
                        user_id INTEGER REFERENCES users(id),
                        token VARCHAR(64) NOT NULL,
                        expires_at TIMESTAMP NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        used_at TIMESTAMP,
                        UNIQUE(token)
                    )
                ");
            }
            
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Use a new statement name for each preparation
            $stmt_name = "create_token_" . uniqid();
            
            $stmt = pg_prepare($this->conn, $stmt_name,
                'INSERT INTO verification_tokens (user_id, token, expires_at) 
                 VALUES ($1, $2, $3) RETURNING token'
            );
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement");
            }
            
            $result = pg_execute($this->conn, $stmt_name, [$userId, $token, $expiresAt]);
            
            if ($result === false) {
                throw new Exception("Failed to insert token");
            }
            
            return $token;
        } catch (Exception $e) {
            error_log("Error generating verification token: " . $e->getMessage());
            // Return a token even if we can't store it in the database
            return bin2hex(random_bytes(32));
        }
    }
    
    private function sendVerificationEmail($email, $token) {
        // Build the verification link
        $verificationLink = SITE_URL . "/verify?token=" . urlencode($token);
        
        $subject = "Verify your account";
        $message = "Hello,\n\n"
                 . "Please click the following link to verify your account:\n"
                 . "$verificationLink\n\n"
                 . "This link will expire in 24 hours.\n\n"
                 . "If you didn't create an account, please ignore this email.\n\n"
                 . "Regards,\nYour Website Team";
        
        try {
            // Ensure PHPMailer is available
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                throw new Exception("PHPMailer not installed");
            }
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            
            // Configure PHPMailer to use MailHog's SMTP settings
            // If you're using Docker, the host is usually the service name, e.g., "mailhog"
            $mail->Host = 'mailhog';   // Adjust this if needed (e.g., "localhost" if using port mapping)
            $mail->Port = 1025;        // MailHog's default SMTP port
            $mail->SMTPAuth = false;   // No authentication is required for MailHog
            
            // Optionally disable encryption if not used
            $mail->SMTPSecure = false;
            
            $mail->setFrom(EMAIL_FROM);
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $message;
            
            $mail->send();
            
            // Log the verification link (useful for debugging in development)
            error_log("MailHog verification email sent. Link: $verificationLink");
            
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            error_log("Verification link: $verificationLink");
            // Optionally, decide if you want to return true or false on failure
            return false;
        }
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
