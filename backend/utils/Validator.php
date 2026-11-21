<?php

declare(strict_types=1);

require_once __DIR__ . '/exceptions.php';

/**
 * Reusable validation helpers for services.
 * Every method throws a ValidationException (HTTP 400) with a readable message
 * and does nothing when the value is correct.
 */
class Validator
{
    /** Every field in $fields must exist and must not be empty. */
    public static function required(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new ValidationException("Field '$field' is required.");
            }
        }
    }

    public static function length($value, string $field, int $min, int $max): void
    {
        $length = mb_strlen(trim((string) $value));
        if ($length < $min || $length > $max) {
            throw new ValidationException("Field '$field' must be between $min and $max characters long.");
        }
    }

    public static function integer($value, string $field, ?int $min = null, ?int $max = null): void
    {
        if (is_bool($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ValidationException("Field '$field' must be a whole number.");
        }
        $number = (int) $value;
        if ($min !== null && $number < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
        if ($max !== null && $number > $max) {
            throw new ValidationException("Field '$field' must be at most $max.");
        }
    }

    public static function one_of($value, string $field, array $allowed): void
    {
        if (!in_array($value, $allowed, true)) {
            throw new ValidationException("Field '$field' must be one of: " . implode(', ', $allowed) . '.');
        }
    }

    public static function email($value, string $field = 'email'): void
    {
        if (filter_var(trim((string) $value), FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException("Field '$field' must be a valid e-mail address.");
        }
    }

    /** A date in the format YYYY-MM-DD that really exists (no 2026-02-31). */
    public static function date($value, string $field): void
    {
        $date = DateTime::createFromFormat('!Y-m-d', (string) $value);
        if ($date === false || $date->format('Y-m-d') !== (string) $value) {
            throw new ValidationException("Field '$field' must be a valid date (YYYY-MM-DD).");
        }
    }

    /** A date and time in the format YYYY-MM-DD HH:MM:SS. */
    public static function datetime($value, string $field): void
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', (string) $value);
        if ($date === false || $date->format('Y-m-d H:i:s') !== (string) $value) {
            throw new ValidationException("Field '$field' must be a valid date and time (YYYY-MM-DD HH:MM:SS).");
        }
    }

    /** $value (YYYY-MM-DD HH:MM:SS) must be later than now. */
    public static function in_future($value, string $field): void
    {
        if ((string) $value <= date('Y-m-d H:i:s')) {
            throw new ValidationException("Field '$field' must be in the future.");
        }
    }

    /** $value (YYYY-MM-DD) must be earlier than today. */
    public static function in_past($value, string $field): void
    {
        if ((string) $value >= date('Y-m-d')) {
            throw new ValidationException("Field '$field' must be in the past.");
        }
    }

    /** At least 8 characters, including a letter and a number. */
    public static function password($value, string $field = 'password'): void
    {
        $value = (string) $value;
        if (strlen($value) < 8 || strlen($value) > 72 || !preg_match('/[A-Za-z]/', $value) || !preg_match('/[0-9]/', $value)) {
            throw new ValidationException("Field '$field' must have 8 to 72 characters, including a letter and a number.");
        }
    }

    /** Text must not contain < or >, so it cannot carry HTML or scripts (XSS). */
    public static function no_html($value, string $field): void
    {
        if (preg_match('/[<>]/', (string) $value)) {
            throw new ValidationException("Field '$field' must not contain HTML.");
        }
    }
}
