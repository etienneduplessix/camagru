<?php
function getConnection() {
    $host = 'db'; // Check if this matches the service name in docker-compose
    $port = '5432';
    $dbname = 'camagru';
    $user = 'myuser';
    $password = 'mypassword';

    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
    
    $conn = pg_connect($conn_string);
    
    if (!$conn) {
        die("❌ Database connection failed: " . pg_last_error());
    }

    echo "✅ Successfully connected to the database!";
    
    return $conn;
}

function getConnection2() {
    $host = 'db'; // Docker service name
    $port = '5432';
    $dbname = 'camagru';
    $user = 'myuser';
    $password = 'mypassword';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    
    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("❌ Database connection failed: " . $e->getMessage());
    }
}
?>
