<section class="create-alert-page">

<form id="createAlertForm" novalidate>

    <!-- Header -->
    <div class="create-alert-header">
        <div>
            <h1>Create New Alert</h1>
            <p>Define where and what you want to monitor.</p>
        </div>
    </div>

    <!-- Location -->
    <div class="form-section">
        <label class="section-title">📍 Location</label>

        <input type="text"
               id="citySearch"
               class="input"
               placeholder="Search city (e.g. Delhi, London)"
               autocomplete="off"
               required>

        <ul id="cityResults" class="autocomplete-list"></ul>

        <!-- Hidden fields -->
        <input type="hidden" id="city_name">
        <input type="hidden" id="lat">
        <input type="hidden" id="lon">

        <small>Start typing and select a city.</small>
    </div>

    <!-- Weather Condition -->
    <div class="form-section">
        <label class="section-title">🌦 Weather Condition</label>

        <input type="hidden" id="condition" value="temperature">

        <div class="condition-grid">
            <button type="button" 
            class="condition-card active" 
            data-condition="temperature"
            aria-pressed="false">
                🌡 <strong>Temperature</strong>
                <span>Heat or Freeze</span>
            </button>

            <button type="button" 
            class="condition-card" 
            data-condition="precipitation"
            aria-pressed="false">
                💧 <strong>Precipitation</strong>
                <span>Rain or Snow</span>
            </button>

            <button type="button" 
            class="condition-card" 
            data-condition="storm"
            aria-pressed="false">
                🌩 <strong>Storm</strong>
                <span>Severe Weather</span>
            </button>

            <button type="button" 
            class="condition-card" 
            data-condition="wind"
            aria-pressed="false">
                🌬 <strong>Wind Speed</strong>
                <span>High Winds</span>
            </button>

            <button type="button" class="condition-card" data-condition="uv" aria-pressed="false">
                ☀ <strong>UV Index</strong>
                <span>Sun Exposure</span>
            </button>
        </div>
        <div class="condition-summary" id="conditionSummary">
            Alert will trigger based on <strong>temperature</strong> conditions.
    </div>
    </div>
    
    
    <!-- Threshold -->
    <div class="form-section">
        <label class="section-title" id="thresholdTitle">⚙ Set Threshold</label>

        <div class="threshold-row" id="thresholdRow">
            <select class="input" id="operator" required>
                <option value=">">Above (>)</option>
                <option value="<">Below (&lt;)</option>
            </select>

            <input type="number"
                   class="input"
                   id="threshold"
                   value="30"
                   step="0.1"
                   required>

            <span class="unit">°C</span>
        </div>

        <small>
            You will be notified immediately when the condition is met.
        </small>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <button type="submit" id="nextCreateAlertBtn" class="btn btn-primary">
            Next
        </button>
    </div>

</form>
</section>
