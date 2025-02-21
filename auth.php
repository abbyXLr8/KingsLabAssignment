<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "blahblahblah";

// Generate Token
function generateJWT($user_id) {
    global $secret_key;
    $payload = [
        "iss" => "localhost",
        "aud" => "localhost",
        "iat" => time(),
        "exp" => time() + (60 * 60),
        "user_id" => $user_id
    ];
    return JWT::encode($payload, $secret_key, 'HS256');
}

// Validate Token
function validateJWT($jwt) {
    global $secret_key;
    try {
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}

// Middleware to secure endpoints
function authenticate() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit();
    }
    $token = str_replace("Bearer ", "", $headers['Authorization']);
    if (!validateJWT($token)) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid Token"]);
        exit();
    }
}
?>