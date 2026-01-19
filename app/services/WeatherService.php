<?php
declare(strict_types=1);

class WeatherService
{
    private mysqli $conn;
    private array $config;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
        $this->config = require __DIR__ . '/../config/app_config.php';
    }

    /**
     * Fetch current weather data from OpenWeatherMap API
     */
    public function fetchCurrentWeather(float $lat, float $lon): ?array
    {
        $url = sprintf(
            '%s?lat=%f&lon=%f&units=metric&appid=%s',
            $this->config['weather_api']['url'],
            $lat,
            $lon,
            $this->config['weather_api']['key']
        );

        $response = @file_get_contents($url);

        if ($response === false) {
            error_log("Weather API call failed for lat: $lat, lon: $lon");
            return null;
        }

        $data = json_decode($response, true);

        if (empty($data) || !isset($data['main'])) {
            error_log("Invalid weather data received for lat: $lat, lon: $lon");
            return null;
        }

        return $data;
    }

    /**
     * Update latest weather and log the API call
     */
    public function updateWeatherData(int $cityId, array $apiData): void
    {
        $temperature = $apiData['main']['temp'] ?? null;
        $feelsLike   = $apiData['main']['feels_like'] ?? null;
        $humidity    = $apiData['main']['humidity'] ?? null;
        $pressure    = $apiData['main']['pressure'] ?? null;
        $windSpeed   = $apiData['wind']['speed'] ?? null;
        $visibility  = $apiData['visibility'] ?? null;
        $weatherMain = $apiData['weather'][0]['main'] ?? null;
        $weatherDesc = $apiData['weather'][0]['description'] ?? null;
        $apiResponse = json_encode($apiData);

        // 1. Update weather_latest
        $stmt = $this->conn->prepare(
            "INSERT INTO weather_latest (city_id, temperature, wind_speed, updated_at) 
             VALUES (?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE 
                temperature = VALUES(temperature),
                wind_speed = VALUES(wind_speed),
                updated_at = VALUES(updated_at)"
        );
        $stmt->bind_param("idd", $cityId, $temperature, $windSpeed);
        $stmt->execute();

        // 2. Fetch city name for logs (weather_logs uses city name string unfortunately)
        $stmt = $this->conn->prepare("SELECT name FROM cities WHERE id = ?");
        $stmt->bind_param("i", $cityId);
        $stmt->execute();
        $cityName = $stmt->get_result()->fetch_assoc()['name'] ?? 'Unknown';

        // 3. Insert into weather_logs
        $stmt = $this->conn->prepare(
            "INSERT INTO weather_logs 
            (city, temperature, feels_like, humidity, pressure, weather_main, weather_description, wind_speed, visibility, api_response, fetched_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        $stmt->bind_param(
            "sddiisssds",
            $cityName,
            $temperature,
            $feelsLike,
            $humidity,
            $pressure,
            $weatherMain,
            $weatherDesc,
            $windSpeed,
            $visibility,
            $apiResponse
        );
        $stmt->execute();
    }
}
