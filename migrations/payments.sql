CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan ENUM('daily','weekly','monthly','yearly') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_gateway VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending','success','failed') DEFAULT 'pending',
    valid_from DATE NOT NULL,
    valid_till DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);
