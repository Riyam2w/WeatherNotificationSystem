document.addEventListener('DOMContentLoaded', () => {
    const data = window.createAlertData;
    if (!data) return;

    // Fill summary
    document.getElementById('summaryCity').textContent = data.city_name;
    document.getElementById('summaryCondition').textContent =
        `${data.condition} ${data.operator} ${data.threshold}`;
    document.getElementById('summaryThreshold').textContent =
        `${data.operator} ${data.threshold}`;

    // Fill hidden fields
    for (const key in data) {
        const el = document.getElementById(key);
        if (el) el.value = data[key];
    }

    // Back button
    document.getElementById('backBtn').addEventListener('click', () => {
        history.back();
    });

    // Submit
    document.getElementById('confirmAlertForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content || '';

        const formData = new FormData(e.target);

        const res = await fetch('/api/alerts/create', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const json = await res.json();

        if (!res.ok || !json.success) {
            alert(json.error || 'Failed to create alert');
            return;
        }

        alert('Alert created successfully');
        window.location.href = '/dashboard';
    });
});
