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

    $routes->post(
        'archivos/procesar',
        '\App\Modules\AsuntosInternos\Controllers\Archivos_Controller::procesar'
    );

    $routes->get(
        'archivos/descargar/(:segment)',
        '\App\Modules\AsuntosInternos\Controllers\Archivos_Controller::descargar/$1'
    );

    $routes->post(
        'archivos/eliminar/(:segment)',
        '\App\Modules\AsuntosInternos\Controllers\Archivos_Controller::eliminar/$1'
    );
});