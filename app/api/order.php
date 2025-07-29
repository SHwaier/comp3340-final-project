<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
    // Handle GET Request - Retrieve All User Profiles
    $payload = authorize_request();

    $userId = $payload['user_id'] ?? null;
    if ($userId === null) {
        http_response_code(400);
        echo json_encode(["error" => "Missing user ID"]);
        exit;
    }
    try {

        $order_id = $_GET['id'] ?? null;
        // Check if the order_id is provided and is numeric, if so fetch only that order
        if (is_numeric($order_id)) {
            $stmt = $pdo->prepare("SELECT order_id, user_id, total_amount, status, order_date FROM orders WHERE order_id = :order_id AND user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':order_id', $order_id);
        } else { // If no order_id is provided, fetch all orders for the user
            $stmt = $pdo->prepare("SELECT order_id, user_id, total_amount, status, order_date FROM orders WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Check if the results are empty and return an empty array
        if (empty($results)) {
            echo json_encode([]);
            exit;
        }
        echo json_encode($results, JSON_PRETTY_PRINT);
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