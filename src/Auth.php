<?php

declare(strict_types=1);

namespace App;

final class Auth
{
    private const IDLE_TIMEOUT = 1800;
    private const ABSOLUTE_TIMEOUT = 28800;
    private const BCRYPT_COST = 12;
    private const DUMMY_HASH = '$2y$12$nKerDsLmu9uCTbXuXUE.ruA4V7CyuAIiirihFRozLLNm6dgQAOoC.';

    public static function attempt(string $email, string $password): bool
    {
        $admin = Database::one('SELECT id, name, email, password_hash FROM admins WHERE email = ? AND is_active = 1', [mb_strtolower($email)]);
        $hash = $admin['password_hash'] ?? self::DUMMY_HASH;
        $valid = password_verify($password, $hash);

        if (!$admin || !$valid) {
            return false;
        }

        if (password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST])) {
            Database::run('UPDATE admins SET password_hash = ? WHERE id = ?', [self::hash($password), $admin['id']]);
        }

        session_regenerate_id(true);
        Csrf::rotate();
        $_SESSION['admin'] = [
            'id' => (int) $admin['id'],
            'name' => $admin['name'],
            'login_at' => time(),
            'last_seen' => time(),
            'ua' => hash('sha256', Request::userAgent()),
        ];
        Database::run('UPDATE admins SET last_login_at = UTC_TIMESTAMP() WHERE id = ?', [$admin['id']]);

        return true;
    }

    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => self::BCRYPT_COST]);
    }

    public static function check(): bool
    {
        $admin = $_SESSION['admin'] ?? null;
        if (!is_array($admin)) {
            return false;
        }
        $now = time();
        $expired = ($now - $admin['last_seen']) > self::IDLE_TIMEOUT
            || ($now - $admin['login_at']) > self::ABSOLUTE_TIMEOUT
            || !hash_equals($admin['ua'], hash('sha256', Request::userAgent()));
        if ($expired) {
            self::logout();
            Session::start();
            Session::flash('login', 'Je sessie is verlopen. Log opnieuw in.');
            return false;
        }
        $_SESSION['admin']['last_seen'] = $now;

        return true;
    }

    public static function require(): void
    {
        if (!self::check()) {
            if (Request::wantsJson()) {
                Response::json(['ok' => false, 'error' => 'Je bent uitgelogd. Log opnieuw in.'], 401);
            }
            Response::redirect(url('admin/login'));
        }
        header('Cache-Control: no-store');
        header('X-Robots-Tag: noindex, nofollow');
    }

    public static function id(): ?int
    {
        return $_SESSION['admin']['id'] ?? null;
    }

    public static function name(): string
    {
        return (string) ($_SESSION['admin']['name'] ?? '');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $p['path'],
                'secure' => $p['secure'],
                'httponly' => $p['httponly'],
                'samesite' => $p['samesite'],
            ]);
        }
        session_destroy();
    }
}
