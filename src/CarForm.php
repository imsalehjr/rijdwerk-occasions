<?php

declare(strict_types=1);

namespace App;

final class CarForm
{
    public static function validate(array $in, ?int $carId = null): array
    {
        $errors = [];
        $data = [];
        $yearMax = (int) date('Y') + 1;

        $brand = trim(is_string($in['brand'] ?? null) ? $in['brand'] : '');
        if (!preg_match('/^[\p{L}\p{N}][\p{L}\p{N} .\-&]{0,58}$/u', $brand)) {
            $errors['brand'] = 'Vul een geldig merk in (letters, cijfers, spatie of streepje).';
        }
        $model = trim(is_string($in['model'] ?? null) ? $in['model'] : '');
        if (!preg_match('/^[\p{L}\p{N}][\p{L}\p{N} .\-+\/]{0,78}$/u', $model)) {
            $errors['model'] = 'Vul een geldig model in.';
        }
        $data['brand'] = $brand;
        $data['model'] = $model;
        $data['variant'] = Request::str($in, 'variant', 120);

        $plate = Plate::normalize(Request::str($in, 'plate', 12));
        if ($plate !== '' && !Plate::isValid($plate)) {
            $errors['plate'] = 'Dit is geen geldig Nederlands kenteken.';
        } elseif ($plate !== '' && Database::value('SELECT id FROM cars WHERE plate = ? AND id <> ?', [$plate, $carId ?? 0])) {
            $errors['plate'] = 'Er staat al een auto met dit kenteken in het systeem.';
        }
        $data['plate'] = $plate !== '' ? $plate : null;

        $ints = [
            'price' => ['Prijs', 500, 1_000_000, true],
            'year' => ['Bouwjaar', 1950, $yearMax, true],
            'mileage_km' => ['Kilometerstand', 0, 2_000_000, true],
            'power_kw' => ['Vermogen', 1, 1500, false],
            'engine_cc' => ['Cilinderinhoud', 1, 10000, false],
            'range_km' => ['Actieradius', 1, 2000, false],
            'doors' => ['Deuren', 1, 9, false],
            'seats' => ['Zitplaatsen', 1, 9, false],
            'owners' => ['Vorige eigenaren', 0, 20, false],
            'warranty_months' => ['Garantie', 0, 60, true],
        ];
        foreach ($ints as $key => [$label, $min, $max, $required]) {
            $raw = Request::str($in, $key, 20);
            if ($raw === '') {
                if ($required) {
                    $errors[$key] = "Vul {$label} in.";
                }
                $data[$key] = null;
                continue;
            }
            $value = Request::int($in, $key, $min, $max);
            if ($value === null) {
                $errors[$key] = "{$label} moet een getal zijn tussen " . number_nl($min) . ' en ' . number_nl($max) . '.';
            }
            $data[$key] = $value;
        }

        $battery = str_replace(',', '.', Request::str($in, 'battery_kwh', 10));
        if ($battery === '') {
            $data['battery_kwh'] = null;
        } elseif (!is_numeric($battery) || (float) $battery <= 0 || (float) $battery > 300) {
            $errors['battery_kwh'] = 'Accucapaciteit moet tussen 0 en 300 kWh liggen.';
        } else {
            $data['battery_kwh'] = round((float) $battery, 1);
        }

        foreach (['first_registration' => 'Datum eerste toelating', 'apk_until' => 'APK-datum'] as $key => $label) {
            $raw = Request::str($in, $key, 10);
            if ($raw === '') {
                $data[$key] = null;
                continue;
            }
            $d = \DateTimeImmutable::createFromFormat('!Y-m-d', $raw);
            if (!$d || $d->format('Y-m-d') !== $raw || (int) $d->format('Y') < 1950 || (int) $d->format('Y') > $yearMax + 3) {
                $errors[$key] = "{$label} is geen geldige datum.";
                $data[$key] = null;
            } else {
                $data[$key] = $raw;
            }
        }

        $enums = [
            'fuel' => array_keys(Labels::FUEL),
            'transmission' => array_keys(Labels::TRANSMISSION),
            'body_type' => array_keys(Labels::BODY),
            'status' => array_keys(Labels::STATUS),
        ];
        foreach ($enums as $key => $allowed) {
            $value = Request::str($in, $key, 20);
            if (!in_array($value, $allowed, true)) {
                $errors[$key] = 'Kies een waarde uit de lijst.';
            }
            $data[$key] = $value;
        }

        $data['color'] = Request::str($in, 'color', 40);
        $description = is_string($in['description'] ?? null) ? trim(str_replace("\r\n", "\n", $in['description'])) : '';
        $description = (string) preg_replace('/[^\P{C}\n]/u', '', $description);
        if (mb_strlen($description) < 20) {
            $errors['description'] = 'Schrijf een omschrijving van minimaal 20 tekens.';
        } elseif (mb_strlen($description) > 5000) {
            $errors['description'] = 'De omschrijving mag maximaal 5000 tekens zijn.';
        }
        $data['description'] = $description;
        $data['is_featured'] = ($in['is_featured'] ?? '') === '1' ? 1 : 0;
        $data['is_published'] = ($in['is_published'] ?? '') === '1' ? 1 : 0;

        $featureIds = array_map('intval', array_filter((array) ($in['features'] ?? []), static fn ($v) => is_string($v) && ctype_digit($v)));
        $valid = $featureIds ? array_map('intval', array_column(Database::all('SELECT id FROM features WHERE id IN (' . implode(',', array_fill(0, count($featureIds), '?')) . ')', $featureIds), 'id')) : [];
        $data['features'] = $valid;

        return [$data, $errors];
    }

