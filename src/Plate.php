<?php

declare(strict_types=1);

namespace App;

final class Plate
{
    private const SIDECODES = [
        1 => ['/^[A-Z]{2}\d{2}\d{2}$/', [2, 2, 2]],
        2 => ['/^\d{2}\d{2}[A-Z]{2}$/', [2, 2, 2]],
        3 => ['/^\d{2}[A-Z]{2}\d{2}$/', [2, 2, 2]],
        4 => ['/^[A-Z]{2}\d{2}[A-Z]{2}$/', [2, 2, 2]],
        5 => ['/^[A-Z]{2}[A-Z]{2}\d{2}$/', [2, 2, 2]],
        6 => ['/^\d{2}[A-Z]{2}[A-Z]{2}$/', [2, 2, 2]],
        7 => ['/^\d{2}[A-Z]{3}\d$/', [2, 3, 1]],
        8 => ['/^\d[A-Z]{3}\d{2}$/', [1, 3, 2]],
        9 => ['/^[A-Z]{2}\d{3}[A-Z]$/', [2, 3, 1]],
        10 => ['/^[A-Z]\d{3}[A-Z]{2}$/', [1, 3, 2]],
        11 => ['/^[A-Z]{3}\d{2}[A-Z]$/', [3, 2, 1]],
        12 => ['/^[A-Z]\d{2}[A-Z]{3}$/', [1, 2, 3]],
        13 => ['/^\d[A-Z]{2}\d{3}$/', [1, 2, 3]],
        14 => ['/^\d{3}[A-Z]{2}\d$/', [3, 2, 1]],
    ];

    public static function normalize(string $input): string
    {
        return strtoupper((string) preg_replace('/[\s\-.]/', '', $input));
    }

    public static function isValid(string $plate): bool
    {
        return self::sidecode($plate) !== null;
    }

    public static function sidecode(string $plate): ?int
    {
        if (!preg_match('/^[A-Z0-9]{6}$/', $plate)) {
            return null;
        }
        foreach (self::SIDECODES as $code => [$regex]) {
            if (preg_match($regex, $plate)) {
                return $code;
            }
        }

        return null;
    }

    public static function format(?string $plate): string
    {
        if ($plate === null || $plate === '') {
            return '';
        }
        $code = self::sidecode($plate);
        if ($code === null) {
            return $plate;
        }
        [$a, $b] = self::SIDECODES[$code][1];

        return substr($plate, 0, $a) . '-' . substr($plate, $a, $b) . '-' . substr($plate, $a + $b);
    }
}
