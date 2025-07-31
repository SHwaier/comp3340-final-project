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

    if (!variantId) {
        alert("Missing variant ID.");
        return;
    }

    try {
        const res = await fetch('/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: variantId,
                quantity: 1
            })
        });

        const result = await res.json();
        if (!res.ok) throw new Error(result.error || 'Failed to add item.');
        console.log("Added to cart:", result.message);
    } catch (err) {
        console.error("Add to cart error:", err);
        alert("Something went wrong. Try again.");
    }
}
