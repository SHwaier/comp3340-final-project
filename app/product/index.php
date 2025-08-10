<?php
$product_id = $_GET['id'] ?? null;
if (!is_numeric($product_id)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid product ID."]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

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
                ← Go Back
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
        const productId = "<?= htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') ?>";
        const fallbackImage = '/assets/img/placeholder.png';
        const token = window.token || localStorage.getItem('token') || '';

        if (!productId) {
            document.getElementById('product-content').innerHTML =
                `<p style="color: var(--error-color);">❌ Product ID is missing.</p>`;
        } else {
            loadProduct(productId);
        }

        async function loadProduct(id) {
            try {
                const res = await fetch(`/api/products.php?id=${encodeURIComponent(id)}`);
                const data = await res.json();
                const product = Array.isArray(data) ? data[0] : data;

                if (!product || !product.product_id) {
                    document.getElementById('product-content').innerHTML =
                        `<p style="color: var(--error-color);">❌ Product not found.</p>`;
                    return;
                }

                // Make available for later use
                window.currentProduct = product;

                const basePrice = Number(product.price || 0);
                const sizes = (product.variants || []).map(v => v.size);
                const uniqueSizes = [...new Set(sizes)];
                const sizeOptions = uniqueSizes.map(size => `<option value="${escapeHtml(size)}">${escapeHtml(size)}</option>`).join('');

                const html = `
                    <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
                        <div style="flex: 1 1 300px;">
                            <img src="${product.image_url || fallbackImage}" alt="${escapeHtml(product.product_name)}"
                                 onerror="this.onerror=null;this.src='${fallbackImage}'"
                                 style="width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                        <div style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 1.2rem;">
                            <h1 style="font-size: 2rem;">${escapeHtml(product.product_name)}</h1>
                            <p id="price" style="font-size: 1.25rem; font-weight: bold;">$${basePrice.toFixed(2)}</p>

                            ${(product.variants && product.variants.length) ? `
                            <div>
                                <label for="size-select" style="font-weight: bold;">Size:</label>
                                <select id="size-select" style="margin-left: 0.5rem;">${sizeOptions}</select>
                                <p id="addon-price-msg" style="margin-top: 0.5rem; color: var(--success-color); display: none;"></p>
                            </div>
                            ` : ''}

                            <p style="font-size: 1rem; line-height: 1.6;">${escapeHtml(product.description || '')}</p>

                            ${(!product.variants || product.variants.length === 0 || Number(product.stock_quantity) === 0)
                        ? `<span style="color: red; font-weight: bold;">Out of Stock</span>`
                        : `
                                <button class="check-button button" id="add-to-cart-button">
                                    <span class="checkmark">&#10003;</span>
                                    <span class="btn-text">Add to Cart</span>
                                </button>
                                `}
                        </div>
                    </div>
                `;

                const container = document.getElementById('product-content');
                container.classList.remove('skeleton');
                container.innerHTML = html;

                // Wire up behaviors if we have variants
                if (product.variants && product.variants.length) {
                    const sizeSelect = document.getElementById('size-select');
                    const priceEl = document.getElementById('price');
                    const msgEl = document.getElementById('addon-price-msg');

                    const updatePrice = () => {
                        const selectedSize = sizeSelect.value;
                        const variant = product.variants.find(v => v.size === selectedSize);
                        if (!variant) return;

                        const total = basePrice + Number(variant.addon_price || 0);
                        priceEl.textContent = `$${total.toFixed(2)}`;

                        if (Number(variant.addon_price) > 0) {
                            msgEl.style.display = 'block';
                            msgEl.textContent = `+ $${Number(variant.addon_price).toFixed(2)} for this size`;
                        } else {
                            msgEl.style.display = 'none';
                            msgEl.textContent = '';
                        }
                    };

                    sizeSelect.addEventListener('change', updatePrice);
                    updatePrice(); // initial

                    const addBtn = document.getElementById('add-to-cart-button');
                    if (addBtn) {
                        addBtn.addEventListener('click', async () => {
                            const selectedSize = sizeSelect.value;
                            const variant = product.variants.find(v => v.size === selectedSize);

                            if (!variant) {
                                alert("Please select a valid size.");
                                return;
                            }
                            try {
                                await addToCart(variant.variant_id, 1);
                                addBtn.classList.add('show-check');
                                setTimeout(() => addBtn.classList.remove('show-check'), 1200);
                            } catch (e) {
                                console.error(e);
                                alert(e.message || 'Failed to add to cart.');
                            }
                        });
                    }
                }
            } catch (err) {
                console.error(err);
                document.getElementById('product-content').innerHTML =
                    `<p style="color: var(--error-color);">Error loading product.</p>`;
            }
        }

        // Consistent contract: pass just the variantId (and optional qty).
        async function addToCart(variantId, quantity = 1) {
            const res = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: `variant_id=${encodeURIComponent(variantId)}&quantity=${encodeURIComponent(quantity)}`
            });

            let data = null;
            try { data = await res.json(); } catch { }

            if (!res.ok) {
                const msg = (data && (data.error || data.message)) || `HTTP ${res.status}`;
                if (res.status === 401) throw new Error('Please sign in to add items to your cart.');
                throw new Error(msg);
            }
            return data;
        }

        // Simple HTML escape for dynamic text nodes
        function escapeHtml(str) {
            return String(str ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    </script>


</body>

</html>