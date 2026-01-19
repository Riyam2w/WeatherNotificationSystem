<?php

class CityService
{
    private $conn;
    // Using the key found in the frontend asset
    private const API_KEY = 'c4e6dd84573d65a9b87404115c759ee7';
    private const API_URL = 'http://api.openweathermap.org/geo/1.0/direct';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;
    }

    /**
     * Fetch city data from OpenWeatherMap API
     */
    public function fetchCityFromApi(string $cityName): ?array
    {
        $url = sprintf(
            '%s?q=%s&limit=1&appid=%s',
            self::API_URL,
            urlencode($cityName),
            self::API_KEY
        );

        $response = @file_get_contents($url);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (empty($data) || !is_array($data)) {
            return null;
        }

        // Return the first result
        return $data[0];
    }

    /**
     * Save city to database if it doesn't exist
     */
    public function saveCity(array $cityData): int
    {
        $name = $cityData['name'];
        $lat = $cityData['lat'];
        $lon = $cityData['lon'];
        $country = $cityData['country'];
        // API doesn't always provide full country name, mapping code to name is complex without a library.
        // For now, we will use the country code as the country name if we can't map it easily, 
        // or just store the code. The existing schema has 'country' and 'country_code'.
        // Let's rely on what we have.
        $countryCode = $cityData['country']; 
        
        // Simple mapping or just use code for both if name unavailable is a reasonable MVP fallback
        // But let's check if we can get a better name. Actually, for now, let's use the code for both to be safe
        // or check if there is a countries.json we can look up.
        // The LocationController has a 'countries.json'.
        
        // Let's try to look up generic name, otherwise use code.
        $countryName = $this->getCountryName($countryCode);

        // Check if exists
        $stmt = $this->conn->prepare("SELECT id FROM cities WHERE name = ? AND country_code = ?");
        $stmt->bind_param("ss", $name, $countryCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return (int)$row['id'];
        }
        $stmt->close();

        // Insert
        $stmt = $this->conn->prepare("INSERT INTO cities (name, lat, lon, country, country_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sddss", $name, $lat, $lon, $countryName, $countryCode);
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }

        throw new Exception("Failed to insert city: " . $stmt->error);
    }

    private function getCountryName(string $code): string {
        // Simple attempt to read from the static JSON if possible, otherwise return code
        $file = __DIR__ . '/../data/countries.json';
        if (file_exists($file)) {
             $data = json_decode(file_get_contents($file), true);
             if (is_array($data)) {
                 foreach ($data as $c) {
                     if (($c['code'] ?? '') === $code || ($c['iso2'] ?? '') === $code) {
                         return $c['name'];
                     }
                 }
             }
        }
        return $code;
    }
}
