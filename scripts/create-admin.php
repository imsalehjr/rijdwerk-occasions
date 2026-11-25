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

$email = mb_strtolower(trim((string) readline('E-mail: ')));
$name = trim((string) readline('Name: '));
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $name === '') {
    fwrite(STDERR, "Invalid e-mail or name.\n");
    exit(1);
}
echo 'Password (min. 12 characters): ';
if (stripos(PHP_OS, 'WIN') !== 0) {
    system('stty -echo');
}
$password = trim((string) fgets(STDIN));
if (stripos(PHP_OS, 'WIN') !== 0) {
    system('stty echo');
}
echo "\n";
if (mb_strlen($password) < 12) {
    fwrite(STDERR, "Password must be at least 12 characters.\n");
    exit(1);
}

Database::run(
    'INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE name = VALUES(name), password_hash = VALUES(password_hash), is_active = 1',
    [$name, $email, Auth::hash($password)]
);
echo "Admin {$email} saved.\n";
