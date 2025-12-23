<?php
declare(strict_types=1);

class Auth
{
    public static function requireLogin(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function id(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return (int) ($_SESSION['user_id'] ?? 0);
    }

    // ✅ ADD THIS
    public static function user(mysqli $conn): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

     public static function check(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }
}
