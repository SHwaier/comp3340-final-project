<a href="/" class="logo-link" aria-label="Home">
    <img class="logo-img" src="/assets/logo/luxe-logo-light.png" alt="Luxe Logo">
</a>
<script>
    function changeLogo() {
        const bgColor = getComputedStyle(document.documentElement)
            .getPropertyValue('--bg-color')
            .trim();

        // Convert hex/rgb to RGB
        function hexToRgb(hex) {
            hex = hex.replace('#', '');
            if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
            const bigint = parseInt(hex, 16);
            return {
                r: (bigint >> 16) & 255,
                g: (bigint >> 8) & 255,
                b: bigint & 255
            };
        }

        function isLightColor({ r, g, b }) {
            const luminance = 0.299 * r + 0.587 * g + 0.114 * b;
            return luminance > 128;
        }

        const rgb = hexToRgb(bgColor);
        const logo = isLightColor(rgb) ? '/assets/logo/luxe-logo-dark.png' : '/assets/logo/luxe-logo-light.png';
        document.querySelectorAll('.logo-img').forEach(img => {
            img.src = logo;
        });
    }
    document.addEventListener("DOMContentLoaded", changeLogo);

</script>