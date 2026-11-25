<?php

declare(strict_types=1);

namespace App;

final class Business
{
    public const NAME = 'Rijdwerk Occasions';
    public const STREET = 'Kanaaldijk 14';
    public const POSTCODE = '5466 XB';
    public const CITY = 'Bremdonk';
    public const REGION = 'Noord-Brabant';
    public const PHONE = '0413 – 000 100';
    public const PHONE_E164 = '+31413000100';
    public const EMAIL = 'verkoop@rijdwerk.test';
    public const KVK = '00000000';

    public const HOURS = [
        1 => ['Maandag', '09:00', '18:00'],
        2 => ['Dinsdag', '09:00', '18:00'],
        3 => ['Woensdag', '09:00', '18:00'],
        4 => ['Donderdag', '09:00', '18:00'],
        5 => ['Vrijdag', '09:00', '18:00'],
        6 => ['Zaterdag', '10:00', '16:00'],
        7 => ['Zondag', null, null],
    ];

    public static function isOpenNow(): bool
    {
        $day = self::HOURS[(int) date('N')];
        if ($day[1] === null) {
            return false;
        }
        $now = date('H:i');

        return $now >= $day[1] && $now < $day[2];
    }

    public static function schema(): array
    {
        $hours = [];
        $days = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach (self::HOURS as $n => [, $open, $close]) {
            if ($open !== null) {
                $hours[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $days[$n], 'opens' => $open, 'closes' => $close];
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'AutoDealer',
            'name' => self::NAME,
            'url' => url('/'),
            'telephone' => self::PHONE_E164,
            'email' => self::EMAIL,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => self::STREET,
                'postalCode' => self::POSTCODE,
                'addressLocality' => self::CITY,
                'addressRegion' => self::REGION,
                'addressCountry' => 'NL',
            ],
            'openingHoursSpecification' => $hours,
        ];
    }
}
