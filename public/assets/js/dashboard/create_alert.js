document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('createAlertForm');
    if (!form) return;

    // Condition selection
    document.querySelectorAll('.condition-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.condition-card')
                .forEach(c => c.classList.remove('active'));

            card.classList.add('active');
            document.getElementById('condition').value = card.dataset.condition;
        });
    });

    // Submit handler
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const data = {
            city_name: document.getElementById('city_name').value,
            lat: document.getElementById('lat').value,
            lon: document.getElementById('lon').value,
            condition: document.getElementById('condition').value,
            operator: document.getElementById('operator').value,
            threshold: document.getElementById('threshold').value
        };

        // Basic validation
        if (!data.city_name || !data.condition || !data.threshold) {
            alert('Please complete all required fields.');
            return;
        }

        // Store globally for confirm page
        window.createAlertData = data;

        // Load confirm page via dashboard loader
        fetch('/dashboard/load?page=create_alert_confirm', {
            credentials: 'same-origin'
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('dashboard-content').innerHTML = html;
        });
    });
});
