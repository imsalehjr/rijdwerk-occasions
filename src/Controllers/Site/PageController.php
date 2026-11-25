<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Business;
use App\Database;
use App\View;

final class PageController
{
    public function about(): void
    {
        $photo = Database::value(
            "SELECT p.filename FROM car_photos p JOIN cars c ON c.id = p.car_id
             WHERE c.is_published = 1 AND c.status = 'available' ORDER BY c.is_featured DESC, c.view_count DESC, p.sort_order LIMIT 1"
        );
        View::render('pages/about', [
            'title' => 'Over ons',
            'description' => 'Rijdwerk Occasions is een garage met showroom in Bremdonk. Elke occasion gaat eerst door onze eigen werkplaats.',
            'photo' => $photo,
            'canonical' => url('over-ons'),
        ]);
    }

    public function contact(): void
    {
        View::render('pages/contact', [
            'title' => 'Contact',
            'description' => 'Adres, openingstijden en route naar Rijdwerk Occasions aan de Kanaaldijk in Bremdonk.',
            'canonical' => url('contact'),
            'jsonLd' => [Business::schema()],
        ]);
    }

    public function privacy(): void
    {
        View::render('pages/privacy', [
            'title' => 'Privacyverklaring',
            'canonical' => url('privacy'),
        ]);
    }

    public function favorites(): void
    {
        View::render('pages/favorites', [
            'title' => 'Mijn favorieten',
            'metaRobots' => 'noindex',
        ]);
    }
}
