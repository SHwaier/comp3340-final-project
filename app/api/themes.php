<?php
// access is restriced to admin only as to not expose available site theemes information
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once './util.php';
require_once 'auth/authorize.php';
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];


if ($method === 'GET') {

    $payload = authorize_request();
    $userId = $payload['user_id'] ?? null;
    if (!isset($userId)) {
        error_respond(401, "Please login to view themes");
    }
    if ($payload["role"] !== 'Admin') {
        error_respond(403, "You do not have permission to change the theme");
    }
    // retrieve db connection
    $pdo = getPDO();


    // simply return the ID and name of all available themes
    try {
        $stmt = $pdo->prepare("SELECT theme_id, theme_name FROM themes");
        $stmt->execute();
        $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($themes);
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