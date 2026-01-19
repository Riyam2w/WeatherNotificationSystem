function initCreateAlertConfirm() {

    const summaryContainer = document.getElementById("alertSummary");
    if (!summaryContainer) return;

    /* ---------------- Load Draft ---------------- */
    const draftRaw = sessionStorage.getItem("alertDraft");
    if (!draftRaw) {
        Toast.error("Alert data missing. Please start again.");
        return;
    }

    let draft;
    try {
        draft = JSON.parse(draftRaw);
    } catch {
        Toast.error("Invalid alert data. Please start again.");
        sessionStorage.removeItem("alertDraft");
        return;
    }

    /* ---------------- Render Summary ---------------- */
    summaryContainer.innerHTML = `
        <div><strong>City:</strong> ${draft.city_name}</div>
        <div><strong>Condition:</strong> ${draft.condition_type}</div>
        <div><strong>Threshold:</strong> ${draft.operator} ${draft.threshold} ${draft.unit}</div>
    `;

    /* ---------------- Back Button ---------------- */
    const backBtn = document.getElementById("backToCreate");
    if (backBtn) {
        backBtn.addEventListener("click", async () => {
            try {
                const res = await fetch("/dashboard/load?page=create-alert", {
                    credentials: "same-origin"
                });

                if (!res.ok) {
                    throw new Error("Failed to load create alert page");
                }

                document.getElementById("dashboard-content").innerHTML =
                    await res.text();

                if (window.initCreateAlertUI) {
                    window.initCreateAlertUI();
                }

            } catch (err) {
                Toast.error("Failed to go back. Please refresh.");
            }
        });
    }

    /* ---------------- Confirm Create ---------------- */
    const confirmBtn = document.getElementById("confirmCreateAlert");
    if (!confirmBtn) return;

    confirmBtn.addEventListener("click", async () => {

        const alertNameInput = document.getElementById("alertName");
        if (alertNameInput && alertNameInput.value.trim()) {
            draft.alert_name = alertNameInput.value.trim();
        }
        const CONDITION_MAP = {
            temperature_above: 1,
            temperature_below: 2,
            rain: 3,
            storm: 4,
            wind: 5
        };
        draft.condition_id = CONDITION_MAP[draft.condition_type];

        if (!draft.condition_id) {
            Toast.error("Invalid condition type. Please go back and try again.");
            confirmBtn.disabled = false;
            confirmBtn.textContent = "Create Alert";
            return;
        }
        delete draft.condition_type;

        confirmBtn.disabled = true;
        confirmBtn.textContent = "Creating...";

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            const res = await fetch("/api/alerts/create", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(draft)
            });

            const json = await res.json();

            if (!res.ok || !json.success) {
                confirmBtn.disabled = false;
                confirmBtn.textContent = "Create Alert";

                const erBox = document.getElementById("alertError");
                if (erBox) {
                    erBox.textContent = json.message || "Failed to create alert";
                    erBox.classList.remove("d-none");
                    erBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    Toast.error(json.message || "Failed to create alert");
                }
                return;
            }

            /* ---------------- Success ---------------- */
            sessionStorage.removeItem("alertDraft");

            const alertsRes = await fetch("/dashboard/load?page=alerts", {
                credentials: "same-origin"
            });

            if (!alertsRes.ok) {
                throw new Error("Failed to load alerts");
            }

            document.getElementById("dashboard-content").innerHTML =
                await alertsRes.text();

        } catch (err) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = "Create Alert";
            Toast.error("Network error. Please try again.");
        }
    });
}

/* -------------------------------------------------
   REQUIRED: expose initializer for SPA
------------------------------------------------- */
window.initCreateAlertConfirm = initCreateAlertConfirm;
