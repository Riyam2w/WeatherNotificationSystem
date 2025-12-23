document.addEventListener("DOMContentLoaded", () => {
    const navItems = document.querySelectorAll(".nav-item");
    const content = document.getElementById("dashboard-content");

    navItems.forEach(item => {
        item.addEventListener("click", (e) => {
            e.preventDefault();

            const page = item.dataset.page;
            if (!page) return;

            // UI: active state
            navItems.forEach(i => i.classList.remove("active"));
            item.classList.add("active");

            // Load content
            fetch(`/dashboard-section.php?page=${page}`, {
                credentials: "same-origin"
            })
            .then(res => {
                if (!res.ok) throw new Error("Failed to load section");
                return res.text();
            })
            .then(html => {
                content.innerHTML = html;
                window.history.pushState({}, "", `/dashboard?page=${page}`);
            })
            .catch(() => {
                content.innerHTML = "<p>Error loading section</p>";
            });
        });
    });

    // Handle browser back/forward
    window.addEventListener("popstate", () => {
        const params = new URLSearchParams(window.location.search);
        const page = params.get("page") || "overview";

        fetch(`/dashboard-section.php?page=${page}`)
            .then(res => res.text())
            .then(html => content.innerHTML = html);
    });
});
