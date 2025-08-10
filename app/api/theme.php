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

// gets the active theme id
if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT theme_id FROM current_theme");
        $stmt->execute();
        $theme = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($theme);
    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }
    // updating the active theme
} elseif ($method === 'PUT') {
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
    $theme_id = $data['theme_id'] ?? null;

    if (!$theme_id || !isset($theme_id) || (int) $theme_id < 1) {
        error_respond(400, "Invalid theme id!");
    }

    try {
        $delete = $pdo->prepare("UPDATE current_theme SET theme_id = :theme_id, set_at = CURRENT_TIMESTAMP WHERE id = 1;");
        $delete->execute(['theme_id' => $theme_id]);
        echo json_encode(["message" => "Theme updated successfully"]);

    } catch (Exception $e) {
        error_respond(500, $e->getMessage());
    }
} elseif ($method === 'OPTIONS') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => "OK"]);
    exit;
} else {
    error_respond(405, "Method not allowed.");
}
