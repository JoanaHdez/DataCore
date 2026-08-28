<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;

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
        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return redirect()
                ->to(
                    base_url(
                        'asuntos-internos/reportes'
                    )
                )
                ->with(
                    'error',
                    'Inicia sesión para continuar.'
                );
        }


        $usuario =
            session()->get(
                'usuario_reportes'
            );


        $esAdmin =
            ($usuario['rol'] ?? null)
            === 'admin';


        $autorizacionTemporal =
            session()->get(
                'reportes_dashboard_autorizado'
            ) === true;


        /*
     * Admin:
     * acceso directo.
     *
     * Usuario:
     * requiere autorización administrativa,
     * salvo que ya haya sido autorizada.
     */
        $requiereAutorizacion =
            !$esAdmin
            && !$autorizacionTemporal;


        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\index',
            [
                'requiereAutorizacionAdmin' =>
                $requiereAutorizacion,
            ]
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

    public function exportarListado()
    {
        $reportes =
            $this->request->getPost(
                'reportes'
            );

        /*
     * Los registros vienen desde JS
     * como JSON.
     */
        if (is_string($reportes)) {

            $reportes =
                json_decode(
                    $reportes,
                    true
                );
        }


        if (
            ! is_array($reportes)
            || empty($reportes)
        ) {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No hay reportes para exportar.',
                ]);
        }


        try {

            $servicio =
                new ListadoExcelService();


            $ruta =
                $servicio->generar(
                    $reportes
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
                'Error exportando listado de reportes: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible generar el archivo de Excel.',
                ]);
        }
    }

    public function autorizarDashboard()
    {
        /*
     * =========================================================
     * SESIÓN
     * =========================================================
     */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'La sesión no es válida.',
                ]);
        }


        $usuario =
            session()->get(
                'usuario_reportes'
            );


        /*
     * =========================================================
     * ADMIN
     * =========================================================
     *
     * El administrador ya tiene acceso directo.
     */

        if (
            ($usuario['rol'] ?? null)
            === 'admin'
        ) {

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Acceso autorizado.',
                ]);
        }


        /*
     * =========================================================
     * CURP ADMINISTRATIVA
     * =========================================================
     */

        $curp =
            strtoupper(
                trim(
                    (string)
                    $this->request->getPost(
                        'password_admin'
                    )
                )
            );


        if ($curp === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Ingresa la contraseña del administrador.',
                ]);
        }


        try {

            $authService =
                new AuthService();


            $autorizado =
                $authService
                ->validarAutorizacionAdmin(
                    $curp
                );
        } catch (\Throwable $e) {

            /*
         * No registramos CURP ni información
         * sensible en el log.
         */
            log_message(
                'error',
                'Error validando autorización administrativa del Dashboard: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible validar la autorización.',
                ]);
        }


        if (!$autorizado) {

            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Contraseña de administrador incorrecta.',
                ]);
        }


        /*
     * =========================================================
     * AUTORIZACIÓN TEMPORAL DEL DASHBOARD
     * =========================================================
     *
     * NO cambiamos:
     *
     * usuario_reportes['rol']
     *
     * El usuario continúa siendo "usuario".
     */

        session()->set(
            'reportes_dashboard_autorizado',
            true
        );


        return $this->response
            ->setJSON([
                'success' => true,
                'message' =>
                'Acceso autorizado.',
            ]);
    }

    public function autorizarEliminacion()
    {
        /* =========================================================
        VALIDAR SESIÓN
        ========================================================= */

        if (
            session()->get('reportes_autenticado') !== true
            || ! session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'La sesión no es válida.',
                ]);
        }


        $usuario =
            session()->get('usuario_reportes');


        /* =========================================================
        ADMIN
        =========================================================
        El administrador no necesita contraseña extra.
        ========================================================= */

        if (
            ($usuario['rol'] ?? null)
            === 'admin'
        ) {

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' => 'Autorización válida.',
                ]);
        }


        /* =========================================================
        CONTRASEÑA ADMINISTRATIVA
        ========================================================= */

        $curp =
            strtoupper(
                trim(
                    (string)
                    $this->request->getPost(
                        'password_admin'
                    )
                )
            );


        if ($curp === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Ingresa la contraseña del administrador.',
                ]);
        }


        try {

            $authService =
                new \App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService();


            $autorizado =
                $authService
                ->validarAutorizacionAdmin(
                    $curp
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error validando autorización para eliminar reporte: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible validar la autorización.',
                ]);
        }


        if (!$autorizado) {

            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Contraseña de administrador incorrecta.',
                ]);
        }


        /* =========================================================
        AUTORIZACIÓN CORRECTA
        ========================================================= */

        return $this->response
            ->setJSON([
                'success' => true,
                'message' =>
                'Autorización válida.',
            ]);
    }
}
