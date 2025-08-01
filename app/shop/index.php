<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>Shop | Luxe</title>
    <meta name="description" content="Welcome to Luxe products, where you can find all the fashion that you like.">
</head>

<body>
    <?php include_once '../components/header.php'; ?>
    <main>
        <div class="container">
            <?php include_once '../components/product-grid.php'; ?>
        </div>

        <div class="horizontal-fluid-row">
            <div class="horizontal-fluid-image">
                <a href="/product/?id=${item.product_id}" aria-label="View product details">
                    <img src="/product/${item.image_url || 'placeholder.png'}" alt="${item.product_name}"
                        onerror="this.onerror=null;this.src='/assets/img/placeholder.png';">
                </a>
            </div>
            <div class="horizontal-fluid-content" style="justify-content: space-between;">
                <div class="horizontal-fluid-row">
                    <h5 id="product-title-${item.variant_id}" class="horizontal-fluid-title" role="region"
                        aria-labelledby="product-title-${item.variant_id}">This is a very good product we sell</h5>
                    <p class="horizontal-fluid-text" role="region"
                        aria-labelledby="product-description-${item.variant_id}">
                        Size: ${item.size}
                    </p>
                    <!-- <p class="horizontal-fluid-text small">Last updated 3 mins ago</p> -->
                </div>
                <div class="actionable-buttons horizontal-fluid-row">
                    <div class="horizontal-fluid-row"></div>
                    <input type="number" id="qty-${item.variant_id}" value="${item.quantity}" min="0"
                        style="width: 60px; padding: 0.4rem; font-size: 0.9rem;" />
                    <button type="button" onclick="removeItem(${item.variant_id})">Remove</button>
                    <p role="region" aria-labelledby="product-price-${item.variant_id}" id="total-${item.variant_id}">
                        ${(item.final_price
                        * item.quantity).toFixed(2)}</p>
                </div>
            </div>
        </div>
    </main>
    <?php include_once '../components/footer.php'; ?>
    <?php include_once '../components/scripts.php'; ?>
</body>

</html>