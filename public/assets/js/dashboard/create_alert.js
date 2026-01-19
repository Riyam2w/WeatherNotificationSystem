const CONDITION_CONFIG = {
    temperature_above: {
        label: "Temperature",
        operators: [">"],
        units: ["°C", "°F"],
        step: 0.1,
        default: 30
    },
    temperature_below: {
        label: "Temperature",
        operators: ["<"],
        units: ["°C", "°F"],
        step: 0.1,
        default: 0
    },

    rain: {
        label: "Rainfall",
        operators: [">", "<"],
        units: ["mm"],
        step: 1,
        default: 40
    },
    wind: {
        label: "Wind Speed",
        operators: [">", "<"],
        units: ["km/h", "mph"],
        step: 1,
        default: 60
    },
    storm: {
        label: "Storm",
        operators: [">"],
        units: ["Severity Level"],
        step: 1,
        default: 3
    }

};

function initCreateAlertUI() {

    const form = document.getElementById("createAlertForm");
    if (!form || form.dataset.bound === "true") return;
    form.dataset.bound = "true";

    const cityInput = document.getElementById("citySearch");
    const cityResults = document.getElementById("cityResults");
    const cityNameInput = document.getElementById("city_name");
    const latInput = document.getElementById("lat");
    const lonInput = document.getElementById("lon");

    const conditionInput = document.getElementById("condition_type");
    const thresholdInput = document.getElementById("threshold");
    const operatorSelect = document.getElementById("operator");
    const unitSelect = document.getElementById("unit");
    const summary = document.getElementById("conditionSummary");

    thresholdInput.readOnly = true;
    operatorSelect.disabled = true;
    unitSelect.style.display = "none";

    function selectCity(city) {
        cityNameInput.value = city.name;
        latInput.value = city.lat;
        lonInput.value = city.lon;
        cityInput.value = city.name;
        cityResults.innerHTML = "";
    }

    /* ================== CITY AUTOCOMPLETE ================== */

    const OPENWEATHER_API_KEY = "c4e6dd84573d65a9b87404115c759ee7";
    let citySearchTimeout = null;

    cityInput.addEventListener("input", () => {
        const query = cityInput.value.trim();

        cityNameInput.value = "";
        latInput.value = "";
        lonInput.value = "";
        cityResults.innerHTML = "";

        if (query.length < 3) return;

        clearTimeout(citySearchTimeout);

        citySearchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(
                    `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(query)}&limit=5&appid=${OPENWEATHER_API_KEY}`
                );

                const cities = await res.json();
                cityResults.innerHTML = "";

                if (!Array.isArray(cities)) return;

                cities.forEach(city => {
                    const li = document.createElement("li");
                    li.className = "autocomplete-item";
                    li.textContent =
                        `${city.name}${city.state ? ", " + city.state : ""}, ${city.country}`;

                    li.addEventListener("click", () => {
                        selectCity({ name: city.name, lat: city.lat, lon: city.lon });
                    });

                    cityResults.appendChild(li);
                });

            } catch (err) {
                console.error("City search failed", err);
            }
        }, 300);
    });

    /* ================== CONDITION SELECTION ================== */

    document.querySelectorAll(".condition-card").forEach(card => {
        card.addEventListener("click", () => {
            document.querySelectorAll(".condition-card").forEach(c => c.classList.remove("active"));
            card.classList.add("active");

            const condition = card.dataset.condition;
            const config = CONDITION_CONFIG[condition];
            conditionInput.value = condition;

            operatorSelect.innerHTML = "";
            config.operators.forEach(op => {
                const option = document.createElement("option");
                option.value = op;
                option.textContent = op === ">" ? "Above (>)" : "Below (<)";
                operatorSelect.appendChild(option);
            });

            operatorSelect.disabled = false;

            unitSelect.innerHTML = "";
            config.units.forEach(unit => {
                const option = document.createElement("option");
                option.value = unit;
                option.textContent = unit;
                unitSelect.appendChild(option);
            });

            unitSelect.style.display = "inline-block";
            thresholdInput.step = config.step;
            thresholdInput.value = config.default;
            thresholdInput.readOnly = false;

            summary.innerHTML = `Alert when <strong>${config.label}</strong> is ${operatorSelect.value} ${thresholdInput.value} ${unitSelect.value}`;
        });
    });

    /* ================== SUBMIT ================== */

    form.addEventListener("submit", e => {
        e.preventDefault();

        if (!cityNameInput.value || !conditionInput.value) {
            Toast.warning("Please select a city and condition.");
            return;
        }

        const draft = {
            city_name: cityNameInput.value,
            lat: latInput.value,
            lon: lonInput.value,
            condition_type: conditionInput.value,
            operator: operatorSelect.value,
            threshold: thresholdInput.value,
            unit: unitSelect.value
        };

        sessionStorage.setItem("alertDraft", JSON.stringify(draft));

        // Load confirm page via dashboard loader
        fetch('/dashboard/load?page=create-alert-confirm', { credentials: 'same-origin' })
            .then(r => r.text())
            .then(html => {
                document.getElementById("dashboard-content").innerHTML = html;
                if (window.initCreateAlertConfirm) {
                    window.initCreateAlertConfirm();
                }
            });
    });
}

window.initCreateAlertUI = initCreateAlertUI;
