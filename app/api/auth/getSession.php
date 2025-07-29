<?php
require_once __DIR__ . '/validate_jwt.php';

function getSession()
{
    if (!isset($_COOKIE['token']))
        return null;

    $jwt = $_COOKIE['token'];
    $payload = validate_jwt($jwt);

    return $payload ?: null;
}
