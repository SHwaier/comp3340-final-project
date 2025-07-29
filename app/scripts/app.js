
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

    toggleBtn.addEventListener("click", openSidebar);
    closeBtn.addEventListener("click", closeSidebar);
    overlay.addEventListener("click", closeSidebar);
});