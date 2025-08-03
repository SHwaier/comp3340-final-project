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
                    <li data-endpoint="/api/products.php">GET /api/products.php – <span
                            class="status">Checking...</span>
                    </li>
                    <li data-endpoint="/api/cart.php" data-method="POST">POST /api/cart.php – <span
                            class="status">Checking...</span></li>
                    <li data-endpoint="/api/cart.php" data-method="PUT">PUT /api/cart.php – <span
                            class="status">Checking...</span></li>
                    <li data-endpoint="/api/cart.php" data-method="DELETE">DELETE /api/cart.php – <span
                            class="status">Checking...</span></li>
                    <li data-endpoint="/api/auth/getSession.php">GET /api/auth/getSession.php – <span
                            class="status">Checking...</span></li>
                    <li data-endpoint="/api/themes.php">GET /api/themes.php – <span class="status">Checking...</span>
                    </li>
                    <li data-endpoint="/api/theme.php">GET /api/theme.php – <span class="status">Checking...</span></li>
                    <li data-endpoint="/api/theme.php" data-method="PUT">PUT /api/theme.php – <span
                            class="status">Checking...</span></li>
                </ul>
            </div>
        </div>

    </main>
    <script>
        async function checkAPIStatus(endpoint, method = 'GET') {
            try {
                const options = { method };
                if (['POST', 'PUT', 'DELETE'].includes(method)) {
                    options.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
                    options.body = 'test=true';
                }
                const res = await fetch(endpoint, options);
                return res.ok;
            } catch (e) {
                return false;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('#api-status-list li').forEach(async (item) => {
                const endpoint = item.getAttribute('data-endpoint');
                const method = item.getAttribute('data-method') || 'GET';
                const ok = await checkAPIStatus(endpoint, method);
                item.querySelector('.status').textContent = ok ? '✅ Working' : '❌ Error';
            });
        });
    </script>

</body>