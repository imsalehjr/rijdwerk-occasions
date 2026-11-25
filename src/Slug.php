<?php

declare(strict_types=1);

namespace App;

final class Slug
{
    public static function make(string $text): string
    {
        $text = \Normalizer::normalize($text, \Normalizer::FORM_D) ?: $text;
        $text = (string) preg_replace('/\p{Mn}+/u', '', $text);
        $text = strtolower((string) preg_replace('/[^A-Za-z0-9]+/', '-', $text));
        $text = trim($text, '-');

        return substr($text, 0, 140) ?: 'auto';
    }

    public static function unique(string $text): string
    {
        $base = self::make($text);
        $slug = $base;
        $i = 2;
        while (Database::value('SELECT 1 FROM cars WHERE slug = ?', [$slug])) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
