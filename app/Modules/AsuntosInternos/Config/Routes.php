<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('asuntos-internos', static function ($routes) {
    $routes->get(
        '/',
        '\App\Modules\AsuntosInternos\Controllers\Inicio_Controller::index'
    );
});