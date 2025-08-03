<?php
require_once __DIR__ . '/../../api/auth/getSession.php';
$user = getSession();

// Block access unless user is admin
if (!$user || $user['role'] !== 'Admin') {
    http_response_code(403);
    exit('Access denied.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../components/metas.php'; ?>
    <title>Admin Products | Luxe</title>
    <meta name="description" content="View and manage Luxe products.">
    <link rel="stylesheet" href="/styles/admin-style.css">
</head>

<body>
    <?php include '../../components/admin-sidebar.php'; ?>
    <main class="admin-main">
        <div style="padding: 2rem;">
            <h2>Manage Products</h2>
            <hr><br>
            <div class="horizontal-fluid-row flex-space-between" style="align-items: center; margin-bottom: 2rem;">
                <p>Here you can see all of your products</p>
                <a href="/admin/products/create" class="button">Create New Product</a>
            </div>
            <div id="product-grid"></div>
        </div>
    </main>

    <?php include_once '../../components/scripts.php'; ?>
    <script src="/scripts/admin-products.js"></script>
</body>

</html>