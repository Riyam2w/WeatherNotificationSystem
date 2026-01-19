<?php
declare(strict_types=1);

class DashboardController extends Controller
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

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
            'history'              => 'dashboard/pages/history',
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
        
        if ($page === 'create-alert') {
            require_once __DIR__ . '/../models/Subscription.php';
            require_once __DIR__ . '/../models/Alert.php';
            $subModel = new Subscription($this->conn);
            $alertModel = new Alert($this->conn);
            $userId = (int)($_SESSION['user_id'] ?? 0);
            
            $data['features'] = $subModel->getFeaturesForUser($userId);
            $data['alertCount'] = $alertModel->countAll($userId);
        }
        

        
        if ($page === 'alerts') {
            require_once __DIR__ . '/../models/Alert.php';
            require_once __DIR__ . '/../models/Subscription.php';
            
            $alertModel = new Alert($this->conn);
            $subModel = new Subscription($this->conn);
            $userId = (int)($_SESSION['user_id'] ?? 0);
            
            $data['alerts'] = $alertModel->all($userId);
            $data['features'] = $subModel->getFeaturesForUser($userId);
            $data['currentAlertCount'] = count($data['alerts']);
        }

        if ($page === 'subscriptions') {
            require_once __DIR__ . '/../models/Plan.php';
            require_once __DIR__ . '/../models/Subscription.php';
            require_once __DIR__ . '/../models/Payment.php';

            $planModel = new Plan($this->conn);
            $subscriptionModel = new Subscription($this->conn);
            $paymentModel = new Payment($this->conn);
            $userId = (int)($_SESSION['user_id'] ?? 0);

            $data['plans'] = $planModel->getPlansWithFeatures();
            $data['currentSubscription'] = $subscriptionModel->current($userId);
            $data['billingHistory'] = $paymentModel->getHistory($userId);
        }

        if ($page === 'history') {
            require_once __DIR__ . '/../models/Activity.php';
            $activityModel = new Activity($this->conn);
            $userId = (int)($_SESSION['user_id'] ?? 0);
            $data['activities'] = $activityModel->getHistory($userId);
        }

        if ($page === 'settings') {
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User($this->conn);
            $userId = (int)($_SESSION['user_id'] ?? 0);
            $data['user'] = $userModel->find($userId);
        }

        // ✅ Render partial WITHOUT layout
        $this->view(
            $pages[$page],
            $data,
            null
        );
    }
}
