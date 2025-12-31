document.addEventListener("DOMContentLoaded", () => {

    const content = document.getElementById("dashboard-content");
    if (!content) return;

    function loadDashboardPage(page, pushState = true) {
        fetch(`/dashboard/load?page=${encodeURIComponent(page)}`, {
            credentials: "same-origin"
        })
        .then(res => {
            if (!res.ok) throw new Error("Failed to load page");
            return res.text();
        })
        .then(html => {
            content.innerHTML = html;

            // 🔁 Initialize page-specific JS
            if (page === "alerts" && window.initAlerts) {
                window.initAlerts();
            }

            if (page === "create-alert" && window.initCreateAlert) {
                window.initCreateAlert();
            }

            if (page === "create-alert-confirm" && window.initCreateAlertConfirm) {
                window.initCreateAlertConfirm();
            }

            if (pushState) {
                history.pushState({}, "", `/dashboard?page=${page}`);
            }
        })
        .catch(() => {
            content.innerHTML = "<p class='error'>Failed to load content</p>";
        });
    }

    // Browser navigation
    window.addEventListener("popstate", () => {
        const page = new URLSearchParams(location.search).get("page") || "overview";
        loadDashboardPage(page, false);
    });

    // Initial load
    const initialPage = new URLSearchParams(location.search).get("page");
    if (initialPage) {
        loadDashboardPage(initialPage, false);
    }

    // Expose globally
    window.loadDashboardPage = loadDashboardPage;
});
