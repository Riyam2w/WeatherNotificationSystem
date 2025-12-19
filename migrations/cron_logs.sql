USE weather_notification_system;

CREATE TABLE cron_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,

    job_name VARCHAR(100) NOT NULL,
    status ENUM('success','error') NOT NULL,
    message TEXT,

    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
