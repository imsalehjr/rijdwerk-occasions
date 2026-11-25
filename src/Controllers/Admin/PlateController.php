<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;
use App\Plate;
use App\RateLimiter;
use App\Rdw;
use App\Request;
use App\Response;
use RuntimeException;

final class PlateController
{
    public function lookup(): void
    {
        Auth::require();
        Csrf::verify();

        $plate = Plate::normalize(Request::str($_POST, 'plate', 12));
        if (!Plate::isValid($plate)) {
            Response::json(['ok' => false, 'error' => 'Dit is geen geldig Nederlands kenteken. Gebruik bijvoorbeeld K-482-TB.'], 422);
        }
        if (RateLimiter::tooMany('rdw', 30, 600, 'admin-' . Auth::id())) {
            Response::json(['ok' => false, 'error' => 'Je hebt veel kentekens opgevraagd. Wacht een paar minuten.'], 429);
        }
        RateLimiter::hit('rdw', 'admin-' . Auth::id());

        try {
            $data = Rdw::lookup($plate);
        } catch (RuntimeException $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 502);
        }

        Response::json(['ok' => true, 'plate' => $plate, 'formatted' => Plate::format($plate), 'data' => $data]);
    }
}
