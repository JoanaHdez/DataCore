<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardExcelService;

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

    public function exportarDashboard()
    {
        $secciones =
            $this->request->getPost(
                'secciones'
            );

        if (
            ! is_array($secciones)
            || empty($secciones)
        ) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Selecciona al menos una sección para exportar.',
                ]);
        }

        try {

            $servicio =
                new DashboardExcelService();

            $ruta =
                $servicio->generar(
                    $secciones
                );

            return $this->response
                ->download(
                    $ruta,
                    null
                )
                ->setFileName(
                    basename($ruta)
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error exportando Dashboard: {mensaje}',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'archivo' => $e->getFile(),
                    'linea' => $e->getLine(),
                ]);
        }
    }
}
