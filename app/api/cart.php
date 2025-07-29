<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once './util.php';
require_once 'auth/authorize.php';

$db_host = getenv('DATABASE_HOST');
$db_user = getenv('DATABASE_USER');
$db_pass = getenv('DATABASE_PASS');
$db_name = getenv('DATABASE_NAME');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$method = $_SERVER['REQUEST_METHOD'];

$payload = authorize_request();
$userId = $payload['user_id'] ?? null;

if ($userId === null) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($method === 'POST') {
    parse_str(file_get_contents("php://input"), $data);
    $productId = $data['product_id'] ?? null;
    $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 1;

    if (!$productId || $quantity <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid product ID or quantity"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
            $update->execute([
                'quantity' => $newQty,
                'user_id' => $userId,
                'product_id' => $productId
            ]);
        } else {
            $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $insert->execute([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        echo json_encode(["message" => "Product added to cart"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} else if ($method === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $productId = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? null;

    if (!$productId || $quantity === null || (int) $quantity < 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid product ID or quantity"]);
        exit;
    }

    try {
        if ((int) $quantity === 0) {
            $delete = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $delete->execute(['user_id' => $userId, 'product_id' => $productId]);
            echo json_encode(["message" => "Product removed from cart"]);
        } else {
            $update = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
            $update->execute([
                'quantity' => $quantity,
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            echo json_encode(["message" => "Cart updated"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} else if ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $productId = $data['product_id'] ?? null;

    if (!$productId) {
        http_response_code(400);
        echo json_encode(["error" => "Missing product ID"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
        echo json_encode(["message" => "Product removed from cart"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} else if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare("
            SELECT c.cart_id, c.product_id, c.quantity, p.product_name, p.price, p.image_url
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($cartItems, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
}
