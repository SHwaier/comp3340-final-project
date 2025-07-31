<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once './util.php';

$db_host = getenv('DATABASE_HOST');
$db_user = getenv('DATABASE_USER');
$db_pass = getenv('DATABASE_PASS');
$db_name = getenv('DATABASE_NAME');

try {
    // Establish a connection using PDO (PHP Data Objects)
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    // Set error reporting to Exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, output the error message and exit
    die("Connection failed: " . $e->getMessage());
}


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $product_id = $_GET['id'] ?? null;

    try {
        if (is_numeric($product_id)) {
            // Fetch single product
            $stmt = $pdo->prepare("SELECT product_id, product_name, description, price, stock_quantity, image_url FROM products WHERE product_id = :id");
            $stmt->bindParam(':id', $product_id);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                echo json_encode([]);
                exit;
            }

            // Fetch size variants for the product
            $variantStmt = $pdo->prepare("SELECT variant_id, size, stock_quantity, addon_price FROM product_variants WHERE product_id = :id");
            $variantStmt->bindParam(':id', $product_id);
            $variantStmt->execute();
            $variants = $variantStmt->fetchAll(PDO::FETCH_ASSOC);

            $product['variants'] = $variants;

            echo json_encode($product, JSON_PRETTY_PRINT);
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