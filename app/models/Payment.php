<?php
declare(strict_types=1);

class Payment
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function createPayment(array $data): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO payments (user_id, plan_id, amount, payment_gateway, transaction_id, status, valid_from, valid_till, billing_cycle)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iidssssss",
            $data['user_id'],
            $data['plan_id'],
            $data['amount'],
            $data['payment_gateway'],
            $data['transaction_id'],
            $data['status'],
            $data['valid_from'],
            $data['valid_till'],
            $data['billing_cycle']
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return 0;
    }

    public function createSubscription(array $data): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO subscriptions (user_id, plan_id, city, is_active, valid_from, valid_till, billing_cycle, status)
             VALUES (?, ?, ?, 1, ?, ?, ?, 'active')"
        );

        $stmt->bind_param(
            "iissss",
            $data['user_id'],
            $data['plan_id'],
            $data['city'],
            $data['valid_from'],
            $data['valid_till'],
            $data['billing_cycle']
        );

        return $stmt->execute();
    }

    public function updateUserPlan(int $userId, int $planId): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET current_plan_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $planId, $userId);
        return $stmt->execute();
    }

    public function getHistory(int $userId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT p.*, pl.name as plan_name 
             FROM payments p
             JOIN plans pl ON p.plan_id = pl.id
             WHERE p.user_id = ?
             ORDER BY p.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        return $history;
    }
}
