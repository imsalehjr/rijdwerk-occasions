<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\App;
use App\Auth;
use App\Cars;
use App\Csrf;
use App\Database;
use App\ImageUpload;
use App\RateLimiter;
use App\Response;
use RuntimeException;

final class PhotoController
{
    private function dir(): string
    {
        return App::root() . '/public/uploads/cars';
    }

    public function upload(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $car = Cars::find((int) $id);
        if (!$car) {
            Response::json(['ok' => false, 'error' => 'Auto niet gevonden.'], 404);
        }
        if (is_demo()) {
            Response::json(['ok' => false, 'error' => 'In de demo staat foto’s uploaden uit.'], 403);
        }
        if (empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            Response::json(['ok' => false, 'error' => 'De upload is groter dan de server toestaat. Upload minder foto’s tegelijk.'], 413);
        }
        if (RateLimiter::tooMany('upload', 120, 3600, 'admin-' . Auth::id())) {
            Response::json(['ok' => false, 'error' => 'Te veel uploads in korte tijd. Probeer het later opnieuw.'], 429);
        }

        $files = ImageUpload::normalizeFiles($_FILES['photos'] ?? []);
        if ($batchError = ImageUpload::validateBatch($files)) {
            Response::json(['ok' => false, 'error' => $batchError], 422);
        }
        $existing = (int) Database::value('SELECT COUNT(*) FROM car_photos WHERE car_id = ?', [$car['id']]);
        $room = ImageUpload::MAX_PER_CAR - $existing;
        if ($room <= 0) {
            Response::json(['ok' => false, 'error' => 'Deze auto heeft al het maximum van ' . ImageUpload::MAX_PER_CAR . ' foto’s.'], 422);
        }

        $added = [];
        $failed = [];
        $order = (int) Database::value('SELECT COALESCE(MAX(sort_order), -1) FROM car_photos WHERE car_id = ?', [$car['id']]);
        foreach ($files as $i => $file) {
            $label = mb_substr(basename((string) $file['name']), 0, 60) ?: 'Foto ' . ($i + 1);
            if (count($added) >= $room) {
                $failed[] = $label . ' is niet toegevoegd: maximum van ' . ImageUpload::MAX_PER_CAR . ' foto’s bereikt';
                continue;
            }
            RateLimiter::hit('upload', 'admin-' . Auth::id());
            try {
                $stored = ImageUpload::store($file, $this->dir());
                $photoId = Database::insert(
                    'INSERT INTO car_photos (car_id, filename, width, height, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$car['id'], $stored['filename'], $stored['width'], $stored['height'], ++$order]
                );
                $added[] = ['id' => $photoId, 'thumb' => upload_url(substr($stored['filename'], 0, 32) . '-640.webp')];
            } catch (RuntimeException $e) {
                $failed[] = $label . ' ' . $e->getMessage();
            }
        }

        Response::json(['ok' => $added !== [], 'added' => $added, 'errors' => $failed], $added ? 200 : 422);
    }

    public function reorder(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $payload = json_decode((string) ($_POST['order'] ?? ''), true);
        if (!is_array($payload) || !array_is_list($payload) || count($payload) > ImageUpload::MAX_PER_CAR) {
            Response::json(['ok' => false, 'error' => 'Ongeldige volgorde.'], 422);
        }
        $ids = array_map('intval', $payload);
        $current = array_map('intval', array_column(Database::all('SELECT id FROM car_photos WHERE car_id = ?', [(int) $id]), 'id'));
        sort($current);
        $check = $ids;
        sort($check);
        if ($check !== $current) {
            Response::json(['ok' => false, 'error' => 'De foto’s zijn intussen gewijzigd. Herlaad de pagina.'], 409);
        }
        Database::transaction(static function () use ($ids, $id): void {
            foreach ($ids as $position => $photoId) {
                Database::run('UPDATE car_photos SET sort_order = ? WHERE id = ? AND car_id = ?', [$position, $photoId, (int) $id]);
            }
        });
        Response::json(['ok' => true]);
    }

    public function destroy(string $id, string $photo): void
    {
        Auth::require();
        Csrf::verify();
        $row = Database::one('SELECT id, filename FROM car_photos WHERE id = ? AND car_id = ?', [(int) $photo, (int) $id]);
        if (!$row) {
            Response::json(['ok' => false, 'error' => 'Foto niet gevonden.'], 404);
        }
        Database::run('DELETE FROM car_photos WHERE id = ?', [$row['id']]);
        ImageUpload::delete($this->dir(), $row['filename']);
        Response::json(['ok' => true]);
    }
}
