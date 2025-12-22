<?php 
declare(strict_types=1);
final class Validator {
    public static function email(string $email): bool {
        $email = trim($email);
        if($email == '') {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) != false;
    }

    public static function password(string $password): bool{
        if(strlen($password) <6 ) {
            return false;
        }
        if (!preg_match('/[A-Za-z]/', $password)) {
            return false;
        }
        if(!preg_match('/\d/', $password)) {
            return false;
        }
        return true;
    }

    public static function name(string $name): bool {
        $name = trim($name);

        if(strlen($name) < 3) {
            return false;
        }
        return preg_match('/^[A-Za-z ]+$/', $name) === 1;
    }

    public static function confirm(string $value, string $confirm): bool {
        return hash_equals($value, $confirm);
    }

    public static function required(?string $value): bool {
        return trim((string)$value) !== '';
    }
    
}



?>