<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
header('Content-Type: application/json'); // Ensure JSON response

$debug_mode = true; // Set to false in production

if (!$debug_mode && !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Use PDO connection with getConnection2()
    $pdo = getConnection2();
    
    // Get user_id from session (or set a default for debugging)
    $user_id =($_SESSION['user']['id']);
    // Always filter by user_id - this is key to your requirement
    $query = "SELECT id, user_id, image_data, created_at FROM images WHERE user_id = :user_id ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process images
    $result = array_map(function($row) {
        $base64 = $row['image_data'];

        // Handle resource type
        if (is_resource($base64)) {
            $base64 = stream_get_contents($base64);
        }

        // Check if already has data URI prefix
        if (is_string($base64) && strpos($base64, 'data:image') === 0) {
            return [
                'id' => $row['id'],
                'user_id' => $row['user_id'],
                'src' => $base64
            ];
        }

        // Add data URI prefix
        return [
            'id' => $row['id'],
            'user_id' => $row['user_id'],
            'src' => 'data:image/png;base64,' . $base64
        ];
    }, $images);
    
    echo json_encode($result);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
