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
            "SELECT s.*, p.name, p.description, p.monthly_price, p.yearly_price
             FROM subscriptions s
             JOIN plans p ON s.plan_id = p.id
             WHERE s.user_id = ?
             AND s.status IN ('active', 'trial')
             AND s.is_active = 1
             AND (s.valid_till IS NULL OR s.valid_till >= CURDATE())
             ORDER BY s.created_at DESC
             LIMIT 1"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getFeaturesForUser(int $userId): array
    {
        require_once __DIR__ . '/Plan.php';
        $planModel = new Plan($this->conn);
        
        $subscription = $this->current($userId);
        
        if ($subscription) {
            $planId = (int)$subscription['plan_id'];
        } else {
            $stmt = $this->conn->prepare("SELECT id FROM plans WHERE slug = 'free' LIMIT 1");
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $planId = $res ? (int)$res['id'] : 4; 
        }

        return $planModel->getParsedFeatures($planId);
    }
}
