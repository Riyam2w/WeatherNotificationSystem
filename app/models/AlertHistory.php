<?php
declare(strict_types=1);

class AlertHistory
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function countToday(int $userId): int
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) 
             FROM notifications n
             JOIN subscriptions s ON n.subscription_id = s.id
             WHERE s.user_id = ?
             AND DATE(n.sent_at) = CURDATE()"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return (int) $stmt->get_result()->fetch_row()[0];
    }

    public function lastSentTime(int $userId): ?string
    {
        $stmt = $this->conn->prepare(
            "SELECT MAX(n.sent_at)
             FROM notifications n
             JOIN subscriptions s ON n.subscription_id = s.id
             WHERE s.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_row()[0] ?: null;
    }

    public function recent(int $userId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT n.*
             FROM notifications n
             JOIN subscriptions s ON n.subscription_id = s.id
             WHERE s.user_id = ?
             ORDER BY n.sent_at DESC
             LIMIT 10"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
