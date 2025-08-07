<?php if (!isset($product_id))
    return; ?>

<div class="product-grid">
    <div class="product-card" id="product-card-<?= $product_id ?>">
        <div style="height: 300px; background: var(--skeleton-bg); border-radius: 8px; margin-bottom: 1rem;"></div>
        <div
            style="height: 24px; width: 70%; background: var(--skeleton-bg); border-radius: 6px; margin-bottom: 0.5rem;">
        </div>
        <div style="height: 20px; width: 50%; background: var(--skeleton-bg); border-radius: 6px;"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const container = document.getElementById('product-card-<?= $product_id ?>');
        if (!container) return;

        try {
            const res = await fetch(`/api/products.php?id=<?= $product_id ?>`);
            const data = await res.json();
            const product = Array.isArray(data) ? data[0] : data;

            if (!product || !product.product_id) {
                container.innerHTML = `<p style="color: var(--error-color);">Product not found.</p>`;
                return;
            }

            const img = (product.image_url || '/assets/img/placeholder.png');
            const outOfStock = product.stock_quantity == 0;
            const basePrice = parseFloat(product.price).toFixed(2);

            const variants = product.variants || [];
            const variantOptions = variants.map(v =>
                `<option value="${v.variant_id}">${v.size} ${v.addon_price > 0 ? `(+ $${parseFloat(v.addon_price).toFixed(2)})` : ''}</option>`
            ).join('');

            container.innerHTML = `
                <a href="/product?id=${product.product_id}">
                    <img src="${img}" alt="${product.product_name}"
                        onerror="this.onerror=null;this.src='/assets/img/placeholder.png';"
                        style="width: 100%; height: auto; border-radius: 8px; margin-bottom: 1rem;">
                </a>
                <h2>${product.product_name}</h2>
                <p>${product.description.slice(0, 100)}...</p>
                <div class="price">$${basePrice}</div>
                ${!outOfStock && variants.length > 0 ? `
                    <div style="margin: 0.5rem 0;">
                        <select id="variant-select-${product.product_id}" style="width: 100%; padding: 0.4rem;">
                            ${variantOptions}
                        </select>
                    </div>
                    <button class="check-button" onclick="handleVariantCart(${product.product_id})">
                        <span class="checkmark">&#10003;</span>
                        <span class="btn-text">Add to Cart</span>
                    </button>
                ` : `<span style="color: red; font-weight: bold;">Out of Stock</span>`}
            `;

            window[`handleVariantCart`] = function (productId) {
                const select = document.getElementById(`variant-select-${productId}`);
                const variantId = select?.value;
                if (!variantId) {
                    alert("Please select a variant.");
                    return;
                }
                addToCart(variantId);
            };
        } catch (err) {
            console.error(err);
            container.innerHTML = `<p style="color: var(--error-color);">Error loading product.</p>`;
        }
    });
</script>