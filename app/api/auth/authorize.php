<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'validate_jwt.php';

/**
 * Authorizes a request by validating the JWT token provided in the Authorization header.
 */
function authorize_request()
{

    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authorization header missing']);
        exit;
    }

    $auth_header = $headers['Authorization'];
    if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        $jwt = $matches[1];
        $payload = validate_jwt($jwt);
        if ($payload === false) {
            http_response_code(401);
            exit;
        }
        return $payload;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid authorization header']);
        exit;
    }
}