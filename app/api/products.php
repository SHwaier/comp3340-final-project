<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once './util.php';
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
// retrieve db connection
$pdo = getPDO();

if ($method === 'GET') {
    $product_id = $_GET['id'] ?? null;

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
} else if ($method === 'POST') {
    // TODO: Check authentication and authorization, only admin can add new products

    // Handle POST Request - Add New User Profile
    $data = json_decode(file_get_contents("php://input"), true);
    $data = sanitizeInput($data);
    // don't allow empty requests 
    if (empty($data)) {
        http_response_code(400);
        echo json_encode(["error" => "Request body is empty"]);
        exit;
    }


} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed. Use GET or POST."]);
}

?>