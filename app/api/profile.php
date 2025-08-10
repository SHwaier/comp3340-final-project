<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once './util.php';
require_once 'auth/authorize.php';
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
// retrieve db connection
$pdo = getPDO();

if ($method === 'GET') {

    $payload = authorize_request();

    $userId = $payload['user_id'] ?? null;
    if (!isset($userId)) {
        error_respond(400, "Missing user ID");
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

} elseif ($method === 'OPTIONS') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => "OK"]);
    exit;
} else {
    error_respond(405, "Method not allowed.");
}
?>