<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once '../components/metas.php'; ?>
    <title>Contact Us | Luxe</title>
    <meta name="description"
        content="Get in touch with Luxe. We'd love to hear from you — whether it's questions, feedback, or support.">
</head>

<body>
    <?php include_once '../components/header.php'; ?>

    <main class="container">
        <!-- Contact Header -->
        <section class="flex flex-col flex-center" style="text-align: center; padding: 4rem 1rem;">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">We’re Here to Help</h1>
            <p style="font-size: 1.1rem; max-width: 600px; margin-bottom: 2rem;">
                Reach out to us with any questions, feedback, or support inquiries. Our team is ready to assist.
            </p>
        </section>

        <!-- Contact Form -->
        <section style="max-width: 600px; margin: 0 auto;">
            <form action="/api/contact.php" id="contact-form" method="POST"
                style="display: flex; flex-direction: column; gap: 1rem;">
                <input type="text" name="name" placeholder="Your Name" required
                    style="padding: 0.8rem; border-radius: 6px; border: 1px solid var(--border-color);">
                <input type="email" name="email" placeholder="Your Email" required
                    style="padding: 0.8rem; border-radius: 6px; border: 1px solid var(--border-color);">
                <textarea name="message" placeholder="Your Message" rows="5" required
                    style="padding: 0.8rem; border-radius: 6px; border: 1px solid var(--border-color);"></textarea>
                <button type="submit" class="button">Send Message</button>
                <p id="form-status" style="margin-top: 1rem;"></p>
            </form>
        </section>

        <!-- Info Section -->
        <section style="margin-top: 3rem; text-align: center;">
            <p style="font-size: 0.95rem; color: var(--text-muted);">
                You can also email us directly at <a href="mailto:support@luxe.com"
                    style="color: var(--accent-color);">hwaier@uwindsor.ca</a>
            </p>
        </section>
    </main>

    <?php include_once '../components/footer.php'; ?>
    <?php include_once '../components/scripts.php'; ?>
    <script>
        document.getElementById('contact-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const status = document.getElementById('form-status');

            const formData = new FormData(form);

            try {
                const res = await fetch('/api/contact.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await res.json();

                if (res.ok && result.success) {
                    status.style.color = 'green';
                    status.textContent = 'Message sent successfully.';
                    form.reset();
                } else {
                    status.style.color = 'red';
                    status.textContent = result.error || 'Something went wrong.';
                }
            } catch (err) {
                status.style.color = 'red';
                status.textContent = 'Error sending message.';
            }
        });
    </script>
</body>

</html>