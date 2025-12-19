CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT NOT NULL,
    condition_type ENUM('temperature','weather') NOT NULL,
    actual_value VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('sent','failed') DEFAULT 'sent',
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_subscription
        FOREIGN KEY (subscription_id)
        REFERENCES subscriptions(id)
        ON DELETE CASCADE,
    INDEX idx_subscription (subscription_id),
    INDEX idx_sent_at (sent_at)
);
