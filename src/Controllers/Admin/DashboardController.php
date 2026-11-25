<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth;
use App\Database;
use App\View;

final class DashboardController
{
    public function index(): void
    {
        Auth::require();

        $statusCounts = array_column(Database::all('SELECT status, COUNT(*) AS total FROM cars GROUP BY status'), 'total', 'status');
        $requestStats = Database::one(
            "SELECT
                SUM(status = 'new') AS new_total,
                SUM(status = 'new' AND type = 'test_drive') AS new_test_drives,
                SUM(created_at > NOW() - INTERVAL 7 DAY) AS last7,
                SUM(status IN ('new','contacted','scheduled')) AS open_total
             FROM requests"
        );
        $upcoming = Database::all(
            "SELECT r.id, r.reference, r.name, r.preferred_date, r.preferred_time, r.status, r.car_label
             FROM requests r WHERE r.type = 'test_drive' AND r.status IN ('new','contacted','scheduled') AND r.preferred_date >= CURDATE()
             ORDER BY r.preferred_date, r.preferred_time LIMIT 6"
        );
        $mostViewed = Database::all(
            "SELECT c.id, c.view_count, c.status, b.name AS brand, m.name AS model, c.variant,
                    (SELECT p.filename FROM car_photos p WHERE p.car_id = c.id ORDER BY p.sort_order LIMIT 1) AS photo
             FROM cars c JOIN models m ON m.id = c.model_id JOIN brands b ON b.id = m.brand_id
             WHERE c.status <> 'sold' ORDER BY c.view_count DESC LIMIT 5"
        );
        $recent = Database::all('SELECT id, reference, type, name, car_label, status, created_at FROM requests ORDER BY created_at DESC LIMIT 6');
        $perDay = Database::all(
            "SELECT DATE(created_at) AS day, COUNT(*) AS total FROM requests
             WHERE created_at > NOW() - INTERVAL 14 DAY GROUP BY DATE(created_at)"
        );
        $days = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $days[$d] = 0;
        }
        foreach ($perDay as $row) {
            if (isset($days[$row['day']])) {
                $days[$row['day']] = (int) $row['total'];
            }
        }

        View::render('admin/dashboard', [
            'title' => 'Dashboard',
            'statusCounts' => $statusCounts,
            'requestStats' => $requestStats,
            'upcoming' => $upcoming,
            'mostViewed' => $mostViewed,
            'recent' => $recent,
            'days' => $days,
        ], 'admin/layout');
    }
}
