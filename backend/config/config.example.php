<?php

declare(strict_types=1);

final class Config
{
    private static function environment(string $name, string $default): string
    {
        $value = getenv($name);

        return $value === false ? $default : $value;
    }

    public static function DB_HOST(): string
    {
        return self::environment('DB_HOST', 'localhost');
    }

    public static function DB_PORT(): int
    {
        return (int) self::environment('DB_PORT', '3306');
    }

    public static function DB_NAME(): string
    {
        return self::environment('DB_NAME', 'rukometni_centar');
    }

    public static function DB_USER(): string
    {
        return self::environment('DB_USER', 'root');
    }

    public static function DB_PASSWORD(): string
    {
        return self::environment('DB_PASSWORD', '');
    }
}
