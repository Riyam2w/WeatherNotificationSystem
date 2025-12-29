<?php
declare(strict_types=1);

class DashboardController extends Controller
{
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
            'dashboard' // ✅ dashboard layout (no navbar/footer)
        );
    }

    /**
     * AJAX loader for dashboard pages
     */
    public function load(): void
    {
        header('Content-Type: text/html; charset=UTF-8');

        $page = $_GET['page'] ?? 'overview';

        $map = [
            'overview'      => 'overview.php',
            'alerts'        => 'alerts.php',
            'create-alert'  => 'create_alert.php',
            'create_alert_confirm'  => 'create_alert_confirm.php',
            'subscriptions' => 'subscriptions.php',
            'settings'      => 'settings.php',
        ];

        if (!isset($map[$page])) {
            http_response_code(400);
            echo 'Invalid page';
            exit;
        }

        require __DIR__ . '/../views/dashboard/pages/' . $map[$page];
        exit;
    }
}
