<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Inicio_Controller extends BaseController
{
    private const TIEMPO_INACTIVIDAD = 7200;

    private const VARIABLES_SESION_REPORTES = [
        'usuario_reportes',
        'reportes_autenticado',
        'reportes_dashboard_autorizado',
        'reportes_ultima_actividad',
    ];

    public function index()
    {
        $sesionValida =
            session()->get('reportes_autenticado') === true
            && session()->has('usuario_reportes');


        if ($sesionValida) {

            $ahora =
                time();

            $ultimaActividad =
                (int) (
                    session()->get(
                        'reportes_ultima_actividad'
                    )
                    ?? 0
                );


            if (
                $ultimaActividad > 0
                && ($ahora - $ultimaActividad) < self::TIEMPO_INACTIVIDAD
            ) {

                return redirect()->to(
                    base_url(
                        'asuntos-internos/reportes/nuevo'
                    )
                );
            }


            session()->remove(
                self::VARIABLES_SESION_REPORTES
            );
        }


        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\auth\login'
        );
    }
}
