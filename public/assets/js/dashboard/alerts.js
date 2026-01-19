document.addEventListener('DOMContentLoaded', () => {
    // Determine which page is loaded. 
    // This script might be loaded globally, so we check for elements unique to the alerts page.
    if (document.getElementById('alertSearch')) {
        initAlertsUI();
    }
});

// Make it available globally if needed for SPA re-init
window.initAlertsUI = function () {
    const searchInput = document.getElementById('alertSearch');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    const tableBody = document.getElementById('alertsTableBody');

    if (!searchInput || !statusFilter || !sortBy || !tableBody) return;

    // Helper to get all rows
    const getRows = () => Array.from(tableBody.querySelectorAll('tr:not(.empty-state)'));

    // Filter Function
    const filterAlerts = () => {
        const query = searchInput.value.toLowerCase();
        const status = statusFilter.value;
        const rows = getRows();
        let visibleCount = 0;

        rows.forEach(row => {
            const city = row.getAttribute('data-city') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesSearch = city.includes(query);
            const matchesStatus = status === '' || rowStatus === status;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/Hide empty state if all filtered out
        // (Assuming there's an empty state row or we need to handle it. 
        // For simplicity, we just filter. Ideally we toggle a "no results" message)
    };

    // Sort Function
    const sortAlerts = () => {
        const criteria = sortBy.value; // 'created_at' or 'city'
        const rows = getRows();

        rows.sort((a, b) => {
            if (criteria === 'city') {
                const cityA = a.getAttribute('data-city');
                const cityB = b.getAttribute('data-city');
                return cityA.localeCompare(cityB);
            } else {
                // Default to date (created_at) descending usually, but user might want specific order.
                // Value is timestamp.
                const timeA = parseInt(a.getAttribute('data-created'));
                const timeB = parseInt(b.getAttribute('data-created'));
                // Sort Descending for date (newest first) usually
                return timeB - timeA;
            }
        });

        // Re-append in new order
        rows.forEach(row => tableBody.appendChild(row));
    };

    // Event Listeners
    searchInput.addEventListener('input', filterAlerts);
    statusFilter.addEventListener('change', filterAlerts);
    sortBy.addEventListener('change', sortAlerts);

    // Delete Handler (using delegation)
    tableBody.addEventListener('click', async (e) => {
        if (e.target.classList.contains('delete-alert-btn')) {
            const btn = e.target;
            const id = btn.getAttribute('data-id');

            if (!confirm('Are you sure you want to delete this alert?')) return;

            try {
                btn.disabled = true;
                btn.textContent = 'Deleting...';

                const response = await fetch(`/api/alerts/delete/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                        // CSRF token should be handled if required, usually via meta tag or cookie
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove row
                    btn.closest('tr').remove();

                    // Check if empty
                    if (getRows().length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="empty-state">No alerts found.</td>
                            </tr>
                        `;
                    }
                } else {
                    Toast.error(data.error || 'Failed to delete alert');
                    btn.disabled = false;
                    btn.textContent = 'Delete';
                }
            } catch (error) {
                console.error('Error:', error);
                Toast.error('An error occurred');
                btn.disabled = false;
                btn.textContent = 'Delete';
            }
        }
    });
};