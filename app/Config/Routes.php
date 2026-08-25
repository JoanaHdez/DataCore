<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

require APPPATH . 'Modules/Asuntos_internos/GestionExcel/Config/Routes.php';

$routesSistemaReportes =
    APPPATH . 'Modules/Asuntos_internos/SistemaReportes/Config/Routes.php';

if (is_file($routesSistemaReportes)) {
    require $routesSistemaReportes;
}