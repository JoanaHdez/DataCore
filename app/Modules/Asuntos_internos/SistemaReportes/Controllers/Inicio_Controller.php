<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Inicio_Controller extends BaseController
{
    public function index()
    {
        return view(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\auth\login'
);
    }
}