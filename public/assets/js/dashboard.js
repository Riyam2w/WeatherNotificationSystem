/* =========================================================
   DASHBOARD PAGE LOADER (AJAX)
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    const navItems = document.querySelectorAll(".nav-item");
    const content  = document.getElementById("dashboard-content");

    function loadPage(page, pushState = true) {
        fetch(`/dashboard/load?page=${page}`, {
            credentials: "same-origin"
        })
        .then(res => {
            if (!res.ok) throw new Error("Failed to load section");
            return res.text();
        })
        .then(html => {
            content.innerHTML = html;

            /* 🔴 IMPORTANT: re-initialize dynamic UI after AJAX load */
            initLocationDropdowns();

            // Update sidebar active state
            navItems.forEach(i => i.classList.remove("active"));
            const active = document.querySelector(`.nav-item[data-page="${page}"]`);
            if (active) active.classList.add("active");

            // Update browser URL
            if (pushState) {
                window.history.pushState({}, "", `/dashboard?page=${page}`);
            }
        })
        .catch(() => {
            content.innerHTML = "<p>Error loading section</p>";
        });
    }

    // Sidebar navigation click
    navItems.forEach(item => {
        item.addEventListener("click", (e) => {
            e.preventDefault();
            const page = item.dataset.page;
            if (!page) return;

            loadPage(page);
        });
    });

    // Browser back / forward
    window.addEventListener("popstate", () => {
        const params = new URLSearchParams(window.location.search);
        const page = params.get("page") || "alerts";
        loadPage(page, false);
    });

    // Initial load if URL has ?page=
    const initialPage = new URLSearchParams(window.location.search).get("page");
    if (initialPage) {
        loadPage(initialPage, false);
    }
});

/* =========================================================
   WEATHER CONDITION SELECTION
   ========================================================= */

document.addEventListener("click", (e) => {
    const card = e.target.closest(".condition-card");
    if (!card) return;

    e.preventDefault();

    document
        .querySelectorAll(".condition-card")
        .forEach(c => c.classList.remove("active"));

    card.classList.add("active");

    const hiddenInput = document.getElementById("selected-condition");
    if (hiddenInput) {
        hiddenInput.value = card.dataset.condition;
    }
});

/* =========================================================
   COUNTRY → STATE → CITY DROPDOWNS
   ========================================================= */

function initLocationDropdowns() {
    const countrySelect = document.getElementById("country");
    const stateSelect   = document.getElementById("state");
    const citySelect    = document.getElementById("city");

    if (!countrySelect || !stateSelect || !citySelect) return;

    countrySelect.innerHTML = '<option value="">Country</option>';
    stateSelect.innerHTML   = '<option value="">State</option>';
    citySelect.innerHTML    = '<option value="">City</option>';

    stateSelect.disabled = true;
    citySelect.disabled  = true;

    // Load countries
    fetch('/api/locations.php?type=countries')
        .then(res => res.json())
        .then(countries => {
            countries.forEach(c => {
                const opt = document.createElement("option");
                opt.value = c.iso2;
                opt.textContent = c.name;
                countrySelect.appendChild(opt);
            });
        });

    // Country → State
    countrySelect.onchange = () => {
        stateSelect.innerHTML = '<option value="">State</option>';
        citySelect.innerHTML  = '<option value="">City</option>';
        stateSelect.disabled = true;
        citySelect.disabled  = true;

        if (!countrySelect.value) return;

        fetch(`/api/locations.php?type=states&country=${countrySelect.value}`)
            .then(res => res.json())
            .then(states => {
                states.forEach(s => {
                    const opt = document.createElement("option");
                    opt.value = s.state_code;
                    opt.textContent = s.name;
                    stateSelect.appendChild(opt);
                });
                stateSelect.disabled = false;
            });
    };

    // State → City
    stateSelect.onchange = () => {
        citySelect.innerHTML = '<option value="">City</option>';
        citySelect.disabled = true;

        if (!stateSelect.value) return;

        fetch(`/api/locations.php?type=cities&country=${countrySelect.value}&state=${stateSelect.value}`)
            .then(res => res.json())
            .then(cities => {
                cities.forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.name;
                    opt.textContent = c.name;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
            });
    };
}
