<?php
// login.php
session_start();

require_once 'app/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $conn = getConnection();
    
    $result = pg_query_params($conn, 
        'SELECT id, password_hash FROM users WHERE email = $1', 
        [$email]
    );
    
    if ($result === false) {
        $_SESSION['error'] = 'Database error';
        header('Location: /login');
        exit();
    }
    
    $user = pg_fetch_assoc($result);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: /dashboard');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid email or password';
        header('Location: /index');
        exit();
    }
}

header('Location: /home');
exit();
?>