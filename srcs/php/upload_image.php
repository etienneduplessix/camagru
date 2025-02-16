<?php
session_start();
header('Content-Type: application/json');

require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['image'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit();
}

// Decode Base64 PNG
$image_data = base64_decode($data['image']);
if ($image_data === false) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid image data']);
    exit();
}

try {
    $pdo = getConnection();

    // Use PostgreSQL-specific bytea escaping
    $image_binary = pg_escape_bytea($image_data);

    $stmt = $pdo->prepare("INSERT INTO images (user_id, image_data, created_at) VALUES (:user_id, :image_data, NOW())");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':image_data', $image_binary, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
