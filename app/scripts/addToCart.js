function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}
async function addToCart(productId) {
    const token = getCookie('token');
    if (!token) {
        alert("You must be logged in to add items to cart.");
        return;
    }

    try {
        const formData = new URLSearchParams();
        formData.append('product_id', productId);
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