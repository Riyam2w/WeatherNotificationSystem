<section class="create-alert-page">

<form id="createAlertForm" novalidate>

    <div class="create-alert-header header-flex">
        <div>
            <h1>Create New Alert</h1>
            <p>Define where and what you want to monitor.</p>
        </div>
        <?php 
            $limit = $features['max_alerts'] ?? 3;
            $count = $alertCount ?? 0;
            $isLimitReached = $count >= $limit;
        ?>
        <div class="alert-usage usage-badge">
            <div class="usage-label">Plan Limit</div>
            <div class="usage-value <?= $isLimitReached ? 'limit-reached' : 'safe' ?>">
                <?= $count ?> / <?= $limit ?>
            </div>
            <div class="usage-sub">Alerts Used</div>
        </div>
    </div>

    <div class="form-section">
        <label class="section-title">📍 Location</label>

        <input type="text"
               id="citySearch"
               class="input"
               placeholder="Search city (e.g. Delhi, London)"
               autocomplete="on"
               required>

        <ul id="cityResults" class="autocomplete-list"></ul>

        <input type="hidden" id="city_name" name="city_name">
        <input type="hidden" id="lat" name="lat">
        <input type="hidden" id="lon" name="lon">

        <!-- <small>Start typing and select a city.</small> -->
    </div>

    <div class="form-section">
        <label class="section-title">🌦 Weather Condition</label>

        <!-- No default condition -->
        <input type="hidden" id="condition_type" name="condition_type">

        <div class="condition-grid">

            <?php
            $conditions = [
                'temperature_above'   => ['🌡', 'Temperature Above', 'Heat or Freeze'],
                'temperature_below'   => ['🌡', 'Temperature Below', 'Heat or Freeze'],
                'rain' => ['💧', 'Rainfall', 'Rain intensity'],
                'storm'         => ['🌩', 'Storm', 'Severe Weather'],
                'wind'          => ['🌬', 'Wind Speed', 'High Winds'],
            ];
            foreach ($conditions as $key => [$icon, $title, $desc]): ?>
                <button type="button"
                        class="condition-card"
                        data-condition="<?= $key ?>"
                        aria-pressed="false">
                    <?= $icon ?> <strong><?= $title ?></strong>
                    <span><?= $desc ?></span>
                </button>
            <?php endforeach; ?>

        </div>

        <div class="condition-summary" id="conditionSummary">
            Please select a weather condition to continue.
        </div>
    </div>
    <div class="form-section">
        <label class="section-title">⚙ Set Threshold</label>

        <div class="threshold-row">

            <select class="input"
                    id="operator"
                    name="operator"
                    disabled
                    required>
                <!-- <option value=">">Above (&gt;)</option>
                <option value="<">Below (&lt;)</option> -->
            </select>

            <input type="number"
                   class="input"
                   id="threshold"
                   name="threshold"
                   
                   required>

            <!-- <span class="unit"></span> -->
             <select 
             class="input"
              id="unit"
              name="unit"
              style="display:none"></select>
        </div>

        <small>
            Threshold becomes active after selecting a condition.
        </small>
    </div>

    <!-- ======================
         Actions
    ======================= -->
    <div class="form-actions">
        <?php if ($isLimitReached): ?>
            <div class="alert alert-danger w-100 mb-3 alert-limit-msg">
                <strong>Limit Reached:</strong> You have used all <?= $limit ?> alerts allowed in your plan. Please delete an existing alert or upgrade your plan to create more.
            </div>
        <?php endif; ?>
        <button type="submit"
                class="btn btn-primary <?= $isLimitReached ? 'limit-reached' : '' ?>" <?= $isLimitReached ? 'disabled' : '' ?>>
            <?= $isLimitReached ? 'Limit Reached' : 'Next' ?>
        </button>
    </div>

</form>

</section>
