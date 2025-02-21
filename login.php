<?php
require "auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

// Dummy user authentication
$data = json_decode(file_get_contents("php://input"), true);
if ($data["username"] === "user1" && $data["password"] === "pass1") {
    echo json_encode(["token" => generateJWT(1)]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}
?>