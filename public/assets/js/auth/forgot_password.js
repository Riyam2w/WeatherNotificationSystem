document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('forgotPasswordForm');
    if (!form) return;

    const messageBox = document.getElementById('formMessage');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        messageBox.innerHTML = '';

        const formData = new FormData(form);

        try {
            const response = await fetch('/forget_password', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                messageBox.innerHTML =
                    `<div class="error">${data.error || 'Something went wrong'}</div>`;
                return;
            }

            messageBox.innerHTML =
                `<div class="success">${data.message}</div>`;
            form.reset();

        } catch (err) {
            messageBox.innerHTML =
                `<div class="error">Network error. Please try again.</div>`;
        }
    });
});
