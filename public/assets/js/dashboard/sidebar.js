document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            if (!confirm('Are you sure you want to logout?')) return;

            try {
                const response = await fetch('/logout', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = '/login';
                }
            } catch (e) {
                Toast.error('Logout failed. Please try again.');
            }
        });
    }
});
