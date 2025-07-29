<?php
$product_id = $_GET['id'] ?? null;
if (!is_numeric($product_id)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid product ID."]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>Product Details | Luxe</title>
    <meta name="description" content="View details about this product from Luxe.">
</head>

<body>
    
    <?php include_once '../components/header.php'; ?>
    <main>
        <div class="container">

            <button onclick="history.back()"
                style="margin-bottom: 1rem; background: none; border: none; color: var(--accent-color); font-size: 1rem; cursor: pointer;">
                ← Back to Shop
            </button>

            <div id="product-content" class="skeleton">
                <!-- Skeleton Loader for 2 columns -->
                <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                    <div style="flex: 1 1 300px; height: 300px; background: var(--skeleton-bg); border-radius: 12px;">
                    </div>
                    <div style="flex: 1 1 300px;">
                        <div
                            style="height: 32px; background: var(--skeleton-bg); width: 70%; border-radius: 8px; margin-bottom: 1rem;">
                        </div>
                        <div
                            style="height: 24px; background: var(--skeleton-bg); width: 40%; border-radius: 6px; margin-bottom: 1rem;">
                        </div>
                        <div
                            style="height: 80px; background: var(--skeleton-bg); border-radius: 8px; margin-bottom: 1rem;">
                        </div>
                        <div style="height: 42px; background: var(--skeleton-bg); width: 50%; border-radius: 6px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include_once '../components/footer.php'; ?>
    <?php include_once '../components/scripts.php'; ?>
    <script>
        const productId = "<?= htmlspecialchars($product_id) ?>";

        if (!productId) {
            document.getElementById('product-content').innerHTML = `<p style="color: var(--error-color);">❌ Product ID is missing.</p>`;
        } else {
            fetch(`/api/products.php?id=${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        document.getElementById('product-content').innerHTML = `<p style="color: var(--error-color);">❌ Product not found.</p>`;
                        return;
                    }

                    const product = data[0];
                    const isOutOfStock = product.stock_quantity == 0;
                    const fallbackImage = '/assets/img/placeholder.png'; // fallback path

                    document.getElementById('product-content').classList.remove('skeleton');
                    document.getElementById('product-content').innerHTML = `
                <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                    <div style="flex: 1 1 300px;">
                        <img src="${product.image_url}" alt="${product.product_name}"
                             onerror="this.onerror=null;this.src='${fallbackImage}'"
                             style="width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    </div>
                    <div style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 1.2rem;">
                        <h1 style="font-size: 2rem;">${product.product_name}</h1>
                        <p style="font-size: 1.25rem; font-weight: bold;">$${parseFloat(product.price).toFixed(2)}</p>
                        <p style="font-size: 1rem; line-height: 1.6;">${product.description}</p>

                        ${isOutOfStock
                            ? `<span style="color: red; font-weight: bold;">Out of Stock</span>`
                            : `<button style="padding: 0.75rem 1.5rem; background: var(--accent-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 1rem;">
                                    Add to Cart
                               </button>`
                        }
                    </div>
                </div>
            `;
                })
                .catch(err => {
                    document.getElementById('product-content').innerHTML = `<p style="color: var(--error-color);">Error loading product.</p>`;
                    console.error(err);
                });
        }

    </script>

</body>

</html>