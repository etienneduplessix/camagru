<?php
session_start();
header('Content-Type: application/json');

require_once 'includes/db.php';
require_once 'modif_img.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user']['id'];
$raw_data = file_get_contents("php://input");
error_log($raw_data);

$data = json_decode($raw_data, true);
if (!$data) {
    error_log("Error decoding JSON");
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit();
} else {
    error_log(print_r($data, true));
}

if (!$data || !isset($data['image']) || !isset($data['effect'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit();
}

try {
    $pdo = getConnection2();
    
    $image_base64 = $data['image'];
    $effect = $data['effect'];
    
    try {
        $modified_image = overlayPngOnBase64($image_base64, $effect);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
    
    if (strpos($modified_image, 'data:image') === 0) {
        $modified_image = substr($modified_image, strpos($modified_image, ',') + 1);
    }

    $stmt = $pdo->prepare("INSERT INTO images (user_id, image_data, created_at) VALUES (:user_id, :image_data, NOW())");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':image_data', $modified_image, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>