<?php
declare(strict_types=1);

class DashboardController extends Controller
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function index(string $page = 'overview'): void
    {
        // USER
        $userName = $_SESSION['full_name'] ?? 'User';

        // PLAN (temporary – replace with DB later)
        $plan = [
            'name'   => 'Free Plan',
            'status' => 'active',
            'expiry' => 'Never',
        ];

        // STATS (temporary)
        $stats = [
            'cities_count'     => 2,
            'active_alerts'    => 3,
            'triggered_today'  => 1,
            'last_alert_time'  => 'Today, 10:30 AM',
        ];

        // ALERTS (temporary)
        $alerts = [
            [
                'city'      => 'Delhi',
                'condition' => 'Temp > 40°C',
                'status'    => 'active',
            ],
            [
                'city'      => 'Mumbai',
                'condition' => 'Rain',
                'status'    => 'paused',
            ],
        ];

        // HISTORY (temporary)
        $history = [
            [
                'time'   => '2025-01-22 09:15',
                'city'   => 'Delhi',
                'event'  => 'Temperature crossed 40°C',
                'status' => 'sent',
            ],
        ];

        $this->view('dashboard/overview', compact(
            'userName',
            'plan',
            'stats',
            'alerts',
            'history'
        ));
    }
}

