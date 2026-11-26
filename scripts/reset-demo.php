<?php

declare(strict_types=1);

use App\App;
use App\Auth;
use App\Database;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/vendor/autoload.php';
App::boot(dirname(__DIR__));

if (!is_demo()) {
    fwrite(STDERR, "DEMO_MODE is not true. Refusing to reset.\n");
    exit(1);
}

$pdo = Database::pdo();

foreach (['schema.sql', 'seed.sql', 'demo.sql'] as $file) {
    $sql = (string) file_get_contents(dirname(__DIR__) . '/database/' . $file);
    $sql = preg_replace('/^\s*(CREATE DATABASE|USE)\b.*$/mi', '', $sql);
    $pdo->exec($sql);
}

$email = (string) env('DEMO_ADMIN_EMAIL', '');
$password = (string) env('DEMO_ADMIN_PASSWORD', '');
if ($email !== '' && $password !== '') {
    Database::run(
        'INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), is_active = 1',
        ['Demo beheerder', mb_strtolower($email), Auth::hash($password)]
    );
}

$referenced = [];
foreach ($pdo->query('SELECT filename FROM car_photos') as $row) {
    $referenced[substr((string) $row['filename'], 0, 32)] = true;
}
foreach (glob(dirname(__DIR__) . '/public/uploads/cars/*.webp') ?: [] as $file) {
    if (!isset($referenced[substr(basename($file), 0, 32)])) {
        unlink($file);
    }
}
foreach (glob(dirname(__DIR__) . '/storage/mail/*.eml') ?: [] as $file) {
    unlink($file);
}

echo 'Demo reset at ' . date('Y-m-d H:i:s') . "\n";
