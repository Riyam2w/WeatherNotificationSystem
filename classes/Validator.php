<?php
declare(strict_types=1);

/**
 * Central validation utility
 * Used by ApiValidator and Controllers
 */
class Validator
{
    /* =========================
       BASIC VALIDATIONS
    ========================= */

    public static function required(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
    }

    public static function email(string $email): bool
    {
        $email = trim($email);

        return $email !== ''
            && strlen($email) <= 255
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /** Optional stricter rule */
    public static function gmail(string $email): bool
    {
        return self::email($email) && str_ends_with($email, '@gmail.com');
    }

    public static function name(string $name): bool
    {
        $name = trim($name);

        return strlen($name) >= 3
            && strlen($name) <= 100
            && preg_match('/^[A-Za-z ]+$/', $name) === 1;
    }

    public static function confirm(string $value, string $confirm): bool
    {
        return hash_equals($value, $confirm);
    }

    /* =========================
       PASSWORD
    ========================= */

    public static function password(string $password, int $minLength = 6): bool
    {
        if (strlen($password) < $minLength) {
            return false;
        }

        if (!preg_match('/[A-Za-z]/', $password)) {
            return false;
        }

        if (!preg_match('/\d/', $password)) {
            return false;
        }

        return true;
    }

    /* =========================
       PHONE
    ========================= */

    /**
     * Indian mobile numbers
     * Starts with 6–9, exactly 10 digits
     */
    public static function phone(string $phone): bool
    {
        $phone = preg_replace('/\s+/', '', $phone);
        return preg_match('/^[6-9]\d{9}$/', $phone) === 1;
    }

    /* =========================
       NUMERIC / RANGE
    ========================= */

    public static function numeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    public static function range(
        float|int|null $value,
        float|int $min,
        float|int $max
    ): bool {
        if ($value === null || !is_numeric($value)) {
            return false;
        }

        return $value >= $min && $value <= $max;
    }
}
