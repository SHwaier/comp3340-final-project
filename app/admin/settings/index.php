<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <?php include_once '../../components/metas.php'; ?>
    <title>Admin Settings | Luxe</title>
    <meta name="description" content="Admin dashboard for managing Luxe store.">
    <style>
        .admin-main {
            padding-left: 18rem;

        }
    </style>
</head>

<body>
    <?php include '../../components/admin-sidebar.php'; ?>
    <main class="admin-main">
        <!-- Admin content goes here -->
        <div class="flex flex-col" style="padding: 2rem;">
            <h2>Site Settings</h2>
            <hr>
            <br>
            <p>Manage your site settings here.</p>
            <div class="settings-option">
                <h3>Theme Settings</h3>
                <p>Change the active theme for the site.</p>
                <form method="PUT" action="/api/theme.php">
                    <label for="theme-select">Select Theme:</label>
                    <select id="theme-select" name="theme_id">
                        <!-- <option value="1">Dark</option> -->
                    </select>
                    <button type="submit" class="button">Save</button>
                </form>
            </div>
        </div>
    </main>

    <script src="/scripts/theme.js"></script>
    <script>
        // a very fancy and efficient of getting a specific cookie value
        function getCookie(name) {
            return Object.fromEntries(document.cookie.split('; ').map(c => c.split('=')))[name] || null;
        }

        // Fetch available themes and populate the select dropdown
        async function fetchThemes() {
            try {
                const token = getCookie('token');
                const response = await fetch('/api/themes.php', {
                    method: 'GET', headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                }
                );
                if (!response.ok) throw new Error('Network response was not ok'); {
                    console.error('Error fetching themes:', response.statusText);
                }
                const themes = await response.json();
                const select = document.getElementById('theme-select');
                themes.forEach(theme => {
                    const option = document.createElement('option');
                    option.value = theme.theme_id;
                    option.textContent = theme.theme_name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error fetching themes:', error);
            }
        }

        fetchThemes();


        addEventListener('submit', async (e) => {
            e.preventDefault(); // consumer default form submission to prevent weird page reloads
            const select = document.getElementById('theme-select');
            const selectedTheme = parseInt(select.value, 10);
            const token = getCookie('token');

            try {
                const response = await fetch('/api/theme.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: new URLSearchParams({ theme_id: selectedTheme })
                });

                if (!response.ok) throw new Error('Failed to update theme');
                const result = await response.json();
                console.log('Theme updated successfully:', result);
                alert('Theme updated successfully!');
                window.location.reload(); // reload the page to apply the new theme
            } catch (error) {
                console.error('Error updating theme:', error);
                alert('Failed to update theme. Please try again.');
            }
        });
    </script>
</body>