<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once './util.php';
require_once 'db.php';
require_once 'auth/authorize.php';


$method = $_SERVER['REQUEST_METHOD'];
// retrieve db connection
$pdo = getPDO();

if ($method === 'GET') {

    try {
        if ($method === 'GET') {
            // Supported query parameters
            $product_id = $_GET['id'] ?? null;          // Single product ID
            $ids = $_GET['ids'] ?? null;                // Comma-separated list of product IDs
            $latest = $_GET['latest'] ?? null;          // Fetch latest N products
            $all = isset($_GET['all']) && $_GET['all'] === 'true'; // Fetch all products

            try {
                // --- 1. Single Product by ID ---
                if (is_numeric($product_id)) {
                    // Fetch one product's base info
                    $stmt = $pdo->prepare("
                SELECT product_id, product_name, description, price, stock_quantity, image_url 
                FROM products 
                WHERE product_id = :id
            ");
                    $stmt->bindParam(':id', $product_id);
                    $stmt->execute();
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$product) {
                        echo json_encode([]);
                        exit;
                    }

                    // Fetch size variants for that product
                    $variantStmt = $pdo->prepare("
                SELECT variant_id, size, stock_quantity, addon_price 
                FROM product_variants 
                WHERE product_id = :id
            ");
                    $variantStmt->bindParam(':id', $product_id);
                    $variantStmt->execute();
                    $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode($product, JSON_PRETTY_PRINT);
                    exit;
                }

                // --- 2. Multiple Products by IDs ---
                elseif (!empty($ids)) {
                    // Convert string "1,2,3" to array [1,2,3]
                    $idArray = array_filter(array_map('intval', explode(',', $ids)));
                    if (empty($idArray)) {
                        echo json_encode([]);
                        exit;
                    }

                    // Prepare dynamic placeholders (?, ?, ?)
                    $placeholders = implode(',', array_fill(0, count($idArray), '?'));
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
                    $stmt->execute($idArray);
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Attach variants to each product
                    foreach ($products as &$product) {
                        $variantStmt = $pdo->prepare("
                    SELECT variant_id, size, stock_quantity, addon_price 
                    FROM product_variants 
                    WHERE product_id = ?
                ");
                        $variantStmt->execute([$product['product_id']]);
                        $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    echo json_encode($products, JSON_PRETTY_PRINT);
                    exit;
                }

                // --- 3. Latest N Products ---
                elseif (is_numeric($latest)) {
                    $limit = (int) $latest;

                    // Order by newest product_id (descending) and limit
                    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY product_id DESC LIMIT ?");
                    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
                    $stmt->execute();
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Attach variants to each product
                    foreach ($products as &$product) {
                        $variantStmt = $pdo->prepare("
                    SELECT variant_id, size, stock_quantity, addon_price 
                    FROM product_variants 
                    WHERE product_id = ?
                ");
                        $variantStmt->execute([$product['product_id']]);
                        $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    echo json_encode($products, JSON_PRETTY_PRINT);
                    exit;
                }

                // --- 4. All Products ---
                elseif ($all) {
                    // Fetch every product
                    $stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Attach variants to each product
                    foreach ($products as &$product) {
                        $variantStmt = $pdo->prepare("
                    SELECT variant_id, size, stock_quantity, addon_price 
                    FROM product_variants 
                    WHERE product_id = ?
                ");
                        $variantStmt->execute([$product['product_id']]);
                        $product['variants'] = $variantStmt->fetchAll(PDO::FETCH_ASSOC);
                    }

                    echo json_encode($products, JSON_PRETTY_PRINT);
                    exit;
                }

                // --- 5. No valid parameters ---
                else {
                    echo json_encode([]); // Return empty array
                    exit;
                }

            } catch (Exception $e) {
                // Catch any unexpected errors
                error_respond(500, $e->getMessage());
            }
        } else {
            // Fetch all products
            $stmt = $pdo->prepare("SELECT product_id, product_name, description, price, stock_quantity, image_url FROM products");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch all variants
            $variantStmt = $pdo->query("SELECT variant_id, product_id, size, addon_price FROM product_variants");
            $allVariants = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

            // Group variants by product_id
            $variantsByProduct = [];
            foreach ($allVariants as $variant) {
                $variantsByProduct[$variant['product_id']][] = $variant;
            }

            // Attach variants to corresponding products
            foreach ($products as &$product) {
                $product['variants'] = $variantsByProduct[$product['product_id']] ?? [];
            }

            echo json_encode($products, JSON_PRETTY_PRINT);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else if ($method === 'POST') { // Creating a new product
    // check if user is logged in and has admin privileges
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;
    if (!isset($userId)) {
        error_respond(401, "Please login to change the theme");
    }
    if ($payload["role"] !== 'Admin') {
        error_respond(403, "You do not have permission to change the theme");
    }

    $data = json_decode(file_get_contents("php://input"), true);


    if (empty($data['product_name']) || !isset($data['price']) || !is_numeric($data['price']) || empty($data['stock_quantity'])) {
        http_response_code(400);
        echo json_encode(["error" => "Product name and price are required"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (product_name, description, price, stock_quantity, category, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['product_name'],
            $data['description'] ?? '',
            $data['price'],
            $data['stock_quantity'] ?? 0,
            $data['category'] ?? null,
            $data['image_url'] ?? null
        ]);

        echo json_encode(["message" => "Product created", "product_id" => $pdo->lastInsertId()]);
        // After product INSERT
        $productId = $pdo->lastInsertId();

        if (!empty($data['variants']) && is_array($data['variants'])) {
            $variantStmt = $pdo->prepare("
        INSERT INTO product_variants (product_id, size, addon_price, stock_quantity)
        VALUES (?, ?, ?, ?)
    ");
            foreach ($data['variants'] as $variant) {
                $variantStmt->execute([
                    $productId,
                    $variant['size'],
                    $variant['addon_price'] ?? 0,
                    $variant['stock_quantity'] ?? 0
                ]);
            }
        }

    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }
} else if ($method === 'PUT') { //updating EXISTING product
    // check if user is logged in and has admin privileges
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;
    if (!isset($userId)) {
        error_respond(401, "Please login to change the theme");
    }
    if ($payload["role"] !== 'Admin') {
        error_respond(403, "You do not have permission to change the theme");
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $data = sanitizeInput($data);

    if (empty($data['product_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "product_id is required"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE products SET 
                product_name = ?, 
                description = ?, 
                price = ?, 
                stock_quantity = ?, 
                category = ?, 
                image_url = ?
            WHERE product_id = ?
        ");
        $stmt->execute([
            $data['product_name'] ?? '',
            $data['description'] ?? '',
            $data['price'] ?? 0,
            $data['stock_quantity'] ?? 0,
            $data['category'] ?? null,
            $data['image_url'] ?? null,
            $data['product_id']
        ]);
        // After product UPDATE
        if (!empty($data['variants']) && is_array($data['variants'])) {
            // Remove existing
            $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$data['product_id']]);

            // Insert new
            $variantStmt = $pdo->prepare("
        INSERT INTO product_variants (product_id, size, addon_price, stock_quantity)
        VALUES (?, ?, ?, ?)
    ");
            foreach ($data['variants'] as $variant) {
                $variantStmt->execute([
                    $data['product_id'],
                    $variant['size'],
                    $variant['addon_price'] ?? 0,
                    $variant['stock_quantity'] ?? 0
                ]);
            }
        }

        echo json_encode(["message" => "Product updated"]);
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }
} else if ($method === 'DELETE') {
    // check if user is logged in and has admin privileges
    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;
    if (!isset($userId)) {
        error_respond(401, "Please login to change the theme");
    }
    if ($payload["role"] !== 'Admin') {
        error_respond(403, "You do not have permission to change the theme");
    }

    parse_str(file_get_contents("php://input"), $data);

    if (empty($data['product_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "product_id is required"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$data['product_id']]);

        echo json_encode(["message" => "Product deleted"]);
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed. Use GET or POST."]);
}

?>