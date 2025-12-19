<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../classes/Validator.php';

class AuthController
{
    private User $user;

    public function __construct(mysqli $conn)
    {
        $this->user = new User($conn);
    }

    public function register(): void
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             
            $fullName = trim($_POST['full_name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if ($fullName === '') {
    $errors[] = "Full name is required";
}
            if ($email === '') {
                $errors[] = "Email is required";
            } elseif (!Validator::email($email)) {
                $errors[] = "Invalid email address";
            }

            if (!Validator::password($password)) {
                $errors[] = "Password must be at least 8 characters and include letters and numbers";
            }

            if ($password !== $confirm) {
                $errors[] = "Passwords do not match";
            }

            if (empty($errors) && $this->user->emailExists($email)) {
                $errors[] = "Email already registered";
            }

            // ✅ INSERT INTO DATABASE
            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_BCRYPT);

                $this->user->create($fullName, $email, $hash);

                header("Location: /login");
                exit;
            }
        }

        require __DIR__ . '/../views/Auth/register.php';
    }
}
