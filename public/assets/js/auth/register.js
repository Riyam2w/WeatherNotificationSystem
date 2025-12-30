document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    if (!form) return;

    const nameField     = document.getElementById('full_name');
    const emailField    = document.getElementById('email');
    const passwordField = document.getElementById('password');
    const confirmField  = document.getElementById('confirm_password');

    /* =========================
       Password Toggle
    ========================= */
    function togglePassword(fieldId, toggleId) {
        const field = document.getElementById(fieldId);
        const toggle = document.getElementById(toggleId);
        if (!field || !toggle) return;

        toggle.addEventListener('click', () => {
            field.type = field.type === 'password' ? 'text' : 'password';
            toggle.classList.toggle('fa-eye');
            toggle.classList.toggle('fa-eye-slash');
        });
    }

    togglePassword('password', 'togglePassword');
    togglePassword('confirm_password', 'toggleConfirmPassword');

    /* =========================
       Error Helpers
    ========================= */
    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        document.querySelectorAll('input').forEach(el => el.classList.remove('error'));
    }

    function showError(field, message) {
        const errorEl = document.getElementById(`error_${field}`);
        const inputEl = document.getElementById(field);
        if (errorEl) errorEl.textContent = message;
        if (inputEl) inputEl.classList.add('error');
    }

    /* =========================
       Validation
    ========================= */
    function validateForm() {
        clearErrors();
        let valid = true;

        const name = nameField.value.trim();
        const email = emailField.value.trim();
        const password = passwordField.value;
        const confirm = confirmField.value;

        if (!name || name.length < 3) {
            showError('full_name', 'Name must be at least 3 letters.');
            valid = false;
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Enter a valid email.');
            valid = false;
        }

        if (!password || password.length < 6) {
            showError('password', 'Password must be at least 6 characters.');
            valid = false;
        }

        if (password !== confirm) {
            showError('confirm_password', 'Passwords do not match.');
            valid = false;
        }

        return valid;
    }

    /* =========================
       Submit
    ========================= */
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateForm()) return;

        const formData = new FormData(form);

        try {
            const response = await fetch('/register', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });

            const text = await response.text();
            const data = text ? JSON.parse(text) : {};

            if (!response.ok || data.success === false) {
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, msg]) =>
                        showError(field, msg)
                    );
                }
                return;
            }

            window.location.href = '/login';

        } catch (err) {
            console.error('Registration failed:', err);
        }
    });
});
