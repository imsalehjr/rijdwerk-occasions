<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\App;
use App\Business;
use App\Cars;
use App\Csrf;
use App\Database;
use App\Labels;
use App\Mailer;
use App\RateLimiter;
use App\Response;
use App\SpamGuard;
use App\Validator;
use App\View;
use DateTimeImmutable;

final class RequestController
{
    private const MAX_REQUESTS_PER_HOUR = 5;
    private const MAX_ATTEMPTS_PER_HOUR = 30;

    public function testDrive(string $slug): void
    {
        $this->handle($slug, 'test_drive');
    }

    public function question(string $slug): void
    {
        $this->handle($slug, 'question');
    }

    private function handle(string $slug, string $type): void
    {
        Csrf::verify();

        $car = Cars::findBySlug($slug);
        if (!$car) {
            Response::notFound();
        }
        $back = url('occasions/' . $car['slug']) . '#contact-auto';

        if (SpamGuard::isBot($type, $_POST)) {
            $_SESSION['_thanks'] = ['reference' => null, 'type' => $type, 'car' => Cars::fullTitle($car)];
            Response::redirect(url('bedankt'));
        }

        if (RateLimiter::tooMany('request_attempt', self::MAX_ATTEMPTS_PER_HOUR, 3600)) {
            http_response_code(429);
            header('Retry-After: 3600');
            View::render('pages/error', ['code' => 429, 'title' => 'Even geduld', 'message' => 'Je hebt veel aanvragen in korte tijd verstuurd. Probeer het over een uur opnieuw of bel ons op ' . Business::PHONE . '.', 'metaRobots' => 'noindex']);
            return;
        }
        RateLimiter::hit('request_attempt');

        $v = new Validator($_POST);
        $v->text('name', 'naam', 2, 100)->email('email');

        if ($type === 'test_drive') {
            $v->phone('phone', true)->text('message', 'opmerking', 0, 2000, false, true);
            if ($car['status'] !== 'available') {
                $v->addError('preferred_date', 'Voor deze auto kun je op dit moment geen proefrit plannen.');
            } else {
                $this->validateSlot($v);
            }
        } else {
            $v->phone('phone', false)->text('message', 'vraag', 10, 2000, true, true);
            if ($car['status'] === 'sold') {
                $v->addError('message', 'Deze auto is al verkocht.');
            }
        }
        $v->accepted('consent', 'Geef toestemming zodat we je aanvraag mogen verwerken.');

        if ($v->fails()) {
            Response::back($v->errors(), $v->old() + ['_form' => $type], $back);
        }

        if (RateLimiter::tooMany('request', self::MAX_REQUESTS_PER_HOUR, 3600)) {
            Response::back(['message' => 'Je hebt het maximale aantal aanvragen per uur bereikt. Bel ons gerust op ' . Business::PHONE . '.'], $v->old() + ['_form' => $type], $back);
        }

        $carLabel = Cars::fullTitle($car);
        $reference = Database::transaction(function () use ($v, $type, $car, $carLabel): string {
            $id = Database::insert(
                'INSERT INTO requests (reference, type, car_id, car_label, name, email, phone, preferred_date, preferred_time, message, consent_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
                [
                    'TMP-' . bin2hex(random_bytes(8)),
                    $type,
                    $car['id'],
                    $carLabel,
                    $v->get('name'),
                    $v->get('email'),
                    $v->get('phone'),
                    $v->get('preferred_date'),
                    $v->get('preferred_time'),
                    $v->get('message'),
                ]
            );
            $reference = sprintf('RW-%s-%04d', date('Y'), $id);
            Database::run('UPDATE requests SET reference = ? WHERE id = ?', [$reference, $id]);
            Database::run("INSERT INTO request_status_log (request_id, from_status, to_status) VALUES (?, NULL, 'new')", [$id]);

            return $reference;
        });
        RateLimiter::hit('request');

        $mailData = [
            'type' => $type,
            'reference' => $reference,
            'name' => $v->get('name'),
            'email' => $v->get('email'),
            'phone' => $v->get('phone'),
            'message' => $v->get('message'),
            'date' => $v->get('preferred_date'),
            'time' => $v->get('preferred_time'),
            'car' => $car,
            'carLabel' => $carLabel,
            'carUrl' => url('occasions/' . $car['slug']),
        ];
        $typeLabel = $type === 'test_drive' ? 'proefrit' : 'vraag';
        Mailer::send($v->get('email'), $v->get('name'), "We hebben je {$typeLabel} ontvangen ({$reference})", 'customer', $mailData);
        Mailer::send((string) App::env('MAIL_DEALER'), Business::NAME, 'Nieuwe ' . $typeLabel . ': ' . $carLabel . " ({$reference})", 'dealer', $mailData, $v->get('email'));

        unset($_SESSION['_old'], $_SESSION['_errors']);
        $_SESSION['_thanks'] = [
            'reference' => $reference,
            'type' => $type,
            'car' => $carLabel,
            'date' => $v->get('preferred_date'),
            'time' => $v->get('preferred_time'),
        ];
        Response::redirect(url('bedankt'));
    }

    private function validateSlot(Validator $v): void
    {
        $dateRaw = is_string($_POST['preferred_date'] ?? null) ? $_POST['preferred_date'] : '';
        $timeRaw = is_string($_POST['preferred_time'] ?? null) ? $_POST['preferred_time'] : '';
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateRaw);
        $tomorrow = new DateTimeImmutable('tomorrow');
        $limit = new DateTimeImmutable('today +30 days');

        if (!$date || $date->format('Y-m-d') !== $dateRaw) {
            $v->addError('preferred_date', 'Kies een datum voor je proefrit.');
            return;
        }
        if ($date < $tomorrow || $date > $limit) {
            $v->addError('preferred_date', 'Kies een datum vanaf morgen en binnen 30 dagen.');
            return;
        }
        $weekday = (int) $date->format('N');
        if ($weekday === 7) {
            $v->addError('preferred_date', 'Op zondag zijn we gesloten. Kies een andere dag.');
            return;
        }
        $slots = $weekday === 6 ? Labels::SATURDAY_SLOTS : Labels::TIME_SLOTS;
        if (!in_array($timeRaw, $slots, true)) {
            $v->addError('preferred_time', 'Kies een tijd die past bij de gekozen dag.');
            return;
        }
        $v->set('preferred_date', $date->format('Y-m-d'))->set('preferred_time', $timeRaw);
    }

    public function thanks(): void
    {
        $thanks = $_SESSION['_thanks'] ?? null;
        if (!$thanks) {
            Response::redirect(url('occasions'));
        }
        header('Cache-Control: no-store');
        View::render('pages/thanks', ['thanks' => $thanks, 'title' => 'Bedankt', 'metaRobots' => 'noindex']);
    }
}
