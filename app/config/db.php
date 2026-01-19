<?php
declare(strict_types=1);
class Database
{
    private string $host = "127.0.0.1";
    private string $user = "phpmyadmin";
    private string $pass = "root";
    private string $name = "weather_notification_system";
    public mysqli $conn;
    public function __construct()
    {

        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->name
        );
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        };
    }
}
