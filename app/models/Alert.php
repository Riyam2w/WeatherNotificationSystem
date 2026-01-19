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
            "SELECT COUNT(DISTINCT city_id) FROM user_cities WHERE user_id=?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_row()[0];
    }

    public function countActive(int $userId): int {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM alerts WHERE user_id=? AND status='active'"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_row()[0];
    }

    public function countAll(int $userId): int {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM alerts where user_id=?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_row()[0];
    }

    public function all(int $userId): array {
        $stmt = $this->conn->prepare(
            "SELECT 
                a.*, 
                c.name as city_name, 
                ac.label as condition_label 
            FROM alerts a
            JOIN cities c ON a.city_id = c.id
            JOIN alert_conditions ac ON a.condition_id = ac.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data): bool {
    $stmt = $this->conn->prepare(
        "INSERT INTO alerts (
        user_id, city_id, condition_id, `operator`, threshold_value, unit, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())"
    );

    $stmt->bind_param(
        "iiissd",
        $data['user_id'],
        $data['city_id'],   
        $data['condition_id'],
        $data['operator'],
        $data['threshold_value'],
        $data['unit']
    );
    return $stmt->execute();
}

    public function delete(int $alertId, int $userId): bool {
        $stmt = $this->conn->prepare("DELETE FROM alerts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $alertId, $userId);
        return $stmt->execute();
    }
}