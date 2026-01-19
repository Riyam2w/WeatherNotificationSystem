<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class UserController extends Controller
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function updateProfile(): void
    {
        $this->ensurePost();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // 2. Input Validation
        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');

        require_once __DIR__ . '/../../classes/Validator.php';
        
        if (!Validator::name($name)) {
            $this->json(['success' => false, 'message' => 'Name must be at least 3 letters and contain only characters.'], 400);
        }

        if (!Validator::email($email)) {
            $this->json(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
        }

        $userModel = new User($this->conn);
        
        // Check if email is taken by another user
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $this->json(['success' => false, 'message' => 'Email is already in use by another account.'], 400);
        }

        if ($userModel->updateProfile($userId, $name, $email)) {
            $_SESSION['user_name'] = $name;
            $this->json(['success' => true, 'message' => 'Profile updated successfully.']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update profile.'], 500);
        }
    }

    public function updatePassword(): void
    {
        $this->ensurePost();

        // 1. Auth Check
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // 2. Input Validation
        $input = json_decode(file_get_contents('php://input'), true);
        $currentPassword = $input['current_password'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';

        require_once __DIR__ . '/../../classes/Validator.php';

        if (empty($currentPassword)) {
             $this->json(['success' => false, 'message' => 'Current password is required.'], 400);
        }

        if (!Validator::password($newPassword)) {
             $this->json(['success' => false, 'message' => 'Password must be at least 6 chars, contain letters and numbers.'], 400);
        }

        if (!Validator::confirm($newPassword, $confirmPassword)) {
            $this->json(['success' => false, 'message' => 'New passwords do not match.'], 400);
        }

        // 3. Verify Current Password
        $userModel = new User($this->conn);
        if (!$userModel->verifyPassword($userId, $currentPassword)) {
            $this->json(['success' => false, 'message' => 'Incorrect current password.'], 400);
        }

        // 4. Update Password
        if ($userModel->updatePassword($userId, $newPassword)) {
            $this->json(['success' => true, 'message' => 'Password updated successfully.']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update password.'], 500);
        }
    }
}
