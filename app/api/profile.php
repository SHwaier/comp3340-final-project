<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once './util.php';
require_once 'auth/authorize.php';

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

    $payload = authorize_request();

    $userId = $payload['user_id'] ?? null;
    if ($userId === null) {
        http_response_code(400);
        echo json_encode(["error" => "Missing user ID"]);
        exit;
    }
    try {
        // 
        $stmt = $pdo->prepare("SELECT username,first_name,last_name FROM user_profiles WHERE user_id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);



        if (empty($results)) {
            echo json_encode([]);
            exit;
        }
        echo json_encode($results, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

    if (empty($results)) {
        http_response_code(404);
        echo json_encode(["error" => "User profile not found"]);
        exit;
    }

} //TODO: maybe switch to PUT method for updating user profile, and POST for creating new users would be handled inside the register.php file 
else if ($method === 'PUT') {

} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed. Use GET or POST."]);
}

?>