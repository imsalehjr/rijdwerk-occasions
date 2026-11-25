<?php

declare(strict_types=1);

use App\App;
use App\Csrf;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

function env(string $key, mixed $default = null): mixed
{
    return App::env($key, $default);
}

function url(string $path = '', array $query = []): string
{
    $base = rtrim((string) env('APP_URL', ''), '/');
    $path = '/' . ltrim($path, '/');
    $query = array_filter($query, static fn ($v) => $v !== null && $v !== '' && $v !== []);

    return $base . ($path === '/' ? '/' : $path) . ($query ? '?' . http_build_query($query) : '');
}

function base_path(): string
{
    return (string) (parse_url((string) env('APP_URL', ''), PHP_URL_PATH) ?? '');
}

function asset(string $path): string
{
    $useMin = env('APP_ENV', 'local') !== 'local';
    if ($useMin && preg_match('/\.(css|js)$/', $path)) {
        $min = preg_replace('/\.(css|js)$/', '.min.$1', $path);
        if (is_file(App::root() . '/public/assets/' . $min)) {
            $path = $min;
        }
    }
    $file = App::root() . '/public/assets/' . $path;
    $version = is_file($file) ? substr(md5((string) filemtime($file)), 0, 8) : '1';

    return url('assets/' . $path) . '?v=' . $version;
}

function upload_url(string $filename): string
{
    return url('uploads/cars/' . $filename);
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function money(int|float|null $amount, bool $decimals = false): string
{
    if ($amount === null) {
        return '';
    }

    return '€ ' . number_format((float) $amount, $decimals ? 2 : 0, ',', '.');
}

function number_nl(int|float|null $value): string
{
    return $value === null ? '' : number_format((float) $value, 0, ',', '.');
}

function date_nl(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }
    $months = ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];
    $out = date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);

    return $withTime ? $out . ', ' . date('H:i', $ts) : $out;
}

function old(string $key, string $default = ''): string
{
    $old = $_SESSION['_old'] ?? [];

    return isset($old[$key]) && is_string($old[$key]) ? $old[$key] : $default;
}

function errors(): array
{
    return $_SESSION['_errors'] ?? [];
}

function error_for(string $key): ?string
{
    return errors()[$key] ?? null;
}

function flash(string $key): ?string
{
    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $value;
}

function is_active(string $path): bool
{
    $current = App::currentPath();
    if ($path === '/') {
        return $current === '/';
    }

    return str_starts_with($current, $path);
}

function icon(string $name, string $class = 'icon'): string
{
    return '<svg class="' . e($class) . '" aria-hidden="true" focusable="false"><use href="' . e(asset('img/icons.svg')) . '#' . e($name) . '"></use></svg>';
}
