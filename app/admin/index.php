<?php
require_once __DIR__ . '/../api/auth/getSession.php';
$user = getSession();

// Block access unless user is admin
if (!$user || $user['role'] !== 'Admin') {
    http_response_code(403);
    exit('Access denied.');
}
?>
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
    <main class="admin-main" role="main">
        <div class="container">
            <!-- Summary Statistics -->
            <section class="grid grid-auto">
                <article class="card">
                    <div class="headline">API Endpoint Status</div>
                    <p class="muted"><strong>THIS FEATURE IS STILL IN PROGRESS!</strong></p>

                    <!-- Summary grid showing total endpoints, latency, last check, and controls -->
                    <div class="grid grid-auto">
                        <!-- Total endpoints working -->
                        <div class="card">
                            <div class="headline">
                                <span id="stat-up" style="font-size:2rem;font-weight:700">0</span>
                                <span>/</span>
                                <span id="stat-total">0</span>
                            </div>
                            <p class="muted">Endpoints reachable</p>
                            <div class="progress" aria-hidden="true"><span id="progress-bar"></span></div>
                        </div>

                        <!-- Average latency -->
                        <div class="card">
                            <div class="headline"><span id="stat-avg-latency">—</span></div>
                            <p class="muted">Average latency (ms)</p>
                        </div>

                        <!-- Last checked timestamp -->
                        <div class="card">
                            <div class="headline"><span id="stat-last-check">—</span></div>
                            <p class="muted">Last checked</p>
                        </div>

                        <!-- Controls: recheck + copy report -->
                        <div class="card flex flex-row flex-space-between flex-wrap" style="gap:.5rem">
                            <div class="flex flex-row" style="gap:.5rem">
                                <button class="button" id="btn-recheck">Recheck</button>
                                <button class="button-secondary border" id="btn-copy">Copy report</button>
                            </div>
                            <span class="badge"><span>Overall:</span><strong id="health-label">Unknown</strong></span>
                        </div>
                    </div>
                </article>
            </section>

            <!-- Detailed endpoints will get auto filled here by the script under this -->
            <section class="card" style="margin-top:1rem;">
                <ul id="api-status-list" class="grid grid-cards" role="list">

                </ul>
            </section>
        </div>

    </main>
    <script>
        /**
         * Single source of truth for endpoints shown on the dashboard.
         * Add/remove items here; UI updates automatically.
         */
        const ENDPOINTS = [
            '/api/auth/register.php',
            '/api/auth/login.php',
            '/api/auth/getSession.php',
            '/api/cart.php',
            '/api/products.php',
            '/api/profile.php',
            '/api/themes.php',
            '/api/theme.php'
        ].map(String);

        // (Optional) tiny helper to catch typos during editing
        function apt(s) { return String(s); }

        /**
         * Build one endpoint card <li> element.
         */
        function createEndpointItem(path) {
            const li = document.createElement('li');
            li.className = 'endpoint-card border rounded';
            li.setAttribute('data-endpoint', path);

            // Top row: path + status chip
            const top = document.createElement('div');
            top.className = 'endpoint-top';

            const pathEl = document.createElement('div');
            pathEl.className = 'endpoint-path';
            pathEl.textContent = path;

            const chip = document.createElement('span');
            chip.className = 'status-chip';
            chip.setAttribute('aria-live', 'polite');
            chip.textContent = 'Checking…';

            top.append(pathEl, chip);

            // Key/Value row: code + latency 
            const kv = document.createElement('div');
            kv.className = 'flex flex-row';
            kv.style.gap = '1rem';
            kv.style.fontSize = '.9rem';
            kv.style.color = 'var(--secondary-text)';

            const codeWrap = document.createElement('span');
            codeWrap.innerHTML = 'Code: <strong class="code-val" style="color:var(--text-color);">—</strong>';

            const latWrap = document.createElement('span');
            latWrap.innerHTML = 'Latency: <strong class="lat-val" style="color:var(--text-color);">—</strong> ms';

            kv.append(codeWrap, latWrap);

            li.append(top, kv);
            return li;
        }

        /**
         * Render all items into the list container.
         */
        function renderEndpointList() {
            const ul = document.getElementById('api-status-list');
            ul.innerHTML = ''; // clear any previous content
            const frag = document.createDocumentFragment();
            ENDPOINTS.forEach(path => frag.appendChild(createEndpointItem(path)));
            ul.appendChild(frag);
        }

        /**
         * OPTIONS probe (safe health check).
         */
        async function probe(endpoint) {
            const start = performance.now();
            try {
                const res = await fetch(endpoint, { method: 'OPTIONS' });
                const end = performance.now();
                return { ok: res.ok, status: res.status, latency: Math.round(end - start) };
            } catch {
                const end = performance.now();
                return { ok: false, status: 0, latency: Math.round(end - start) };
            }
        }

        /**
         * Update status chip text + color.
         */
        function setChip(el, ok) {
            el.classList.toggle('ok', ok);
            el.textContent = ok ? '✅ Working' : '❌ Error';
        }

        /**
         * Update summary row counts, progress, and labels.
         */
        function updateSummary(results) {
            const total = results.length;
            const up = results.filter(r => r.ok).length;
            const pct = total ? Math.round((up / total) * 100) : 0;

            document.getElementById('stat-total').textContent = total;
            document.getElementById('stat-up').textContent = up;
            document.getElementById('progress-bar').style.width = pct + '%';

            const avg = total ? Math.round(results.reduce((a, b) => a + (b.latency || 0), 0) / total) : 0;
            document.getElementById('stat-avg-latency').textContent = total ? avg : '—';
            document.getElementById('stat-last-check').textContent = new Date().toLocaleString();

            const label = pct === 100 ? 'Healthy' : pct >= 70 ? 'Mostly OK' : 'Degraded';
            document.getElementById('health-label').textContent = label;
        }

        /**
         * Run health checks for all rendered items on demand
         */
        async function runChecks() {
            const items = Array.from(document.querySelectorAll('#api-status-list .endpoint-card'));
            const results = [];

            await Promise.all(items.map(async (item) => {
                const endpoint = item.getAttribute('data-endpoint');
                const chip = item.querySelector('.status-chip');
                const codeEl = item.querySelector('.code-val');
                const latEl = item.querySelector('.lat-val');

                chip.textContent = 'Checking…';
                chip.classList.remove('ok');

                const res = await probe(endpoint);
                setChip(chip, res.ok);
                codeEl.textContent = res.status || '—';
                latEl.textContent = res.latency ?? '—';
                results.push(res);
            }));

            updateSummary(results);
        }

        /**
         * Boot: render list, hook buttons, run initial checks.
         */
        document.addEventListener('DOMContentLoaded', () => {
            renderEndpointList();
            runChecks();

            // Recheck button
            document.getElementById('btn-recheck').addEventListener('click', runChecks);

            // Copy report button
            document.getElementById('btn-copy').addEventListener('click', async () => {
                const lines = [];
                document.querySelectorAll('#api-status-list .endpoint-card').forEach((item) => {
                    const path = item.querySelector('.endpoint-path').textContent.trim();
                    const code = item.querySelector('.code-val').textContent.trim();
                    const lat = item.querySelector('.lat-val').textContent.trim();
                    const status = item.querySelector('.status-chip').classList.contains('ok') ? 'OK' : 'ERROR';
                    lines.push(`${path} — ${status} (code ${code}, ${lat} ms)`);
                });
                const report = `API Health Report — ${new Date().toLocaleString()}\n` + lines.join('\n');
                try {
                    await navigator.clipboard.writeText(report);
                    alert('Report copied to clipboard');
                } catch {
                    alert('Could not copy report');
                }
            });
        });
    </script>


</body>

</html>