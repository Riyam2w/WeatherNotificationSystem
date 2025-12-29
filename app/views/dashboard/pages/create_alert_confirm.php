<?php
// app/views/dashboard/create_alert_confirm.php
?>

<section class="confirm-alert-page">

    <h3>Configuration Summary</h3>

    <div class="summary-grid">
        <div>
            <strong>Location:</strong>
            <?= htmlspecialchars($_POST['city_name'] ?? '') ?>
        </div>

        <div>
            <strong>Condition:</strong>
            <?= ucfirst(htmlspecialchars($_POST['condition'] ?? '')) ?>
        </div>

        <div>
            <strong>Threshold:</strong>
            <?= htmlspecialchars($_POST['operator'] ?? '') ?>
            <?= htmlspecialchars($_POST['threshold'] ?? '') ?>
        </div>
    </div>

    <!-- IMPORTANT: no action attribute -->
    <form id="confirmForm">

        <label>Alert Name <span class="optional">(Optional)</span></label>
        <input type="text"
               name="alert_name"
               placeholder="e.g. Heatwave Warning">

        <!-- Pass data forward -->
        <?php foreach ($_POST as $k => $v): ?>
            <input type="hidden"
                   name="<?= htmlspecialchars($k) ?>"
                   value="<?= htmlspecialchars($v) ?>">
        <?php endforeach; ?>

        <div class="form-actions">
            <button type="button"
                    class="btn btn-light"
                    onclick="history.back()">
                ← Back
            </button>

            <button type="button"
                    id="confirmCreateAlert"
                    class="btn btn-primary">
                Create Alert
            </button>
        </div>

    </form>

</section>
