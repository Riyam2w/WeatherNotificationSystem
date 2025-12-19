CREATE TABLE weather_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100) NOT NULL,
    temperature DECIMAL(5,2) DEFAULT NULL,
    feels_like DECIMAL(5,2) DEFAULT NULL,
    humidity INT DEFAULT NULL,
    pressure INT DEFAULT NULL,
    weather_main VARCHAR(50) DEFAULT NULL,
    weather_description VARCHAR(100) DEFAULT NULL,
    wind_speed DECIMAL(5,2) DEFAULT NULL,
    visibility INT DEFAULT NULL,
    api_response JSON DEFAULT NULL,
    fetched_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_city (city),
    INDEX idx_fetched (fetched_at)
);
