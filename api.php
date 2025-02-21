<?php
header("Content-Type: application/json");
require "config.php";
require "auth.php";

$method = $_SERVER["REQUEST_METHOD"];
$request = explode("/", trim($_SERVER["REQUEST_URI"], "/"));

if ($request[0] !== "products") {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
    exit();
}

// Secure API (except GET all products)
if ($method !== "GET" || (isset($request[1]) && is_numeric($request[1]))) {
    authenticate();
}

switch ($method) {
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["name"], $data["price"])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }
        $stmt = $conn->prepare("INSERT INTO products (name, price, description) VALUES (:name, :price, :description)");
        $stmt->execute([
            ":name" => $data["name"],
            ":price" => $data["price"],
            ":description" => $data["description"] ?? null
        ]);
        echo json_encode(["message" => "Product created", "id" => $conn->lastInsertId()]);
        break;

    case "GET":
        if (isset($request[1]) && is_numeric($request[1])) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([":id" => $request[1]]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) {
                http_response_code(404);
                echo json_encode(["error" => "Product not found"]);
                exit();
            }
            echo json_encode($product);
        } else {
            $stmt = $conn->query("SELECT * FROM products");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case "PUT":
        if (!isset($request[1]) || !is_numeric($request[1])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID"]);
            exit();
        }
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data["name"], $data["price"])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }
        $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price, description = :description WHERE id = :id");
        $stmt->execute([
            ":id" => $request[1],
            ":name" => $data["name"],
            ":price" => $data["price"],
            ":description" => $data["description"] ?? null
        ]);
        echo json_encode(["message" => "Product updated"]);
        break;

    case "DELETE":
        if (!isset($request[1]) || !is_numeric($request[1])) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid ID"]);
            exit();
        }
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([":id" => $request[1]]);
        echo json_encode(["message" => "Product deleted"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}
?>