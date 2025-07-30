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
    echo json_encode(["error" => "Please login to change the theme"]);
    exit;
}
if ($payload["role"] !== 'Admin') {
    http_response_code(403);
    echo json_encode(["error" => "You do not have permission to change the theme"]);
    exit;
}
// gets the active theme id
if ($method === 'GET') {
    // simply return the ID and name of all available themes
    try {
        $stmt = $pdo->prepare("SELECT theme_id, theme_name FROM themes");
        $stmt->execute();
        $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($themes);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}