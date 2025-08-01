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
// adding products to cart
// POST: Add item to cart
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $variantId = $data['variant_id'] ?? null;
    $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 1;

    if (!$variantId || $quantity <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid variant ID or quantity"]);
        exit;
    }

    try {
        // Get product_id from variant


        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = :user_id AND variant_id = :variant_id");
        $stmt->execute(['user_id' => $userId, 'variant_id' => $variantId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $update = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND variant_id = :variant_id");
            $update->execute([
                'quantity' => $newQty,
                'user_id' => $userId,
                'variant_id' => $variantId
            ]);
        } else {
            $insert = $pdo->prepare("INSERT INTO cart (user_id, variant_id, quantity) VALUES (:user_id, :variant_id, :quantity)");
            $insert->execute([
                'user_id' => $userId,
                'variant_id' => $variantId,
                'quantity' => $quantity
            ]);
        }

        echo json_encode(["message" => "Product added to cart"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $variantId = $data['variant_id'] ?? null;
    $quantity = $data['quantity'] ?? null;

    if (!$variantId || $quantity === null || (int) $quantity < 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid variant ID or quantity"]);
        exit;
    }

    try {
        if ((int) $quantity === 0) {
            $delete = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND variant_id = :variant_id");
            $delete->execute(['user_id' => $userId, 'variant_id' => $variantId]);
            echo json_encode(["message" => "Item removed from cart"]);
        } else {
            $update = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND variant_id = :variant_id");
            $update->execute([
                'quantity' => $quantity,
                'user_id' => $userId,
                'variant_id' => $variantId
            ]);
            echo json_encode(["message" => "Cart updated"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $variantId = $data['variant_id'] ?? null;

    if (!$variantId) {
        http_response_code(400);
        echo json_encode(["error" => "Missing variant ID"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND variant_id = :variant_id");
    $stmt->execute([
        'user_id' => $userId,
        'variant_id' => $variantId
    ]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) {
        http_response_code(404);
        echo json_encode(["error" => "Item not found in cart"]);
        exit;
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id  AND variant_id = :variant_id");
        $stmt->execute([
            'user_id' => $userId,
            'variant_id' => $variantId
        ]);
        echo json_encode(["message" => "Item removed from cart"]);
        http_response_code(200);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} elseif ($method === 'GET') {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.cart_id,
                c.variant_id,
                c.quantity,
                p.product_name,
                p.image_url,
                p.price AS base_price,
                v.size,
                v.addon_price,
                (p.price + v.addon_price) AS final_price
            FROM cart c
            JOIN product_variants v ON c.variant_id = v.variant_id
            JOIN products p ON v.product_id = p.product_id
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