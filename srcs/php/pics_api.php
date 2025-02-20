<?php
// pics_api.php
require_once 'includes/db.php'; // Ensure your database connection is set up here
header('Content-Type: application/json');

// Initialize the PDO connection.
$pdo = getConnection2();

try {
    $stmt = $pdo->query("SELECT id, image_data, created_at FROM images ORDER BY id DESC");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert each row into an object with 'id' and 'src' properties
    $result = array_map(function($row) {
        $base64 = $row['image_data'];
        // If the data is returned as a resource, convert it to a string
        if (is_resource($base64)) {
            $base64 = stream_get_contents($base64);
        }
        
        // If the stored data already has the data URI prefix, use it directly
        if (strpos($base64, 'data:image') === 0) {
            return [
                'id' => $row['id'],
                'src' => $base64
            ];
        }
        
        // Otherwise, prepend the PNG data URI prefix
        return [
            'id' => $row['id'],
            'src' => 'data:image/png;base64,' . $base64
        ];
        }, $images);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
