<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Business;
use App\Cars;
use App\Database;
use App\View;

final class HomeController
{
    public function index(): void
    {
        $featured = Cars::featured(6);
        $hero = $featured[0] ?? null;
        $brands = Database::all(
            "SELECT b.slug, b.name, COUNT(*) AS total FROM cars c JOIN models m ON m.id = c.model_id JOIN brands b ON b.id = m.brand_id
             WHERE c.is_published = 1 AND c.status IN ('available','reserved') GROUP BY b.slug, b.name ORDER BY b.name"
        );
        $stock = (int) Database::value("SELECT COUNT(*) FROM cars WHERE is_published = 1 AND status IN ('available','reserved')");

        View::render('pages/home', [
            'featured' => $featured,
            'hero' => $hero,
            'brands' => $brands,
            'stock' => $stock,
            'canonical' => url('/'),
            'jsonLd' => [Business::schema()],
            'ogImage' => $hero && $hero['photo'] ? upload_url($hero['photo']) : null,
        ]);
    }
}
