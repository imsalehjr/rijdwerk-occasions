<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class Rdw
{
    private const VEHICLES = 'https://opendata.rdw.nl/resource/m9d7-ebf2.json';
    private const FUELS = 'https://opendata.rdw.nl/resource/8ys7-d773.json';
    private const MAX_BYTES = 200_000;
    private const CACHE_HOURS = 24;

    private const UPPER_BRANDS = ['BMW', 'KIA', 'MG', 'DS', 'MINI', 'BYD', 'GMC', 'MAN', 'DAF', 'NIO'];
    private const BODY_MAP = [
        'hatchback' => 'hatchback',
        'stationwagen' => 'estate',
        'sedan' => 'sedan',
        'mpv' => 'mpv',
        'cabriolet' => 'convertible',
        'coupe' => 'coupe',
        'coupé' => 'coupe',
        'terreinvoertuig' => 'suv',
        'gesloten opbouw' => 'suv',
    ];

    public static function lookup(string $plate): array
    {
        $cached = Database::one('SELECT payload FROM rdw_cache WHERE plate = ? AND fetched_at > NOW() - INTERVAL ? HOUR', [$plate, self::CACHE_HOURS]);
        if ($cached) {
            $data = json_decode($cached['payload'], true);
            if (is_array($data)) {
                return $data + ['cached' => true];
            }
        }

        $vehicles = self::fetch(self::VEHICLES, $plate, 'vehicle');
        if ($vehicles === []) {
            throw new RuntimeException('Geen voertuig gevonden met kenteken ' . Plate::format($plate) . '. Controleer het kenteken.');
        }
        $vehicle = $vehicles[0];
        if (!is_array($vehicle)) {
            throw new RuntimeException('De RDW gaf een onverwacht antwoord. Probeer het later opnieuw of vul de gegevens handmatig in.');
        }
        $kind = self::str($vehicle, 'voertuigsoort', 40);
        if ($kind !== '' && mb_strtolower($kind) !== 'personenauto') {
            throw new RuntimeException('Dit kenteken hoort bij een ' . mb_strtolower($kind) . ', niet bij een personenauto.');
        }

        $fuels = self::fetch(self::FUELS, $plate, 'fuel');
        $result = self::map($vehicle, $fuels);
        Database::run(
            'REPLACE INTO rdw_cache (plate, payload, fetched_at) VALUES (?, ?, NOW())',
            [$plate, json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)]
        );

        return $result + ['cached' => false];
    }

    private static function fetch(string $endpoint, string $plate, string $kind): array
    {
        if (App::env('RDW_DRIVER', 'live') === 'fake') {
            $file = App::root() . '/tests/fixtures/rdw-' . $kind . '-' . $plate . '.json';
            if ($plate === 'XX999X') {
                throw new RuntimeException('De RDW is op dit moment niet bereikbaar. Probeer het over een paar minuten opnieuw of vul de gegevens handmatig in.');
            }

            return is_file($file) ? self::decode((string) file_get_contents($file)) : [];
        }

        $url = $endpoint . '?' . http_build_query(['kenteken' => $plate]);
        $headers = ['Accept: application/json'];
        $token = (string) App::env('RDW_APP_TOKEN', '');
        if ($token !== '' && preg_match('/^[A-Za-z0-9]{10,64}$/', $token)) {
            $headers[] = 'X-App-Token: ' . $token;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_USERAGENT => 'RijdwerkOccasions/1.0',
            CURLOPT_MAXFILESIZE => self::MAX_BYTES,
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($errno === CURLE_OPERATION_TIMEDOUT) {
            throw new RuntimeException('De RDW reageerde niet op tijd. Probeer het opnieuw of vul de gegevens handmatig in.');
        }
        if ($body === false || $errno !== 0) {
            error_log('RDW request failed, curl errno ' . $errno);
            throw new RuntimeException('De RDW is op dit moment niet bereikbaar. Probeer het over een paar minuten opnieuw of vul de gegevens handmatig in.');
        }
        if ($status === 429) {
            throw new RuntimeException('Er zijn te veel kentekens opgevraagd. Wacht een minuut en probeer het opnieuw.');
        }
        if ($status !== 200) {
            error_log('RDW returned HTTP ' . $status);
            throw new RuntimeException('De RDW gaf een foutmelding (' . $status . '). Vul de gegevens handmatig in.');
        }
        if (strlen((string) $body) > self::MAX_BYTES) {
            throw new RuntimeException('Het antwoord van de RDW was onverwacht groot en is genegeerd.');
        }

        return self::decode((string) $body);
    }

    private static function decode(string $json): array
    {
        try {
            $data = json_decode($json, true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new RuntimeException('De RDW gaf een onleesbaar antwoord. Vul de gegevens handmatig in.');
        }
        if (!is_array($data) || !array_is_list($data)) {
            throw new RuntimeException('De RDW gaf een onverwacht antwoord. Vul de gegevens handmatig in.');
        }

        return array_slice($data, 0, 10);
    }

    private static function map(array $v, array $fuelRows): array
    {
        $brand = self::brandName(self::str($v, 'merk', 60));
        $model = self::modelName(self::str($v, 'handelsbenaming', 80), $brand);

        $firstReg = self::date(self::str($v, 'datum_eerste_toelating', 8));
        $apk = self::date(self::str($v, 'vervaldatum_apk', 8));
        $color = self::str($v, 'eerste_kleur', 40);
        $color = ($color === '' || stripos($color, 'n.v.t') !== false || stripos($color, 'niet geregistreerd') !== false) ? '' : mb_convert_case(mb_strtolower($color), MB_CASE_TITLE);

        $bodyRaw = mb_strtolower(self::str($v, 'inrichting', 40));
        $body = self::BODY_MAP[$bodyRaw] ?? null;

        $fuelNames = [];
        $power = null;
        foreach ($fuelRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $fuelNames[] = mb_strtolower(self::str($row, 'brandstof_omschrijving', 30));
            foreach (['nettomaximumvermogen', 'netto_max_vermogen_elektrisch'] as $field) {
                $kw = self::number($row, $field, 1, 1500);
                if ($kw !== null && ($power === null || $kw > $power)) {
                    $power = (int) round($kw);
                }
            }
        }

        return [
            'brand' => $brand,
            'model' => $model,
            'year' => $firstReg ? (int) substr($firstReg, 0, 4) : null,
            'first_registration' => $firstReg,
            'apk_until' => $apk,
            'color' => $color,
            'body_type' => $body,
            'body_raw' => $bodyRaw,
            'fuel' => self::fuel($fuelNames),
            'power_kw' => $power,
            'engine_cc' => ($cc = self::number($v, 'cilinderinhoud', 1, 10000)) !== null ? (int) $cc : null,
            'doors' => ($d = self::number($v, 'aantal_deuren', 1, 9)) !== null ? (int) $d : null,
            'seats' => ($s = self::number($v, 'aantal_zitplaatsen', 1, 9)) !== null ? (int) $s : null,
            'catalog_price' => ($p = self::number($v, 'catalogusprijs', 1, 2_000_000)) !== null ? (int) $p : null,
        ];
    }

    private static function str(array $row, string $key, int $max): string
    {
        $value = $row[$key] ?? '';
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return '';
        }
        $value = (string) preg_replace('/[^\p{L}\p{N} .,\-\/()&+\']/u', '', (string) $value);

        return trim(mb_substr($value, 0, $max));
    }

    private static function number(array $row, string $key, float $min, float $max): ?float
    {
        $value = $row[$key] ?? null;
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return null;
        }
        $value = str_replace(',', '.', (string) $value);
        if (!is_numeric($value)) {
            return null;
        }
        $n = (float) $value;

        return $n >= $min && $n <= $max ? $n : null;
    }

    private static function date(string $raw): ?string
    {
        if (!preg_match('/^(\d{4})(\d{2})(\d{2})$/', $raw, $m) || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return null;
        }
        if ((int) $m[1] < 1950 || (int) $m[1] > (int) date('Y') + 3) {
            return null;
        }

        return "{$m[1]}-{$m[2]}-{$m[3]}";
    }

    private static function fuel(array $names): ?string
    {
        $names = array_unique(array_filter($names));
        $hasElectric = in_array('elektriciteit', $names, true);
        $others = array_diff($names, ['elektriciteit']);
        if ($hasElectric && !$others) {
            return 'electric';
        }
        if ($hasElectric) {
            return 'hybrid';
        }

        return match (true) {
            in_array('diesel', $names, true) => 'diesel',
            in_array('lpg', $names, true) => 'lpg',
            in_array('benzine', $names, true) => 'petrol',
            default => null,
        };
    }

    private static function brandName(string $raw): string
    {
        if ($raw === '') {
            return '';
        }
        $known = Database::value('SELECT name FROM brands WHERE LOWER(name) = LOWER(?)', [$raw]);
        if (is_string($known)) {
            return $known;
        }
        if (in_array(strtoupper($raw), self::UPPER_BRANDS, true)) {
            return strtoupper($raw);
        }

        return implode('-', array_map(
            static fn ($part) => mb_convert_case(mb_strtolower($part), MB_CASE_TITLE),
            explode('-', $raw)
        ));
    }

    private static function modelName(string $raw, string $brand): string
    {
        if ($brand !== '' && stripos($raw, $brand . ' ') === 0) {
            $raw = substr($raw, strlen($brand) + 1);
        }
        $words = preg_split('/\s+/', trim($raw)) ?: [];

        return implode(' ', array_map(
            static fn ($w) => preg_match('/\d/', $w) || mb_strlen($w) <= 2 ? mb_strtoupper($w) : mb_convert_case(mb_strtolower($w), MB_CASE_TITLE),
            $words
        ));
    }
}
