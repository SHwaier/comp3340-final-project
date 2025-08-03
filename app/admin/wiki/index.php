<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../components/metas.php'; ?>
    <title>Admin Wiki | Luxe</title>
    <meta name="description" content="Learn how to manage products, themes, and more on Luxe Admin.">
    <link rel="stylesheet" href="/styles/admin-style.css">
</head>

<body>
    <?php include '../../components/admin-sidebar.php'; ?>
    <main id="admin-main">
        <div class="container">
            <h2 style="margin-bottom: 1rem;">📘 Admin Wiki</h2>
            <p style="margin-bottom: 2rem;">Watch short walkthroughs on how to manage your Luxe store through the admin
                dashboard.</p>

            <div class="wiki-section">
                <h3>🛍️ Create a New Product</h3>
                <p>This video shows you how to add a new product with size variants and price adjustments.</p>
                <div class="video-wrapper">
                    <iframe src="/create.mp4" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>

            <div class="wiki-section">
                <h3>✏️ Edit an Existing Product</h3>
                <p>Learn how to update product info like price, stock, and variants using the edit page.</p>
                <div class="video-wrapper">
                    <iframe src="/edit.mp4" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>

            <div class="wiki-section">
                <h3>🗑️ Delete a Product</h3>
                <p>This demo explains how to safely remove a product from your store.</p>
                <div class="video-wrapper">
                    <iframe src="/delete" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>

            <div class="wiki-section">
                <h3>🎨 Change the Active Theme</h3>
                <p>Here’s how you can preview available themes and set a new active one for your storefront.</p>
                <div class="video-wrapper">
                    <iframe src="/theme.mp4" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </main>

    <?php include_once '../../components/footer.php'; ?>
    <?php include_once '../../components/scripts.php'; ?>

    <style>
        .wiki-section {
            margin-bottom: 3rem;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</body>

</html>