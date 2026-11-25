<?php

declare(strict_types=1);

namespace App;

final class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(): string
    {
        $uri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $base = rtrim(base_path(), '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $uri = '/' . trim(rawurldecode($uri), '/');

        return $uri;
    }

    public static function isFetch(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    }

    public static function wantsJson(): bool
    {
        return self::isFetch() || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public static function str(array $source, string $key, int $max = 255): string
    {
        $value = $source[$key] ?? '';
        if (!is_string($value)) {
            return '';
        }
        $value = trim(str_replace("\0", '', $value));

        return mb_substr($value, 0, $max);
    }

    public static function int(array $source, string $key, ?int $min = null, ?int $max = null): ?int
    {
        $value = $source[$key] ?? null;
        if (!is_string($value) && !is_int($value)) {
            return null;
        }
        $value = is_string($value) ? str_replace(['.', ' '], '', trim($value)) : $value;
        $options = [];
        if ($min !== null) {
            $options['min_range'] = $min;
        }
        if ($max !== null) {
            $options['max_range'] = $max;
        }
        $result = filter_var($value, FILTER_VALIDATE_INT, ['options' => $options]);

        return $result === false ? null : $result;
    }

    public static function ip(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public static function ipHash(): string
    {
        return hash_hmac('sha256', self::ip(), (string) App::env('APP_KEY'));
    }

    public static function userAgent(): string
    {
        return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }
}
