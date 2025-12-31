<?php
declare(strict_types=1);

class Auth
{
    
    public static function check(): void
    {
        if (!empty($_SESSION['user_id'])) {
            return;
        }

        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error'   => 'Unauthenticated'
            ]);
            exit;
        }

         else {
            header('Location: /login');
        }

        exit;
    }

    public static function id(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    public static function user($conn): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $conn->prepare(
            "SELECT id, full_name AS name, email
             FROM users
             WHERE id = ?
             LIMIT 1"
        );

        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * AJAX logout helper
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
        
    }
}
