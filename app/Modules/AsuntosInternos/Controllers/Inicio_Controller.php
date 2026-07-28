<?php

namespace App\Modules\AsuntosInternos\Controllers;

use App\Controllers\BaseController;

class Inicio_Controller extends BaseController
{
    public function index()
    {
        return view('App\Modules\AsuntosInternos\Views\inicio');
    }
}