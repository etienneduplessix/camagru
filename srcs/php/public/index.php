<?php

// Database connection
$dbConnection = pg_connect("host=postgres_db dbname={$_ENV['POSTGRES_DB']} user={$_ENV['POSTGRES_USER']} password={$_ENV['POSTGRES_PASSWORD']}");

// Insert user
function createUser($username, $email, $passwordHash) {
    $query = "INSERT INTO users (username, email, password_hash) VALUES ($1, $2, $3) RETURNING id";
    $result = pg_query_params($dbConnection, $query, [$username, $email, $passwordHash]);
    return pg_fetch_assoc($result)['id'];
}

// Get user
function getUserById($id) {
    $query = "SELECT * FROM users WHERE id = $1";
    $result = pg_query_params($dbConnection, $query, [$id]);
    return pg_fetch_assoc($result);
}

// Update user
function updateUser($id, $data) {
    $query = "UPDATE users SET username = $1, email = $2 WHERE id = $3";
    return pg_query_params($dbConnection, $query, [$data['username'], $data['email'], $id]);
}

// Delete user
function deleteUser($id) {
    $query = "DELETE FROM users WHERE id = $1";
    return pg_query_params($dbConnection, $query, [$id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }

        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        h1 {
            text-align: center;
            color: #1a73e8;
            margin-bottom: 1.5rem;
        }

        input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        button {
            background-color: #1a73e8;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #1557b0;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #1a73e8;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form class="login-form" action="/login" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>
        <div class="forgot-password">
            <a href="#">Forgot password?</a>
        </div>
    </div>
</body>
</html>