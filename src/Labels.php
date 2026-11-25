<?php

declare(strict_types=1);

namespace App;

final class Labels
{
    public const FUEL = [
        'petrol' => ['slug' => 'benzine', 'label' => 'Benzine'],
        'diesel' => ['slug' => 'diesel', 'label' => 'Diesel'],
        'hybrid' => ['slug' => 'hybride', 'label' => 'Hybride'],
        'phev' => ['slug' => 'plug-in-hybride', 'label' => 'Plug-in hybride'],
        'electric' => ['slug' => 'elektrisch', 'label' => 'Elektrisch'],
        'lpg' => ['slug' => 'lpg', 'label' => 'LPG'],
    ];

    public const TRANSMISSION = [
        'manual' => ['slug' => 'handgeschakeld', 'label' => 'Handgeschakeld'],
        'automatic' => ['slug' => 'automaat', 'label' => 'Automaat'],
    ];

    public const BODY = [
        'hatchback' => ['slug' => 'hatchback', 'label' => 'Hatchback'],
        'sedan' => ['slug' => 'sedan', 'label' => 'Sedan'],
        'estate' => ['slug' => 'stationwagen', 'label' => 'Stationwagen'],
        'suv' => ['slug' => 'suv', 'label' => 'SUV'],
        'mpv' => ['slug' => 'mpv', 'label' => 'MPV'],
        'coupe' => ['slug' => 'coupe', 'label' => 'Coupé'],
        'convertible' => ['slug' => 'cabrio', 'label' => 'Cabrio'],
    ];

    public const STATUS = [
        'available' => 'Beschikbaar',
        'reserved' => 'Gereserveerd',
        'sold' => 'Verkocht',
    ];

    public const REQUEST_TYPE = [
        'test_drive' => 'Proefrit',
        'question' => 'Vraag',
    ];

    public const REQUEST_STATUS = [
        'new' => 'Nieuw',
        'contacted' => 'Contact opgenomen',
        'scheduled' => 'Ingepland',
        'completed' => 'Afgerond',
        'cancelled' => 'Geannuleerd',
    ];

    public const FEATURE_CATEGORY = [
        'comfort' => 'Comfort',
        'safety' => 'Veiligheid',
        'multimedia' => 'Multimedia',
        'exterior' => 'Exterieur',
    ];

    public const TIME_SLOTS = ['09:30', '10:30', '11:30', '13:00', '14:00', '15:00', '16:00'];
    public const SATURDAY_SLOTS = ['10:00', '11:00', '12:00', '13:30', '14:30'];

    public static function fuel(string $key): string
    {
        return self::FUEL[$key]['label'] ?? $key;
    }

    public static function transmission(string $key): string
    {
        return self::TRANSMISSION[$key]['label'] ?? $key;
    }

    public static function body(string $key): string
    {
        return self::BODY[$key]['label'] ?? $key;
    }

    public static function status(string $key): string
    {
        return self::STATUS[$key] ?? $key;
    }

    public static function keyFromSlug(array $map, string $slug): ?string
    {
        foreach ($map as $key => $item) {
            if ($item['slug'] === $slug) {
                return $key;
            }
        }

        return null;
    }
}
