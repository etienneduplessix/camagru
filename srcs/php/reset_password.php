<?php
require_once('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    if (strlen($newPassword) < 6) {
        echo json_encode(["error" => "Password must be at least 6 characters."]);
        exit;
    }

    $db = getConnection();
    $tokenEscaped = pg_escape_string($db, $token);

    // Validate token
    $query = "SELECT id FROM users WHERE password_reset_token = '$tokenEscaped' AND token_expiry > NOW()";
    $result = pg_query($db, $query);

    if (!$result || pg_num_rows($result) === 0) {
        echo json_encode(["error" => "Invalid or expired token."]);
        pg_close($db);
        exit;
    }

    $userId = pg_fetch_result($result, 0, 'id');
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password and remove token
    $updateQuery = "UPDATE users SET password_hash = $1, password_reset_token = NULL, token_expiry = NULL WHERE id = $2";
    $stmt = pg_prepare($db, "update_password", $updateQuery);
    pg_execute($db, "update_password", [$hashedPassword, $userId]);

    echo json_encode(["message" => "Password reset successful."]);
    pg_close($db);
}
?>
