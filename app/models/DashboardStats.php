<?php 
declare(strict_types=1);

class DashboardStats {
    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }
    public function getOverviewStats(int $userId): array {
        require_once __DIR__ . '/Alert.php';
        require_once __DIR__ . '/Plan.php';

        $alertModel = new Alert($this->conn);
        $planModel = new Plan($this->conn);

        return[
            'totalAlerts' => $alertModel->countAll($userId),
            'activeAlerts' => $alertModel->countActive($userId),
            'countCities' => $alertModel->countCities($userId),
            'currentPlan' => $planModel->getActivePlan($userId),
        ];
    }
}