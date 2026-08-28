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

    public function eliminarReporte(int $idReporte)
    {
        /* =========================================================
        VALIDAR SESIÓN
        ========================================================= */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'La sesión no es válida.',
                ]);
        }


        $usuario = session()->get('usuario_reportes');

        $idUsuario = (int) ($usuario['id_usuario'] ?? 0);
        $rol       = $usuario['rol'] ?? 'usuario';


        if ($idUsuario <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'No fue posible identificar al usuario.',
                ]);
        }


        /* =========================================================
        CONEXIÓN DATACORE
        ========================================================= */

        $db = \Config\Database::connect('datacore');


        /* =========================================================
        BUSCAR REPORTE
        ========================================================= */

        $reporte = $db
            ->table('ai_reportes')
            ->where('id_reporte', $idReporte)
            ->get()
            ->getRowArray();


        if (!$reporte) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'El reporte no existe.',
                ]);
        }


        /*
     * Si ya estaba eliminado, no volvemos a procesarlo.
     */
        if (
            (int) ($reporte['eliminado'] ?? 0)
            === 1
        ) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' => 'El reporte ya fue eliminado.',
                ]);
        }


        /* =========================================================
        AUTORIZACIÓN
        ========================================================= */

        $idAdministradorAutorizador = null;


        /*
     * ADMIN
     *
     * Puede eliminar directamente.
     */
        if ($rol === 'admin') {

            $idAdministradorAutorizador = $idUsuario;
        } else {

            /*
         * USUARIO NORMAL
         *
         * IMPORTANTE:
         * No confiamos en la autorización que ocurrió antes
         * solamente en JavaScript.
         *
         * El backend vuelve a exigir la contraseña administrativa
         * para ejecutar la operación real.
         */

            $passwordAdmin = strtoupper(
                trim(
                    (string) $this->request->getPost('password_admin')
                )
            );


            if ($passwordAdmin === '') {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Se requiere autorización administrativa.',
                    ]);
            }


            try {

                $authService = new AuthService();

                $autorizado = $authService
                    ->validarAutorizacionAdmin($passwordAdmin);
            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'Error validando autorización administrativa para eliminar reporte.'
                );

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'No fue posible validar la autorización.',
                    ]);
            }


            if (!$autorizado) {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Contraseña de administrador incorrecta.',
                    ]);
            }


            /*
         * Buscamos el usuario local correspondiente al
         * administrador de plantilla ID 758.
         */
            $adminLocal = $db
                ->table('dc_usuarios')
                ->select('id_usuario')
                ->where('plantilla_id', 758)
                ->get()
                ->getRowArray();


            if (!$adminLocal) {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'No fue posible identificar al administrador autorizador.',
                    ]);
            }


            $idAdministradorAutorizador =
                (int) $adminLocal['id_usuario'];
        }


        /* =========================================================
        TRANSACCIÓN
        ========================================================= */

        $db->transBegin();


        try {

            $ahora = date('Y-m-d H:i:s');


            /* =====================================================
            BORRADO LÓGICO
            ===================================================== */

            $db
                ->table('ai_reportes')
                ->where('id_reporte', $idReporte)
                ->update([
                    'eliminado'     => 1,
                    'eliminado_at'  => $ahora,
                    'eliminado_por' => $idUsuario,
                    'updated_at'    => $ahora,
                ]);


            /* =====================================================
            REGISTRO DE ELIMINACIÓN
            ===================================================== */

            $db
                ->table('ai_reporte_eliminaciones')
                ->insert([
                    'id_reporte' =>
                    $idReporte,

                    'solicitado_por' =>
                    $idUsuario,

                    'autorizado_por' =>
                    $idAdministradorAutorizador,

                    'requirio_autorizacion' =>
                    $rol === 'admin'
                        ? 0
                        : 1,

                    'motivo' =>
                    'Eliminación solicitada desde el listado de reportes.',

                    'ip' =>
                    $this->request
                        ->getIPAddress(),

                    'created_at' =>
                    $ahora,
                ]);


            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'La transacción de eliminación no pudo completarse.'
                );
            }


            $db->transCommit();
        } catch (\Throwable $e) {

            $db->transRollback();


            log_message(
                'error',
                'Error eliminando lógicamente reporte {id}: {mensaje}',
                [
                    'id'      => $idReporte,
                    'mensaje' => $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'No fue posible eliminar el reporte.',
                ]);
        }


        /* =========================================================
        RESPUESTA
        ========================================================= */

        return $this->response
            ->setJSON([
                'success' => true,
                'message' => 'El reporte fue eliminado correctamente.',
            ]);
    }

    public function buscarPersonal()
    {
        $termino =
            trim(
                (string)
                $this->request->getGet('q')
            );


        if (
            mb_strlen($termino) < 2
        ) {

            return $this->response
                ->setJSON([
                    'success' => true,
                    'personal' => [],
                ]);
        }


        try {

            $db =
                \Config\Database::connect(
                    'plantilla'
                );


            $builder =
                $db
                ->table('plantilla')
                ->select([
                    'ID',
                    'PERSCOD',
                    'NOMBRE_COMPLETO',
                    'NO_NOMINA',
                    'AREA',
                    'TURNO',
                ])
                ->where(
                    'ESTADO',
                    'ACTIVO'
                );

            $builder
                ->groupStart()
                ->like(
                    'NOMBRE_COMPLETO',
                    $termino
                )
                ->orLike(
                    'NO_NOMINA',
                    $termino
                )
                ->groupEnd();


            $personal =
                $builder
                ->orderBy(
                    'NOMBRE_COMPLETO',
                    'ASC'
                )
                ->limit(10)
                ->get()
                ->getResultArray();


            $resultado = [];


            foreach ($personal as $persona) {

                $perscod =
                    trim(
                        (string)
                        ($persona['PERSCOD'] ?? '')
                    );


                $foto =
                    null;


                if ($perscod !== '') {

                    $foto =
                        'http://10.8.6.2:8083/dgsc/images/fotos/'
                        . rawurlencode($perscod)
                        . '/F.F.R.E.jpg';
                }


                $resultado[] = [

                    'id' =>
                    (int) $persona['ID'],

                    'perscod' =>
                    $perscod,

                    'nombre' =>
                    trim(
                        (string)
                        ($persona['NOMBRE_COMPLETO'] ?? '')
                    ),

                    'nomina' =>
                    trim(
                        (string)
                        ($persona['NO_NOMINA'] ?? '')
                    ),

                    'area' =>
                    trim(
                        (string)
                        ($persona['AREA'] ?? '')
                    ),

                    'turno' =>
                    trim(
                        (string)
                        ($persona['TURNO'] ?? '')
                    ),

                    'foto' =>
                    $foto,
                ];
            }


            return $this->response
                ->setJSON([
                    'success' => true,
                    'personal' => $resultado,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error buscando personal para SistemaReportes: {mensaje}',
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
                    'No fue posible consultar el personal.',
                ]);
        }
    }
}
