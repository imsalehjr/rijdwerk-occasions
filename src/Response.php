<?php

declare(strict_types=1);

namespace App;

final class Response
{
    public static function redirect(string $to, int $status = 303): never
    {
        header('Location: ' . $to, true, $status);
        exit;
    }

    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function notFound(): never
    {
        http_response_code(404);
        View::render('pages/error', [
            'code' => 404,
            'title' => 'Pagina niet gevonden',
            'metaRobots' => 'noindex',
        ]);
        exit;
    }

    public static function forbidden(string $message = 'Deze actie is niet toegestaan.'): never
    {
        http_response_code(403);
        if (Request::wantsJson()) {
            self::json(['ok' => false, 'error' => $message], 403);
        }
        View::render('pages/error', ['code' => 403, 'title' => 'Geen toegang', 'message' => $message, 'metaRobots' => 'noindex']);
        exit;
    }

    public static function back(array $errors = [], array $old = [], string $fallback = ''): never
    {
        $_SESSION['_errors'] = $errors;
        $_SESSION['_old'] = $old;
        $target = $fallback !== '' ? $fallback : url('/');
        self::redirect($target);
    }

    public static function securityHeaders(): void
    {
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'; object-src 'none'");
        if (App::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
