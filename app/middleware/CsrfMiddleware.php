<?php
declare(strict_types=1);

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (
            empty($token) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error'   => 'Invalid CSRF token'
            ]);
            exit;
        }
    }
}
