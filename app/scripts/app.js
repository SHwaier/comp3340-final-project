
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

});

// // this needs to run after everything loads to ensure all elements are available
// window.onload = function () {

// };