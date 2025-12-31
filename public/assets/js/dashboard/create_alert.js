const CONDITION_CONFIG = {
    temperature: {
        unit: "°C",
        step: 0.1,
        default: 30,
        operators: [">", "<", "="]
    },
    precipitation: {
        unit: "mm",
        step: 1,
        default: 40,
        operators: [">"]
    },
    wind: {
        unit: "km/h",
        step: 1,
        default: 40,
        operators: [">"]
    },
    storm: {
        unit: "",
        step: 1,
        default: 1,
        operators: [">"]
    },
    uv: {
        unit: "",
        step: 1,
        default: 6,
        operators: [">"]
    }
};


function initCreateAlertUI() {

    const form = document.getElementById("createAlertForm");
    if (!form) return;

    if (form.dataset.bound === "true") return;
    form.dataset.bound = "true";

    const conditionInput = document.getElementById("condition");
    const thresholdInput = document.getElementById("threshold");
    const operatorSelect = document.getElementById("operator");
    const unitSpan       = document.querySelector(".unit");
    const summary        = document.getElementById("conditionSummary");

    // Initial neutral state
   
    thresholdInput.disabled = true;
    operatorSelect.disabled = true;
    if (unitSpan) unitSpan.textContent = "";

    /* -----------------------------------------
       Condition selection
    ----------------------------------------- */
    document.querySelectorAll(".condition-card").forEach(card => {

        card.addEventListener("click", () => {

            document.querySelectorAll(".condition-card").forEach(c => {
                c.classList.remove("active");
                c.setAttribute("aria-pressed", "false");
            });

            card.classList.add("active");
            card.setAttribute("aria-pressed", "true");

            const condition = card.dataset.condition;
            conditionInput.value = condition;

            thresholdInput.disabled = false;
            operatorSelect.disabled = false;

            updateThresholdUI(condition);

            if (summary) {
                summary.innerHTML =
                    `Alert will trigger based on <strong>${condition}</strong> conditions.`;
            }
        });
    });

    /* -----------------------------------------
       Submit → Confirmation page
    ----------------------------------------- */
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        
        const required = ['city_name', 'lat', 'lon'];
        for (const field of required) {
            if (!form[field] || form[field].value === "") {
                alert("Please select a valid city first.");
                return;
            }
        }
        if (!conditionInput.value || thresholdInput.value === "") {
            alert("Please select a condition and threshold.");
            return;
        }

        const formData = new FormData(form);
        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) {
            formData.append('csrf_token', csrf.content);
        }
        
        fetch("/dashboard/load?page=create_alert_confirm", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        })
        .then(res => {
            if (!res.ok) throw new Error("Failed to load confirmation");
            return res.text();
        })
        .then(html => {
            const container = document.getElementById("dashboard-content");
            if (!container) throw new Error("Dashboard container missing");
            container.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong. Please try again.");
        });
    });
}

/* =========================================================
   Threshold UI updater
   ========================================================= */

function updateThresholdUI(condition) {

    const config = CONDITION_CONFIG[condition];
    if (!config) return;

    const thresholdInput = document.getElementById("threshold");
    const operatorSelect = document.getElementById("operator");
    const unitSpan       = document.querySelector(".unit");

    if (unitSpan) unitSpan.textContent = config.unit;

    thresholdInput.step  = config.step;
    thresholdInput.value = config.default;

    operatorSelect.innerHTML = "";
    config.operators.forEach(op => {
        const option = document.createElement("option");
        option.value = op;
        option.textContent =
            op === ">" ? "Above (>)" :
            op === "<" ? "Below (<)" :
            "Equals (=)";
        operatorSelect.appendChild(option);
    });
}
