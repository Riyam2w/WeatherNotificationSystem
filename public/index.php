<?php
declare(strict_types=1);

/* -------------------------
   Error & Session Settings
-------------------------- */
error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* -------------------------
   Core Includes
-------------------------- */
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/config/db.php';

/* -------------------------
   Database (single instance)
-------------------------- */
$db   = new Database();
$conn = $db->conn;

/* -------------------------
   Models
-------------------------- */
require_once __DIR__ . '/../app/models/Alert.php';
require_once __DIR__ . '/../app/models/Subscription.php';
require_once __DIR__ . '/../app/models/AlertHistory.php';

/* -------------------------
   Controllers
-------------------------- */
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

$authController      = new AuthController($conn);
$dashboardController = new DashboardController($conn);

/* -------------------------
   Router
-------------------------- */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim(str_replace('/index.php', '', $path), '/');
$path = $path === '' ? '/' : $path;

switch ($path) {

    case '/':
    case '/home':
        // Redirect based on login state
        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        require __DIR__ . '/../app/views/home.php';
        break;

    case '/dashboard':
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

    default:
        http_response_code(404);
        require __DIR__ . '/../app/views/errors/404.php';
        break;
}