    public static function save(array $data, ?int $carId = null): int
    {
        return Database::transaction(static function () use ($data, $carId): int {
            $brandId = Database::value('SELECT id FROM brands WHERE LOWER(name) = LOWER(?)', [$data['brand']]);
            if (!$brandId) {
                $brandId = Database::insert('INSERT INTO brands (name, slug) VALUES (?, ?)', [$data['brand'], Slug::make($data['brand'])]);
            }
            $modelSlug = Slug::make($data['model']);
            $modelId = Database::value('SELECT id FROM models WHERE brand_id = ? AND slug = ?', [$brandId, $modelSlug]);
            if (!$modelId) {
                $modelId = Database::insert('INSERT INTO models (brand_id, name, slug) VALUES (?, ?, ?)', [$brandId, $data['model'], $modelSlug]);
            }

            $current = $carId ? Database::one('SELECT status, slug FROM cars WHERE id = ?', [$carId]) : null;
            $soldAt = $data['status'] === 'sold' ? ($current && $current['status'] === 'sold' ? null : 'now') : 'clear';

            $fields = [
                'model_id' => $modelId,
                'variant' => $data['variant'],
                'plate' => $data['plate'],
                'price' => $data['price'],
                'year' => $data['year'],
                'first_registration' => $data['first_registration'],
                'mileage_km' => $data['mileage_km'],
                'fuel' => $data['fuel'],
                'transmission' => $data['transmission'],
                'body_type' => $data['body_type'],
                'color' => $data['color'],
                'power_kw' => $data['power_kw'],
                'engine_cc' => $data['engine_cc'],
                'battery_kwh' => $data['battery_kwh'],
                'range_km' => $data['range_km'],
                'doors' => $data['doors'],
                'seats' => $data['seats'],
                'owners' => $data['owners'],
                'apk_until' => $data['apk_until'],
                'warranty_months' => $data['warranty_months'],
                'description' => $data['description'],
                'status' => $data['status'],
                'is_featured' => $data['is_featured'],
                'is_published' => $data['is_published'],
            ];

            if ($carId === null) {
                $fields['slug'] = Slug::unique($data['brand'] . ' ' . $data['model'] . ' ' . $data['variant'] . ' ' . $data['year']);
                $columns = implode(', ', array_keys($fields));
                $placeholders = implode(', ', array_fill(0, count($fields), '?'));
                $carId = Database::insert("INSERT INTO cars ({$columns}, sold_at) VALUES ({$placeholders}, " . ($soldAt === 'now' ? 'NOW()' : 'NULL') . ')', array_values($fields));
            } else {
                $set = implode(', ', array_map(static fn ($k) => "{$k} = ?", array_keys($fields)));
                $soldSql = match ($soldAt) {
                    'now' => ', sold_at = NOW()',
                    'clear' => ', sold_at = NULL',
                    default => '',
                };
                Database::run("UPDATE cars SET {$set}{$soldSql} WHERE id = ?", [...array_values($fields), $carId]);
            }

            Database::run('DELETE FROM car_features WHERE car_id = ?', [$carId]);
            foreach ($data['features'] as $featureId) {
                Database::run('INSERT INTO car_features (car_id, feature_id) VALUES (?, ?)', [$carId, $featureId]);
            }

            return $carId;
        });
    }
}
