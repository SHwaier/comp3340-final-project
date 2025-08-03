
// This function is used to toggle the mobile menu on and off
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("mobile-menu-toggle");
    const closeBtn = document.getElementById("mobile-menu-close");
    const sidebar = document.getElementById("mobile-sidebar");
    const overlay = document.getElementById("sidebar-overlay");

    const openSidebar = () => {
        sidebar.classList.add("show");
        overlay.classList.add("show");
    };

    const closeSidebar = () => {
        sidebar.classList.remove("show");
        overlay.classList.remove("show");
    };
    if (toggleBtn)
        toggleBtn.addEventListener("click", openSidebar);
    if (closeBtn)
        closeBtn.addEventListener("click", closeSidebar);
    if (overlay)
        overlay.addEventListener("click", closeSidebar);


    // adds a class to show animation to buttons with the class 'check-button'
    document.addEventListener('click', (e) => {
        const button = e.target.closest('.check-button');
        if (!button) return;

        button.classList.add('show-check');
        setTimeout(() => {
            button.classList.remove('show-check');
        }, 2000);
    });

    // depending on the theme, change the icons
    changeIcons();
});

function changeIcons() {
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
    if (!isLightColor(rgb)) {
        document.querySelectorAll('.icon').forEach(icon => {
            icon.classList.add('light-icon');
        }
        );
    }
}