/* =========================================================
   DASHBOARD PAGE LOADER (AJAX)
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    const navItems = document.querySelectorAll(".nav-item");
    const content  = document.getElementById("dashboard-content");

    function loadPage(page, pushState = true) {
        fetch(`/dashboard/load?page=${encodeURIComponent(page)}`, {
            credentials: "same-origin"
        })
        .then(res => {
            if (!res.ok) throw new Error("Failed to load section");
            return res.text();
        })
        .then(html => {
            content.innerHTML = html;

            // Re-init dynamic features
            initCityAutocomplete();
            initCreateAlertForm();

            // Sidebar active state
            navItems.forEach(i => i.classList.remove("active"));
            const active = document.querySelector(`.nav-item[data-page="${page}"]`);
            if (active) active.classList.add("active");

            if (pushState) {
                history.pushState({}, "", `/dashboard?page=${page}`);
            }
        })
        .catch(() => {
            content.innerHTML = "<p>Error loading section</p>";
        });
    }

    navItems.forEach(item => {
        item.addEventListener("click", e => {
            e.preventDefault();
            const page = item.dataset.page;
            if (page) loadPage(page);
        });
    });

    window.addEventListener("popstate", () => {
        const page = new URLSearchParams(location.search).get("page") || "alerts";
        loadPage(page, false);
    });

    const initialPage = new URLSearchParams(location.search).get("page");
    if (initialPage) loadPage(initialPage, false);
});


/* =========================================================
   WEATHER CONDITION SELECTION
   ========================================================= */

document.addEventListener("click", e => {
    const card = e.target.closest(".condition-card");
    if (!card) return;

    e.preventDefault();

    document.querySelectorAll(".condition-card")
        .forEach(c => c.classList.remove("active"));

    card.classList.add("active");

    const hiddenInput = document.getElementById("selected-condition");
    if (hiddenInput) {
        hiddenInput.value = card.dataset.condition;
    }
});


/* =========================================================
   CITY AUTOCOMPLETE (OpenWeather GEO API)
   ========================================================= */

function initCityAutocomplete() {

    const API_KEY = "c4e6dd84573d65a9b87404115c759ee7";

    const input = document.getElementById("citySearch");
    const list  = document.getElementById("cityResults");

    if (!input || !list) return;
    if (input.dataset.initialized === "true") return;
    input.dataset.initialized = "true";

    const cityNameInput = document.getElementById("city_name");
    const latInput      = document.getElementById("lat");
    const lonInput      = document.getElementById("lon");

    let debounceTimer = null;

    input.addEventListener("input", () => {
        const query = input.value.trim();
        list.innerHTML = "";

        if (query.length < 3) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch(
                `https://api.openweathermap.org/geo/1.0/direct?q=${encodeURIComponent(query)}&limit=5&appid=${API_KEY}`
            )
            .then(res => res.json())
            .then(data => {
                list.innerHTML = "";
                if (!Array.isArray(data)) return;

                data.forEach(city => {
                    const li = document.createElement("li");
                    li.textContent =
                        `${city.name}${city.state ? ", " + city.state : ""}, ${city.country}`;

                    li.addEventListener("click", () => {
                        input.value = li.textContent;
                        cityNameInput.value = city.name;
                        latInput.value = city.lat;
                        lonInput.value = city.lon;
                        list.innerHTML = "";
                    });

                    list.appendChild(li);
                });
            })
            .catch(console.error);
        }, 300);
    });

    document.addEventListener("click", e => {
        if (!e.target.closest(".form-section")) {
            list.innerHTML = "";
        }
    });
}


/* =========================================================
   CREATE ALERT → CONFIRM (AJAX POST)
   ========================================================= */

function initCreateAlertForm() {

    const form = document.querySelector(".create-alert-page form");
    if (!form || form.dataset.bound === "true") return;

    form.dataset.bound = "true";

    form.addEventListener("submit", e => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("page", "create_alert_confirm");

        fetch("/dashboard/load", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById("dashboard-content").innerHTML = html;
            initCreateAlertConfirm();
        })
        .catch(() => {
            alert("Failed to load confirmation page");
        });
    });
}


/* =========================================================
   CONFIRM PAGE → STORE ALERT (FIXED)
   ========================================================= */

function initCreateAlertConfirm() {

    const btn  = document.getElementById("confirmCreateAlert");
    const form = document.getElementById("confirmForm");

    if (!btn || !form) return;
    if (btn.dataset.bound === "true") return;

    btn.dataset.bound = "true";

    btn.addEventListener("click", () => {

        const data = new FormData(form);
        data.append("page", "create-alert-store");

        fetch("/dashboard/load", {
            method: "POST",
            body: data,
            credentials: "same-origin"
        })
        .then(res => {
            if (!res.ok) throw new Error("Insert failed");
            window.location.href = "/dashboard?page=alerts";
        })
        .catch(err => {
            console.error(err);
            alert("Failed to create alert");
        });
    });
}
