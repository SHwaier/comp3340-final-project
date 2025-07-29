<?php
?><!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>Cart | Luxe</title>
    <meta name="description"
        content="View and manage items in your cart before checkout at Luxe. Stylish jackets, hoodies, and more await.">
</head>

<body>
    <?php include_once '../components/header.php'; ?>

    <main class="container">
        <h1 style="margin-bottom: 2rem;">Your Cart</h1>

        <div id="cart-items"></div>

        <div id="cart-summary" style="margin-top: 2rem; text-align: right;"></div>
    </main>

    <?php include_once '../components/footer.php'; ?>
    <?php include_once '../components/scripts.php'; ?>

    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        const token = getCookie('token');

        async function fetchCart() {
            const cartContainer = document.getElementById('cart-items');
            const summaryContainer = document.getElementById('cart-summary');
            cartContainer.innerHTML = '<p>Loading cart...</p>';

            try {
                const res = await fetch('/api/cart.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                const items = await res.json();
                if (!res.ok || !Array.isArray(items)) throw new Error();

                if (items.length === 0) {
                    cartContainer.innerHTML = '<p>Your cart is empty.</p>';
                    summaryContainer.innerHTML = '';
                    return;
                }

                let total = 0;
                cartContainer.innerHTML = '<div class="product-grid"></div>';
                const grid = cartContainer.querySelector('.product-grid');

                // looping through each item in the cart and adding them as rows
                items.forEach(item => {
                    total += item.price * item.quantity;

                    const card = document.createElement('div');
                    card.className = 'cart-item-card';

                    // using a two column style for the cart items with flex wrapping for responsiveness
                    card.innerHTML = `
                         <div class="flex flex-wrap w-full rounded" style="border: 1px solid var(--border-color); background-color: var(--card-bg); padding: 1rem; gap: 1.5rem; align-items: center;">

                             <!-- Image -->
                             <div style="flex: 0 0 120px; max-width: 120px;">
                                 <img src="/product/${item.image_url || 'placeholder.png'}"
                                      alt="${item.product_name}"
                                      onerror="this.onerror=null;this.src='/assets/img/placeholder.png';"
                                      style="width: 100%; height: auto; object-fit: cover; border-radius: 6px;" />
                             </div>

                             <!-- Content -->
                             <div class="flex flex-col" style="flex: 1 1 300px; gap: 0.8rem;">
                                 <h2 style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">${item.product_name}</h2>

                                 <div class="flex flex-wrap flex-space-between w-full" style="gap: 1rem; align-items: center;">
                                     <div class="flex flex-row gap-1" style="align-items: center;">
                                         <label for="qty-${item.product_id}" style="font-size: 0.9rem;">Quantity:</label>
                                         <input type="number" id="qty-${item.product_id}" value="${item.quantity}" min="0"
                                                style="width: 60px; padding: 0.4rem; font-size: 0.9rem;" />
                                     </div>
                                     <p class="price" style="font-weight: bold; font-size: 1rem; color: var(--accent-color); margin: 0;">
                                         $<span id="total-${item.product_id}">${(item.price * item.quantity).toFixed(2)}</span>
                                     </p>
                                 </div>

                                 <div style="text-align: right;">
                                     <button onclick="removeItem(${item.product_id})"
                                             style="background-color: red; color: white; padding: 0.4rem 0.8rem; border: none; border-radius: 6px; font-size: 0.85rem; cursor: pointer;">
                                         Remove
                                     </button>
                                 </div>
                             </div>
                         </div>
                     `;

                    // Attach quantity update handler
                    card.querySelector(`#qty-${item.product_id}`).addEventListener('change', e => {
                        const newQty = parseInt(e.target.value);
                        if (isNaN(newQty) || newQty < 0) return;
                        updateQuantity(item.product_id, newQty, item.price);
                    });

                    grid.appendChild(card);
                });

                summaryContainer.innerHTML = `<h3>Subtotal: $${total.toFixed(2)}</h3>`;
            } catch (err) {
                cartContainer.innerHTML = '<p style="color: var(--error-color);">Error loading cart.</p>';
            }
        }

        async function updateQuantity(productId, quantity, unitPrice) {
            const input = document.getElementById(`qty-${productId}`);
            input.disabled = true;

            try {
                const res = await fetch('/api/cart.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Authorization': `Bearer ${token}`
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                });

                if (!res.ok) throw new Error();
                document.getElementById(`total-${productId}`).textContent = (unitPrice * quantity).toFixed(2);
                await fetchCart(); // reload cart totals only (not full page)
            } catch (err) {
                alert("Failed to update cart. Try again.");
            } finally {
                input.disabled = false;
            }
        }

        async function removeItem(productId) {
            const card = document.getElementById(`qty-${productId}`).closest('.product-card');
            card.style.opacity = '0.5';

            try {
                const res = await fetch('/api/cart.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Authorization': `Bearer ${token}`
                    },
                    body: `product_id=${productId}`
                });

                if (!res.ok) throw new Error();
                card.remove();
                await fetchCart(); // refresh totals
            } catch (err) {
                alert("Failed to remove item.");
            }
        }

        document.addEventListener('DOMContentLoaded', fetchCart);
    </script>

</body>

</html>