<?php


function getPDO(): PDO
{
    $db_host = getenv('DATABASE_HOST');
    $db_user = getenv('DATABASE_USER');
    $db_pass = getenv('DATABASE_PASS');
    $db_name = getenv('DATABASE_NAME');

    static $pdo;
    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}