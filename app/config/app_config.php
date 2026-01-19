<?php
declare(strict_types=1);

return [
    'weather_api' => [
        'key' => $_ENV['WEATHER_API_KEY'] ?? '',
        'url' => $_ENV['WEATHER_API_URL'] ?? 'https://api.openweathermap.org/data/2.5/weather',
    ],

    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'port' => (int) ($_ENV['MAIL_PORT'] ?? 8000),
        'auth' => filter_var($_ENV['MAIL_AUTH'] ?? false, FILTER_VALIDATE_BOOL),
        'from_email' => $_ENV['MAIL_FROM_EMAIL'] ?? 'alerts@weather.system',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Weather Alerts',
    ],

    'app' => [
        'alert_cooldown_hours' => (int) ($_ENV['ALERT_COOLDOWN_HOURS'] ?? 1),
    ],
    'stripe' => [
    'secret_key'      => $_ENV['STRIPE_SECRET_KEY'] ?? '',
    'publishable_key' => $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '',
    'currency'        => $_ENV['STRIPE_CURRENCY'] ?? 'inr',
],
];
