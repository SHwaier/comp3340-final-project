<!-- common meta tags and links that are used site wide are added here -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="Saymon Hwaier">
<meta name="keywords" content="clothing, fashion, modern, digital">
<meta name="robots" content="index, follow">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="/styles/style.css">
<link rel="icon" href="/assets/logo/favicon.ico" type="image/x-icon">
<script>
    (function () {
        let themeId = null;

        try {
            themeId = localStorage.getItem('theme_id');
        } catch (e) {
            // localStorage might be blocked, ignore
        }

        if (!themeId) {
            const match = document.cookie.match(/theme_id=(\d+)/);
            if (match) themeId = match[1];
        }

        let theme = 'dark';
        if (themeId === '2') theme = 'light';
        else if (themeId === '3') theme = 'black-friday';

        const month = new Date().getMonth() + 1;
        if (month === 11 || month === 12) theme = 'black-friday';

        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>