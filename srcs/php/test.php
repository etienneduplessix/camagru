<?php


$data = [
    'email' => 'testuser@example.com',
    'password' => 'StrongP@ssw0rd123',
    'confirm_password' => 'StrongP@ssw0rd123'
];

$options = [
    'http' => [
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($apiUrl, false, $context);

if ($result === FALSE) {
    echo "Error: Registration test failed!\n";
} else {
    echo "Registration test response:\n";
    echo $result . "\n";
}

?>