<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/config/db.php';

$db   = new Database();
$conn = $db->conn;

if (!($conn instanceof mysqli)) {
    die('Database connection failed');
}
require_once __DIR__ . '/../app/controllers/HomeController.php';

require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Auth.php';

require_once __DIR__ . '/../app/models/Alert.php';
require_once __DIR__ . '/../app/models/Subscription.php';
require_once __DIR__ . '/../app/models/AlertHistory.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';


$homeController = new HomeController();

$authController      = new AuthController($conn);
$dashboardController = new DashboardController($conn);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim(str_replace('/index.php', '', $path), '/');
$path = $path === '' ? '/' : $path;

switch ($path) {

    case '/':
    case '/home':
        $homeController->index();
        break;
    

    case '/dashboard':
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $dashboardController->index();
        break;

    case '/register':
        $authController->register();
        break;

    case '/login':
        $authController->login();
        break;

    case '/forget_password':
        $authController->forgotPassword();
        break;

        case '/logout':
        session_unset();
         session_destroy();
        header('Location: /login');
        break;

    default:
        http_response_code(404);
        echo '404 Not Found';
}
