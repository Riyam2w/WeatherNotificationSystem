<?php
declare(strict_types=1);

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
         if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)){
            return;
         }
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');

        if (
            empty($token) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error'   => 'Invalid CSRF token'
            ]);
            exit;
        }
    }
}
