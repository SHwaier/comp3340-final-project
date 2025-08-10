<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>Admin Dashboard | Luxe</title>
    <meta name="description" content="Admin dashboard for managing Luxe store.">
    <link rel="stylesheet" href="/styles/admin-style.css">
</head>

<body>
    <?php include '../components/admin-sidebar.php'; ?>
    <main class="admin-main">
        <!-- Admin content goes here -->

        <div class="admin-overview">
            <h2>API Endpoint Status</h2>
            <strong>
                <p>THIS FEATURE IS STILL IN PROGRESS!</p>
            </strong>
            <div class="container">
                <ul id="api-status-list">
                   

                    <li data-endpoint="/api/auth/register.php">/api/auth/register.php – <span class="status">Checking...</span>
                    </li>

                    <li data-endpoint="/api/auth/login.php">/api/auth/login.php – <span class="status">Checking...</span>
                    </li>

                    <li data-endpoint="/api/auth/getSession.php">/api/auth/getSession.php – <span
                            class="status">Checking...</span></li>
                    </li>

                    <li data-endpoint="/api/cart.php">/api/cart.php – <span class="status">Checking...</span></li>
                    <li data-endpoint="/api/products.php">/api/products.php – <span class="status">Checking...</span>
                    </li>
                    <li data-endpoint="/api/profile.php">/api/profile.php – <span class="status">Checking...</span>
                    </li>
                    <li data-endpoint="/api/themes.php">/api/themes.php – <span class="status">Checking...</span>
                    <li data-endpoint="/api/theme.php">/api/theme.php – <span class="status">Checking...</span></li>
                </ul>
            </div>
        </div>

    </main>
    <script>
        async function checkAPIStatus(endpoint, originalMethod = 'GET') {
            try {
                // each testable endpoint has an options method that simply returns status 200 if the file is active, this tests for any api's that would be down
                const res = await fetch(endpoint, { method: 'OPTIONS' });
                return res.ok;
            } catch (e) {
                return false;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('#api-status-list li').forEach(async (item) => {
                const endpoint = item.getAttribute('data-endpoint');
                const method = item.getAttribute('data-method') || 'GET'; // Just for display
                const ok = await checkAPIStatus(endpoint, method);
                item.querySelector('.status').textContent = ok ? '✅ Working' : '❌ Error';
            });
        });
    </script>


</body>