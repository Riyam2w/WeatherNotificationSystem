<?php
declare(strict_types=1);

class ApiValidator
{
    private array $errors = [];

    public function require(string $field, mixed $value): self
    {
        if (!Validator::required((string)$value)) {
            $this->errors[$field] = ucfirst($field) . ' is required.';
        }
        return $this;
    }

    public function email(string $field, string $value): self
    {
        if (!Validator::email($value)) {
            $this->errors[$field] = 'Invalid email address.';
        }
        return $this;
    }
    public function password(string $field, string $value): self
    {
        if (!Validator::password($value)) {
            $this->errors[$field] =
                'Password must be at least 6 characters long and contain both letters and numbers.';
        }
        return $this;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }
    

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
