<?php

declare(strict_types=1);

namespace App;

final class CarFilter
{
    public const PER_PAGE = 12;
    public const PRICE_MAX = 250000;
    public const MILEAGE_MAX = 400000;
    public const YEAR_MIN = 1990;

    public const SORTS = [
        'nieuwste' => ['label' => 'Nieuwste aanbod', 'sql' => 'c.published_at DESC, c.id DESC'],
        'prijs-oplopend' => ['label' => 'Prijs laag naar hoog', 'sql' => 'c.price ASC, c.id ASC'],
        'prijs-aflopend' => ['label' => 'Prijs hoog naar laag', 'sql' => 'c.price DESC, c.id DESC'],
        'bouwjaar-nieuw' => ['label' => 'Bouwjaar nieuw naar oud', 'sql' => 'c.year DESC, c.mileage_km ASC, c.id DESC'],
        'bouwjaar-oud' => ['label' => 'Bouwjaar oud naar nieuw', 'sql' => 'c.year ASC, c.id ASC'],
        'km-laag' => ['label' => 'Kilometerstand laag naar hoog', 'sql' => 'c.mileage_km ASC, c.id ASC'],
    ];

    public ?array $brand = null;
    public ?array $model = null;
    public ?int $priceMin = null;
    public ?int $priceMax = null;
    public ?int $yearMin = null;
    public ?int $yearMax = null;
    public ?int $mileageMax = null;
    public array $fuels = [];
    public ?string $transmission = null;
    public array $bodies = [];
    public string $search = '';
    public string $sort = 'nieuwste';
    public int $page = 1;

    public static function fromQuery(array $q): self
    {
        $f = new self();
        $yearMax = (int) date('Y') + 1;

        $brandSlug = Request::str($q, 'merk', 60);
        if ($brandSlug !== '') {
            $f->brand = Database::one('SELECT id, name, slug FROM brands WHERE slug = ?', [$brandSlug]);
        }
        $modelSlug = Request::str($q, 'model', 80);
        if ($f->brand && $modelSlug !== '') {
            $f->model = Database::one('SELECT id, name, slug FROM models WHERE brand_id = ? AND slug = ?', [$f->brand['id'], $modelSlug]);
        }

        $f->priceMin = Request::int($q, 'prijs_min', 0, self::PRICE_MAX);
        $f->priceMax = Request::int($q, 'prijs_max', 0, self::PRICE_MAX);
        if ($f->priceMin !== null && $f->priceMax !== null && $f->priceMin > $f->priceMax) {
            [$f->priceMin, $f->priceMax] = [$f->priceMax, $f->priceMin];
        }

        $f->yearMin = Request::int($q, 'bouwjaar_min', self::YEAR_MIN, $yearMax);
        $f->yearMax = Request::int($q, 'bouwjaar_max', self::YEAR_MIN, $yearMax);
        if ($f->yearMin !== null && $f->yearMax !== null && $f->yearMin > $f->yearMax) {
            [$f->yearMin, $f->yearMax] = [$f->yearMax, $f->yearMin];
        }

        $f->mileageMax = Request::int($q, 'km_max', 0, self::MILEAGE_MAX);
        $f->fuels = self::slugList($q['brandstof'] ?? '', Labels::FUEL);
        $f->bodies = self::slugList($q['carrosserie'] ?? '', Labels::BODY);
        $transmission = Request::str($q, 'transmissie', 30);
        $f->transmission = $transmission !== '' ? Labels::keyFromSlug(Labels::TRANSMISSION, $transmission) : null;

        $search = Request::str($q, 'zoek', 60);
        $f->search = preg_replace('/[^\p{L}\p{N} .\-]/u', '', $search) ?? '';

        $sort = Request::str($q, 'sorteer', 30);
        $f->sort = array_key_exists($sort, self::SORTS) ? $sort : 'nieuwste';
        $f->page = Request::int($q, 'pagina', 1, 10000) ?? 1;

        return $f;
    }

    private static function slugList(mixed $raw, array $map): array
    {
        $values = is_array($raw) ? $raw : explode(',', is_string($raw) ? $raw : '');
        $keys = [];
        foreach ($values as $slug) {
            if (!is_string($slug)) {
                continue;
            }
            $key = Labels::keyFromSlug($map, trim($slug));
            if ($key !== null) {
                $keys[$key] = true;
            }
        }

        return array_keys($keys);
    }

