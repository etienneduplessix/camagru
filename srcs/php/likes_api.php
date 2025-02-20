<?php
// like_api.php
require_once 'includes/db.php'; // Ensure this file contains your database connection setup
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'Unauthorized. Please log in.']);
    exit();
}

$user_id = $_SESSION['user']['id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['image_id']) || !is_numeric($data['image_id'])) {
    echo json_encode(['error' => 'Invalid image ID']);
    exit();
}

$image_id = intval($data['image_id']);

// Initialize database connection
$pdo = getConnection2();

try {
    // Check if the user has already liked the image
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND image_id = ?");
    $stmt->execute([$user_id, $image_id]);
    $likeExists = $stmt->fetchColumn();

    if ($likeExists > 0) {
        echo json_encode(['error' => 'You have already liked this image.']);
        exit();
    }

    // Insert like into the database
    $stmt = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $image_id]);

    echo json_encode(['success' => 'Image liked successfully!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
