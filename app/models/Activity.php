<?php
declare(strict_types=1);

class Activity
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Get unified activity history for a user
     */
    public function getHistory(int $userId, int $limit = 50): array
    {
        $sql = "
            (SELECT 
                'alert_created' as type,
                a.created_at as event_date,
                CONCAT('Alert created for ', c.name, ': ', ac.label, ' ', a.operator, ' ', a.threshold_value, a.unit) as message,
                'info' as status_class
            FROM alerts a
            JOIN cities c ON a.city_id = c.id
            JOIN alert_conditions ac ON a.condition_id = ac.id
            WHERE a.user_id = ?)
            
            UNION ALL
            
            (SELECT 
                'notification_sent' as type,
                n.sent_at as event_date,
                n.message as message,
                CASE WHEN n.status = 'sent' THEN 'success' ELSE 'danger' END as status_class
            FROM notifications n
            WHERE n.user_id = ?)
            
            ORDER BY event_date DESC
            LIMIT ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $userId, $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
