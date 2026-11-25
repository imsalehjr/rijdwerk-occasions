<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Cars;
use App\Request;
use App\View;

final class CompareController
{
    public const MAX = 3;

    public function index(): void
    {
        $raw = Request::str($_GET, 'ids', 60);
        $ids = preg_match('/^\d+(,\d+){0,2}$/', $raw) ? explode(',', $raw) : [];
        $cars = array_slice(Cars::byIds($ids), 0, self::MAX);

        View::render('pages/compare', [
            'title' => 'Auto’s vergelijken',
            'cars' => $cars,
            'metaRobots' => 'noindex, follow',
            'hasIds' => $raw !== '',
        ]);
    }
}
