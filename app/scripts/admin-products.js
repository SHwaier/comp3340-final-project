async function fetchProducts() {
    const productsContainer = document.getElementById('product-grid');
    productsContainer.innerHTML = '<p>Loading items...</p>';

    try {
        const res = await fetch('/api/products.php?all=true', {
            method: 'GET'
        });

        const items = await res.json();
        if (!res.ok || !Array.isArray(items)) throw new Error();

        if (items.length === 0) {
            productsContainer.innerHTML = '<p>You have no items create new ones.</p>';
            return;
        }
        let total = 0;
        productsContainer.innerHTML = ''; // Clear loading message

        items.forEach(item => {
            total += item.final_price * item.quantity;

            const card = document.createElement('section');
            card.id = `item-card-${item.product_id}`;
            card.className = 'horizontal-fluid-card';

            card.innerHTML = `
            <div class="horizontal-fluid-row">
            <div class="horizontal-fluid-image">
                <a href="/product/?id=${item.product_id}" aria-label="View product details">
                    <img src="${item.image_url || 'placeholder.png'}" alt="${item.product_name}"
                        onerror="this.onerror=null;this.src='/assets/img/placeholder.png';">
                </a>
            </div>
            <div class="horizontal-fluid-content" style="justify-content: space-between;">
                <div class="horizontal-fluid-row">
                    <h5 id="product-title-${item.variant_id}" class="horizontal-fluid-title" role="region"
                        aria-labelledby="product-title-${item.variant_id}">${item.product_name}</h5>
                </div>
                <div class="actionable-buttons horizontal-fluid-row">
                    <button type="button" class="button" onclick="removeItem(${item.product_id})">Remove</button>
                    <a href="/admin/products/edit?id=${item.product_id}" class="button" >Edit</a>
                    <p role="region" aria-labelledby="product-price-${item.product_id}" id="total-${item.product_id}">
                        ${item.price}</p>
                </div>
            </div>
        </div>
        `;
            productsContainer.appendChild(card);
        });

    } catch (err) {
        productsContainer.innerHTML = '<p style="color: var(--error-color);">Error loading products.</p>';
    }
}
async function removeItem(productId) {
    if (!confirm("Are you sure you want to delete this product? This cannot be undone.")) return;

    const token = document.cookie.split("; ").find(r => r.startsWith("token="))?.split("=")[1];
    if (!token) { alert("Not authenticated."); return; }

    const res = await fetch('/api/products.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ product_id: productId })
    });


    if (res.status === 204) {
        alert("Product deleted.");
        location.reload();
        return;
    }

    let data = null;
    try { data = await res.json(); } catch (_) { }

    if (res.ok) {
        alert(data?.message || "Product deleted.");
        location.reload();
    } else {
        console.error("Delete failed:", data);
        alert(data?.error || `Failed to delete product (${res.status}).`);
    }
}


document.addEventListener('DOMContentLoaded', fetchProducts);