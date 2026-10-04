<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Inicio_Controller extends BaseController
{
    public function index()
    {
        $sesionValida =
            session()->get('reportes_autenticado') === true
            && session()->has('usuario_reportes');


        if ($sesionValida) {

            return redirect()->to(
                base_url(
                    'asuntos-internos/reportes/nuevo'
                )
            );
        }


        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\auth\login'
        );
    }
}
