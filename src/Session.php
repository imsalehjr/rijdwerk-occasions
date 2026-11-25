<?php

declare(strict_types=1);

namespace App;

final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_name('rijdwerk_sid');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => base_path() !== '' ? rtrim(base_path(), '/') . '/' : '/',
            'secure' => App::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.sid_length', '48');
        ini_set('session.sid_bits_per_character', '6');
        session_start();
    }

    public static function consumeFormState(): void
    {
        if (isset($_SESSION['_errors_shown'])) {
            unset($_SESSION['_errors'], $_SESSION['_old'], $_SESSION['_errors_shown']);
        } elseif (isset($_SESSION['_errors']) || isset($_SESSION['_old'])) {
            $_SESSION['_errors_shown'] = true;
        }
    }

    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }
}
