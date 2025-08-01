<?php
require_once 'db.php';
function getCurrentThemeId(): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT theme_id FROM current_theme");
    $stmt->execute();
    $theme = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) $theme['theme_id'];
}