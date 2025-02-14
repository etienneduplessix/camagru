<?php
function getConnection() {
    $host = 'db'; // The Docker service name
    $port = '5432';
    $dbname = 'camagru';
    $user = 'myuser';
    $password = 'mypassword';

    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
    $conn = pg_connect($conn_string);

    if (!$conn) {
        die("❌ Database connection failed: " . pg_last_error());
    }

    return $conn;
}
?>


<?php
function getConnection2() {
    $dsn = "pgsql:host=db;dbname=camagru;port=5432"; // Adjust according to your PostgreSQL settings
    $user = "myuser";
    $password = "mypassword";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
