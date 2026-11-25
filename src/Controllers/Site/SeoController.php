<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Database;

final class SeoController
{
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $cars = Database::all(
            "SELECT slug, updated_at FROM cars WHERE is_published = 1 AND (status <> 'sold' OR sold_at > NOW() - INTERVAL 30 DAY) ORDER BY published_at DESC"
        );
        $brands = Database::all(
            "SELECT DISTINCT b.slug FROM brands b JOIN models m ON m.brand_id = b.id JOIN cars c ON c.model_id = m.id WHERE c.is_published = 1 AND c.status <> 'sold'"
        );
        $lastmod = Database::value('SELECT MAX(updated_at) FROM cars WHERE is_published = 1');

        $urls = [
            ['loc' => url('/'), 'lastmod' => $lastmod, 'priority' => '1.0'],
            ['loc' => url('occasions'), 'lastmod' => $lastmod, 'priority' => '0.9'],
            ['loc' => url('over-ons'), 'priority' => '0.5'],
            ['loc' => url('contact'), 'priority' => '0.6'],
            ['loc' => url('privacy'), 'priority' => '0.2'],
        ];
        foreach ($brands as $brand) {
            $urls[] = ['loc' => url('occasions', ['merk' => $brand['slug']]), 'lastmod' => $lastmod, 'priority' => '0.6'];
        }
        foreach ($cars as $car) {
            $urls[] = ['loc' => url('occasions/' . $car['slug']), 'lastmod' => $car['updated_at'], 'priority' => '0.8'];
        }

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($urls as $u) {
            $xml->startElement('url');
            $xml->writeElement('loc', $u['loc']);
            if (!empty($u['lastmod'])) {
                $xml->writeElement('lastmod', date('Y-m-d', strtotime($u['lastmod'])));
            }
            $xml->writeElement('priority', $u['priority']);
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endDocument();
        echo $xml->outputMemory();
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\n";
        echo "Disallow: " . parse_url(url('admin'), PHP_URL_PATH) . "\n";
        echo "Disallow: " . parse_url(url('api/'), PHP_URL_PATH) . "\n";
        echo "Disallow: " . parse_url(url('bedankt'), PHP_URL_PATH) . "\n";
        echo "\nSitemap: " . url('sitemap.xml') . "\n";
    }
}
