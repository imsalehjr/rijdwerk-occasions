<?php

declare(strict_types=1);

use App\App;
use App\Controllers\Admin;
use App\Controllers\Site;
use App\Request;
use App\Response;
use App\Router;
use App\Session;

require dirname(__DIR__) . '/vendor/autoload.php';

App::boot(dirname(__DIR__));
Response::securityHeaders();
Session::start();
Session::consumeFormState();

$router = new Router();

$router->get('/', [Site\HomeController::class, 'index']);
$router->get('/occasions', [Site\CatalogController::class, 'index']);
$router->get('/occasions/{slug:[a-z0-9-]+}', [Site\CarController::class, 'show']);
$router->post('/occasions/{slug:[a-z0-9-]+}/proefrit', [Site\RequestController::class, 'testDrive']);
$router->post('/occasions/{slug:[a-z0-9-]+}/vraag', [Site\RequestController::class, 'question']);
$router->get('/bedankt', [Site\RequestController::class, 'thanks']);
$router->get('/favorieten', [Site\PageController::class, 'favorites']);
$router->get('/vergelijken', [Site\CompareController::class, 'index']);
$router->get('/over-ons', [Site\PageController::class, 'about']);
$router->get('/contact', [Site\PageController::class, 'contact']);
$router->get('/privacy', [Site\PageController::class, 'privacy']);
$router->get('/api/kaarten', [Site\CatalogController::class, 'cards']);
$router->get('/sitemap.xml', [Site\SeoController::class, 'sitemap']);
$router->get('/robots.txt', [Site\SeoController::class, 'robots']);

$router->get('/admin', [Admin\DashboardController::class, 'index']);
$router->get('/admin/login', [Admin\AuthController::class, 'form']);
$router->post('/admin/login', [Admin\AuthController::class, 'login']);
$router->post('/admin/logout', [Admin\AuthController::class, 'logout']);
$router->get('/admin/autos', [Admin\CarController::class, 'index']);
$router->get('/admin/autos/nieuw', [Admin\CarController::class, 'create']);
$router->post('/admin/autos/nieuw', [Admin\CarController::class, 'store']);
$router->get('/admin/autos/{id:\d+}', [Admin\CarController::class, 'edit']);
$router->post('/admin/autos/{id:\d+}', [Admin\CarController::class, 'update']);
$router->post('/admin/autos/{id:\d+}/verwijderen', [Admin\CarController::class, 'destroy']);
$router->post('/admin/autos/{id:\d+}/status', [Admin\CarController::class, 'quickStatus']);
$router->post('/admin/autos/{id:\d+}/fotos', [Admin\PhotoController::class, 'upload']);
$router->post('/admin/autos/{id:\d+}/fotos/volgorde', [Admin\PhotoController::class, 'reorder']);
$router->post('/admin/autos/{id:\d+}/fotos/{photo:\d+}/verwijderen', [Admin\PhotoController::class, 'destroy']);
$router->post('/admin/kenteken', [Admin\PlateController::class, 'lookup']);
$router->get('/admin/aanvragen', [Admin\RequestController::class, 'index']);
$router->get('/admin/aanvragen/{id:\d+}', [Admin\RequestController::class, 'show']);
$router->post('/admin/aanvragen/{id:\d+}/status', [Admin\RequestController::class, 'status']);
$router->post('/admin/aanvragen/{id:\d+}/notitie', [Admin\RequestController::class, 'note']);
$router->post('/admin/aanvragen/{id:\d+}/verwijderen', [Admin\RequestController::class, 'destroy']);

$path = Request::path();
App::setCurrentPath($path);
$router->dispatch(Request::method(), $path);
