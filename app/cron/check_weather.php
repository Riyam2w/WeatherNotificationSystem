<?php
declare(strict_types=1);

/**
 * Weather System Cron Job
 * Run this script to check active weather alerts and trigger notifications.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../services/WeatherService.php';
require_once __DIR__ . '/../services/MailService.php';

// Disable error reporting to screen, log to file if needed
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

$db = new Database();
$conn = $db->conn;

if (!$conn) {
    die("Database connection failed.\n");
}

$weatherService = new WeatherService($conn);
$mailService = new MailService($conn);

$cronJobName = 'check_weather';
$status = 'success';
$message = '';

try {
    $config = require __DIR__ . '/../config/app_config.php';
    $cooldownHours = $config['app']['alert_cooldown_hours'] ?? 1;

    // 1. Fetch all active alerts (or recently triggered ones that might need re-notification)
    $sql = "SELECT a.*, c.name as city_name, c.lat, c.lon, u.email, ac.label as condition_label, s.id as subscription_id
            FROM alerts a 
            JOIN cities c ON a.city_id = c.id 
            JOIN users u ON a.user_id = u.id
            JOIN alert_conditions ac ON a.condition_id = ac.id
            LEFT JOIN subscriptions s ON u.id = s.user_id AND s.status IN ('active', 'trial')
            WHERE a.status = 'active'";
    
    $result = $conn->query($sql);
    $alerts = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($alerts)) {
        $message = "No active alerts to process.";
    } else {
        $processedCities = [];
        $triggeredCount = 0;

        foreach ($alerts as $alert) {
            // Cooldown check: Don't notify if notified within the last X hours
            if ($alert['last_notified_at']) {
                $lastNotified = new DateTime($alert['last_notified_at']);
                $now = new DateTime();
                $diff = $now->getTimestamp() - $lastNotified->getTimestamp();
                if ($diff < ($cooldownHours * 3600)) {
                    continue; // Skip if in cooldown
                }
            }

            $cityId = (int)$alert['city_id'];
            $lat = (float)$alert['lat'];
            $lon = (float)$alert['lon'];

            // Fetch live weather data for the city (cached per run)
            if (!isset($processedCities[$cityId])) {
                $weatherData = $weatherService->fetchCurrentWeather($lat, $lon);
                if ($weatherData) {
                    $weatherService->updateWeatherData($cityId, $weatherData);
                    $processedCities[$cityId] = $weatherData;
                } else {
                    error_log("Failed to fetch weather for city ID: $cityId");
                    continue;
                }
            }

            $currentData = $processedCities[$cityId];
            $currentValue = null;

            // Map alert_conditions
            $conditionId = (int)$alert['condition_id'];
            
            switch ($conditionId) {
                case 1: // Temperature Above
                case 2: // Temperature Below
                    $currentValue = $currentData['main']['temp'] ?? null;
                    break;
                case 3: // Rainfall
                    $currentValue = isset($currentData['rain']['1h']) ? $currentData['rain']['1h'] : (isset($currentData['rain']['3h']) ? $currentData['rain']['3h'] : 0);
                    break;
                case 5: // Wind Speed
                    $currentValue = $currentData['wind']['speed'] ?? null;
                    break;
            }

            if ($currentValue === null) continue;

            $threshold = (float)$alert['threshold_value'];
            $operator = $alert['operator'];
            $isTriggered = false;

            switch ($operator) {
                case '>':  $isTriggered = ($currentValue > $threshold); break;
                case '<':  $isTriggered = ($currentValue < $threshold); break;
                case '>=': $isTriggered = ($currentValue >= $threshold); break;
                case '<=': $isTriggered = ($currentValue <= $threshold); break;
                case '=':  $isTriggered = ($currentValue == $threshold); break;
            }

            if ($isTriggered) {
                // Update last_notified_at to avoid immediate duplicate emails
                $stmt = $conn->prepare("UPDATE alerts SET last_notified_at = NOW(), updated_at = NOW() WHERE id = ?");
                $stmt->bind_param("i", $alert['id']);
                $stmt->execute();
                
                // Send Email Notification
                $alert['current_value'] = $currentValue;
                $alert['notif_sub_id'] = $alert['subscription_id'] ?? null; // Allow NULL
                
                $mailService->sendWeatherAlert($alert['email'], $alert, (int)$alert['user_id']);
                
                error_log("Alert triggered and email processed for user {$alert['user_id']} in {$alert['city_name']}: $currentValue $operator $threshold");
                $triggeredCount++;
            }
        }
        $message = "Processed " . count($processedCities) . " cities, triggered $triggeredCount alerts.";
    }

} catch (Throwable $e) {
    $status = 'error';
    $message = "Cron Exception: " . $e->getMessage();
    error_log($message);
}

// 2. Record cron execution
$stmt = $conn->prepare("INSERT INTO cron_logs (job_name, status, message, executed_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $cronJobName, $status, $message);
$stmt->execute();

echo $message . "\n";
