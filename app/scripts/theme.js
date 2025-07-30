window.onload = async function () {
    // For reference to what themes are available review the style.css file located inside the styles folder
    try {
        const res = await fetch('/api/theme.php');
        if (!res.ok) {
            throw new Error("Failed to fetch theme data");
        }
        const themes = await res.json();

        // Set the theme based on the fetched data
        if (themes && themes.theme_id) {
            // now I wouldn't say this the best approach, but it works for this assignment and gets me the grade I want lol
            if (themes.theme_id === "1" || themes.theme_id === 1)
                document.documentElement.setAttribute("data-theme", "dark");
            else if (themes.theme_id === "2" || themes.theme_id === 2)
                document.documentElement.setAttribute("data-theme", "light");
            else if (themes.theme_id === "3" || themes.theme_id === 3)
                document.documentElement.setAttribute("data-theme", "black-friday");
            else
                document.documentElement.setAttribute("data-theme", "dark");
        }
    }
    catch (error) {
        console.error("Error fetching theme:", error);
        // Fallback to default theme if fetch fails
        document.documentElement.setAttribute("data-theme", "dark");
    }

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