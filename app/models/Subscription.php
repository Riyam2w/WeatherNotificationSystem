<?php 
declare(strict_types=1);
class Subscription {
    private mysqli $conn;
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }
    public function create(array $data): bool {
        $sql = "insert into subscriptions(user_id, city, condition_type, condition_operator, condition_value, is_active, valid_from, created_at) values(?, ?, ?, ?, ?, 1, now(), now())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "isssd",
            $data['user_id'],
            $data['city'],
            $data['condition_type'],
            $data['condition_operator'],
            $data['condition_value']
        );
        return $stmt->execute();
    }

}



?>