<?php
declare(strict_types=1);

class Auth
{
    /**
     * Authentication guard used by AuthMiddleware
     */
    public static function check(): void
    {
        if (!empty($_SESSION['user_id'])) {
            return;
        }

         else {
            header('Location: /login');
        }

        exit;
    }

    /**
     * Get logged-in user ID
     */
    public static function id(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    /**
     * Get logged-in user details
     */
    public static function user($conn): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        if (!$conn instanceof mysqli) {
            throw new RuntimeException('Invalid database connection');
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
    public static function logout(): void
    {
        session_unset();
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'redirect' => '/login'
        ]);
        exit;
    }
}
