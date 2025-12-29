document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("billingToggle");

    if (!toggle) return;

    toggle.addEventListener("change", () => {
        const yearly = toggle.checked;

        document.querySelectorAll(".price").forEach(price => {
            const value = yearly
                ? price.dataset.yearly
                : price.dataset.monthly;

            price.innerHTML = `₹${value} <span>/${yearly ? "year" : "month"}</span>`;
        });
    });
});
