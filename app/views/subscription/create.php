<!DOCTYPE html>
<html lang="en">
<head>
<?php  //$indexPagePath = __DIR__ . '/../app/';  ?>

    <meta charset="UTF-8">
    <title>Weather Alert Setup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="/public/assets/css/subscription.css">
    <base href="http://localhost:8000/public/" >

</head>
<body>
<div class="page-wrapper">
    <div class="center-content">
        <div class="card">
            <div class="card-header">
                <h3>Weather Alert</h3>
                <p>Get notified when weather conditions meet your criteria.</p>
            </div>
            <form id="subscriptionForm" method="POST" vovalidate>
                <div id="responseBox"></div>
                <div class="form-group">
                    <label>Email Address <span>*</span></label>
                    <input name="email" type="email" required placeholder="Where should we send alert?">
                </div>
                <div class="form-group">
                    <label>City <span>*</span></label>
                    <input name="city" type="text" required placeholder="City name (e.g., Delhi, IN)">
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Weather Condition <span>*</span></label>
                        <select name="condition_type" id="condition-select" required>
                            <option value="">Select condition</option>
                            <option value="temp_above">Temperature Above</option>
                            <option value="temp_below">Temperature Below</option>
                            <option value="rain_alert">Rain Alert</option>
                            <option value="humidity_above">Humidity Above</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Threshold <span>*</span></label>
                        <div class="threshold-group">
                            <select name="condition_operator">
                                <option value="gt">&gt;</option>
                                <option value="lt">&lt;</option>
                            </select>
                            <input name="condition_value" type="number" required placeholder="Value">
                            <div class="threshold-unit">Units</div>
                        </div>
                        <div class="help-text">
                            Alert triggers when condition exceeds this value.
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    Activate Alert
                </button>

            </form>

        </div>
    </div>
</div>

<script src="/public/assets/js/subscription.js"></script>
</body>
</html>
