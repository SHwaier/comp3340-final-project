<?php
?><!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>About | Luxe</title>
    <meta name="description"
        content="Learn more about Luxe, your go-to destination for stylish top-wear and seamless online shopping.">
</head>

<body>
    <?php include_once '../components/header.php'; ?>

    <main class="container">
        <section style="text-align: center; max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Our Story</h1>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--secondary-text);">
                Welcome to <strong>Luxe</strong>, your go-to destination for modern fashion and premium top-wear.
                Founded with a passion for blending style and functionality, Luxe is more than just an online shop —
                it's a curated space for those who seek self-expression through clothing. Whether you're chasing
                streetwear vibes or timeless classics, we make it easy to discover what fits your mood and style.
            </p>
        </section>

        <section style="margin-top: 3rem; max-width: 900px; margin-left: auto; margin-right: auto;">
            <h2 style="font-size: 2rem; margin-bottom: 1rem;">What We Offer</h2>
            <ul style="list-style-type: disc; padding-left: 2rem; font-size: 1.1rem; line-height: 1.8;">
                <li>Premium hoodies, jackets, shirts, and seasonal fashion pieces.</li>
                <li>Secure and user-friendly online shopping experience.</li>
            </ul>
        </section>

        <section
            style="margin-top: 3rem; background-color: var(--card-bg); padding: 2rem; border-radius: 10px; max-width: 900px; margin-left: auto; margin-right: auto;">
            <h2 style="font-size: 2rem; margin-bottom: 1rem;">Built for a Seamless Shopping Experience</h2>
            <p style="font-size: 1.1rem; line-height: 1.8;">
                Luxe is a modern and stylish online fashion platform specializing in premium top-wear such as hoodies,
                jackets,
                shirts, and streetwear. Designed with user experience and performance in mind, the platform offers a
                seamless
                shopping experience powered by a secure authentication system, dynamic product catalog, real-time cart
                management,
                and a flexible theming engine. Each product can be browsed by variant, such as size, and managed through
                a robust
                backend built using PHP and MySQL. Whether you're looking to refresh your wardrobe or explore trending
                styles,
                Luxe delivers high-quality fashion with the convenience of a fully responsive and fast-loading
                interface.
            </p>
        </section>

        <section style="margin-top: 3rem; text-align: center;">
            <h2 style="font-size: 2rem; margin-bottom: 1rem;">Join Our Community</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; max-width: 700px; margin: 0 auto;">
                Whether you're new here or a returning customer, we’re thrilled to have you explore our collections.
                Keep an eye out for new arrivals, seasonal offers, and exclusive drops.
            </p>
            <a href="/shop"
                style="margin-top: 2rem; display: inline-block; padding: 0.75rem 1.5rem; background-color: var(--accent-color); color: white; border-radius: 6px; font-weight: bold;">Browse
                Our Shop</a>
        </section>
    </main>

    <?php include_once '../components/footer.php'; ?>
    <?php include_once '../components/scripts.php'; ?>
</body>

</html>