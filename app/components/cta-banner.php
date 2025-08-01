<?php
require_once __DIR__ . '/../api/auth/getSession.php';
$user = getSession();

if (!isset($user)) {
    ?>
    <div class="cta-banner">

        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Sign up for exclusive drops & discounts</h3>
        <p style="margin-bottom: 1rem;">Be the first to know when new topwear hits our shop.</p>
        <a href="/register" class="button">
            Join Now
        </a>
    </div>
    <?php
} else {
    ?>
    <div class="cta-banner">

        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Welcome back, <?= htmlspecialchars($user['username']) ?>!</h3>
        <p style="margin-bottom: 1rem;">Check out our latest arrivals and exclusive offers.</p>
        <a href="/shop" class="button">
            Shop Now
        </a>
    </div>
    <?php
}