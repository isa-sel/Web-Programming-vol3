<?php

declare(strict_types=1);

/**
 * Writes one line per event to backend/logs/info.log or backend/logs/error.log.
 * Milestone 3 uses it for the error handler and the external API;
 * Milestone 4 adds request logging middleware on top of it.
 */
class Logger
{
    public static function info(string $message): void
    {
        self::write('info.log', 'INFO', $message);
    }

    public static function error(string $message): void
    {
        self::write('error.log', 'ERROR', $message);
    }

    private static function write(string $file, string $level, string $message): void
    {
        $directory = dirname(__DIR__) . '/logs';
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $message = str_replace(["\r", "\n"], ' ', $message);
        $line = sprintf('[%s] %s %s%s', date('Y-m-d H:i:s'), $level, $message, PHP_EOL);

        // A logging problem must never break the API response.
        @file_put_contents($directory . '/' . $file, $line, FILE_APPEND | LOCK_EX);
    }
}
