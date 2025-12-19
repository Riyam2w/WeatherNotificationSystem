CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    condition_type ENUM('temperature', 'weather') NOT NULL,
    condition_operator ENUM('>', '<', '=', '>=', '<=') DEFAULT NULL,
    condition_value VARCHAR(50) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    valid_from DATE NOT NULL,
    valid_till DATE DEFAULT NULL,
    last_checked_at DATETIME DEFAULT NULL,
    last_alert_sent_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_subscription_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_city (city),
    INDEX idx_active (is_active),
    INDEX idx_condition (condition_type)
);
