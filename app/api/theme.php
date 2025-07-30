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


// gets the active theme id
if ($method === 'GET') {

    try {
        $stmt = $pdo->prepare("SELECT theme_id FROM current_theme");
        $stmt->execute();
        $themes = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($themes);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    // updating the active theme
} else if ($method === 'PUT') {
    // check if user is logged in and has admin privileges
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

    parse_str(file_get_contents("php://input"), $data);
    $theme_id = $data['theme_id'] ?? null;

    if (!$theme_id || $theme_id === null || (int) $theme_id < 1) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid theme id!"]);
        exit;
    }

    try {
        $delete = $pdo->prepare("UPDATE current_theme SET theme_id = :theme_id, set_at = CURRENT_TIMESTAMP WHERE id = 1;");
        $delete->execute(['theme_id' => $theme_id]);
        echo json_encode(["message" => "Theme updated successfully"]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}