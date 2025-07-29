<?php
require_once 'jwt_helpers.php';


function validate_jwt($jwt)
{
    $secret = getenv('JWT_SECRET');
    // split the token into different parts using the '.' separator
    $parts = explode('.', $jwt);
    // make sure if the token has 3 parts (header, payload, signature)
    if (count($parts) !== 3) {
        return false;
    }

    // decode the header and payload using base64url_decode function as well as the jwt secret
    list($header_encoded, $payload_encoded, $signature_encoded) = $parts;
    $signature_check = base64url_encode(hash_hmac('sha256', "$header_encoded.$payload_encoded", $secret, true));
    if (!hash_equals($signature_check, $signature_encoded)) {
        return false;
    }
    $payload = json_decode(base64url_decode($payload_encoded), true);

    // Check expiry, reject if expired
    if (isset($payload['exp']) && time() > $payload['exp']) {
        return false;
    }

    return $payload;
}