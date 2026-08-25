<?php

namespace App\Modules\Asuntos_internos\GestionExcel\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\GestionExcel\Models\ArchivoModel;

class Inicio_Controller extends BaseController
{
    protected ArchivoModel $archivoModel;

    public function __construct()
    {
        $this->archivoModel = new ArchivoModel();
    }

    public function index()
    {
        return view(
            'App\Modules\Asuntos_internos\GestionExcel\Views\inicio',
            [
                'archivosRecientes' => $this->obtenerArchivosRecientes(3),
            ]
        );
    }

    private function obtenerArchivosRecientes(int $limite = 5): array
    {
        return $this->archivoModel
            ->where('estado', 'completado')
            ->orderBy('fecha_procesamiento', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll($limite);
    }
}
