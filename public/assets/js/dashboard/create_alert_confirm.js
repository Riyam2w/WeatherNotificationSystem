document.addEventListener('DOMContentLoaded', () => {
    const data = window.createAlertData;
    if (!data) {
        alert('Alert data missing. Please go back.');
        return;
    }

    const cityEl = document.getElementById('summaryCity');
    const conditionEl = document.getElementById('summaryCondition');
    const thresholdEl = document.getElementById('summaryThreshold');
    
    if (cityEl) cityEl.textContent = data.city_name;
    if (conditionEl) {
        conditionEl.textContent = 
            `${data.condition} ${data.operator} ${data.threshold}`;
    }
    if (thresholdEl) {
        thresholdEl.textContent = 
            `${data.operator} ${data.threshold}`;
    }
    const backBtn = document.getElementById('backBtn');
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            fetch('/dashboard/load?page=create_alert', {
                credentials: 'same-origin'
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('dashboard-content').innerHTML = html;

            }); 
        });
    }

    const form = document.getElementById('confirmAlertForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content || '';
    // 
        const payload = {
            ...data,
            alert_name: document.getElementById('alert_name')?.value || ''
        };

        try {
            const res = await fetch('/alerts/create', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                },
                body: JSON.stringify(payload)
            });

            const json = await res.json();

            if (!res.ok || !json.success) {
                alert(json.message || 'Failed to create alert');
                return;
            }

            // Load alerts list (NO full reload)
            const alertsRes = await fetch('/dashboard/load?page=alerts', {
                credentials: 'same-origin'
            });
            const alertsHtml = await alertsRes.text();
            document.getElementById('dashboard-content').innerHTML = alertsHtml;

        } catch (err) {
            alert('Network error. Please try again.');
        }
    });
});