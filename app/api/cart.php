<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once './util.php';
require_once 'auth/authorize.php';
require_once 'db.php';


$method = $_SERVER['REQUEST_METHOD'];


// retrieve db connection
$pdo = getPDO();
// POST: Add item to cart
if ($method === 'POST') {
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;

    if (!isset($userId)) {
        error_respond(401, "Unauthorized");
    }
    $data = json_decode(file_get_contents("php://input"), true);
    $variantId = $data['variant_id'] ?? null;
    $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 1;

    if (!$variantId || $quantity <= 0) {
        error_respond(400, "Invalid variant ID or quantity");
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
        http_response_code(201);
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }

} elseif ($method === 'PUT') {
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;

    if (!isset($userId)) {
        error_respond(401, "Unauthorized");
    }
    $data = json_decode(file_get_contents("php://input"), true);
    $variantId = $data['variant_id'] ?? null;
    $quantity = $data['quantity'] ?? null;

    if (!$variantId || $quantity === null || (int) $quantity < 0) {
        error_respond(400, "Invalid variant ID or quantity");
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
            http_response_code(200);
        }
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }

} elseif ($method === 'DELETE') {
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;

    if (!isset($userId)) {
        error_respond(401, "Unauthorized");
    }
    $data = json_decode(file_get_contents("php://input"), true);
    $variantId = $data['variant_id'] ?? null;

    if (!$variantId)
        error_respond(400, "Missing variant ID");


    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND variant_id = :variant_id");
    $stmt->bindValue(':user_id', $userId);
    $stmt->bindValue(':variant_id', $variantId);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item)
        error_respond(404, "Item not found in cart");

    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id  AND variant_id = :variant_id");
        $stmt->execute([
            'user_id' => $userId,
            'variant_id' => $variantId
        ]);
        echo json_encode(["message" => "Item removed from cart"]);
        http_response_code(200);
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }
} elseif ($method === 'GET') {
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;

    if (!isset($userId)) {
        error_respond(401, "Unauthorized");
    }
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.cart_id,
                c.variant_id,
                c.quantity,
                p.product_name,
                p.image_url,
                p.product_id,
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

        echo json_encode($cartItems);
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }

} elseif ($method === 'OPTIONS') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => "OK"]);
    exit;
} else {
    error_respond(405, "Method Not Allowed.");
}