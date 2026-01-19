<?php
declare(strict_types=1);

class Plan
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getAllActive(): array
    {
        $sql = "SELECT * FROM plans 
                WHERE is_active = 1 
                ORDER BY monthly_price ASC";

        $res = $this->conn->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getFeatures(int $planId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT feature, is_available
             FROM plan_features
             WHERE plan_id = ?
             ORDER BY id ASC"
        );

        $stmt->bind_param("i", $planId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPlansWithFeatures(): array
    {
        $plans = $this->getAllActive();

        foreach ($plans as &$plan) {
            $plan['features'] = $this->getFeatures((int)$plan['id']);
        }

        return $plans;
    }
    public function getActivePlan(int $userId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT p.id, p.name, s.valid_till 
             FROM subscriptions s 
             JOIN plans p ON p.id = s.plan_id
             WHERE s.user_id = ? AND s.status='active'
             LIMIT 1"
        );

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result ?: null;
    }

    public function getParsedFeatures(int $planId): array
    {
        $features = $this->getFeatures($planId);
        $parsed = [];

        foreach ($features as $f) {
            if ($f['is_available']) {
                $featureStr = trim($f['feature']);
                
                // Support both ':' and '='
                $delimiter = strpos($featureStr, ':') !== false ? ':' : (strpos($featureStr, '=') !== false ? '=' : null);
                
                if ($delimiter) {
                    list($key, $value) = explode($delimiter, $featureStr, 2);
                    // Normalize key: lowercase, replace spaces with underscores (e.g., "Max Alerts" -> "max_alerts")
                    $cleanKey = strtolower(str_replace(' ', '_', trim($key)));
                    $cleanVal = trim($value);
                    
                    // Cast to int if it looks like one, otherwise keep as string
                    $parsed[$cleanKey] = is_numeric($cleanVal) ? (int)$cleanVal : $cleanVal;
                } else {
                    // Normalize boolean key
                    $cleanKey = strtolower(str_replace(' ', '_', $featureStr));
                    $parsed[$cleanKey] = true;
                }
            }
        }

        return $parsed;
    }
}
