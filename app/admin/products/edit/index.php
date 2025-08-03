<?php
require_once __DIR__ . '/../../../api/auth/getSession.php';
$user = getSession();

// Block access unless user is admin
if (!$user || $user['role'] !== 'Admin') {
    http_response_code(403);
    exit('Access denied.');
}


$product_id = $_GET['id'] ?? null;
if (!is_numeric($product_id)) {
    http_response_code(400);
    echo "Invalid product ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../../components/metas.php'; ?>
    <title>Edit Product | Luxe Admin</title>
    <meta name="description" content="Edit existing product.">
</head>

<body>
    <?php include_once '../../../components/header.php'; ?>
    <main>
        <div class="container">
            <button onclick="history.back()"
                style="margin-bottom: 1rem; background: none; border: none; color: var(--accent-color); font-size: 1rem; cursor: pointer;">
                ← Go Back
            </button>

            <h2>Edit Product</h2>

            <form id="product-form" style="display: flex; flex-wrap: wrap; gap: 2rem;"
                onsubmit="return submitProduct(event)">
                <div style="flex: 1 1 300px;">
                    <label>Image URL:</label>
                    <input type="text" name="image_url" required style="width: 100%;">
                    <img id="preview" src="/assets/img/placeholder.png"
                        style="margin-top: 1rem; max-width: 100%; border-radius: 12px;">
                </div>

                <div style="flex: 1 1 300px; display: flex; flex-direction: column; gap: 1rem;">
                    <label>Product Name:</label>
                    <input type="text" name="product_name" required>

                    <label>Description:</label>
                    <textarea name="description" rows="4"></textarea>

                    <label>Base Price ($):</label>
                    <input type="number" name="price" step="0.01" required>

                    <label>Total Stock Quantity:</label>
                    <input type="number" name="stock_quantity" required>

                    <label>Category:</label>
                    <input type="text" name="category">

                    <hr>
                    <h3>Size Variants</h3>
                    <div id="variants">
                        <!-- JS will populate -->
                    </div>
                    <button type="button" class="button" onclick="addVariant()">+ Add Size</button>

                    <button type="submit" class="button" style="margin-top: 1rem;">Update Product</button>
                </div>
            </form>
        </div>
    </main>

    <?php include_once '../../../components/footer.php'; ?>
    <?php include_once '../../../components/scripts.php'; ?>

    <script>
        const productId = <?= (int) $product_id ?>;
        const token = document.cookie.split('; ').find(r => r.startsWith('token='))?.split('=')[1];

        async function fetchProduct() {
            const res = await fetch(`/api/products.php?id=${productId}`);
            const product = await res.json();

            if (!product || !product.product_id) {
                alert("❌ Product not found.");
                return;
            }

            const form = document.getElementById('product-form');
            form.product_name.value = product.product_name;
            form.description.value = product.description;
            form.price.value = product.price;
            form.stock_quantity.value = product.stock_quantity;
            form.category.value = product.category ?? '';
            form.image_url.value = product.image_url ?? '';
            document.getElementById('preview').src = product.image_url || '/assets/img/placeholder.png';

            // Populate variants
            product.variants.forEach(v => {
                addVariant(v.size, v.addon_price, v.stock_quantity);
            });

            // If no variants exist, add one default
            if (product.variants.length === 0) {
                addVariant("M", 0, 0);
            }
        }

        function addVariant(size = '', addon_price = 0, stock = 0) {
            const container = document.getElementById('variants');
            const index = container.children.length;
            container.insertAdjacentHTML('beforeend', `
                <div style="margin-bottom: 1rem; border: 1px solid var(--border-color); padding: 0.5rem; border-radius: 8px;">
                    <label>Size:</label>
                    <input type="text" name="variants[${index}][size]" value="${size}" required>
                    <label>Add-on Price ($):</label>
                    <input type="number" step="0.01" name="variants[${index}][addon_price]" value="${addon_price}">
                    <label>Stock Quantity:</label>
                    <input type="number" name="variants[${index}][stock_quantity]" value="${stock}">
                </div>
            `);
        }

        function submitProduct(event) {
            event.preventDefault();
            const form = document.getElementById('product-form');
            const formData = new FormData(form);
            const product = {
                product_id: productId,
                product_name: formData.get("product_name"),
                description: formData.get("description"),
                price: parseFloat(formData.get("price")),
                stock_quantity: parseInt(formData.get("stock_quantity")),
                category: formData.get("category"),
                image_url: formData.get("image_url"),
                variants: []
            };

            const variantBlocks = document.querySelectorAll('#variants > div');
            variantBlocks.forEach((block, index) => {
                const size = block.querySelector(`input[name="variants[${index}][size]"]`).value;
                const addon_price = parseFloat(block.querySelector(`input[name="variants[${index}][addon_price]"]`).value) || 0;
                const stock = parseInt(block.querySelector(`input[name="variants[${index}][stock_quantity]"]`).value) || 0;
                product.variants.push({ size, addon_price, stock_quantity: stock });
            });

            fetch('/api/products.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(product)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.message === "Product updated") {
                        alert("Product updated successfully!");
                        window.location.href = `/product?id=${productId}`;
                    } else {
                        alert("Failed to update product.");
                        console.error(data);
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("Server error.");
                });

            return false;
        }

        document.querySelector('input[name="image_url"]').addEventListener('input', (e) => {
            document.getElementById('preview').src = e.target.value || '/assets/img/placeholder.png';
        });

        fetchProduct();
    </script>

</body>

</html>