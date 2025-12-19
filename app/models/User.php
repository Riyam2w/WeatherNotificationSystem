<?php
declare(strict_types=1);

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
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return (bool) $stmt->get_result()->fetch_assoc();
    }

    public function create(string $fullName, string $email, string $passwordHash): void
{
    $stmt = $this->conn->prepare(
        "INSERT INTO users (full_name, email, password_hash)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $fullName, $email, $passwordHash);
    $stmt->execute();
}

}
