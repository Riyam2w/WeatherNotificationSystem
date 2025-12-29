document.addEventListener('DOMContentLoaded', () => {
    const createBtn = document.getElementById('createAlertBtn');
    const content  = document.getElementById('dashboard-content');

    if (!createBtn || !content) return;

    createBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('/dashboard/load?page=create-alert', {
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to load page');
            }

            const html = await response.text();
            content.innerHTML = html;

            // Optional: update URL (nice UX)
            history.pushState({}, '', '/dashboard?page=create-alert');
        } catch (e) {
            content.innerHTML = '<p class="error">Failed to load Create Alert page.</p>';
        }
    });
});
