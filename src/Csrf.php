<?php

declare(strict_types=1);

namespace App;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function verify(): void
    {
        $sent = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $known = $_SESSION['_csrf'] ?? '';
        if (!is_string($sent) || $known === '' || !hash_equals($known, $sent)) {
            Response::forbidden('Je sessie is verlopen. Herlaad de pagina en probeer het opnieuw.');
        }
    }

    public static function rotate(): void
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
}
