<?php
class User
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    public function create(string $name, string $email, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (full_name, email, password)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $hash);

        return $stmt->execute();
    }
}
