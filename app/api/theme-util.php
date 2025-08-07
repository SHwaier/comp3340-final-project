<?php
/**
 * Retrieves the current theme ID from the database.
 * @return int The ID of the current theme.
 */
require_once 'db.php';
function getCurrentThemeId(): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT theme_id FROM current_theme");
    $stmt->execute();
    $theme = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int) $theme['theme_id'];
}