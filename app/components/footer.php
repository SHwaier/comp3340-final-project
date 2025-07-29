<footer id="footer" class="footer-container">
    <div class="container flex flex-row flex-wrap flex-space-between"
        style="gap: 2rem; padding-top: 2rem; padding-bottom: 2rem;">

        <!-- Column 1: Logo -->
        <div class="flex flex-col">
            <a href="/">
                <img class="logo" src="/assets/logo/luxe-logo-light.png" alt="Luxe Logo" width="100" height="100">
            </a>
            <p style="margin-top: 0.5rem; font-size: 0.9rem;">© <?= date("Y") ?> Luxe. All rights reserved.</p>
        </div>

        <!-- Column 2: Useful Links -->
        <div class="flex flex-col">
            <h4>Useful Links</h4>
            <ul style="list-style: none; padding: 0;">
                <li><a class="hover-underline-animation left" href="/about">About Us</a></li>
                <li><a class="hover-underline-animation left" href="/shop">Shop</a></li>
                <li><a class="hover-underline-animation left" href="/faq">FAQ</a></li>
                <li><a class="hover-underline-animation left" href="/returns">Returns</a></li>
            </ul>
        </div>

        <!-- Column 3: Contact Info -->
        <div class="flex flex-col">
            <h4>Contact</h4>
            <p>Email: <a href="mailto:hwaier@uwindsor.ca">hwaier@uwindsor.ca</a></p>
            <p>Phone: <a href="tel:+15192533000">+1 (519) 253-3000</a></p>
            <p>Address: 401 Sunset Ave, Windsor, ON N9B 3P4</p>
        </div>

    </div>
</footer>