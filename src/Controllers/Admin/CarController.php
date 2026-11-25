<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\App;
use App\Auth;
use App\CarForm;
use App\Cars;
use App\Csrf;
use App\Database;
use App\ImageUpload;
use App\Labels;
use App\Request;
use App\Response;
use App\Session;
use App\View;

final class CarController
{
    private const PER_PAGE = 20;

    public function index(): void
    {
        Auth::require();
        $status = Request::str($_GET, 'status', 20);
        $status = array_key_exists($status, Labels::STATUS) ? $status : '';
        $search = Request::str($_GET, 'zoek', 60);
        $page = Request::int($_GET, 'pagina', 1, 1000) ?? 1;

        $where = ['1 = 1'];
        $params = [];
        if ($status !== '') {
            $where[] = 'c.status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $where[] = "(CONCAT_WS(' ', b.name, m.name, c.variant) LIKE ? OR c.plate LIKE ?)";
            $like = '%' . addcslashes($search, '%_\\') . '%';
            array_push($params, $like, '%' . addcslashes(strtoupper(str_replace('-', '', $search)), '%_\\') . '%');
        }
        $from = ' FROM cars c JOIN models m ON m.id = c.model_id JOIN brands b ON b.id = m.brand_id WHERE ' . implode(' AND ', $where);
        $total = (int) Database::value('SELECT COUNT(*)' . $from, $params);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $cars = Database::all(
            'SELECT c.id, c.slug, c.variant, c.plate, c.price, c.year, c.mileage_km, c.status, c.is_featured, c.is_published, c.view_count, c.updated_at,
                    b.name AS brand, m.name AS model,
                    (SELECT p.filename FROM car_photos p WHERE p.car_id = c.id ORDER BY p.sort_order, p.id LIMIT 1) AS photo,
                    (SELECT COUNT(*) FROM car_photos p WHERE p.car_id = c.id) AS photo_count'
            . $from . ' ORDER BY c.updated_at DESC LIMIT ' . self::PER_PAGE . ' OFFSET ' . (($page - 1) * self::PER_PAGE),
            $params
        );
        $counts = array_column(Database::all('SELECT status, COUNT(*) AS total FROM cars GROUP BY status'), 'total', 'status');

        View::render('admin/cars', compact('cars', 'status', 'search', 'page', 'pages', 'total', 'counts') + ['title' => 'Auto’s'], 'admin/layout');
    }

    public function create(): void
    {
        Auth::require();
        View::render('admin/car-form', $this->formData(null), 'admin/layout');
    }

    public function store(): void
    {
        Auth::require();
        Csrf::verify();
        [$data, $errors] = CarForm::validate($_POST);
        if ($errors) {
            Response::back($errors, $this->old(), url('admin/autos/nieuw'));
        }
        $id = CarForm::save($data);
        Session::flash('success', 'Auto opgeslagen. Voeg nu foto’s toe.');
        Response::redirect(url('admin/autos/' . $id) . '#fotos');
    }

    public function edit(string $id): void
    {
        Auth::require();
        $car = Cars::find((int) $id);
        if (!$car) {
            Response::notFound();
        }
        View::render('admin/car-form', $this->formData($car), 'admin/layout');
    }

    public function update(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $car = Cars::find((int) $id);
        if (!$car) {
            Response::notFound();
        }
        [$data, $errors] = CarForm::validate($_POST, (int) $car['id']);
        if ($errors) {
            Response::back($errors, $this->old(), url('admin/autos/' . $car['id']));
        }
        CarForm::save($data, (int) $car['id']);
        Session::flash('success', 'Wijzigingen opgeslagen.');
        Response::redirect(url('admin/autos/' . $car['id']));
    }

    public function quickStatus(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $status = Request::str($_POST, 'status', 20);
        if (!array_key_exists($status, Labels::STATUS)) {
            Response::json(['ok' => false, 'error' => 'Onbekende status.'], 422);
        }
        $sql = $status === 'sold'
            ? "UPDATE cars SET status = ?, sold_at = COALESCE(sold_at, NOW()) WHERE id = ?"
            : 'UPDATE cars SET status = ?, sold_at = NULL WHERE id = ?';
        Database::run($sql, [$status, (int) $id]);
        if (Request::wantsJson()) {
            Response::json(['ok' => true, 'status' => $status, 'label' => Labels::status($status)]);
        }
        Session::flash('success', 'Status bijgewerkt.');
        Response::redirect(url('admin/autos'));
    }

    public function destroy(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $car = Cars::find((int) $id);
        if (!$car) {
            Response::notFound();
        }
        $photos = Cars::photos((int) $car['id']);
        Database::run('DELETE FROM cars WHERE id = ?', [$car['id']]);
        foreach ($photos as $photo) {
            ImageUpload::delete(App::root() . '/public/uploads/cars', $photo['filename']);
        }
        Session::flash('success', Cars::fullTitle($car) . ' is verwijderd.');
        Response::redirect(url('admin/autos'));
    }

    private function formData(?array $car): array
    {
        $features = Database::all('SELECT id, name, category FROM features ORDER BY category, name');
        $grouped = [];
        foreach ($features as $f) {
            $grouped[$f['category']][] = $f;
        }
        $selected = $car ? array_map('intval', array_column(Database::all('SELECT feature_id FROM car_features WHERE car_id = ?', [$car['id']]), 'feature_id')) : [];
        $models = [];
        foreach (Database::all('SELECT b.name AS brand, m.name FROM models m JOIN brands b ON b.id = m.brand_id ORDER BY m.name') as $row) {
            $models[$row['brand']][] = $row['name'];
        }

        return [
            'title' => $car ? Cars::fullTitle($car) : 'Nieuwe auto',
            'car' => $car,
            'photos' => $car ? Cars::photos((int) $car['id']) : [],
            'features' => $grouped,
            'selectedFeatures' => $selected,
            'brands' => array_column(Database::all('SELECT name FROM brands ORDER BY name'), 'name'),
            'models' => $models,
        ];
    }

    private function old(): array
    {
        $old = [];
        foreach ($_POST as $key => $value) {
            if (is_string($value) && $key !== '_token') {
                $old[$key] = mb_substr($value, 0, 5000);
            }
        }
        $old['features'] = implode(',', array_filter((array) ($_POST['features'] ?? []), 'is_string'));
        $old['_submitted'] = '1';

        return $old;
    }
}
