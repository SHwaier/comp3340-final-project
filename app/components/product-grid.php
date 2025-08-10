<!-- Sort Dropdown -->
<div style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem; margin-bottom: 1rem; align-items: center;">
    <label for="sort-select">Sort by:</label>
    <select id="sort-select">
        <option value="default">Default</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="name-asc">Alphabetical (A-Z)</option>
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

    function renderProducts(data) {
        const container = document.getElementById('product-grid');
        container.innerHTML = '';

        const fragment = document.createDocumentFragment();

        data.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';

            const img = document.createElement('img');
            img.src = `${product.image_url || '/assets/img/placeholder.png'}`;
            img.alt = product.product_name;
            img.onerror = () => img.src = '/assets/img/placeholder.png';
            img.loading = 'lazy';

            const link = document.createElement('a');
            link.href = `/product?id=${product.product_id}`;
            link.appendChild(img);

            const name = document.createElement('h2');
            name.textContent = product.product_name;

            const desc = document.createElement('p');
            desc.textContent = product.description.slice(0, 100) + '...';

            const price = document.createElement('div');
            price.className = 'price';
            price.textContent = `$${parseFloat(product.price).toFixed(2)}`;

            card.appendChild(link);
            card.appendChild(name);
            card.appendChild(desc);
            card.appendChild(price);

            if (product.stock_quantity === 0) {
                const out = document.createElement('span');
                out.textContent = 'Out of Stock';
                out.style.color = 'red';
                out.style.fontWeight = 'bold';
                card.appendChild(out);
            } else if (product.variants?.length > 0) {
                const select = document.createElement('select');
                select.id = `variant-select-${product.product_id}`;
                select.style.cssText = 'margin: 0.5rem 0; width: 100%';

                product.variants.forEach(v => {
                    const option = document.createElement('option');
                    option.value = v.variant_id;
                    option.textContent = `${v.size}${v.addon_price > 0 ? ` (+$${parseFloat(v.addon_price).toFixed(2)})` : ''}`;
                    select.appendChild(option);
                });

                const btn = document.createElement('button');
                btn.className = 'check-button button';
                btn.innerHTML = `<span class="checkmark">&#10003;</span><span class="btn-text">Add to Cart</span>`;
                btn.onclick = () => handleVariantCart(product.product_id);

                card.appendChild(select);
                card.appendChild(btn);
            }

            fragment.appendChild(card);
        });

        container.appendChild(fragment);
    }

    window.handleVariantCart = function (productId) {
        const select = document.getElementById(`variant-select-${productId}`);
        const variantId = select?.value;
        if (!variantId) {
            alert("Please select a variant.");
            return;
        }
        addToCart(variantId);
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

    fetch('/api/products.php?all=true')
        .then(res => res.json())
        .then(data => {
            products = data;
            renderProducts(products);
        });

</script>