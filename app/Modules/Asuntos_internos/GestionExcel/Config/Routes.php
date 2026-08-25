<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('asuntos-internos', static function ($routes) {
    $routes->get(
        '/',
        '\App\Modules\Asuntos_internos\GestionExcel\Controllers\Inicio_Controller::index'
    );

    $routes->get(
        'archivos',
        '\App\Modules\Asuntos_internos\GestionExcel\Controllers\Archivos_Controller::index'
    );

    $routes->post(
        'archivos/procesar',
        '\App\Modules\Asuntos_internos\GestionExcel\Controllers\Archivos_Controller::procesar'
    );

    $routes->get(
        'archivos/descargar/(:num)',
        '\App\Modules\Asuntos_internos\GestionExcel\Controllers\Archivos_Controller::descargar/$1'
    );

    $routes->post(
        'archivos/eliminar/(:num)',
        '\App\Modules\Asuntos_internos\GestionExcel\Controllers\Archivos_Controller::eliminar/$1'
    );
});
