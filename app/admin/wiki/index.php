<?php
require_once __DIR__ . '/../../api/auth/getSession.php';
$user = getSession();
if (!$user || ($user['role'] ?? null) !== 'Admin') {
    http_response_code(403);
    exit; // avoid printing text into a 403 page
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../components/metas.php'; ?>
    <title>Admin Wiki | Luxe</title>
    <meta name="description" content="Learn how to manage products, themes, and more on Luxe Admin.">
    <link rel="stylesheet" href="/styles/admin-style.css">
    <style>
        /* page-specific */
        .wiki-section {
            margin-bottom: 3rem;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
            margin-top: 1rem;
            background: var(--card-bg);
        }

        .video-wrapper video,
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
        }
    </style>
</head>

<body>
    <?php include '../../components/admin-sidebar.php'; ?>

    <main class="admin-main" role="main">
        <div class="container">
            <h2 class="headline" style="margin-bottom:1rem">📘 Admin Wiki</h2>
            <p class="muted" id="wiki-intro" style="margin-bottom:2rem">
                Watch a short walkthrough on how to manage your Luxe store through the admin dashboard.
            </p>

            <section class="wiki-section" aria-labelledby="create-title">
                <h3 id="create-title">🛍️ Create a New Product</h3>
                <p id="create-desc">This video shows you how to add a new product with size variants and price
                    adjustments.</p>
                <div class="video-wrapper">
                    <video controls preload="metadata" aria-describedby="create-desc"
                        title="Create a new product tutorial">
                        <source src="./create.mp4" type="video/mp4">
                        Your browser does not support embedded videos.
                    </video>
                </div>
            </section>

            <section class="wiki-section" aria-labelledby="edit-title">
                <h3 id="edit-title">✏️ Edit an Existing Product</h3>
                <p id="edit-desc">Learn how to update product info like price, stock, and variants using the edit page.
                </p>
                <div class="video-wrapper">
                    <video controls preload="metadata" aria-describedby="edit-desc" title="Edit product tutorial">
                        <source src="./edit.mp4" type="video/mp4">
                        Your browser does not support embedded videos.
                    </video>
                </div>
            </section>

            <section class="wiki-section" aria-labelledby="delete-title">
                <h3 id="delete-title">🗑️ Delete a Product</h3>
                <p id="delete-desc">This demo explains how to safely remove a product from your store.</p>
                <div class="video-wrapper">
                    <video controls preload="metadata" aria-describedby="delete-desc" title="Delete product tutorial">
                        <source src="./delete.mp4" type="video/mp4">
                        Your browser does not support embedded videos.
                    </video>
                </div>
            </section>

            <section class="wiki-section" aria-labelledby="theme-title">
                <h3 id="theme-title">🎨 Change the Active Theme</h3>
                <p id="theme-desc">Here’s how you can preview available themes and set a new active one for your
                    storefront.</p>
                <div class="video-wrapper">
                    <video controls preload="metadata" aria-describedby="theme-desc" title="Change theme tutorial">
                        <source src="./theme.mp4" type="video/mp4">
                        Your browser does not support embedded videos.
                    </video>
                </div>
            </section>
        </div>
    </main>

    <?php include_once '../../components/footer.php'; ?>
    <?php include_once '../../components/scripts.php'; ?>
</body>

</html>