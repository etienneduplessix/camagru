<?php
// get_comment.php
require_once 'includes/db.php'; // Ensure your database connection is set up here
header('Content-Type: application/json');

// Initialize the PDO connection.
$pdo = getConnection2();

try {
    // Fetch all comments with their associated image_id
    $stmt = $pdo->query("SELECT image_id, id AS comment_id, user_id, comment_text, created_at 
                         FROM comments ORDER BY created_at ASC");
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group comments by image_id
    $groupedComments = [];
    foreach ($comments as $comment) {
        $imageId = $comment['image_id'];
        if (!isset($groupedComments[$imageId])) {
            $groupedComments[$imageId] = [];
        }
        $groupedComments[$imageId][] = [
            'comment_id' => $comment['comment_id'],
            'user_id' => $comment['user_id'],
            'comment_text' => $comment['comment_text'],
            'created_at' => $comment['created_at']
        ];
    }

    echo json_encode($groupedComments);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
