<?php
// app/models/Alert.php
declare(strict_types=1);

class Alert {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    public function countCities(int $userId): int {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(DISTINCT city_id) FROM alerts WHERE user_id=?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }

    public function countActive(int $userId): int {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM alerts WHERE user_id=? AND status='active'"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }

    public function all(int $userId): array {
        $stmt = $this->conn->prepare(

            "SELECT
            a.id,
            a.alert_name,
            a.operator,
            a.threshold_value,
            a.status,
            a.created_at,
            c.name AS city_name,
            ac.code AS condition_code,
            ac.label AS condition_label
            FROM alerts a 
            JOIN cities c ON c.id = a.city_id
            JOIN alert_conditions ac ON ac.id = a.condition_id
            WHERE a.user_id=?
            ORDER BY a.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(int $userId): int {
        $stmt = $this->conn->prepare (
            "SELECT COUNT(*) FROM alerts where user_id=?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }
}
