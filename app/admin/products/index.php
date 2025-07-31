<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../components/metas.php'; ?>
    <title>Admin Products | Luxe</title>
    <meta name="description" content="Admin dashboard to manage Luxe store products.">
    <style>
        .admin-main {
            padding-left: 18rem;
        }

        .product-row {
            border: 1px solid var(--border-color);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .product-row input,
        .product-row select {
            padding: 0.5rem;
            margin: 0.3rem 0;
        }

        .product-actions {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include '../../components/admin-sidebar.php'; ?>
    <main class="admin-main">
        <div class="flex flex-col" style="padding: 2rem;">
            <h2>Manage Products</h2>
            <hr><br>
            <div id="product-list"></div>

            <h3 style="margin-top: 3rem;">Create New Product</h3>
            <form id="create-form">
                <input type="text" name="product_name" placeholder="Product Name" required>
                <textarea name="description" placeholder="Description" required></textarea>
                <input type="number" step="0.01" name="price" placeholder="Base Price" required>
                <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
                <input type="text" name="image_url" placeholder="Image Filename" required>
                <input type="text" name="sizes"
                    placeholder="Sizes (comma separated, with optional +price, e.g., S,M+5,L+10)" required>
                <button type="submit" class="button">Add Product</button>
            </form>
        </div>
    </main>

    <?php include_once '../../components/scripts.php'; ?>
    <script>
        const token = document.cookie.split('; ').find(c => c.startsWith('token='))?.split('=')[1];

        async function fetchProducts() {
            const res = await fetch('/api/products.php');
            const data = await res.json();
            renderProductList(data);
        }

        function renderProductList(products) {
            const container = document.getElementById('product-list');
            container.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('div');
                row.className = 'product-row';
                row.innerHTML = `
                    <strong>${product.product_name}</strong>
                    <p>${product.description}</p>
                    <p>Base Price: $${parseFloat(product.price).toFixed(2)}</p>
                    <p>Stock: ${product.stock_quantity}</p>
                    <p>Variants: ${product.variants?.map(v => `${v.size} (+$${v.addon_price})`).join(', ') || 'None'}</p>
                    <div class="product-actions">
                        <button onclick="editProduct(${product.product_id})">Edit</button>
                        <button onclick="deleteProduct(${product.product_id})" style="color: red;">Delete</button>
                    </div>
                `;
                container.appendChild(row);
            });
        }

        async function deleteProduct(id) {
            if (!confirm("Delete this product and all its variants?")) return;

            const res = await fetch(`/api/products.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (res.ok) fetchProducts();
            else alert("Failed to delete.");
        }

        document.getElementById('create-form').addEventListener('submit', async e => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const sizes = formData.get('sizes').split(',').map(s => {
                const [size, price = 0] = s.split('+');
                return { size: size.trim(), addon_price: parseFloat(price || 0) };
            });

            const payload = {
                product_name: formData.get('product_name'),
                description: formData.get('description'),
                price: formData.get('price'),
                stock_quantity: formData.get('stock_quantity'),
                image_url: formData.get('image_url'),
                variants: sizes
            };

            const res = await fetch('/api/products.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                alert("Product added!");
                form.reset();
                fetchProducts();
            } else {
                alert("Failed to add product.");
            }
        });

        fetchProducts();
    </script>
</body>

</html>