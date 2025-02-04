<?php
namespace app\Controllers;

use Exception;

class AuthController extends Controller {
    private $dbConnection;

    public function __construct() {
        $host = getenv('POSTGRES_HOST') ?: 'db'; // Use 'db' if that's your service name
        $dbname = getenv('POSTGRES_DB');
        $user = getenv('POSTGRES_USER');
        $password = getenv('POSTGRES_PASSWORD');

        $this->dbConnection = pg_connect("host=$host dbname=$dbname user=$user password=$password");
        if (!$this->dbConnection) {
            // Log the error instead of exposing it
            error_log("Database connection failed: " . pg_last_error());
            throw new Exception("Database connection failed.");
        }
    }

    public function login() {
        session_start(); // Ensure session is started

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                return $this->view('login', ['error' => 'Email and password are required']);
            }

            // Use prepared statements to prevent SQL injection
            $query = "SELECT * FROM users WHERE email = $1";
            $result = pg_query_params($this->dbConnection, $query, [$email]);

            if (!$result) {
                // Log the error instead of exposing it
                error_log("Database error: " . pg_last_error());
                return $this->view('login', ['error' => 'An error occurred. Please try again later.']);
            }

            $user = pg_fetch_assoc($result);
            if ($user && password_verify($password, $user['password_hash'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                header('Location: /dashboard');
                exit();
            }

            return $this->view('login', ['error' => 'Invalid credentials']);
        }

        return $this->view('login');
    }
}