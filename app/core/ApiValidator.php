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

    public function phone(string $field, string $value): self
    {
        if (!Validator::phone($value)) {
            $this->errors[$field] = 'Invalid phone number.';
        }
        return $this;
    }

    public function range(
        string $field,
        float|int|null $value,
        float|int $min,
        float|int $max
    ): self {
        if (!Validator::range($value, $min, $max)) {
            $this->errors[$field] =
                "Value must be between {$min} and {$max}.";
        }
        return $this;
    }

    public function password(string $field, string $value): self
    {
        if (!Validator::password($value)) {
            $this->errors[$field] =
                'Password must contain letters and numbers.';
        }
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
