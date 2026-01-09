<section class="create-alert-page">

<form id="createAlertForm" novalidate>

    <div class="create-alert-header">
        <div>
            <h1>Create New Alert</h1>
            <p>Define where and what you want to monitor.</p>
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
                'temperature'   => ['🌡', 'Temperature', 'Heat or Freeze'],
                'precipitation' => ['💧', 'Precipitation', 'Rain or Snow'],
                'storm'         => ['🌩', 'Storm', 'Severe Weather'],
                'wind'          => ['🌬', 'Wind Speed', 'High Winds'],
                'uv'            => ['☀', 'UV Index', 'Sun Exposure'],
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
                   disabled
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
        <button type="submit"
                class="btn btn-primary">
            Next
        </button>
    </div>

</form>

</section>
