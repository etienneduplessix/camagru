<?php
require_once('includes/db.php'); // Ensure this file exists
session_start();

class User {
    private $db;
    private $errors = [];

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($username, $email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format.";
            return false;
        }

        if (strlen($password) < 6) {
            $this->errors[] = "Password must be at least 6 characters long.";
            return false;
        }

        // Check if email already exists
        $emailEscaped = pg_escape_string($this->db, $email);
        $query = "SELECT COUNT(*) FROM users WHERE email = '$emailEscaped'";
        $result = pg_query($this->db, $query);

        if (!$result) {
            $this->errors[] = "Database query failed: " . pg_last_error($this->db);
            return false;
        }

        if (pg_fetch_result($result, 0, 0) > 0) {
            $this->errors[] = "Email already registered.";
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $token = bin2hex(random_bytes(32));

      $query = "INSERT INTO users (username, email, password_hash, verification_token) 
                  VALUES ($1, $2, $3, $4)";

      $stmt = pg_prepare($this->db, "insert_user", $query);
      $success = pg_execute($this->db, "insert_user", [$username, $email, $hashedPassword, $token]);


        if (!$success) {
            $this->errors[] = "Database insert failed: " . pg_last_error($this->db);
            return false;
        }

        $this->sendVerificationEmail($email, $token);
        return true;
    }

    private function sendVerificationEmail($email, $token) {
      $verificationLink = "http://localhost:8000/verify.php" . "/verify?token=" . urlencode($token);
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Verify Your Camagru Account</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <h2>Welcome to Camagru!</h2>
            <p>Please verify your email by clicking the link below:</p>
            <p><a href='{$verificationLink}' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none;'>Verify Email</a></p>
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p>{$verificationLink}</p>
        </body>
        </html>";

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Camagru <noreply@camagru.com>'
        ];

        mail($email, "Verify Your Camagru Account", $message, implode("\r\n", $headers));
    }

    public function getErrors() {
        return $this->errors;
    }
}

// API Request Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('includes/db.php'); // Ensure correct path

    $db = getConnection(); // Now using the correct function
    $user = new User($db);

    $username = $_POST['username'] ?? 'Guest';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($user->create($username, $email, $password)) {
        http_response_code(200);
        echo json_encode(["message" => "Registration successful. Please check your email."]);
    } else {
        http_response_code(400);
        echo json_encode(["errors" => $user->getErrors()]);
    }

    pg_close($db); // Close connection
}
?>