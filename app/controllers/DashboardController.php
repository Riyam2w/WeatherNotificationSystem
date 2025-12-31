<?php
declare(strict_types=1);

class DashboardController extends Controller
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Loads the main dashboard shell
     */
    public function index(): void
    {
        $activePage = $_GET['page'] ?? 'overview';

        $this->view(
            'dashboard/main',
            [
                'activePage' => $activePage,
                'userName'   => $_SESSION['user_name'] ?? 'User',
            ],
            'dashboard'
        );
    }

    /**
     * AJAX loader for dashboard pages
     */
    public function load(): void
    {
        $page = $_GET['page'] ?? 'overview';

        $pages = [
            'overview'             => 'dashboard/pages/overview',
            'alerts'               => 'dashboard/pages/alerts',
            'create-alert'         => 'dashboard/pages/create_alert',
            'create-alert-confirm' => 'dashboard/pages/create_alert_confirm',
            'subscriptions'        => 'dashboard/pages/subscriptions',
            'settings'             => 'dashboard/pages/settings',
        ];

        if (!isset($pages[$page])) {
            http_response_code(400);
            echo 'Invalid page';
            return;
        }
        $data = [];

        if ($page === 'overview') {
            require_once __DIR__ . '/../models/DashboardStats.php';

            $stats = new DashboardStats($this->conn);
            $data = $stats->getOverviewStats((int)$_SESSION['user_id']);
        }

        // ✅ Render partial WITHOUT layout
        $this->view(
            $pages[$page],
            $data,
            null
        );
    }
}
