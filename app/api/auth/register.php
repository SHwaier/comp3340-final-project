<?php
header("Content-Type: application/json");

require_once 'generate_jwt.php';

// Database connection
$db_host = getenv('DATABASE_HOST');
$db_user = getenv('DATABASE_USER');
$db_pass = getenv('DATABASE_PASS');
$db_name = getenv('DATABASE_NAME');

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get and sanitize JSON input
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validate required fields
if (empty($username) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["error" => "Username, email, and password are required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email format"]);
    exit;
}

try {
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_profiles WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        http_response_code(409);
        echo json_encode(["error" => "Username or email already exists"]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO user_profiles (username, email, password) 
                           VALUES (:username, :email, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();

    http_response_code(201);
    echo json_encode(["message" => "User registered successfully"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Registration failed", "details" => $e->getMessage()]);
}
