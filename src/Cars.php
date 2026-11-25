<?php

declare(strict_types=1);

namespace App;

final class Cars
{
    private const LIST_COLUMNS = "c.id, c.slug, c.variant, c.price, c.year, c.mileage_km, c.fuel, c.transmission, c.body_type,
        c.status, c.is_featured, c.power_kw, c.published_at, b.name AS brand, b.slug AS brand_slug, m.name AS model, m.slug AS model_slug,
        (SELECT p.filename FROM car_photos p WHERE p.car_id = c.id ORDER BY p.sort_order, p.id LIMIT 1) AS photo";

    private const FROM = ' FROM cars c JOIN models m ON m.id = c.model_id JOIN brands b ON b.id = m.brand_id ';

    public static function search(CarFilter $filter): array
    {
        [$where, $params] = $filter->where();
        $total = (int) Database::value('SELECT COUNT(*)' . self::FROM . 'WHERE ' . $where, $params);
        $pages = max(1, (int) ceil($total / CarFilter::PER_PAGE));
        $filter->page = min($filter->page, $pages);
        $offset = ($filter->page - 1) * CarFilter::PER_PAGE;

        $items = Database::all(
            'SELECT ' . self::LIST_COLUMNS . self::FROM . 'WHERE ' . $where
            . ' ORDER BY ' . $filter->orderBy() . ' LIMIT ' . CarFilter::PER_PAGE . ' OFFSET ' . $offset,
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $filter->page];
    }

    public static function brandCounts(CarFilter $filter): array
    {
        [$where, $params] = $filter->where(false);

        return Database::all(
            'SELECT b.id, b.name, b.slug, COUNT(*) AS total' . self::FROM . 'WHERE ' . $where . ' GROUP BY b.id, b.name, b.slug ORDER BY b.name',
            $params
        );
    }

    public static function modelsByBrand(): array
    {
        $rows = Database::all(
            "SELECT b.slug AS brand, m.slug, m.name, COUNT(c.id) AS total FROM models m
             JOIN brands b ON b.id = m.brand_id
             JOIN cars c ON c.model_id = m.id AND c.is_published = 1 AND c.status IN ('available','reserved')
             GROUP BY b.slug, m.slug, m.name ORDER BY m.name"
        );
        $map = [];
        foreach ($rows as $row) {
            $map[$row['brand']][] = ['slug' => $row['slug'], 'name' => $row['name'], 'total' => (int) $row['total']];
        }

        return $map;
    }

    public static function featured(int $limit = 6): array
    {
        return Database::all(
            'SELECT ' . self::LIST_COLUMNS . self::FROM
            . "WHERE c.is_published = 1 AND c.status = 'available' ORDER BY c.is_featured DESC, c.published_at DESC LIMIT " . max(1, min(12, $limit))
        );
    }

    public static function byIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn ($id) => $id > 0)));
        $ids = array_slice($ids, 0, 24);
        if (!$ids) {
            return [];
        }
        $rows = Database::all(
            'SELECT ' . self::LIST_COLUMNS . ', c.color, c.doors, c.seats, c.engine_cc, c.range_km, c.battery_kwh, c.owners, c.apk_until, c.warranty_months'
            . self::FROM . 'WHERE c.is_published = 1 AND c.id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')',
            $ids
        );
        $byId = array_column($rows, null, 'id');

        return array_values(array_filter(array_map(static fn ($id) => $byId[$id] ?? null, $ids)));
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::one(
            'SELECT c.*, b.name AS brand, b.slug AS brand_slug, m.name AS model, m.slug AS model_slug' . self::FROM
            . 'WHERE c.slug = ? AND c.is_published = 1',
            [$slug]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one(
            'SELECT c.*, b.id AS brand_id, b.name AS brand, b.slug AS brand_slug, m.name AS model, m.slug AS model_slug' . self::FROM . 'WHERE c.id = ?',
            [$id]
        );
    }

    public static function photos(int $carId): array
    {
        return Database::all('SELECT id, filename, width, height, sort_order FROM car_photos WHERE car_id = ? ORDER BY sort_order, id', [$carId]);
    }

    public static function features(int $carId): array
    {
        $rows = Database::all(
            'SELECT f.id, f.name, f.category FROM car_features cf JOIN features f ON f.id = cf.feature_id WHERE cf.car_id = ? ORDER BY f.category, f.name',
            [$carId]
        );
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['category']][] = $row['name'];
        }

        return $grouped;
    }

    public static function similar(array $car, int $limit = 3): array
    {
        return Database::all(
            'SELECT ' . self::LIST_COLUMNS . self::FROM
            . "WHERE c.is_published = 1 AND c.status = 'available' AND c.id <> ?
               ORDER BY (c.body_type = ?) DESC, ABS(CAST(c.price AS SIGNED) - ?) ASC LIMIT " . max(1, min(6, $limit)),
            [$car['id'], $car['body_type'], (int) $car['price']]
        );
    }

    public static function registerView(int $carId): void
    {
        $ua = strtolower(Request::userAgent());
        if ($ua === '' || preg_match('/bot|crawl|spider|slurp|preview|headless/', $ua)) {
            return;
        }
        $seen = $_SESSION['_seen'] ?? [];
        if (in_array($carId, $seen, true)) {
            return;
        }
        $_SESSION['_seen'] = array_slice([...$seen, $carId], -100);
        Database::run('UPDATE cars SET view_count = view_count + 1 WHERE id = ?', [$carId]);
    }

    public static function title(array $car): string
    {
        return trim($car['brand'] . ' ' . $car['model']);
    }

    public static function fullTitle(array $car): string
    {
        return trim(self::title($car) . ' ' . ($car['variant'] ?? ''));
    }
}
