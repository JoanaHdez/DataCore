<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('asuntos-internos/reportes', static function ($routes) {

    // Login
    $routes->get(
        '/',
        '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Inicio_Controller::index'
    );

    // Procesar inicio de sesión
    $routes->post(
        'login',
        '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Auth_Controller::autenticar'
    );

    // Cerrar sesión
    $routes->get(
        'logout',
        '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Auth_Controller::logout'
    );

    // Nuevo reporte
$routes->get(
    'nuevo',
    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::nuevo'
);
});