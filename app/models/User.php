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
            "INSERT INTO users (full_name, email, password)
             VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            throw new RuntimeException('Database error');
        }
        $stmt->bind_param("sss", $name, $email, $hash);

        return $stmt->execute();
    }
}
