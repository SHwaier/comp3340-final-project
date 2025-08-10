<?php
require_once __DIR__ . '/../../../api/auth/getSession.php';
$user = getSession();

// Block access unless user is admin
if (!$user || $user['role'] !== 'Admin') {
    http_response_code(403);
    exit('Access denied.');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../../../components/metas.php'; ?>
    <title>Create Product | Luxe Admin</title>
    <meta name="description" content="Add a new product to Luxe.">
</head>

<body>
    <?php include_once '../../../components/header.php'; ?>
    <main>
        <div class="container">
            <button onclick="history.back()"
                style="margin-bottom: 1rem; background: none; border: none; color: var(--accent-color); font-size: 1rem; cursor: pointer;">
                ← Go Back
            </button>

            <h2>Create New Product</h2>

            <form id="product-form" style="display: flex; flex-wrap: wrap; gap: 2rem;"
                onsubmit="return submitProduct(event)">

                <div style="flex: 1 1 300px;">
                    <div id="image-drop-zone" style="position: relative; cursor: pointer;">
                        <img id="preview" src="/assets/img/placeholder.png"
                            style="max-width: 100%; border-radius: 12px; border: 2px dashed var(--border-color); padding: 0.5rem;">
                        <input type="file" name="image_file" id="image-file-input" accept=".jpg,.jpeg,.png,.webp"
                            style="display: none;">
                    </div>
                    <p style="color: var(--text-muted);">Click or drag an image to upload
                        <br>
                        Recommended dimensions: 1024x1024 pixels. Max size: 2MB.
                        <br>
                        Supported formats: JPG, PNG, WEBP.
                    </p>

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
                        <!-- JavaScript will populate here -->
                    </div>
                    <button type="button" class="button" onclick="addVariant()">+ Add Size</button>

                    <button type="submit" class="button" style="margin-top: 1rem;">Create Product</button>
                </div>
            </form>
        </div>
    </main>

    <?php include_once '../../../components/footer.php'; ?>
    <?php include_once '../../../components/scripts.php'; ?>

    <script>
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
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        const token = getCookie('token');

        // Submit product form
        function submitProduct(event) {
            event.preventDefault();
            const form = document.getElementById('product-form');
            const formData = new FormData(form);

            // Build JSON string for product info
            const product = {
                product_name: formData.get("product_name"),
                description: formData.get("description"),
                price: parseFloat(formData.get("price")),
                stock_quantity: parseInt(formData.get("stock_quantity")),
                category: formData.get("category"),
                variants: []
            };

            const variantBlocks = document.querySelectorAll('#variants > div');
            variantBlocks.forEach((block, index) => {
                const size = block.querySelector(`input[name="variants[${index}][size]"]`).value;
                const addon_price = parseFloat(block.querySelector(`input[name="variants[${index}][addon_price]"]`).value) || 0;
                const stock = parseInt(block.querySelector(`input[name="variants[${index}][stock_quantity]"]`).value) || 0;
                product.variants.push({ size, addon_price, stock_quantity: stock });
            });

            // Append product JSON and image file to form data
            formData.set("product_data", JSON.stringify(product)); // wrap JSON string
            formData.delete("product_name"); // remove individual inputs to avoid duplication
            formData.delete("description");
            formData.delete("price");
            formData.delete("stock_quantity");
            formData.delete("category");

            fetch('/api/products.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': `Bearer ${token}`
                },
            })
                .then(res => res.json())
                .then(data => {
                    if (data.product_id) {
                        alert(" Product created successfully!");
                        window.location.href = `/product/?id=${data.product_id}`;
                    } else {
                        alert("❌ Failed to create product.");
                        console.error(data);
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("❌ Server error.");
                });

            return false;
        }


        /**
         * Image upload and preview
         */
        const dropZone = document.getElementById('image-drop-zone');
        const fileInput = document.getElementById('image-file-input');
        const preview = document.getElementById('preview');

        // Click on image opens file input
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        // File input change = update preview
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                previewImage(file);
            }
        });

        // Drag & drop functionality
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--accent-color)';
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = 'var(--border-color)';
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = 'var(--border-color)';
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                fileInput.files = e.dataTransfer.files;
                previewImage(file);
            }
        });

        // Preview helper
        function previewImage(file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const maxSize = 2 * 1024 * 1024; // 2MB


            // 1. Validate file type
            if (!allowedTypes.includes(file.type)) {
                alert("❌ Invalid file format. Allowed: JPG, PNG, WEBP.");
                fileInput.value = ''; // reset input
                return;
            }

            // 2. Validate file size
            if (file.size > maxSize) {
                alert("❌ File too large. Max size is 2MB.");
                fileInput.value = '';
                return;
            }

            // 3. Try to load image
            const img = new Image();
            img.onload = function () {


                // Passed all checks — show preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            };
            img.onerror = function () {
                alert("❌ Failed to load image for validation.");
                fileInput.value = '';
            };

            // Read the file as blob URL to validate dimensions
            img.src = URL.createObjectURL(file);
        }

        /**
         * End Image upload and preview
         */


        // Add one default variant
        addVariant("M", 0, 5);
    </script>

</body>

</html>