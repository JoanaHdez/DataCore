<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('asuntos-internos/reportes', static function ($routes) {

    /* =========================================================
       RUTAS PÚBLICAS
    ========================================================= */

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


    /* =========================================================
       RUTAS PROTEGIDAS
    ========================================================= */

    $routes->group(
        '',
        [
            'filter' => 'reportesAuth',
        ],
        static function ($routes) {

            // Listado de reportes
            $routes->get(
                'listado',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::index'
            );

            // Dashboard
            $routes->get(
                'dashboard',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::dashboard'
            );

            // Autorizar Dashboard
            $routes->post(
                'dashboard/autorizar',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::autorizarDashboard'
            );

            // Exportar Dashboard
            $routes->post(
                'dashboard/exportar',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::exportarDashboard'
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

            // Ubicación
            $routes->get(
                'ubicacion/buscar',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Ubicacion_Controller::buscar'
            );

            $routes->get(
                'ubicacion/direccion',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Ubicacion_Controller::direccion'
            );

            // Exportar listado
            $routes->post(
                'listado/exportar',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::exportarListado'
            );

            // Autorizar eliminación
            $routes->post(
                'listado/autorizar-eliminacion',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::autorizarEliminacion'
            );

            // Eliminar reporte
            $routes->post(
                'listado/eliminar/(:num)',
                '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::eliminarReporte/$1'
            );
        }
    );
    $routes->get(
        'personal/buscar',
        '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::buscarPersonal'
    );

    $routes->get(
        'unidades/buscar',
        '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::buscarUnidades'
    );
});
