<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../components/metas.php'; ?>
    <title>Admin Products | Luxe</title>
    <meta name="description" content="View and manage Luxe products.">
    <style>
        .admin-main {
            padding-left: 18rem;
        }

        .product-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .product-card {
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            background: var(--card-bg);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-details {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .product-details input,
        .product-details textarea {
            width: 100%;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .product-actions {
            margin-top: 0.5rem;
            display: flex;
            justify-content: space-between;
        }

        @media (min-width: 768px) {
            .product-card {
                flex-direction: row;
                height: auto;
            }

            .product-card img {
                width: 200px;
                height: 100%;
            }

            .product-details {
                flex: 1;
            }
        }
    </style>
</head>

<body>
    <?php include '../../components/admin-sidebar.php'; ?>
    <main class="admin-main">
        <div style="padding: 2rem;">
            <h2>Manage Products</h2>
            <hr><br>
            <div id="product-grid" class="product-grid"></div>
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
            const container = document.getElementById('product-grid');
            container.innerHTML = '';

            products.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card';

                const image = document.createElement('img');
                image.src = "/product/" + (product.image_url || 'placeholder.png');
                image.onerror = () => image.src = '/assets/img/placeholder.png';

                const form = document.createElement('form');
                form.className = 'product-details';
                form.innerHTML = `
                    <strong>${product.product_name}</strong>
                    <label>Description:</label>
                    <textarea name="description">${product.description}</textarea>
                    <label>Price:</label>
                    <input type="number" name="price" step="0.01" value="${product.price}">
                    <label>Stock:</label>
                    <input type="number" name="stock_quantity" value="${product.stock_quantity}">
                    <label>Image Filename:</label>
                    <input type="text" name="image_url" value="${product.image_url}">
                    <div class="product-actions">
                        <button type="submit">Save</button>
                        <button type="button" onclick="deleteProduct(${product.product_id})" style="color: red;">Delete</button>
                    </div>
                `;

                form.onsubmit = async e => {
                    e.preventDefault();
                    const formData = new FormData(form);
                    const payload = {
                        id: product.product_id,
                        description: formData.get('description'),
                        price: parseFloat(formData.get('price')),
                        stock_quantity: parseInt(formData.get('stock_quantity')),
                        image_url: formData.get('image_url')
                    };

                    const res = await fetch('/api/products.php', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        },
                        body: JSON.stringify(payload)
                    });

                    if (res.ok) {
                        alert('Product updated');
                        fetchProducts();
                    } else {
                        alert('Update failed');
                    }
                };

                card.appendChild(image);
                card.appendChild(form);
                container.appendChild(card);
            });
        }

        async function deleteProduct(id) {
            if (!confirm("Delete this product and all its variants?")) return;

            const res = await fetch(`/api/products.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (res.ok) {
                alert("Deleted");
                fetchProducts();
            } else {
                alert("Delete failed");
            }
        }

        fetchProducts();
    </script>
</body>

</html>