<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Reportes_Controller extends BaseController
{
    public function index()
    {
        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\index'
        );
    }

    public function nuevo()
    {
        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\nuevo'
        );
    }
}