<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1'); 
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$db = new Database();
$conn = $db->conn;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);
$path = rtrim($path, '/') ?: '/';
$auth = new AuthController($conn);

if ($path === '/register') {
    $auth->register();
    exit;
}
http_response_code(404);
echo '404 - Page Not Found';
?>