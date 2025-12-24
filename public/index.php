<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

/* -------------------------
   Session
   ------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* -------------------------
   Database
   ------------------------- */
require_once __DIR__ . '/../app/config/db.php';

$db   = new Database();
$conn = $db->conn;

if (!($conn instanceof mysqli)) {
    die('Database connection failed');
}

/* -------------------------
   Core + Controllers
   ------------------------- */
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Auth.php';

require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

/* -------------------------
   Controller Instances
   ------------------------- */
$homeController      = new HomeController();
$authController      = new AuthController($conn);
$dashboardController = new DashboardController($conn);

/* -------------------------
   Routing
   ------------------------- */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim(str_replace('/index.php', '', $path), '/');
$path = $path === '' ? '/' : $path;

switch ($path) {

    /* ---------- Public ---------- */
    case '/':
    case '/home':
        $homeController->index();
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
        exit;

    /* ---------- Dashboard ---------- */
    case '/dashboard':
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $dashboardController->index();
        break;

    case '/dashboard/load':
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            exit;
        }
        $dashboardController->load();
        break;

    /* ---------- 404 ---------- */
    default:
        http_response_code(404);
        echo '404 Not Found';
}
