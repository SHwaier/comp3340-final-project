/**
 * Register Form Validation and Submission
 * This script handles the registration form submission, validates the input fields,
 * and sends the data to the server for registration.
 */
document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const email = document.getElementById('email').value.trim();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const errorMsg = document.getElementById('register-error');
    errorMsg.style.display = 'none';

    // Regex patterns
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const usernamePattern = /^[a-zA-Z0-9_]{3,20}$/;
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;

    // Validate
    if (!emailPattern.test(email)) {
        errorMsg.textContent = "Invalid email format.";
        errorMsg.style.display = "block";
        return;
    }
    if (!usernamePattern.test(username)) {
        errorMsg.textContent = "Username must be 3-20 characters (letters, numbers, underscores).";
        errorMsg.style.display = "block";
        return;
    }
    if (!passwordPattern.test(password)) {
        errorMsg.textContent = "Password must be at least 6 characters with letters and numbers.";
        errorMsg.style.display = "block";
        return;
    }

    try {
        /**
         * Sends a POST request to the server to register a new user.
         *
         * @constant {Response} res - The response object returned from the fetch API.
         * @property {number} res.status - The HTTP status code of the response.
         * @property {boolean} res.ok - Indicates whether the response was successful (status in the range 200-299).
         * @property {Function} res.json - A method to parse the response body as JSON.
         *
         * @throws {TypeError} Throws an error if the fetch request fails (e.g., network issues).
         * @throws {Error} Throws an error if the server returns a non-2xx status code.
         */
        const res = await fetch('/api/auth/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, username, password })
        });

        const data = await res.json();

        if (!res.ok) {
            errorMsg.textContent = data.error || "Registration failed. Try again.";
            errorMsg.style.display = "block";
            return;
        }

        const loginRes = await fetch('/api/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const loginData = await loginRes.json();

        if (!loginRes.ok || !loginData.token) {
            errorMsg.textContent = loginData.error || "Auto-login failed. Please login manually.";
            errorMsg.style.display = "block";
            return;
        }

        // ✅ Token received — store and redirect
        localStorage.setItem('token', loginData.token);
        document.cookie = `token=${loginData.token}; path=/; secure; samesite=strict`;

        window.location.href = '/';

    } catch (err) {
        errorMsg.textContent = "Something went wrong. Please try again.";
        errorMsg.style.display = "block";
    }
});