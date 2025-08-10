<?php
require_once 'generate_jwt.php';
require_once '../util.php';
require_once '../db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    // establish database connection
    $pdo = getPDO();

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    // $data = sanitizeInput($data);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(["error" => "Username and password are required"]);
        exit;
    }

    try {
        // Find user by username
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM user_profiles WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user found and password matches
        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                "user_id" => $user['user_id'],
                "role" => $user['role'],
                "username" => $user['username'],
                "exp" => time() + (60 * 60) // 1 hour expiry
            ];
            $jwt = generate_jwt($payload);
            echo json_encode(["token" => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Invalid credentials"]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Login failed", "details" => $e->getMessage()]);
    }
} elseif ($method === 'OPTIONS') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(["status" => "OK"]);
    exit;
} else {
    error_respond(405, "Method not allowed");
}