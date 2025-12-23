<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Auth.php';

Auth::requireLogin();

$allowedPages = [
    'overview',
    'alerts',
    'history',
    'subscriptions',
    'settings'
];

$page = $_GET['page'] ?? 'overview';

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    exit('Invalid section');
}

$file = __DIR__ . '/../app/views/dashboard/' . $page . '.php';

if (!file_exists($file)) {
    http_response_code(404);
    exit('Section not found');
}

require $file;
