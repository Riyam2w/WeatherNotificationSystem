document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registerForm');
    if (!form) return;

    const messageBox = document.getElementById('formMessage');
    const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.content || '';

    const nameField = form.querySelector('input[name="full_name"]');
    const emailField = form.querySelector('input[name="email"]');
    const passwordField = document.getElementById('password-field');
    const confirmField = document.getElementById('confirm-password-field');

    /* --------------------------------
       Password Toggle Helper
    -------------------------------- */
    function togglePassword(fieldId, toggleId) {
        const field = document.getElementById(fieldId);
        const toggle = document.getElementById(toggleId);
        if (!field || !toggle) return;

        toggle.addEventListener('click', () => {
            const isPassword = field.type === 'password';
            field.type = isPassword ? 'text' : 'password';
            toggle.classList.toggle('fa-eye');
            toggle.classList.toggle('fa-eye-slash');
        });
    }

    togglePassword('password-field', 'togglePassword');
    togglePassword('confirm-password-field', 'toggleConfirmPassword');

    /* --------------------------------
       Client-side Validators
    -------------------------------- */
    function isValidName(name) {
        return /^[A-Za-z ]+$/.test(name) && name.length >= 3;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validateForm() {
        const errors = [];

        const name = nameField.value.trim();
        const email = emailField.value.trim();
        const password = passwordField.value;
        const confirm = confirmField.value;

        if (!name) {
            errors.push('Full name is required.');
        } else if (!isValidName(name)) {
            errors.push('Name must contain only letters and be at least 3 characters.');
        }

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

        if (!confirm) {
            errors.push('Please confirm your password.');
        } else if (password !== confirm) {
            errors.push('Passwords do not match.');
        }

        return errors;
    }

    /* --------------------------------
       AJAX Register Submit
    -------------------------------- */
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        messageBox.innerHTML = '';

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
            const response = await fetch('/register', {
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
                const serverErrors = data.errors
                    ? Object.values(data.errors).join('<br>')
                    : (data.error || 'Registration failed');

                messageBox.innerHTML =
                    `<div class="error">${serverErrors}</div>`;
                return;
            }

            messageBox.innerHTML =
                `<div class="success">${data.message || 'Registration successful'}</div>`;

            setTimeout(() => {
                window.location.href = '/login';
            }, 1200);

        } catch (err) {
            messageBox.innerHTML =
                `<div class="error">Network error. Please try again.</div>`;
        }
    });
});
