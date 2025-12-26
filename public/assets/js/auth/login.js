document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    if (!form) return;

    const messageBox = document.getElementById('messageBox');
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content || '';

    const emailField = form.querySelector('input[name="email"]');
    const passwordField = document.getElementById('password-field');

    /* --------------------------------
       Password Toggle
    -------------------------------- */
    const toggle = document.getElementById('togglePassword');
    if (toggle && passwordField) {
        toggle.addEventListener('click', function () {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            toggle.classList.toggle('fa-eye');
            toggle.classList.toggle('fa-eye-slash');
        });
    }

    /* --------------------------------
       Client-side Validators
    -------------------------------- */
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validateForm() {
        const errors = [];

        const email = emailField.value.trim();
        const password = passwordField.value;

        if (!email) {
            errors.push('Email is required.');
        } else if (!isValidEmail(email)) {
            errors.push('Please enter a valid email address.');
        }

        if (!password) {
            errors.push('Password is required.');
        } else if (password.length < 6) {
            errors.push('Password must be at least 6 characters.');
        }

        return errors;
    }

    /* --------------------------------
       AJAX Login Submit
    -------------------------------- */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        // messageBox.innerHTML = '';

        const errors = validateForm();

        if (errors.length > 0) {
            messageBox.innerHTML = `
                <div class="error">
                    ${errors.map(e => `<p>${e}</p>`).join('')}
                </div>
            `;
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch('/login', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-Token': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                messageBox.innerHTML =
                    `<div class="error">${data.error || 'Login failed'}</div>`;
                return;
            }

            window.location.href = data.redirect || '/dashboard';

        } catch (err) {
            messageBox.innerHTML =
                `<div class="error">Network error. Please try again.</div>`;
        }
    });
});
