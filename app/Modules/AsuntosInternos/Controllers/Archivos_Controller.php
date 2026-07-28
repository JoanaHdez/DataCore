<?php

namespace App\Modules\AsuntosInternos\Controllers;

use App\Controllers\BaseController;

class Archivos_Controller extends BaseController
{
    public function index()
    {
        return view(
            'App\Modules\AsuntosInternos\Views\archivos\index'
        );
    }
}