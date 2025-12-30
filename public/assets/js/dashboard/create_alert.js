const CONDITION_CONFIG = {
    temperature: {
        unit: "°C",
        step: 0.1,
        default: 30,
        operators: [">", "<"]
    },
    precipitation: {
        unit: "mm",
        step: 1,
        default: 40,
        operators: [">"]
    },
    uv: {
        unit: "",
        step: 1,
        default: 6,
        operators: [">"]
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('createAlertForm');
    if (!form) return;

    // Condition selection
    document.querySelectorAll('.condition-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.condition-card')
                .forEach(c => c.classList.remove('active'));

            card.classList.add('active');

            const condition = card.dataset.condition;
            document.getElementById('condition').value = condition;

            updateThresholdUI(condition);
        });
    });

    // Initialize default state
    updateThresholdUI('temperature');

    // Submit handler
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const thresholdValue = document.getElementById('threshold').value;

        const data = {
            city_name: document.getElementById('city_name').value,
            lat: document.getElementById('lat').value,
            lon: document.getElementById('lon').value,
            condition: document.getElementById('condition').value,
            operator: document.getElementById('operator').value,
            threshold: Number(thresholdValue)
        };

        if (!data.city_name || !data.condition || thresholdValue === '') {
            alert('Please complete all required fields.');
            return;
        }

        window.createAlertData = data;

        fetch('/dashboard/load?page=create_alert_confirm', {
            credentials: 'same-origin'
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('dashboard-content').innerHTML = html;
        });
    });
});

function updateThresholdUI(condition) {
    const config = CONDITION_CONFIG[condition];
    if (!config) return;

    const thresholdInput = document.getElementById('threshold');
    const operatorSelect = document.getElementById('operator');
    const unitSpan = document.querySelector('.unit');

    if (unitSpan) {
        unitSpan.textContent = config.unit;
    }

    thresholdInput.step = config.step;
    thresholdInput.value = config.default;
    thresholdInput.disabled = false;

    operatorSelect.innerHTML = '';
    config.operators.forEach(op => {
        const option = document.createElement('option');
        option.value = op;
        option.textContent =
            op === '>' ? 'Above (>)' :
            op === '<' ? 'Below (<)' :
            'Equals (=)';
        operatorSelect.appendChild(option);
    });
}
