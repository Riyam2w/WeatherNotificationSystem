document.addEventListener('DOMContentLoaded', () => {
    initSettingsUI();
});

window.initSettingsUI = function () {
    console.log('Settings UI Initialized');

    // 1. Password Toggles
    function setupPasswordToggle(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);
        if (!input || !toggle) return;

        // Cleanup old listeners if any (AJAX compatible)
        const newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);

        newToggle.addEventListener('click', () => {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            newToggle.classList.toggle('fa-eye');
            newToggle.classList.toggle('fa-eye-slash');
        });
    }

    setupPasswordToggle('current_password', 'toggleCurrentPassword');
    setupPasswordToggle('new_password', 'toggleNewPassword');
    setupPasswordToggle('confirm_password', 'toggleConfirmPassword');

    // 2. Profile Update Form
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-profile');
            const originalText = btn.innerText;

            const name = document.getElementById('full_name').value;
            const email = document.getElementById('email').value;
            const csrfToken = this.querySelector('input[name="csrf_token"]').value;

            btn.disabled = true;
            btn.innerText = 'Updating...';

            try {
                const response = await fetch('/api/user/profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({ full_name: name, email: email })
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    Toast.success('Profile updated successfully!');
                    // Update header name if needed or just reload part
                } else {
                    Toast.error(result.message || 'Failed to update profile.');
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.error('An error occurred. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        });
    }

    // 3. Password Update Form
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('btn-change-password');
            const originalText = btn.innerText;
            const csrfToken = this.querySelector('input[name="csrf_token"]').value;

            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                Toast.warning('Passwords do not match');
                return;
            }

            btn.disabled = true;
            btn.innerText = 'Changing...';

            try {
                const response = await fetch('/api/user/password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    Toast.success('Password changed successfully!');
                    this.reset();
                } else {
                    Toast.error(result.message || 'Failed to change password.');
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.error('An error occurred. Please try again.');
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        });
    }
};
