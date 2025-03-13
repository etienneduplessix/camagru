<?php
session_start();
header('Content-Type: application/json');

require_once 'includes/db.php';
require_once 'modif_img.php';

// Ensure no output before JSON response
ob_start(); // Start output buffering

$response = [];

// Check for a valid session
if (!isset($_SESSION['user']['id'])) {
    $response = ['status' => 'error', 'message' => 'Unauthorized'];
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user']['id'];
$raw_data = file_get_contents("php://input");

// Debug raw input
error_log("Raw input: " . $raw_data);

$data = json_decode($raw_data, true);
if (!$data) {
    error_log("JSON Decode Error: " . json_last_error_msg());
    $response = ['status' => 'error', 'message' => 'Invalid JSON data'];
    echo json_encode($response);
    exit();
}

// Validate required parameters
if (!isset($data['image']) || !isset($data['effect'])) {
    $response = ['status' => 'error', 'message' => 'Missing required parameters'];
    echo json_encode($response);
    exit();
}

$image_base64 = $data['image'];
$effect = $data['effect'];

try {
    $pdo = getConnection2();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Apply the effect
    try {
        $modified_image = overlayPngOnBase64($image_base64, $effect);
    } catch (Exception $e) {
        error_log("Image processing error: " . $e->getMessage());
        $response = ['status' => 'error', 'message' => $e->getMessage()];
        echo json_encode($response);
        exit();
    }

    // Remove Base64 header before storing
    if (strpos($modified_image, 'data:image') === 0) {
        $modified_image = substr($modified_image, strpos($modified_image, ',') + 1);
    }

    // Debugging logs
    error_log("Final Image Data: " . substr($modified_image, 0, 100)); // Limit for logs

    // Insert into the database
    $stmt = $pdo->prepare("INSERT INTO images (user_id, image_data, created_at) VALUES (:user_id, :image_data, NOW())");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':image_data', $modified_image, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $response = ['status' => 'success', 'message' => 'Image uploaded successfully'];
    } else {
        $response = ['status' => 'error', 'message' => 'Database error'];
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response = ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
}

// Ensure only JSON is returned
ob_end_clean();
echo json_encode($response);
exit();
?>