    public function where(bool $includeBrand = true): array
    {
        $sql = ['c.is_published = 1', "c.status IN ('available','reserved')"];
        $params = [];

        if ($includeBrand && $this->brand) {
            $sql[] = 'm.brand_id = ?';
            $params[] = $this->brand['id'];
            if ($this->model) {
                $sql[] = 'c.model_id = ?';
                $params[] = $this->model['id'];
            }
        }
        if ($this->priceMin !== null) {
            $sql[] = 'c.price >= ?';
            $params[] = $this->priceMin;
        }
        if ($this->priceMax !== null) {
            $sql[] = 'c.price <= ?';
            $params[] = $this->priceMax;
        }
        if ($this->yearMin !== null) {
            $sql[] = 'c.year >= ?';
            $params[] = $this->yearMin;
        }
        if ($this->yearMax !== null) {
            $sql[] = 'c.year <= ?';
            $params[] = $this->yearMax;
        }
        if ($this->mileageMax !== null) {
            $sql[] = 'c.mileage_km <= ?';
            $params[] = $this->mileageMax;
        }
        if ($this->fuels) {
            $sql[] = 'c.fuel IN (' . implode(',', array_fill(0, count($this->fuels), '?')) . ')';
            array_push($params, ...$this->fuels);
        }
        if ($this->transmission) {
            $sql[] = 'c.transmission = ?';
            $params[] = $this->transmission;
        }
        if ($this->bodies) {
            $sql[] = 'c.body_type IN (' . implode(',', array_fill(0, count($this->bodies), '?')) . ')';
            array_push($params, ...$this->bodies);
        }
        if ($this->search !== '') {
            $sql[] = "CONCAT_WS(' ', b.name, m.name, c.variant) LIKE ?";
            $params[] = '%' . addcslashes($this->search, '%_\\') . '%';
        }

        return [implode(' AND ', $sql), $params];
    }

    public function orderBy(): string
    {
        return self::SORTS[$this->sort]['sql'];
    }

    public function toQuery(array $override = []): array
    {
        $q = [
            'zoek' => $this->search,
            'merk' => $this->brand['slug'] ?? null,
            'model' => $this->model['slug'] ?? null,
            'prijs_min' => $this->priceMin,
            'prijs_max' => $this->priceMax,
            'bouwjaar_min' => $this->yearMin,
            'bouwjaar_max' => $this->yearMax,
            'km_max' => $this->mileageMax,
            'brandstof' => implode(',', array_map(static fn ($k) => Labels::FUEL[$k]['slug'], $this->fuels)),
            'transmissie' => $this->transmission ? Labels::TRANSMISSION[$this->transmission]['slug'] : null,
            'carrosserie' => implode(',', array_map(static fn ($k) => Labels::BODY[$k]['slug'], $this->bodies)),
            'sorteer' => $this->sort !== 'nieuwste' ? $this->sort : null,
            'pagina' => $this->page > 1 ? $this->page : null,
        ];
        $q = array_merge($q, $override);

        return array_filter($q, static fn ($v) => $v !== null && $v !== '');
    }

    public function activeChips(): array
    {
        $chips = [];
        if ($this->search !== '') {
            $chips[] = ['label' => '“' . $this->search . '”', 'remove' => ['zoek' => null]];
        }
        if ($this->brand) {
            $chips[] = ['label' => $this->brand['name'] . ($this->model ? ' ' . $this->model['name'] : ''), 'remove' => ['merk' => null, 'model' => null]];
        }
        if ($this->priceMin !== null || $this->priceMax !== null) {
            $chips[] = ['label' => self::rangeLabel($this->priceMin, $this->priceMax, static fn ($v) => money($v)), 'remove' => ['prijs_min' => null, 'prijs_max' => null]];
        }
        if ($this->yearMin !== null || $this->yearMax !== null) {
            $chips[] = ['label' => 'Bouwjaar ' . self::rangeLabel($this->yearMin, $this->yearMax, static fn ($v) => (string) $v), 'remove' => ['bouwjaar_min' => null, 'bouwjaar_max' => null]];
        }
        if ($this->mileageMax !== null) {
            $chips[] = ['label' => 'Max. ' . number_nl($this->mileageMax) . ' km', 'remove' => ['km_max' => null]];
        }
        foreach ($this->fuels as $fuel) {
            $rest = array_values(array_diff($this->fuels, [$fuel]));
            $chips[] = ['label' => Labels::fuel($fuel), 'remove' => ['brandstof' => implode(',', array_map(static fn ($k) => Labels::FUEL[$k]['slug'], $rest))]];
        }
        if ($this->transmission) {
            $chips[] = ['label' => Labels::transmission($this->transmission), 'remove' => ['transmissie' => null]];
        }
        foreach ($this->bodies as $body) {
            $rest = array_values(array_diff($this->bodies, [$body]));
            $chips[] = ['label' => Labels::body($body), 'remove' => ['carrosserie' => implode(',', array_map(static fn ($k) => Labels::BODY[$k]['slug'], $rest))]];
        }

        return $chips;
    }

    private static function rangeLabel(?int $min, ?int $max, callable $fmt): string
    {
        if ($min !== null && $max !== null) {
            return $fmt($min) . ' – ' . $fmt($max);
        }

        return $min !== null ? 'vanaf ' . $fmt($min) : 'tot ' . $fmt($max);
    }
}
