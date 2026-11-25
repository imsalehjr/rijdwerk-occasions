<?php

declare(strict_types=1);

namespace App;

final class SpamGuard
{
    private const MIN_SECONDS = 3;
    private const MAX_SECONDS = 7200;

    public static function fields(string $form): string
    {
        $issued = time();
        $sig = hash_hmac('sha256', $form . '|' . $issued, (string) App::env('APP_KEY'));

        return '<div class="hp" aria-hidden="true"><label for="hp-' . e($form) . '">Laat dit veld leeg</label>'
            . '<input type="text" id="hp-' . e($form) . '" name="website" tabindex="-1" autocomplete="off"></div>'
            . '<input type="hidden" name="_ts" value="' . $issued . '.' . e($sig) . '">';
    }

    public static function isBot(string $form, array $input): bool
    {
        if (($input['website'] ?? '') !== '') {
            return true;
        }
        $raw = is_string($input['_ts'] ?? null) ? $input['_ts'] : '';
        if (!preg_match('/^(\d{10})\.([a-f0-9]{64})$/', $raw, $m)) {
            return true;
        }
        $expected = hash_hmac('sha256', $form . '|' . $m[1], (string) App::env('APP_KEY'));
        if (!hash_equals($expected, $m[2])) {
            return true;
        }
        $age = time() - (int) $m[1];

        return $age < self::MIN_SECONDS || $age > self::MAX_SECONDS;
    }
}
