<?php
// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once 'includes/db.php'; // Ensure this file contains your database connection setup
session_start();

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
$comment = isset($data['comment']) ? trim($data['comment']) : '';

if (empty($comment)) {
    echo json_encode(['error' => 'Comment cannot be empty.']);
    exit();
}

// Initialize database connection
$pdo = getConnection2();

try {
    // Check how many comments the user has made on this image
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ? AND image_id = ?");
    $stmt->execute([$user_id, $image_id]);
    $commentCount = $stmt->fetchColumn();

    // Enforce a max of 3 comments per user per image
    if ($commentCount >= 3) {
        echo json_encode(['error' => 'You have already reached the maximum of 3 comments for this image.']);
        exit();
    }

    // Insert the new comment into the database
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, image_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $image_id, $comment]);

    echo json_encode(['success' => 'Comment added successfully.']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
