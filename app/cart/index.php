<?php
?><!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>Cart | Luxe</title>
    <meta name="description"
        content="View and manage items in your cart before checkout at Luxe. Stylish jackets, hoodies, and more await.">
</head>

<body>
    <?php include_once '../components/header.php'; ?>

    <main class="container">
        <h1 style="margin-bottom: 2rem;">Your Cart</h1>

        <div id="cart-items"></div>

        <div id="cart-summary" style="margin-top: 2rem; text-align: right;"></div>
    </main>

    <?php include_once '../components/footer.php'; ?>
    <?php include_once '../components/scripts.php'; ?>

    <script src="/scripts/cart.js" defer></script>
    
</body>

</html>