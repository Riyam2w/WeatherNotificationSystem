<?php
class AuthController
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function register(): void
{
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name  = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $cpass = $_POST['confirm_password'] ?? '';

        if ($name === '' || !preg_match('/^[a-zA-Z ]+$/', $name)) {
            $errors[] = "Name must contain only letters.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }

        if (strlen($pass) < 6) {
            $errors[] = "Password must be at least 6 characters.";
        }

        if ($pass !== $cpass) {
            $errors[] = "Passwords do not match.";
        }

        if (empty($errors)) {

            $stmt = $this->conn->prepare(
                "SELECT id FROM users WHERE email = ?"
            );

            if (!$stmt) {
                die("Prepare failed (select): " . $this->conn->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Email already registered.";
            } else {

                $hash = password_hash($pass, PASSWORD_DEFAULT);

                $insert = $this->conn->prepare(
                    "INSERT INTO users (full_name, email, password_hash)
                     VALUES (?, ?, ?)"
                );

                if (!$insert) {
                    die("Prepare failed (insert): " . $this->conn->error);
                }

                $insert->bind_param("sss", $name, $email, $hash);

                if (!$insert->execute()) {
                    die("Execute failed: " . $insert->error);
                }

                header("Location: /login");
                exit;
            }
        }
    }

    require __DIR__ . '/../views/Auth/register.php';
}

  public function login(): void
{
    // Sessions MUST start before any output
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if ($email === '' || $pass === '') {
            $error = "Email and password are required.";
        } else {

            $stmt = $this->conn->prepare(
                "SELECT id, password_hash, is_active
                 FROM users
                 WHERE email = ?
                 LIMIT 1"
            );

            if (!$stmt) {
                throw new RuntimeException("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {

                if ((int)$user['is_active'] !== 1) {
                    $error = "Account is inactive.";
                } elseif (!password_verify($pass, $user['password_hash'])) {
                    $error = "Invalid credentials.";
                } else {
                    // 🔐 SUCCESSFUL LOGIN

                    // Regenerate BEFORE any output
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int)$user['id'];

                    // Redirect immediately, no view rendering
                    header("Location: /dashboard");
                    exit;
                }

            } else {
                $error = "Invalid credentials.";
            }
        }
    }

    // View is loaded ONLY if no redirect occurred
    require __DIR__ . '/../views/Auth/login.php';
}


    public function forgotPassword(): void
{
    $message = '';
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            // NOTE: Actual email sending will be added later.
            // Always show a neutral message to prevent account enumeration.
            $message = "If an account exists for this email, you’ll receive reset instructions.";
        }
    }

    require __DIR__ . '/../views/Auth/forget_password.php';
}

    public function logout(): void
    {
        session_destroy();
        header("Location: /login");
        exit;
    }
}
