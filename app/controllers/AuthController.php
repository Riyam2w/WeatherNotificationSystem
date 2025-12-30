<?php
declare(strict_types=1);

require_once __DIR__ . '/../../classes/Validator.php';
require_once __DIR__ . '/../core/ApiValidator.php';

class AuthController extends Controller
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /* ==========================
       SHOW PAGES (GET)
    ========================== */

    public function showRegister(): void
    {
        $this->view('Auth/register', [
            'title' => 'Create Account'
        ]);
    }

    public function showLogin(): void
    {
        $this->view('Auth/login', [
            'title' => 'Login'
        ]);
    }

    public function showForgotPassword(): void
    {
        $this->view('Auth/forget_password', [
            'title' => 'Forgot Password'
        ]);
    }

    /* ==========================
       REGISTER (POST – AJAX)
    ========================== */

    public function register(): void
    {
        $this->jsonHeader();
        try {

        
        $this->ensurePost();

        $name  = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $cpass = $_POST['confirm_password'] ?? '';

        $v = new ApiValidator();
        $v->require('full_name', $name)
          ->require('email', $email)
          ->require('password', $pass)
          ->email('email', $email)
          ->password('password', $pass);

        if (!Validator::name($name)) {
            $v->addError('full_name', 'Name must contain only letters.');
        }

        if (!Validator::confirm($pass, $cpass)) {
            $v->addError('confirm_password', 'Passwords do not match.');
        }

        if ($v->fails()) {
            $this->jsonError($v->errors(), 422);
            return;
        }

        $stmt = $this->conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );
        if(!$stmt) {
            $this->jsonError('Database prepare failed (email check)', 500);
            return;
        }
        $stmt->bind_param("s", $email);
        if(!$stmt->execute()) {
            $this->jsonError('Database execute failed (email check)', 500);
            return;
        }
        
        // $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
                   $this->jsonError(['email' => 'Email already registered.'], 422);

            return;
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $insert = $this->conn->prepare(
            "INSERT INTO users (full_name, email, password_hash)
             VALUES (?, ?, ?)"
        );
        if(!$insert) {
            $this->jsonError('Database prepare failed', 500);
            return;
        }
        $insert->bind_param("sss", $name, $email, $hash);

        if (!$insert->execute()) {
            $this->jsonError('Database insert failed', 500);
            return;
        }

        $this->jsonSuccess('Registration successful');
    }
    catch(Throwable $e) {
        print_r($e);
        $this->jsonError('Internal server error', 500);
    }
}

    /* ==========================
       LOGIN (POST – AJAX)
    ========================== */

    public function login(): void
    {
        $this->jsonHeader();
        try {
            $this->ensurePost();
      
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->jsonError('Email and password required', 400);
            return;
        }

        $stmt = $this->conn->prepare(
            "SELECT id, full_name, password_hash
             FROM users WHERE email = ? LIMIT 1"
        );
        if (!$stmt) {
            throw new RuntimeException('DB prepare failed (login)');
        }
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new RuntimeException('DB execute failed (login)');

        }
        // $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->jsonError('Invalid credentials', 401);
            return;
        }

        // Session setup
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];

        echo json_encode([
            'success'  => true,
            'redirect' => '/dashboard'
        ]);
        exit;
    } catch (Throwable $e){
        $this->jsonError('Internal server error', 500);
}
        
    }
    /* ==========================
       LOGOUT (POST – AJAX)
       (NO REDIRECT HERE)
    ========================== */

    public function logout(): void
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

    /* ==========================
       HELPERS
    ========================== */

    private function ensurePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonError('Method not allowed', 405);
            exit;
        }
    }

    private function jsonHeader(): void
    {
        header('Content-Type: application/json');
    }

    private function jsonError(array|string $errors, int $code = 422): void
    {
        // header('Content-Type: application/json');
        http_response_code($code);

        if(is_string($errors)) {
            $errors = ['general' => $errors];
        }
        echo json_encode([
            'success' => false,
            'errors'   => $errors
        ]);
        exit;
    }

    private function jsonSuccess(string $message): void
    {
        // header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
        exit;
    }
}
