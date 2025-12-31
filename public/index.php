<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


/* Session */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* Database */
require_once __DIR__ . '/../app/config/db.php';

$db   = new Database();
$conn = $db->conn;

if (!($conn instanceof mysqli)) {
    http_response_code(500);
    die('Database connection failed');
}

/* Core */
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/Router.php';


/* Controllers */
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/PricingController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/AlertController.php';
require_once __DIR__ . '/../app/controllers/LocationController.php';

/* Middleware */
require_once __DIR__ . '/../app/middleware/MiddlewareInterface.php';
require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/middleware/CsrfMiddleware.php';

$router = new Router($conn);

/* HOME (CRITICAL) */
$router->get('/', 'HomeController@index');
$router->get('/pricing', 'PricingController@index');

/* Auth */
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');

$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

$router->get('/forget_password', 'AuthController@showForgotPassword');
$router->post('/forget_password', 'AuthController@forgotPassword');

$router->match(['GET','POST'], '/logout', 'AuthController@logout');

/* Dashboard */
$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class]);
$router->get('/dashboard/load', 'DashboardController@load', [AuthMiddleware::class]);

/* API */
$router->get('/api/locations', 'LocationController@index');
$router->post(
    '/api/alerts/create',
    'AlertController@store',
    [AuthMiddleware::class, CsrfMiddleware::class]
);

$router->dispatch();
