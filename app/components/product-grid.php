<!-- Sort Dropdown -->
<div class="flex flex-row" style="margin-bottom: 1rem;">
    <label for="sort-select" style="margin-right: 0.5rem;">Sort by:</label>
    <select id="sort-select">
        <option value="default">Default</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="name-asc">Alphabetical (A–Z)</option>
    </select>
</div>

<!-- Product Grid -->
<div id="product-grid" class="product-grid"></div>

<script>
    let products = [];

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    async function addToCart(productId, variantId) {
        const token = getCookie('token');
        if (!token) {
            alert("You must be logged in to add items to cart.");
            return;
        }

        try {
            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            formData.append('variant_id', variantId);
            formData.append('quantity', 1);

            const res = await fetch('/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            const result = await res.json();
            if (!res.ok) throw new Error(result.error || 'Failed to add item.');
        } catch (err) {
            console.error("Add to cart error:", err);
            alert("Something went wrong. Try again.");
        }
    }

    function renderProducts(data) {
        const container = document.getElementById('product-grid');
        container.innerHTML = data.map(product => {
            const price = parseFloat(product.price).toFixed(2);
            const variants = product.variants || [];

            const variantOptions = variants.map(v => `
                <option value="${v.variant_id}">
                    ${v.size}${v.addon_price > 0 ? ` (+$${parseFloat(v.addon_price).toFixed(2)})` : ''}
                </option>`).join('');

            const variantSelect = variants.length > 0
                ? `<select id="variant-select-${product.product_id}" style="margin: 0.5rem 0; width: 100%;">${variantOptions}</select>`
                : '';

            const addToCartButton = variants.length > 0
                ? `<button class="check-button" onclick="handleVariantCart(${product.product_id})">
                        <span class="checkmark">&#10003;</span>
                        <span class="btn-text">Add to Cart</span>
                   </button>`
                : `<span style="color: red; font-weight: bold;">No Variants</span>`;

            return `
                <div class="product-card">
                    <a href="/product?id=${product.product_id}">
                        <img src="${"/product/" + product.image_url || '/assets/img/placeholder.png'}"
                            alt="${product.product_name}"
                            onerror="this.onerror=null;this.src='/assets/img/placeholder.png';">
                    </a>
                    <h2>${product.product_name}</h2>
                    <p>${product.description.slice(0, 100)}...</p>
                    <div class="price">$${price}</div>
                    ${product.stock_quantity == 0
                    ? `<span style="color: red; font-weight: bold;">Out of Stock</span>`
                    : `
                        ${variantSelect}
                        ${addToCartButton}
                    `}
                </div>
            `;
        }).join('');
    }

    window.handleVariantCart = function (productId) {
        const select = document.getElementById(`variant-select-${productId}`);
        const variantId = select?.value;
        if (!variantId) {
            alert("Please select a variant.");
            return;
        }
        addToCart(productId, variantId);
    };

    function sortProducts(criteria) {
        let sorted = [...products];

        switch (criteria) {
            case 'price-asc':
                sorted.sort((a, b) => a.price - b.price);
                break;
            case 'price-desc':
                sorted.sort((a, b) => b.price - a.price);
                break;
            case 'name-asc':
                sorted.sort((a, b) => a.product_name.localeCompare(b.product_name));
                break;
            default:
                sorted = [...products];
        }

        renderProducts(sorted);
    }

    document.getElementById('sort-select').addEventListener('change', e => {
        sortProducts(e.target.value);
    });

    fetch('/api/products.php')
        .then(res => res.json())
        .then(data => {
            products = data;
            renderProducts(products);
        });
</script>