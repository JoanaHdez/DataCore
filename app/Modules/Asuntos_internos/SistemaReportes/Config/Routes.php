<?php 

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('asuntos-internos/reportes', static function ($routes) {

    $routes->get(
        '/',
        '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Inicio_Controller::index'
    );
});
