<?php
header("Content-Type: application/json");

require_once '../util.php';
require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {

    // establish database connection
    $pdo = getPDO();

    // Get and sanitize JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    // Validate required fields
    $emailValidator = new EmailValidator();
    $passValidator = new PasswordValidator();

    // Validate required fields
    if (!isset($username)) {
        http_response_code(400);
        echo json_encode(["error" => "Username is required"]);
        exit;
    }

    if (!$emailValidator->validate($email)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid email format", "details" => $emailValidator->getErrors()]);
        exit;
    }

    if (!$passValidator->validate($password)) {
        http_response_code(400);
        echo json_encode(["error" => "Password does not meet requirements", "details" => $passValidator->getErrors()]);
        exit;
    }


    try {
        // Check username exists
        $stmt = $pdo->prepare("SELECT 1 FROM user_profiles WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch())
            error_respond(409, "Username already exists");

        // Check email exists
        $stmt = $pdo->prepare("SELECT 1 FROM user_profiles WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch())
            error_respond(409, "Email already exists");

        // for peroformance optimization I check for existing user first then sanitize after this way fields aren't altered then checked 
        $password = $passValidator->sanitize($password);
        $email = $emailValidator->sanitize($email);
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO user_profiles (username, email, password) 
                           VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        echo json_encode(["message" => "User registered successfully"]);
        http_response_code(201);

    } catch (Exception $e) {
        echo json_encode(["error" => "Registration failed", "details" => $e->getMessage()]);
        http_response_code(500);
    }
} else {
    echo json_encode(["error" => "Method not allowed"]);
    http_response_code(405);
}