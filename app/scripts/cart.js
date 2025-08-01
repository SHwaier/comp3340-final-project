
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

const token = getCookie('token');
cartItems = null;
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
        cartItems = items;
        let total = 0;
        cartContainer.innerHTML = '<div class="product-grid"></div>';
        const grid = cartContainer.querySelector('.product-grid');

        items.forEach(item => {
            total += item.final_price * item.quantity;

            const card = document.createElement('div');
            card.id = `item-card-${item.variant_id}`;
            card.className = 'cart-item-card';

            card.innerHTML = `
                <div class="flex flex-wrap w-full rounded" style="border: 1px solid var(--border-color); background-color: var(--card-bg); padding: 1rem; gap: 1.5rem; align-items: center;">
                    <div style="flex: 0 0 120px; max-width: 120px;">
                        <img src="/product/${item.image_url || 'placeholder.png'}"
                             alt="${item.product_name}"
                             onerror="this.onerror=null;this.src='/assets/img/placeholder.png';"
                             style="width: 100%; height: auto; object-fit: cover; border-radius: 6px;" />
                    </div>
                    <div class="flex flex-col" style="flex: 1 1 300px; gap: 0.8rem;">
                        <h2 style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">${item.product_name}</h2>
                        <p style="font-size: 0.9rem; color: var(--muted-color); margin: 0;">Size: ${item.size}</p>

                        <div class="flex flex-wrap flex-space-between w-full" style="gap: 1rem; align-items: center;">
                            <div class="flex flex-row gap-1" style="align-items: center;">
                                <label for="qty-${item.variant_id}" style="font-size: 0.9rem;">Quantity:</label>
                                <input type="number" id="qty-${item.variant_id}" value="${item.quantity}" min="0"
                                       style="width: 60px; padding: 0.4rem; font-size: 0.9rem;" />
                            </div>
                            <p class="price" style="font-weight: bold; font-size: 1rem; color: var(--accent-color); margin: 0;">
                                $<span id="total-${item.variant_id}">${(item.final_price * item.quantity).toFixed(2)}</span>
                            </p>
                        </div>

                        <div style="text-align: right;">
                            <button onclick="removeItem(${item.variant_id})"
                                    style="background-color: red; color: white; padding: 0.4rem 0.8rem; border: none; border-radius: 6px; font-size: 0.85rem; cursor: pointer;">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;

            card.querySelector(`#qty-${item.variant_id}`).addEventListener('change', e => {
                const newQty = parseInt(e.target.value);
                if (isNaN(newQty) || newQty < 0) return;
                updateQuantity(item.variant_id, newQty, item.final_price);
            });

            grid.appendChild(card);
        });

        summaryContainer.innerHTML = `<h3>Subtotal: $${total.toFixed(2)}</h3>`;
    } catch (err) {
        cartContainer.innerHTML = '<p style="color: var(--error-color);">Error loading cart.</p>';
    }
}

async function updateQuantity(variantId, quantity, unitPrice) {
    const input = document.getElementById(`qty-${variantId}`);
    input.disabled = true;

    try {
        const res = await fetch('/api/cart.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ variant_id: variantId, quantity })
        });

        if (!res.ok) throw new Error();
        updateItemQuantity(variantId, quantity);
        if (quantity < 1) {
            removeItemFromDOM(variantId);
            return;
        }
        document.getElementById(`total-${variantId}`).textContent = "$" + (unitPrice * quantity).toFixed(2);
    } catch (err) {
        alert("Failed to update cart. Try again.");
    } finally {
        input.disabled = false;
    }
}
// sends the request to remove the item from the cart to the backend
async function removeItem(variantId) {
    try {
        const res = await fetch('/api/cart.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ variant_id: variantId })
        });

        const data = await res.json();
        if (!res.ok || !data.message) throw new Error(data.error || 'Failed to remove item.');
        removeItemFromDOM(variantId);
    } catch (err) {
        alert("Failed to remove item.");
    }
}

// removes the item from the DOM and updates the cart summary
function removeItemFromDOM(variantId) {
    //  Remove the item card from the DOM
    const itemCard = document.getElementById("item-card-" + variantId);
    if (itemCard) {
        itemCard.remove();
    }
    // Remove the item from the cartItems array
    cartItems = cartItems.filter(item => item.variant_id !== variantId);
    // anytime an item is removed, we need to update the cart summary price
    updateCartSummary();
}
// updatest the UI for the cart summary subtotal price
function updateCartSummary() {
    const summaryContainer = document.getElementById('cart-summary');
    if (!cartItems || cartItems.length === 0) {
        summaryContainer.innerHTML = '<h3>Subtotal: $0.00</h3>';
        return;
    }
    const total = getTotalPrice();
    summaryContainer.innerHTML = `<h3>Subtotal: $${total.toFixed(2)}</h3>`;
}
// helper function to calculate the total price of all items in the cart
function getTotalPrice() {
    if (!cartItems || cartItems.length === 0) return 0;

    return cartItems.reduce((total, item) => total + (item.final_price * item.quantity), 0); // Calculate total price from cart items
}
// updates the quantity of an item in the cart item array and updates the substotal price by calling updateCartSummary
function updateItemQuantity(variantId, quantity) {
    // Find the item in the cartItems array and update its quantity to the new value
    cartItems.forEach(item => {
        if (item.variant_id === variantId) {
            item.quantity = quantity;
        }
    });
    updateCartSummary();
}
document.addEventListener('DOMContentLoaded', fetchCart);
