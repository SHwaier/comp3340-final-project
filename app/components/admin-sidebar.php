<?php
require_once __DIR__ . '/../api/auth/getSession.php';
$user = getSession();

// Block access unless user is admin
if (!$user || $user['role'] !== 'Admin') {
    http_response_code(403);
    exit('Access denied.');
}
?>

<!-- Admin Sidebar -->
<div id="admin-sidebar">
    <!-- Logo + Title -->
    <div class="flex flex-col items-center">
        <div class="container">
            <?php include_once 'logo.php'; ?>

        </div>
        <h2 style="margin-top: 1rem; font-size: 1.2rem; color: var(--text-color);">Admin Panel</h2>
    </div>

    <!-- Nav Links -->
    <nav class="flex flex-col" style="gap: 1rem;">
        <a href="/admin" class="hover-underline-animation left">Overview</a>
        <a href="/admin/products" class="hover-underline-animation left">Manage Products</a>
        <a href="/admin/wiki" class="hover-underline-animation left">Wiki</a>
        <a href="/admin/settings" class="hover-underline-animation left">Settings</a>
    </nav>

    <!-- Profile & Logout -->
    <div style="margin-top: auto;">
        <hr style="border-color: var(--border-color); margin-bottom: 1rem;">
        <a href="/logout" class="hover-underline-animation left">Logout</a>
    </div>
</div>