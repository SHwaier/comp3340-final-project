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
                .then(product => {
                    if (!product || !product.product_id) {
                        document.getElementById('product-content').innerHTML = `<p style="color: var(--error-color);">❌ Product not found.</p>`;
                        return;
                    }

                    const fallbackImage = '/assets/img/placeholder.png';
                    const basePrice = parseFloat(product.price).toFixed(2);
                    const sizes = product.variants.map(v => v.size);
                    const uniqueSizes = [...new Set(sizes)];

                    const sizeOptions = uniqueSizes.map(size => `<option value="${size}">${size}</option>`).join('');

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
                            <p id="price" style="font-size: 1.25rem; font-weight: bold;">$${basePrice}</p>
                            <div>
                                <label for="size-select" style="font-weight: bold;">Size:</label>
                                <select id="size-select" style="margin-left: 0.5rem;">${sizeOptions}</select>
                                <p id="addon-price-msg" style="margin-top: 0.5rem; color: var(--success-color); display: none;"></p>
                            </div>
                            <p style="font-size: 1rem; line-height: 1.6;">${product.description}</p>

                            <button class="check-button button" id="add-to-cart-button">
                                <span class="checkmark">&#10003;</span>
                                <span class="btn-text">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                `;

                    // Store for later lookup
                    window.currentProduct = product;

                    document.getElementById('size-select').addEventListener('change', () => {
                        const selectedSize = document.getElementById('size-select').value;
                        const variant = product.variants.find(v => v.size === selectedSize);
                        const total = (parseFloat(product.price) + parseFloat(variant.addon_price)).toFixed(2);
                        document.getElementById('price').innerText = `$${total}`;

                        const msgEl = document.getElementById('addon-price-msg');
                        if (variant.addon_price > 0) {
                            msgEl.style.display = 'block';
                            msgEl.textContent = `+ $${parseFloat(variant.addon_price).toFixed(2)} for this size`;
                        } else {
                            msgEl.style.display = 'none';
                            msgEl.textContent = '';
                        }
                    });
                    document.getElementById('add-to-cart-button').addEventListener('click', () => {
                        const selectedSize = document.getElementById('size-select').value;
                        const product = window.currentProduct;
                        const variant = product.variants.find(v => v.size === selectedSize);

                        if (!variant) {
                            alert("Please select a valid size.");
                            return;
                        }

                        addToCart(product.product_id, variant.variant_id);
                    });

                    // Trigger price update on first render
                    document.getElementById('size-select').dispatchEvent(new Event('change'));
                })
                .catch(err => {
                    document.getElementById('product-content').innerHTML = `<p style="color: var(--error-color);">Error loading product.</p>`;
                    console.error(err);
                });
        }

        function addToCart(productId) {
            const product = window.currentProduct;
            const selectedSize = document.getElementById('size-select').value;
            const variant = product.variants.find(v => v.size === selectedSize);

            if (!variant) {
                alert("Please select a valid size.");
                return;
            }

            fetch('/api/cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ variant_id: variant.variant_id, quantity: 1 })
            })
                .then(res => res.json())
                .then(data => {
                    console.log("Added to cart:", data);
                })
                .catch(err => {
                    console.error("Cart error:", err);
                });
        }
    </script>


</body>

</html>