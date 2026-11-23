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

    /** Open-Meteo needs no API key; only the URLs and the timeout are configured. */
    public static function WEATHER_GEOCODING_URL(): string
    {
        return self::environment('WEATHER_GEOCODING_URL', 'https://geocoding-api.open-meteo.com/v1/search');
    }

    public static function WEATHER_FORECAST_URL(): string
    {
        return self::environment('WEATHER_FORECAST_URL', 'https://api.open-meteo.com/v1/forecast');
    }

    /** ISO country code used to find venue cities, e.g. BA for Bosnia and Herzegovina. */
    public static function WEATHER_COUNTRY_CODE(): string
    {
        return self::environment('WEATHER_COUNTRY_CODE', 'BA');
    }

    public static function WEATHER_TIMEOUT_SECONDS(): int
    {
        return (int) self::environment('WEATHER_TIMEOUT_SECONDS', '5');
    }
}
