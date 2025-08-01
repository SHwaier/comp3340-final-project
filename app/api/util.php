<?php

function sanitizeInput($data)
{
    if (!is_array($data)) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    foreach ($data as $key => $value) {
        // Skip password sanitization
        if ($key === 'password') {
            continue;
        }
        $data[$key] = htmlspecialchars(strip_tags(trim($value)));
    }
    return $data;
}


class EmailValidator
{
    private int $maxLength;
    private array $errors = [];

    public function __construct(int $maxLength = 100)
    {
        $this->maxLength = $maxLength;
    }

    public function sanitize(string $email): string
    {
        $email = trim($email);
        $email = str_replace(["\r", "\n", "\t", "\0", "\x0B"], '', $email); // Remove control chars
        $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Strip illegal characters
        return strtolower($email); // Normalize case
    }

    public function validate(string $email): bool
    {
        $this->errors = [];

        if (empty($email)) {
            $this->errors[] = "Email cannot be empty.";
        }

        if (strlen($email) > $this->maxLength) {
            $this->errors[] = "Email must not exceed {$this->maxLength} characters.";
        }

        if (preg_match('/[\r\n]/', $email)) {
            $this->errors[] = "Email cannot contain line breaks.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Email format is invalid.";
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

class PasswordValidator
{
    private array $rules;
    private array $errors = [];

    public function __construct(array $rules = [])
    {
        // Default rules
        $this->rules = array_merge([
            'minLength' => 8,
            'maxLength' => 255,
            'requireUppercase' => true,
            'requireLowercase' => true,
            'requireDigit' => true,
            'requireSpecial' => true,
            'blockLineBreaks' => true,
        ], $rules);
    }

    public function validate(string $password): bool
    {
        $this->errors = [];

        if (strlen($password) < $this->rules['minLength']) {
            $this->errors[] = "Password must be at least {$this->rules['minLength']} characters.";
        }

        if (strlen($password) > $this->rules['maxLength']) {
            $this->errors[] = "Password must be no more than {$this->rules['maxLength']} characters.";
        }

        if ($this->rules['blockLineBreaks'] && preg_match('/[\r\n]/', $password)) {
            $this->errors[] = "Password cannot contain line breaks.";
        }

        if ($this->rules['requireUppercase'] && !preg_match('/[A-Z]/', $password)) {
            $this->errors[] = "Password must contain at least one uppercase letter.";
        }

        if ($this->rules['requireLowercase'] && !preg_match('/[a-z]/', $password)) {
            $this->errors[] = "Password must contain at least one lowercase letter.";
        }

        if ($this->rules['requireDigit'] && !preg_match('/\d/', $password)) {
            $this->errors[] = "Password must contain at least one number.";
        }

        if ($this->rules['requireSpecial'] && !preg_match('/[\W_]/', $password)) {
            $this->errors[] = "Password must contain at least one special character.";
        }

        return empty($this->errors);
    }

    public function sanitize(string $password): string
    {
        // Remove invisible characters, trim, normalize line endings
        $password = trim($password);
        $password = preg_replace('/[\x00-\x1F\x7F]/u', '', $password);
        $password = str_replace(["\r\n", "\r"], "\n", $password);
        return $password;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
function error_respond($code, $message) {
    http_response_code($code);
    echo json_encode(["error" => $message]);
    exit;
}
