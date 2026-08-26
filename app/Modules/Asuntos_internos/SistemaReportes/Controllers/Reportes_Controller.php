<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Controllers\BaseController;

class Reportes_Controller extends BaseController
{
    public function index()
    {

        $reportes = [
            [
                'folio' => 'AI-2026-001',
                'fecha_queja' => '25/08/2026',
                'expediente' => 'CAI/001/2026',
                'clasificacion' => 'Queja',
                'quejoso' => 'Ciudadano de prueba',
                'area' => 'Seguridad Ciudadana',
                'turno' => 'Primer turno',
                'resolucion' => 'En proceso',
            ],
            [
                'folio' => 'AI-2026-002',
                'fecha_queja' => '24/08/2026',
                'expediente' => 'CAI/002/2026',
                'clasificacion' => 'Denuncia',
                'quejoso' => 'Usuario de prueba',
                'area' => 'Tránsito',
                'turno' => 'Segundo turno',
                'resolucion' => 'Finalizado',
            ],
            [
                'folio' => 'AI-2026-003',
                'fecha_queja' => '23/08/2026',
                'expediente' => 'CAI/003/2026',
                'clasificacion' => 'Queja',
                'quejoso' => 'Persona de prueba',
                'area' => 'Seguridad Ciudadana',
                'turno' => 'Primer turno',
                'resolucion' => 'En proceso',
            ],
        ];

        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\index',
            [
                'reportes' => $reportes,
            ]
        );
    }

    public function nuevo()
    {
        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\nuevo'
        );
    }

    public function dashboard()
    {
        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\index'
        );
    }
}
