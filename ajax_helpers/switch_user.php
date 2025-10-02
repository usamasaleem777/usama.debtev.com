<?php
require_once '../functions.php';

header('Content-Type: application/json');

if (empty($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

$email = $_POST['email'];

// Fetch user by email
$user = DB::queryFirstRow("SELECT * FROM users WHERE email = %s", $email);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$postData = http_build_query([
    'login' => $user['email'],
    'password' => $user['password']
]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://localhost/craftHiring/ajax_helpers/ajax_check_login.php',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true, // Include headers in output
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Curl error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

curl_close($ch);

// Parse Set-Cookie headers
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);

if (!empty($matches[1])) {
    foreach ($matches[1] as $cookie) {
        // Parse cookie name and value
        list($name, $value) = explode('=', $cookie, 2);

        // Set cookie in client with default params (adjust as needed)
        setcookie($name, $value, [
            'path' => '/',
            'httponly' => true,
            'secure' => false, // Set true if using HTTPS
            'samesite' => 'Lax'
        ]);
    }
}

$body = trim($body);

if ($body === '1') {
    echo json_encode([
        'success' => true,
        'message' => 'User authenticated successfully',
        'role' => $user['role'] ?? 'user'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication failed',
        'raw_response' => $body
    ]);
}
exit;
