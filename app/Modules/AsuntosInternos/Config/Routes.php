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

    $routes->get(
        'archivos',
        '\App\Modules\AsuntosInternos\Controllers\Archivos_Controller::index'
    );

});