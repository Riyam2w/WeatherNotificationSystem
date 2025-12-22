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
            "SELECT COUNT(DISTINCT city) FROM alerts WHERE user_id=?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }

    public function countActive(int $userId): int {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM alerts WHERE user_id=? AND status='monitoring'"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_row()[0];
    }

    public function all(int $userId): array {
        $stmt = $this->conn->prepare(
            "SELECT * FROM alerts WHERE user_id=? ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
