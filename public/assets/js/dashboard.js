/* =========================================================
   DASHBOARD PAGE LOADER (AJAX)
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    const navItems = document.querySelectorAll(".nav-item");
    const content  = document.getElementById("dashboard-content");

    if (!content) return;

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

            /* Re-initialize dynamic features AFTER AJAX load */
            if (typeof initCityAutocomplete === "function") {
                initCityAutocomplete();
            }
            if (typeof initCreateAlertUI === "function") {
                initCreateAlertUI();
            }
            if (typeof initCreateAlertForm === "function") {
                initCreateAlertForm();
            }
            if (typeof initConfirmPreview === "function") {
                initConfirmPreview();
            }

            /* Sidebar active state */
            navItems.forEach(i => i.classList.remove("active"));
            const active = document.querySelector(`.nav-item[data-page="${page}"]`);
            if (active) active.classList.add("active");

            if (pushState) {
                history.pushState({}, "", `/dashboard?page=${page}`);
            }
        })
        .catch(() => {
            content.innerHTML = "<p class='error'>Error loading section</p>";
        });
    }

    /* Sidebar navigation */
    navItems.forEach(item => {
        item.addEventListener("click", e => {
            e.preventDefault();
            const page = item.dataset.page;
            if (page) loadPage(page);
        });
    });

    /* Browser back / forward */
    window.addEventListener("popstate", () => {
        const page = new URLSearchParams(location.search).get("page") || "alerts";
        loadPage(page, false);
    });

    /* Initial page load */
    const initialPage = new URLSearchParams(location.search).get("page");
    if (initialPage) {
        loadPage(initialPage, false);
    }

    /* Expose loader globally */
    window.loadDashboardPage = loadPage;
});

/* =========================================================
   CREATE ALERT BUTTON (OPEN CREATE PAGE)
   ========================================================= */

document.addEventListener("click", e => {
    const btn = e.target.closest("#createAlertBtn");
    if (!btn) return;

    e.preventDefault();
    if (typeof window.loadDashboardPage === "function") {
        window.loadDashboardPage("create-alert");
    }
});

/* =========================================================
   CREATE ALERT → NEXT → CONFIRM (SAVE DATA)
   ========================================================= */

document.addEventListener("click", e => {

    const nextBtn = e.target.closest("#nextCreateAlertBtn");
    if (!nextBtn) return;

    e.preventDefault();

    const form = document.getElementById("createAlertForm");
    if (!form) return;

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    /* Save form data for preview */
    const data = {};
    new FormData(form).forEach((value, key) => {
        data[key] = value;
    });

    sessionStorage.setItem("createAlertData", JSON.stringify(data));

    if (typeof window.loadDashboardPage === "function") {
        window.loadDashboardPage("create-alert-confirm");
    }
});

/* =========================================================
   CITY AUTOCOMPLETE (OpenWeather GEO API)
   ========================================================= */

function initCityAutocomplete() {

    const API_KEY = "c4e6dd84573d65a9b87404115c759ee7";

    const input = document.getElementById("citySearch");
    const list  = document.getElementById("cityResults");

    if (!input || !list || input.dataset.initialized === "true") return;
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
            .catch(() => {});
        }, 300);
    });

    document.addEventListener("click", e => {
        if (!e.target.closest(".form-section")) {
            list.innerHTML = "";
        }
    });
}

/* =========================================================
   CONFIRM PAGE PREVIEW
   ========================================================= */

function initConfirmPreview() {

    const data = sessionStorage.getItem("createAlertData");
    if (!data) return;

    const alert = JSON.parse(data);

    const cityEl      = document.getElementById("preview-city");
    const conditionEl = document.getElementById("preview-condition");
    const thresholdEl = document.getElementById("preview-threshold");

    if (cityEl) {
        cityEl.textContent = alert.city_name || "—";
    }
    if (conditionEl) {
        conditionEl.textContent = alert.condition_type || "—";
    }
    if (thresholdEl) {
        thresholdEl.textContent =
            alert.threshold && alert.unit
                ? `${alert.threshold} ${alert.unit}`
                : "—";
    }
}

/* =========================================================
   CREATE ALERT FORM (FINAL API SUBMIT)
   ========================================================= */

function initCreateAlertForm() {

    const form = document.querySelector(".create-alert-page form");
    if (!form || form.dataset.bound === "true") return;

    form.dataset.bound = "true";

    form.addEventListener("submit", e => {

        e.preventDefault();

        const formData = new FormData(form);

        fetch("/api/alerts/create", {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        })
        .then(res => {
            if (!res.ok) throw new Error("Failed");
            return res.json();
        })
        .then(() => {
            sessionStorage.removeItem("createAlertData");
            window.loadDashboardPage("alerts");
        })
        .catch(() => {
            alert("Failed to create alert. Please try again.");
        });
    });
}
