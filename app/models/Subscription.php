<?php
declare(strict_types=1);

class Subscription
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function current(int $userId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM subscriptions
             WHERE user_id = ?
             AND is_active = 1
             AND CURDATE() BETWEEN valid_from AND valid_till
             ORDER BY valid_till DESC
             LIMIT 1"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?: null;
    }
}
