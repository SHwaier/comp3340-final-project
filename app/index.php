<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'components/metas.php'; ?>
    <title>Home | Luxe </title>
    <meta name="description"
        content="Discover premium jackets, hoodies, shirts, and streetwear at Luxe. Elevate your everyday style with our top fashion picks.">
</head>

<body>
    <?php include_once 'components/header.php'; ?>

    <main class="container">
        <!-- Hero Section -->
        <section class="flex flex-col flex-center" style="text-align: center; padding: 4rem 1rem;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Elevate Your Style</h1>
            <p style="font-size: 1.1rem; max-width: 600px; margin-bottom: 2rem;">
                Explore our exclusive collection of jackets, hoodies, and tops. Modern fashion for your everyday
                lifestyle.
            </p>
            <a href="/shop" class="button">
                Shop Now
            </a>
        </section>

        <!-- Featured Products -->
        <section style="margin-top: 3rem;">
            <h2 style="font-size: 1.8rem; margin-bottom: 1.5rem; text-align: center;">Featured Pieces</h2>
            <div class="product-grid">
                <?php
                $featured = [1, 2, 3];

                foreach ($featured as $product_id) {
                    ?>
                    <div class="flex flex-col">

                        <?php
                        include 'components/product-card.php';
                        ?>
                    </div><?php
                } ?>

            </div>
        </section>

        <!-- CTA Banner -->
        <section>
            <?php include_once 'components/cta-banner.php'; ?>
        </section>
    </main>

    <?php include_once 'components/footer.php'; ?>
    <?php include_once 'components/scripts.php'; ?>
</body>

</html>