<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once '../components/metas.php'; ?>
    <title>Register – Luxe</title>
    <style>
        .register-container {
            display: flex;
            height: 100vh;
            background-color: inherit;
        }

        .register-left {
            flex: 3;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .register-left img {
            width: 150px;
            height: auto;
            margin-bottom: 2rem;
        }

        .register-left h1 {
            font-size: 2rem;
            font-weight: bold;
        }

        .register-right {
            flex: 2;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background-color: var(--bg-color);
        }

        .register-form {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .register-form h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .register-form input {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background-color: var(--input-bg);
            color: var(--text-color);
        }

        .register-form input:focus {
            outline: 2px solid var(--accent-color);
        }

        .register-form button {
            padding: 0.75rem;
            background-color: var(--accent-color);
            color: var(--text-color);
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .register-form button:hover {
            background-color: var(--accent-hover);
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }

            .register-left,
            .register-right {
                flex: none;
                width: 100%;
                height: auto;
            }

            .register-left {
                padding: 4rem 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- Left Panel -->
        <div class="register-left">
            <img src="/assets/logo/luxe-logo-light.png" alt="Luxe Logo">
            <h1>Join Luxe</h1>
            <p style="max-width: 400px; text-align: center; margin-top: 1rem;">
                Create your free account and start shopping personalized fashion today.
            </p>
        </div>

        <!-- Right Panel -->
        <div class="register-right">
            <form class="register-form" id="registerForm">
                <h2>Register</h2>
                <div>

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div>

                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <div>

                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <button type="submit">Create Account</button>
                <p id="register-error" style="color: var(--error-color); display: none; font-size: 0.9rem;"></p>
                <p style="font-size: 0.9rem; color: var(--secondary-text);">
                    Already have an account? <a href="/login">Login</a>
                </p>
            </form>
        </div>
    </div>
    <script src="/scripts/register.js"></script>
</body>

</html>