<?php
// Ensure required variables are passed
if (!isset($product_id, $product_name, $image_url, $price, $quantity)) {
    echo "<!-- Missing cart item data -->";
    return;
}

$total_price = number_format($price * $quantity, 2);
$img_path = "/product/" . ($image_url ?: "placeholder.png");
?>

<div class="flex flex-wrap w-full rounded"
    style="border: 1px solid var(--border-color); background-color: var(--card-bg); box-shadow: 0 2px 6px rgba(0,0,0,0.1); padding: 1rem; gap: 1.5rem; align-items: center;">

    <!-- Image -->
    <div style="flex: 0 0 120px; max-width: 120px;">
        <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($product_name) ?>"
            onerror="this.onerror=null;this.src='/assets/img/placeholder.png';"
            style="width: 100%; height: auto; object-fit: cover; border-radius: 6px;" />
    </div>

    <!-- Content -->
    <div class="flex flex-col" style="flex: 1 1 300px; gap: 0.8rem;">
        <!-- Title -->
        <h2 style="font-size: 1.1rem; font-weight: 600; color: var(--text-color);">
            <?= htmlspecialchars($product_name) ?>
        </h2>

        <!-- Quantity and Price -->
        <div class="flex flex-wrap flex-space-between w-full" style="gap: 1rem; align-items: center;">
            <div class="flex flex-row gap-1" style="align-items: center;">
                <label for="qty-<?= $product_id ?>" style="font-size: 0.9rem;">Quantity:</label>
                <input type="number" id="qty-<?= $product_id ?>" value="<?= $quantity ?>" min="0"
                    style="width: 60px; padding: 0.4rem; font-size: 0.9rem;" />
            </div>
            <p class="price" style="font-weight: bold; font-size: 1rem; color: var(--accent-color); margin: 0;">
                $<span id="total-<?= $product_id ?>"><?= $total_price ?></span>
            </p>
        </div>

        <!-- Remove Button -->
        <div style="text-align: right;">
            <button onclick="removeItem(<?= $product_id ?>)"
                style="background-color: red; color: white; padding: 0.4rem 0.8rem; border: none; border-radius: 6px; font-size: 0.85rem; cursor: pointer;">
                Remove
            </button>
        </div>
    </div>
</div>