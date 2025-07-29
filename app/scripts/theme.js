window.onload = async function () {
    // For reference to what themes are available review the style.css file located inside the styles folder
    document.documentElement.setAttribute("data-theme", "dark");

    // change the theme sitewide to black-friday if the month is November or December
    const month = new Date().getMonth() + 1;
    if (month === 11 || month === 12) {
        document.documentElement.setAttribute("data-theme", "black-friday");
    }
    // if the theme is set to light, change the logo to the dark version for better visibility
    if (document.documentElement.getAttribute('data-theme') === "light") {
        let logos = document.getElementsByClassName('logo');
        for (let i = 0; i < logos.length; i++) {
            logos[i].setAttribute('src', '/assets/logo/luxe-logo-dark.png');
        }
    }
};