<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ReporteService;

use App\Controllers\BaseController;

class Reportes_Controller extends BaseController
{
    public function index()
    {
        /* =========================================================
        CONEXIÓN DATACORE
        ========================================================= */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =========================================================
        CONSULTAR REPORTES
        ========================================================= */

        $reportes =
            $db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.folio',
                'r.fecha_queja',
                'r.expediente',
                'r.clasificacion',
                'r.nombre_quejoso',
                'r.resolucion',
                'r.estado_actual',
                'r.created_at',
            ])
            ->where(
                'r.eliminado',
                0
            )
            ->orderBy(
                'r.id_reporte',
                'DESC'
            )
            ->get()
            ->getResultArray();


        /* =========================================================
        PERSONAL RELACIONADO
        ========================================================= */

        foreach ($reportes as &$reporte) {

            $personal =
                $db
                ->table('ai_reporte_personal')
                ->select([
                    'nombre_snapshot',
                    'area_snapshot',
                    'turno_snapshot',
                ])
                ->where(
                    'id_reporte',
                    $reporte['id_reporte']
                )
                ->orderBy(
                    'id_reporte_personal',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            /*
         * Conservamos todo el personal porque más adelante
         * lo necesitaremos para detalle, filtros y edición.
         */
            $reporte['personal'] =
                $personal;


            /*
         * Por ahora la tabla principal necesita un valor
         * simple para Área y Turno.
         *
         * Si hay varias personas relacionadas, obtenemos
         * los valores únicos y los mostramos separados.
         */

            $areas = [];

            $turnos = [];


            foreach ($personal as $persona) {

                $area =
                    trim(
                        (string)
                        ($persona['area_snapshot'] ?? '')
                    );

                $turno =
                    trim(
                        (string)
                        ($persona['turno_snapshot'] ?? '')
                    );


                if (
                    $area !== ''
                    && !in_array(
                        $area,
                        $areas,
                        true
                    )
                ) {
                    $areas[] = $area;
                }


                if (
                    $turno !== ''
                    && !in_array(
                        $turno,
                        $turnos,
                        true
                    )
                ) {
                    $turnos[] = $turno;
                }
            }


            $reporte['area'] =
                !empty($areas)
                ? implode(', ', $areas)
                : '—';


            $reporte['turno'] =
                !empty($turnos)
                ? implode(', ', $turnos)
                : '—';


            /* =====================================================
            ADAPTAR CAMPOS A LA VISTA ACTUAL
            ===================================================== */

            $reporte['quejoso'] =
                trim(
                    (string)
                    ($reporte['nombre_quejoso'] ?? '')
                );


            /*
         * Mientras la tabla siga utilizando "resolucion",
         * mostramos primero la resolución real y, si todavía
         * no existe, utilizamos el estado actual.
         */

            $resolucion =
                trim(
                    (string)
                    ($reporte['resolucion'] ?? '')
                );


            if ($resolucion === '') {

                $resolucion =
                    trim(
                        (string)
                        ($reporte['estado_actual'] ?? '')
                    );
            }


            $reporte['resolucion'] =
                $resolucion !== ''
                ? $resolucion
                : '—';


            /* =====================================================
            FECHA PARA LA VISTA
            ===================================================== */

            $fechaQueja =
                trim(
                    (string)
                    ($reporte['fecha_queja'] ?? '')
                );


            if ($fechaQueja !== '') {

                $fecha =
                    \DateTime::createFromFormat(
                        'Y-m-d',
                        $fechaQueja
                    );


                if ($fecha !== false) {

                    $reporte['fecha_queja'] =
                        $fecha->format(
                            'd/m/Y'
                        );
                }
            }
        }

        unset($reporte);


        /* =========================================================
        VISTA
        ========================================================= */

        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\index',
            [
                'reportes' =>
                $reportes,
            ]
        );
    }

    public function nuevo()
    {
        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\nuevo'
        );
    }

    public function guardarReporte()
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


        $usuario =
            session()->get(
                'usuario_reportes'
            );


        $idUsuario =
            (int) (
                $usuario['id_usuario']
                ?? 0
            );


        if ($idUsuario <= 0) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible identificar al usuario.',
                ]);
        }


        /* =========================================================
        DATOS DEL FORMULARIO
        ========================================================= */

        $datos =
            $this->request
            ->getPost();


        /*
     * Las relaciones múltiples llegan mediante:
     *
     * personal[0][...]
     * personal[1][...]
     *
     * unidades[0][...]
     * unidades[1][...]
     */

        $personal =
            $this->request
            ->getPost('personal');


        $unidades =
            $this->request
            ->getPost('unidades');


        if (!is_array($personal)) {
            $personal = [];
        }


        if (!is_array($unidades)) {
            $unidades = [];
        }


        /* =========================================================
        EVIDENCIAS
        ========================================================= */

        $archivos = [];


        $evidencias =
            $this->request
            ->getFiles();


        if (
            isset(
                $evidencias['evidencia_fotografica']
            )
        ) {

            $archivos =
                $evidencias['evidencia_fotografica'];


            /*
         * CodeIgniter puede entregar un solo UploadedFile
         * o un arreglo dependiendo del request.
         */
            if (!is_array($archivos)) {

                $archivos = [
                    $archivos,
                ];
            }
        }


        /* =========================================================
        GUARDAR
        ========================================================= */

        try {

            $servicio =
                new ReporteService();


            $resultado =
                $servicio->guardar(
                    $datos,
                    $personal,
                    $unidades,
                    $archivos,
                    $idUsuario
                );


            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'success' => true,

                    'message' =>
                    'El reporte fue guardado correctamente.',

                    'id_reporte' =>
                    $resultado['id_reporte']
                        ?? null,

                    'folio' =>
                    $resultado['folio']
                        ?? null,
                ]);
        } catch (\InvalidArgumentException $e) {

            /*
         * Error provocado por datos inválidos
         * enviados desde el formulario.
         */
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    $e->getMessage(),
                ]);
        } catch (\Throwable $e) {

            /*
         * El detalle técnico únicamente va al log.
         * No exponemos rutas, SQL ni stack trace
         * al navegador.
         */
            log_message(
                'error',
                'Error guardando reporte de Asuntos Internos: {mensaje}',
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
                    'No fue posible guardar el reporte.',
                ]);
        }
    }

    public function detalleReporte(int $idReporte)
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


        if ($idReporte <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'El reporte solicitado no es válido.',
                ]);
        }


        try {

            /* =====================================================
            CONEXIÓN DATACORE
            ===================================================== */

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            /* =====================================================
            REPORTE PRINCIPAL
            ===================================================== */

            $reporte =
                $db
                ->table('ai_reportes')
                ->select([
                    'id_reporte',
                    'folio',
                    'fecha_registro',
                    'folio_ip',
                    'fecha_queja',
                    'fecha_acuerdo',
                    'expediente',
                    'nomenclatura',
                    'numero_oficio',

                    'fecha_hechos',
                    'hora_hechos',
                    'descripcion_hechos',

                    'calle',
                    'numero_exterior',
                    'colonia',
                    'entre_calle',
                    'y_calle',
                    'municipio',
                    'estado',
                    'sector',
                    'cuadrante',
                    'latitud',
                    'longitud',
                    'origen_ubicacion',

                    'nombre_quejoso',
                    'edad_quejoso',
                    'genero_quejoso',
                    'telefono_quejoso',
                    'correo_quejoso',

                    'clasificacion',
                    'inspector',
                    'investigador',
                    'quien_emite_resolucion',
                    'resolucion',
                    'motivos',
                    'estado_actual',
                    'observaciones',

                    'created_at',
                    'updated_at',
                ])
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'eliminado',
                    0
                )
                ->get()
                ->getRowArray();


            if (!$reporte) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El reporte no existe.',
                    ]);
            }


            /* =====================================================
            PERSONAL
            ===================================================== */

            $personalBD =
                $db
                ->table('ai_reporte_personal')
                ->select([
                    'id_reporte_personal',
                    'plantilla_id',
                    'perscod',
                    'nombre_snapshot',
                    'area_snapshot',
                    'turno_snapshot',
                ])
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->orderBy(
                    'id_reporte_personal',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            $personal =
                [];


            $dbPlantilla =
                \Config\Database::connect(
                    'plantilla'
                );


            foreach ($personalBD as $persona) {

                $plantillaId =
                    (int) (
                        $persona['plantilla_id']
                        ?? 0
                    );


                $perscod =
                    trim(
                        (string) (
                            $persona['perscod']
                            ?? ''
                        )
                    );


                $nomina =
                    '';


                if ($plantillaId > 0) {

                    $personaPlantilla =
                        $dbPlantilla
                        ->table('plantilla')
                        ->select(
                            'NO_NOMINA'
                        )
                        ->where(
                            'ID',
                            $plantillaId
                        )
                        ->get()
                        ->getRowArray();


                    if ($personaPlantilla) {

                        $nomina =
                            trim(
                                (string) (
                                    $personaPlantilla['NO_NOMINA']
                                    ?? ''
                                )
                            );
                    }
                }


                $foto =
                    null;


                if ($perscod !== '') {

                    $foto =
                        'http://10.8.6.2:8083/dgsc/images/fotos/'
                        . rawurlencode($perscod)
                        . '/F.F.R.E.jpg';
                }


                $personal[] = [

                    'id' =>
                    $plantillaId,

                    'perscod' =>
                    $perscod,

                    'nombre' =>
                    $persona['nombre_snapshot']
                        ?? '',

                    'nomina' =>
                    $nomina,

                    'area' =>
                    $persona['area_snapshot']
                        ?? '',

                    'turno' =>
                    $persona['turno_snapshot']
                        ?? '',

                    'foto' =>
                    $foto,

                ];
            }


            /* =====================================================
            UNIDADES
            ===================================================== */

            $unidadesBD =
                $db
                ->table('ai_reporte_unidades u')
                ->select([
                    'u.id_reporte_unidad',
                    'u.parque_vehicular_id',
                    'u.no_economico_snapshot',
                    'u.placas_snapshot',
                    'u.marca_snapshot',
                    'u.submarca_snapshot',
                    'u.color_snapshot',
                    'u.estatus_snapshot',
                    'u.servicio_snapshot',
                    'u.tipo_snapshot',
                    'u.id_origen',
                    'o.clave AS origen',
                ])
                ->join(
                    'ai_cat_origen_unidad o',
                    'o.id_origen = u.id_origen',
                    'left'
                )
                ->where(
                    'u.id_reporte',
                    $idReporte
                )
                ->orderBy(
                    'u.id_reporte_unidad',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            $unidades =
                [];


            foreach ($unidadesBD as $unidad) {

                $unidades[] = [

                    'id' =>
                    (int) (
                        $unidad['parque_vehicular_id']
                        ?? 0
                    ),

                    'no_economico' =>
                    $unidad['no_economico_snapshot']
                        ?? '',

                    'placas' =>
                    $unidad['placas_snapshot']
                        ?? '',

                    'marca' =>
                    $unidad['marca_snapshot']
                        ?? '',

                    'submarca' =>
                    $unidad['submarca_snapshot']
                        ?? '',

                    'color' =>
                    $unidad['color_snapshot']
                        ?? '',

                    'estatus' =>
                    $unidad['estatus_snapshot']
                        ?? '',

                    'servicio' =>
                    $unidad['servicio_snapshot']
                        ?? '',

                    'tipo' =>
                    $unidad['tipo_snapshot']
                        ?? '',

                    'origen' =>
                    $unidad['origen']
                        ?? '',

                ];
            }


            /* =====================================================
            EVIDENCIAS
            ===================================================== */

            $evidencias =
                $db
                ->table('ai_reporte_evidencias')
                ->select([
                    'id_evidencia',
                    'nombre_original',
                    'nombre_archivo',
                    'ruta_archivo',
                    'extension',
                    'mime_type',
                    'tamano_bytes',
                    'orden',
                    'created_at',
                ])
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'eliminado',
                    0
                )
                ->orderBy(
                    'orden',
                    'ASC'
                )
                ->orderBy(
                    'id_evidencia',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return $this->response
                ->setJSON([

                    'success' =>
                    true,

                    'reporte' =>
                    $reporte,

                    'personal' =>
                    $personal,

                    'unidades' =>
                    $unidades,

                    'evidencias' =>
                    $evidencias,

                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando detalle del reporte {id}: {mensaje}',
                [
                    'id' =>
                    $idReporte,

                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible consultar el detalle del reporte.',
                ]);
        }
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

    public function buscarUnidades()
    {
        $termino =
            trim(
                (string)
                $this->request->getGet('q')
            );


        if (
            mb_strlen($termino) < 1
        ) {

            return $this->response
                ->setJSON([
                    'success' => true,
                    'unidades' => [],
                ]);
        }


        try {

            $db =
                \Config\Database::connect(
                    'unidades'
                );


            $builder =
                $db
                ->table('parque_vehicular')
                ->select([
                    'id',
                    'no_economico',
                    'placas',
                    'servicio',
                    'estatus',
                    'marca',
                    'submarca',
                    'tipo',
                    'color',
                    'modelo',
                    'serie',
                ]);


            $builder
                ->groupStart()
                ->like(
                    'no_economico',
                    $termino
                )
                ->orLike(
                    'placas',
                    $termino
                )
                ->groupEnd();


            $unidades =
                $builder
                ->orderBy(
                    'no_economico',
                    'ASC'
                )
                ->limit(10)
                ->get()
                ->getResultArray();


            $resultado = [];


            foreach ($unidades as $unidad) {

                $resultado[] = [

                    'id' =>
                    (int) $unidad['id'],

                    'no_economico' =>
                    trim(
                        (string)
                        ($unidad['no_economico'] ?? '')
                    ),

                    'placas' =>
                    trim(
                        (string)
                        ($unidad['placas'] ?? '')
                    ),

                    'marca' =>
                    trim(
                        (string)
                        ($unidad['marca'] ?? '')
                    ),

                    'submarca' =>
                    trim(
                        (string)
                        ($unidad['submarca'] ?? '')
                    ),

                    'color' =>
                    trim(
                        (string)
                        ($unidad['color'] ?? '')
                    ),

                    'estatus' =>
                    trim(
                        (string)
                        ($unidad['estatus'] ?? '')
                    ),

                    'servicio' =>
                    trim(
                        (string)
                        ($unidad['servicio'] ?? '')
                    ),

                    'tipo' =>
                    trim(
                        (string)
                        ($unidad['tipo'] ?? '')
                    ),

                    'modelo' =>
                    trim(
                        (string)
                        ($unidad['modelo'] ?? '')
                    ),

                    'serie' =>
                    trim(
                        (string)
                        ($unidad['serie'] ?? '')
                    ),
                ];
            }


            return $this->response
                ->setJSON([
                    'success' => true,
                    'unidades' => $resultado,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error buscando unidades para SistemaReportes: {mensaje}',
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
                    'No fue posible consultar las unidades.',
                ]);
        }
    }
}
