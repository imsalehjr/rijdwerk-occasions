<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\CarFilter;
use App\Cars;
use App\Labels;
use App\Request;
use App\Response;
use App\View;

final class CatalogController
{
    public function index(): void
    {
        $filter = CarFilter::fromQuery($_GET);

        if (isset($_GET['aantal']) && Request::isFetch()) {
            $result = Cars::search($filter);
            Response::json(['total' => $result['total']]);
        }

        $result = Cars::search($filter);

        if (Request::isFetch()) {
            header('Cache-Control: no-store');
            header('Vary: X-Requested-With');
            echo View::partial('results', ['filter' => $filter, 'result' => $result]);
            return;
        }

        $title = 'Occasions';
        if ($filter->brand) {
            $title = trim($filter->brand['name'] . ' ' . ($filter->model['name'] ?? '')) . ' occasions';
        }
        $query = $filter->toQuery();
        $isFiltered = count(array_diff_key($query, ['pagina' => 1])) > 0;
        $canonicalQuery = array_intersect_key($query, ['merk' => 1, 'model' => 1, 'pagina' => 1]);

        View::render('pages/catalog', [
            'title' => $title,
            'description' => 'Bekijk ' . $result['total'] . ' gecontroleerde occasions bij Rijdwerk Occasions in Bremdonk. Filter op merk, prijs, bouwjaar, kilometerstand en brandstof.',
            'filter' => $filter,
            'result' => $result,
            'brands' => Cars::brandCounts($filter),
            'models' => Cars::modelsByBrand(),
            'canonical' => url('occasions', $canonicalQuery),
            'metaRobots' => $isFiltered && count($query) > 2 ? 'noindex, follow' : null,
        ]);
    }

    public function cards(): void
    {
        $raw = Request::str($_GET, 'ids', 200);
        $ids = preg_match('/^\d+(,\d+)*$/', $raw) ? explode(',', $raw) : [];
        $cars = Cars::byIds($ids);
        header('Cache-Control: no-store');
        header('X-Robots-Tag: noindex');
        foreach ($cars as $car) {
            echo View::partial('car-card', ['car' => $car]);
        }
    }
}
