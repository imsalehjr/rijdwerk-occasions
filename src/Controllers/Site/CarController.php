<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\App;
use App\Business;
use App\Cars;
use App\Labels;
use App\Response;
use App\View;

final class CarController
{
    public function show(string $slug): void
    {
        $car = Cars::findBySlug($slug);
        if (!$car) {
            Response::notFound();
        }
        Cars::registerView((int) $car['id']);

        $photos = Cars::photos((int) $car['id']);
        $features = Cars::features((int) $car['id']);
        $title = Cars::fullTitle($car);
        $rate = (float) str_replace(',', '.', (string) App::env('FINANCE_RATE', '8.9'));

        View::render('pages/car', [
            'title' => $title . ' (' . $car['year'] . ')',
            'description' => sprintf(
                '%s uit %d met %s km, %s, %s. %s bij Rijdwerk Occasions in Bremdonk.',
                $title, $car['year'], number_nl($car['mileage_km']), strtolower(Labels::fuel($car['fuel'])),
                strtolower(Labels::transmission($car['transmission'])), money($car['price'])
            ),
            'car' => $car,
            'photos' => $photos,
            'features' => $features,
            'similar' => Cars::similar($car),
            'rate' => $rate,
            'canonical' => url('occasions/' . $car['slug']),
            'ogType' => 'product',
            'ogImage' => $photos ? upload_url($photos[0]['filename']) : null,
            'jsonLd' => [self::schema($car, $photos), self::breadcrumbs($car)],
            'bodyClass' => 'has-mobile-bar',
        ]);
    }

    private static function schema(array $car, array $photos): array
    {
        $availability = [
            'available' => 'https://schema.org/InStock',
            'reserved' => 'https://schema.org/LimitedAvailability',
            'sold' => 'https://schema.org/SoldOut',
        ][$car['status']];
        $fuel = ['petrol' => 'Gasoline', 'diesel' => 'Diesel', 'hybrid' => 'Hybrid', 'phev' => 'Plug-in hybrid', 'electric' => 'Electric', 'lpg' => 'LPG'][$car['fuel']];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Car',
            'name' => Cars::fullTitle($car),
            'brand' => ['@type' => 'Brand', 'name' => $car['brand']],
            'model' => $car['model'],
            'vehicleConfiguration' => $car['variant'],
            'vehicleModelDate' => (string) $car['year'],
            'productionDate' => (string) $car['year'],
            'bodyType' => Labels::body($car['body_type']),
            'color' => $car['color'],
            'fuelType' => $fuel,
            'vehicleTransmission' => $car['transmission'] === 'automatic' ? 'Automatic' : 'Manual',
            'mileageFromOdometer' => ['@type' => 'QuantitativeValue', 'value' => (int) $car['mileage_km'], 'unitCode' => 'KMT'],
            'itemCondition' => 'https://schema.org/UsedCondition',
            'url' => url('occasions/' . $car['slug']),
            'image' => array_map(static fn ($p) => upload_url($p['filename']), array_slice($photos, 0, 5)),
            'description' => mb_substr(str_replace("\n", ' ', $car['description']), 0, 300),
            'offers' => [
                '@type' => 'Offer',
                'price' => (int) $car['price'],
                'priceCurrency' => 'EUR',
                'availability' => $availability,
                'seller' => ['@type' => 'AutoDealer', 'name' => Business::NAME],
            ],
        ];
        if ($car['doors']) {
            $schema['numberOfDoors'] = (int) $car['doors'];
        }
        if ($car['seats']) {
            $schema['seatingCapacity'] = (int) $car['seats'];
        }
        if ($car['first_registration']) {
            $schema['dateVehicleFirstRegistered'] = $car['first_registration'];
        }
        if ($car['power_kw']) {
            $schema['vehicleEngine'] = ['@type' => 'EngineSpecification', 'enginePower' => ['@type' => 'QuantitativeValue', 'value' => (int) $car['power_kw'], 'unitCode' => 'KWT']];
        }

        return $schema;
    }

    private static function breadcrumbs(array $car): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Occasions', 'item' => url('occasions')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $car['brand'], 'item' => url('occasions', ['merk' => $car['brand_slug']])],
                ['@type' => 'ListItem', 'position' => 4, 'name' => Cars::fullTitle($car)],
            ],
        ];
    }
}
