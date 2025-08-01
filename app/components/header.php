<?php
require_once __DIR__ . '/../api/auth/getSession.php';
$user = getSession();
?>

<header id="desktop-header">
    <!-- toggle switch to open the mobile menu, onyl visible on phones and tablets -->
    <div class="mobile-header container flex flex-row flex-space-between" id="mobile-toggle">
        <button id="mobile-menu-toggle" aria-label="Toggle menu" style="background: none; border: none;">
            <img src="/assets/svg/menu.svg" alt="Menu Icon" width="30" height="30">
        </button>
        <?php include_once 'logo.php'; ?>
    </div>

    <!-- Sidebar Menu -->
    <div id="mobile-sidebar" class="mobile-sidebar">
        <button id="mobile-menu-close" class="close-button" aria-label="Close menu">✕</button>
        <nav class="mobile-nav-links">
            <ul class="flex flex-col" style="gap: 1.5rem;">
                <li><a href="/">Home</a></li>
                <li><a href="/about">About</a></li>
                <li><a href="/shop">Shop</a></li>
                <li><a href="/contact">Contact</a></li>
                <?php if ($user !== null) { ?>
                    <li><a href="/cart">Cart</a></li>
                    <li><a href="/profile">Profile</a></li>
                    <li><a href="/logout">Logout</a></li>
                <?php } else { ?>
                    <li><a href="/login">Login</a></li>
                <?php } ?>

            </ul>
        </nav>
    </div>

    <!-- Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>


    <!-- full desktop only menu -->
    <div class="flex flex-row flex-space-between container" id="desktop-only">
        <div class="flex flex-col">
            <a href="/">
                <img class="logo" src="/assets/logo/luxe-logo-light.png" alt="Luxe Logo - Transparent" width="100"
                    height="100">
            </a>
        </div>
        <nav class="flex flex-col">
            <ul class="flex flex-center">
                <li><a class="hover-underline-animation left" href="/">Home</a></li>
                <li><a class="hover-underline-animation left" href="/about">About</a></li>
                <li><a class="hover-underline-animation left" href="/shop">Shop</a></li>
                <li><a class="hover-underline-animation left" href="/contact">Contact</a></li>
            </ul>
        </nav>

        <!-- show cart and user profile icon only if the user is signed in, otherwise show a login text, handled in app.js -->

        <?php if ($user !== null) { ?>
            <div class="flex flex-row flex-space-between" style="gap: 1rem;">
                <a class="hover-underline-animation left" href="/cart">
                    <img class="cart-icon icon" src="/assets/svg/cart.svg" alt="Cart Icon" width="30" height="30">
                </a>
                <div class="user-menu-wrapper">
                    <a class="user-menu-toggle" href="#">
                        <img class="user-icon icon" src="/assets/svg/user.svg" alt="User Icon" width="30" height="30">
                    </a>
                    <ul class="user-menu-dropdown rounded">
                        <li><a class="hover-underline-animation left" href="/profile">Profile</a></li>
                        <li><a class="hover-underline-animation left" href="/orders">Orders</a></li>
                        <li><a class="hover-underline-animation left" href="/logout">Logout</a></li>
                    </ul>
                </div>


            </div>
        <?php } else { ?>
            <nav>
                <a class="hover-underline-animation left" href="/login">Login</a>
            </nav>
        <?php } ?>

    </div>
</header>