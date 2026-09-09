<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group(
    'asuntos-internos/reportes',
    static function ($routes) {

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

                /* =================================================
                   LISTADO
                ================================================= */

                $routes->get(
                    'listado',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::index'
                );


                /* =================================================
                FELICITACIONES
                ================================================= */

                $routes->get(
                    'felicitaciones',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Felicitaciones_Controller::index'
                );

                $routes->get(
                    'felicitaciones/detalle/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Felicitaciones_Controller::detalle/$1'
                );

                $routes->post(
                    'felicitaciones/actualizar/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Felicitaciones_Controller::actualizar/$1'
                );

                $routes->post(
                    'felicitaciones/eliminar/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Felicitaciones_Controller::eliminar/$1'
                );

                /* =================================================
                   DASHBOARD
                ================================================= */

                $routes->get(
                    'dashboard',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::dashboard'
                );


                $routes->post(
                    'dashboard/autorizar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::autorizarDashboard'
                );


                $routes->post(
                    'dashboard/exportar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::exportarDashboard'
                );


                /* =================================================
                   SESIÓN
                ================================================= */

                $routes->get(
                    'logout',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Auth_Controller::logout'
                );


                /* =================================================
                   NUEVO REPORTE
                ================================================= */

                $routes->get(
                    'nuevo',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::nuevo'
                );


                $routes->post(
                    'guardar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::guardarReporte'
                );


                $routes->post(
                    'felicitaciones/guardar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::guardarFelicitacion'
                );

                /* =================================================
                FOLIOS
                ================================================= */

                $routes->get(
                    'validar-folio',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::validarFolio'
                );


                $routes->get(
                    'previsualizar-folio',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::previsualizarFolio'
                );


                /* =================================================
                   PERSONAL
                ================================================= */

                $routes->get(
                    'personal/buscar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::buscarPersonal'
                );


                /* =================================================
                   UNIDADES
                ================================================= */

                $routes->get(
                    'unidades/buscar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::buscarUnidades'
                );


                /* =================================================
                   UBICACIÓN
                ================================================= */

                $routes->get(
                    'ubicacion/buscar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Ubicacion_Controller::buscar'
                );


                $routes->get(
                    'ubicacion/direccion',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Ubicacion_Controller::direccion'
                );


                $routes->get(
                    'ubicacion/territorio',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Ubicacion_Controller::territorio'
                );


                /* =================================================
                   DETALLE
                ================================================= */

                $routes->get(
                    'detalle/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::detalleReporte/$1'
                );


                /* =================================================
                   EVIDENCIAS
                ================================================= */

                $routes->get(
                    'evidencia/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::verEvidencia/$1'
                );


                /* =================================================
                   EDITAR REPORTE
                ================================================= */

                $routes->post(
                    'actualizar/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::actualizarReporte/$1'
                );


                /* =================================================
                   SEGUIMIENTOS
                ================================================= */

                // Consultar
                $routes->get(
                    'seguimientos/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::obtenerSeguimientos/$1'
                );


                // Registrar
                $routes->post(
                    'seguimientos/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::guardarSeguimiento/$1'
                );


                // Editar
                $routes->put(
                    'seguimientos/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::actualizarSeguimiento/$1'
                );


                /* =================================================
                   EXPORTAR LISTADO
                ================================================= */

                $routes->post(
                    'listado/exportar',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::exportarListado'
                );


                /* =================================================
                   ELIMINACIÓN
                ================================================= */

                $routes->post(
                    'listado/autorizar-eliminacion',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::autorizarEliminacion'
                );


                $routes->post(
                    'listado/eliminar/(:num)',
                    '\App\Modules\Asuntos_internos\SistemaReportes\Controllers\Reportes_Controller::eliminarReporte/$1'
                );
            }
        );
    }
);
