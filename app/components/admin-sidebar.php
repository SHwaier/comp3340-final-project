<?php
require_once __DIR__ . '/../api/auth/getSession.php';
$user = getSession();

// Block access unless user is admin
if (!$user || $user['role'] !== 'Admin') {
    http_response_code(403);
    exit('Access denied.');
}
?>

<!-- Sidebar Toggle Button (Visible on Small Screens) -->
<button id="admin-sidebar-toggle" aria-label="Toggle admin menu"
    style="position: fixed; top: 1rem; left: 1rem; z-index: 1001; background: none; border: none;">
    <img src="/assets/svg/menu.svg" alt="Menu Icon" width="30" height="30">
</button>

<!-- Sidebar Overlay (for mobile) -->
<div id="admin-sidebar-overlay" class="sidebar-overlay"></div>

<!-- Admin Sidebar -->
<div id="admin-sidebar" class="admin-sidebar" style="
    width: 18rem;
    height: 100vh;
    background-color: var(--card-bg);
    border-right: 1px solid var(--border-color);
    position: fixed;
    top: 0;
    left: -250px;
    transition: left 0.3s ease;
    display: flex;
    flex-direction: column;
    padding: 1rem;
    gap: 2rem;
    z-index: 1002;
">
    <!-- Close Button (for mobile) -->
    <button id="admin-sidebar-close" aria-label="Close admin menu"
        style="align-self: flex-end; background: none; border: none; font-size: 1.5rem;">✕</button>

    <!-- Logo + Title -->
    <div class="flex flex-col items-center">
        <a href="/admin">
            <img src="/assets/logo/luxe-logo-light.png" alt="Luxe Logo" width="100" height="100" />
        </a>
        <h2 style="margin-top: 1rem; font-size: 1.2rem; color: var(--text-color);">Admin Panel</h2>
    </div>

    <!-- Nav Links -->
    <nav class="flex flex-col" style="gap: 1rem;">
        <a href="/admin" class="hover-underline-animation left">Overview</a>
        <a href="/admin/products" class="hover-underline-animation left">Manage Products</a>
        <a href="/admin/orders" class="hover-underline-animation left">Orders</a>
        <a href="/admin/users" class="hover-underline-animation left">Users</a>
        <a href="/admin/settings" class="hover-underline-animation left">Settings</a>
    </nav>

    <!-- Profile & Logout -->
    <div style="margin-top: auto;">
        <hr style="border-color: var(--border-color); margin-bottom: 1rem;">
        <a href="/profile" class="hover-underline-animation left">Profile</a><br>
        <a href="/logout" class="hover-underline-animation left">Logout</a>
    </div>
</div>

<!-- JS to Toggle Sidebar -->
<script>
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    const toggleBtn = document.getElementById('admin-sidebar-toggle');
    const closeBtn = document.getElementById('admin-sidebar-close');

    toggleBtn.addEventListener('click', () => {
        sidebar.style.left = '0';
        overlay.classList.add('show');
    });

    closeBtn.addEventListener('click', () => {
        sidebar.style.left = '-250px';
        overlay.classList.remove('show');
    });

    overlay.addEventListener('click', () => {
        sidebar.style.left = '-250px';
        overlay.classList.remove('show');
    });
</script>

<!-- Optional Overlay Styling -->
<style>
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    @media (min-width: 1024px) {
        #admin-sidebar {
            left: 0 !important;
        }

        #admin-sidebar-toggle,
        #admin-sidebar-close,
        #admin-sidebar-overlay {
            display: none;
        }
    }
</style>