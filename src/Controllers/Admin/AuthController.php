<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;
use App\RateLimiter;
use App\Request;
use App\Response;
use App\Session;
use App\View;

final class AuthController
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW = 900;

    public function form(): void
    {
        if (Auth::check()) {
            Response::redirect(url('admin'));
        }
        header('Cache-Control: no-store');
        View::render('admin/login', ['title' => 'Inloggen'], 'admin/layout-auth');
    }

    public function login(): void
    {
        Csrf::verify();
        $email = mb_strtolower(Request::str($_POST, 'email', 190));
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $emailKey = hash_hmac('sha256', $email, (string) env('APP_KEY'));

        if (RateLimiter::tooMany('login_ip', self::MAX_ATTEMPTS * 4, self::WINDOW) || RateLimiter::tooMany('login_email', self::MAX_ATTEMPTS, self::WINDOW, $emailKey)) {
            Response::back(['login' => 'Te veel inlogpogingen. Wacht 15 minuten en probeer het opnieuw.'], ['email' => $email], url('admin/login'));
        }

        if ($email === '' || $password === '' || strlen($password) > 200 || !Auth::attempt($email, $password)) {
            RateLimiter::hit('login_ip');
            RateLimiter::hit('login_email', $emailKey);
            Response::back(['login' => 'Dit e-mailadres en wachtwoord horen niet bij elkaar.'], ['email' => $email], url('admin/login'));
        }

        RateLimiter::clear('login_email', $emailKey);
        Response::redirect(url('admin'));
    }

    public function logout(): void
    {
        Csrf::verify();
        Auth::logout();
        Session::start();
        Session::flash('login', 'Je bent uitgelogd.');
        Response::redirect(url('admin/login'));
    }
}
