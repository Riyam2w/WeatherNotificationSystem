<?php
declare (strict_types=1);
class User
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function emailExists(string $email): bool
    { 
        $email = strtolower(trim($email));

        $stmt = $this->conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        if (!$stmt) {
            throw new RuntimeException('Database error');
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    public function create(
        string $name, 
        string $email, 
        string $password
        ): bool {
          $email = strtolower(trim($email));
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (full_name, email, password_hash)
             VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            throw new RuntimeException('Database error');
        }
        $stmt->bind_param("sss", $name, $email, $hash);

        return $stmt->execute();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, full_name, email, created_at, password_hash FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function verifyPassword(int $userId, string $password): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;
        
        return password_verify($password, $user['password_hash']);
    }

    public function updateProfile(int $userId, string $name, string $email): bool
    {
        $email = strtolower(trim($email));
        $stmt = $this->conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $userId);
        return $stmt->execute();
    }

    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $userId);
        return $stmt->execute();
    }
}
