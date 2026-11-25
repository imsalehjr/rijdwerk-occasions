<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;
use App\Database;
use App\Labels;
use App\Request;
use App\Response;
use App\Session;
use App\View;

final class RequestController
{
    private const PER_PAGE = 20;

    public function index(): void
    {
        Auth::require();
        $type = Request::str($_GET, 'type', 20);
        $type = array_key_exists($type, Labels::REQUEST_TYPE) ? $type : '';
        $status = Request::str($_GET, 'status', 20);
        $status = $status === 'open' || array_key_exists($status, Labels::REQUEST_STATUS) ? $status : '';
        $search = Request::str($_GET, 'zoek', 80);
        $page = Request::int($_GET, 'pagina', 1, 1000) ?? 1;

        $where = ['1 = 1'];
        $params = [];
        if ($type !== '') {
            $where[] = 'r.type = ?';
            $params[] = $type;
        }
        if ($status === 'open') {
            $where[] = "r.status IN ('new','contacted','scheduled')";
        } elseif ($status !== '') {
            $where[] = 'r.status = ?';
            $params[] = $status;
        }
        if ($search !== '') {
            $where[] = '(r.name LIKE ? OR r.email LIKE ? OR r.reference LIKE ? OR r.car_label LIKE ?)';
            $like = '%' . addcslashes($search, '%_\\') . '%';
            array_push($params, $like, $like, $like, $like);
        }
        $whereSql = implode(' AND ', $where);
        $total = (int) Database::value("SELECT COUNT(*) FROM requests r WHERE {$whereSql}", $params);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $pages);
        $requests = Database::all(
            "SELECT r.id, r.reference, r.type, r.name, r.email, r.car_label, r.car_id, r.preferred_date, r.preferred_time, r.status, r.created_at,
                    (SELECT COUNT(*) FROM request_notes n WHERE n.request_id = r.id) AS notes
             FROM requests r WHERE {$whereSql} ORDER BY (r.status = 'new') DESC, r.created_at DESC
             LIMIT " . self::PER_PAGE . ' OFFSET ' . (($page - 1) * self::PER_PAGE),
            $params
        );

        View::render('admin/requests', compact('requests', 'type', 'status', 'search', 'page', 'pages', 'total') + ['title' => 'Aanvragen'], 'admin/layout');
    }

    public function show(string $id): void
    {
        Auth::require();
        $request = Database::one(
            'SELECT r.*, c.slug AS car_slug, c.status AS car_status FROM requests r LEFT JOIN cars c ON c.id = r.car_id WHERE r.id = ?',
            [(int) $id]
        );
        if (!$request) {
            Response::notFound();
        }
        $notes = Database::all(
            'SELECT n.body, n.created_at, a.name AS author FROM request_notes n LEFT JOIN admins a ON a.id = n.admin_id WHERE n.request_id = ? ORDER BY n.created_at DESC',
            [$request['id']]
        );
        $log = Database::all(
            'SELECT l.from_status, l.to_status, l.created_at, a.name AS author FROM request_status_log l LEFT JOIN admins a ON a.id = l.admin_id WHERE l.request_id = ? ORDER BY l.created_at DESC, l.id DESC',
            [$request['id']]
        );

        View::render('admin/request', ['title' => $request['reference'], 'request' => $request, 'notes' => $notes, 'log' => $log], 'admin/layout');
    }

    public function status(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $request = Database::one('SELECT id, status FROM requests WHERE id = ?', [(int) $id]);
        if (!$request) {
            Response::notFound();
        }
        $new = Request::str($_POST, 'status', 20);
        if (!array_key_exists($new, Labels::REQUEST_STATUS)) {
            Response::back(['status' => 'Kies een geldige status.'], [], url('admin/aanvragen/' . $request['id']));
        }
        if ($new !== $request['status']) {
            Database::transaction(static function () use ($request, $new): void {
                Database::run('UPDATE requests SET status = ? WHERE id = ?', [$new, $request['id']]);
                Database::run(
                    'INSERT INTO request_status_log (request_id, admin_id, from_status, to_status) VALUES (?, ?, ?, ?)',
                    [$request['id'], Auth::id(), $request['status'], $new]
                );
            });
            Session::flash('success', 'Status gewijzigd naar “' . Labels::REQUEST_STATUS[$new] . '”.');
        }
        Response::redirect(url('admin/aanvragen/' . $request['id']));
    }

    public function note(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $request = Database::one('SELECT id FROM requests WHERE id = ?', [(int) $id]);
        if (!$request) {
            Response::notFound();
        }
        $body = is_string($_POST['body'] ?? null) ? trim(str_replace("\r\n", "\n", $_POST['body'])) : '';
        $body = (string) preg_replace('/[^\P{C}\n]/u', '', $body);
        if ($body === '' || mb_strlen($body) > 2000) {
            Response::back(['body' => 'Schrijf een notitie van maximaal 2000 tekens.'], ['body' => mb_substr($body, 0, 2000)], url('admin/aanvragen/' . $request['id']) . '#notities');
        }
        Database::run('INSERT INTO request_notes (request_id, admin_id, body) VALUES (?, ?, ?)', [$request['id'], Auth::id(), $body]);
        Session::flash('success', 'Notitie toegevoegd.');
        Response::redirect(url('admin/aanvragen/' . $request['id']) . '#notities');
    }

    public function destroy(string $id): void
    {
        Auth::require();
        Csrf::verify();
        $request = Database::one('SELECT id, reference FROM requests WHERE id = ?', [(int) $id]);
        if (!$request) {
            Response::notFound();
        }
        if (($_POST['confirm'] ?? '') !== $request['reference']) {
            Response::back(['confirm' => 'Typ het referentienummer over om te bevestigen.'], [], url('admin/aanvragen/' . $request['id']) . '#verwijderen');
        }
        Database::run('DELETE FROM requests WHERE id = ?', [$request['id']]);
        Session::flash('success', 'Aanvraag ' . $request['reference'] . ' en alle persoonsgegevens zijn verwijderd.');
        Response::redirect(url('admin/aanvragen'));
    }
}
