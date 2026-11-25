<?php

declare(strict_types=1);

namespace App;

use Throwable;

final class App
{
    private static array $env = [];
    private static string $root = '';
    private static string $path = '/';

    public static function boot(string $root): void
    {
        self::$root = $root;
        self::loadEnv($root . '/.env');

        $debug = self::env('APP_DEBUG') === 'true' && self::env('APP_ENV') === 'local';
        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', $root . '/storage/logs/php-error.log');
        date_default_timezone_set('Europe/Amsterdam');

        if (strlen((string) self::env('APP_KEY', '')) < 32) {
            http_response_code(500);
            exit('APP_KEY is missing or too short. Run the setup script or set it in .env.');
        }

        set_exception_handler(static function (Throwable $e) use ($debug): void {
            error_log((string) $e);
            if (!headers_sent()) {
                http_response_code(500);
            }
            if ($debug) {
                echo '<pre>' . e((string) $e) . '</pre>';
                return;
            }
            View::render('pages/error', ['code' => 500, 'title' => 'Er ging iets mis'], 'layout');
        });
    }

    public static function root(): string
    {
        return self::$root;
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        return self::$env[$key] ?? $default;
    }

    public static function setCurrentPath(string $path): void
    {
        self::$path = $path;
    }

    public static function currentPath(): string
    {
        return self::$path;
    }

    public static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? null) == 443);
    }

    private static function loadEnv(string $file): void
    {
        if (!is_file($file)) {
            http_response_code(500);
            exit('.env file not found. Copy .env.example to .env.');
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if (preg_match('/^"(.*)"$/', $value, $m) || preg_match("/^'(.*)'$/", $value, $m)) {
                $value = $m[1];
            }
            self::$env[$key] = $value;
        }
    }
}
