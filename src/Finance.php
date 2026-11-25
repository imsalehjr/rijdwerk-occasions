<?php

declare(strict_types=1);

namespace App;

final class Finance
{
    public const TERMS = [24, 36, 48, 60, 72];
    public const MIN_LOAN = 2500;

    public static function monthly(float $loan, float $annualRate, int $months): float
    {
        if ($loan <= 0 || $months <= 0) {
            return 0.0;
        }
        $r = $annualRate / 100 / 12;
        if ($r <= 0) {
            return $loan / $months;
        }

        return $loan * $r / (1 - (1 + $r) ** -$months);
    }

    public static function rate(): float
    {
        $rate = (float) str_replace(',', '.', (string) App::env('FINANCE_RATE', '8.9'));

        return max(0.0, min(25.0, $rate));
    }
}
