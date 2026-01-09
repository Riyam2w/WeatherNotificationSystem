const CONDITION_CONFIG = {
    temperature: {
        label: "Temperature",
        units: ["°C", "°F"],
        defaultUnit: "°C",
        step: 0.1,
        default: 30,
        operators: [">", "<"]
    },
    precipitation: {
        label: "Precipitation",
        units: ["mm"],
        step: 1,
        default: 40,
        operators: [">"]
    },
    wind: {
        label: "Wind Speed",
        units: ["km/h"],
        step: 1,
        default: 40,
        operators: [">"]
    },
    storm: {
        label: "Storm Severity",
        units: ["level"],
        step: 1,
        default: 1,
        operators: [">"]
    },
    uv: {
        label: "UV Index",
        units: ["index"],
        step: 1,
        default: 6,
        operators: [">"]
    }
};

function initCreateAlertUI() {

    const form = document.getElementById("createAlertForm");
    if (!form || form.dataset.bound === "true") return;
    form.dataset.bound = "true";

    const conditionInput = document.getElementById("condition_type");
    const thresholdInput = document.getElementById("threshold");
    const operatorSelect = document.getElementById("operator");
    const unitSelect     = document.getElementById("unit");
    const summary        = document.getElementById("conditionSummary");

    thresholdInput.disabled = true;
    operatorSelect.disabled = true;
    unitSelect.style.display = "none";

    document.querySelectorAll(".condition-card").forEach(card => {
        card.addEventListener("click", () => {

            document.querySelectorAll(".condition-card").forEach(c => {
                c.classList.remove("active");
                c.setAttribute("aria-pressed", "false");
            });

            card.classList.add("active");
            card.setAttribute("aria-pressed", "true");

            const condition = card.dataset.condition;
            const config = CONDITION_CONFIG[condition];

            conditionInput.value = condition;
            thresholdInput.disabled = true;

            /* ---------- Operator ---------- */
            operatorSelect.innerHTML = "";
            config.operators.forEach(op => {
                const option = document.createElement("option");
                option.value = op;
                option.textContent = op === ">" ? "Above (>)" : "Below (<)";
                operatorSelect.appendChild(option);
            });

            if (config.operators.length === 1) {
                operatorSelect.value = config.operators[0];
                operatorSelect.disabled = true;
            } else {
                operatorSelect.disabled = false;
            }

            /* ---------- Unit ---------- */
            unitSelect.innerHTML = "";
            config.units.forEach(unit => {
                const option = document.createElement("option");
                option.value = unit;
                option.textContent = unit;
                unitSelect.appendChild(option);
            });

            unitSelect.style.display = "inline-block";
            unitSelect.value = config.defaultUnit || config.units[0];

            /* ---------- Threshold ---------- */
            thresholdInput.step  = config.step;
            thresholdInput.value = config.default;

            /* ---------- Summary ---------- */
            if (summary) {
                summary.innerHTML =
                    `Alert when <strong>${config.label}</strong> is ${operatorSelect.value} threshold`;
            }
        });
    });

    form.addEventListener("submit", e => {

        const required = ["city_name", "lat", "lon"];
        for (const field of required) {
            if (!form[field] || form[field].value === "") {
                e.preventDefault();
                alert("Please select a valid city first.");
                return;
            }
        }

        if (
            !conditionInput.value ||
            !thresholdInput.value ||
            !operatorSelect.value ||
            !unitSelect.value
        ) {
            e.preventDefault();
            alert("Please complete all alert fields.");
        }
    });
}   