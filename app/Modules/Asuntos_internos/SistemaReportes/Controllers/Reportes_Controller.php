<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ReporteService;

use App\Controllers\BaseController;

class Reportes_Controller extends BaseController
{
    private function agregarValorExportacion(
        array &$valores,
        mixed $valor
    ): void {

        $texto =
            trim(
                (string) (
                    $valor
                    ?? ''
                )
            );


        if (
            $texto === ''
            || in_array(
                $texto,
                $valores,
                true
            )
        ) {
            return;
        }


        $valores[] =
            $texto;
    }

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

    public function actualizarReporte(int $idReporte)
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


        if ($idReporte <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El reporte proporcionado no es válido.',
                ]);
        }


        /* =========================================================
        DATOS DEL FORMULARIO
        ========================================================= */

        $datos =
            $this->request
            ->getPost();


        /* =========================================================
        PERSONAL
        ========================================================= */

        $personal =
            $this->request
            ->getPost(
                'personal'
            );


        if (!is_array($personal)) {

            $personal = [];
        }


        /* =========================================================
        UNIDADES
        ========================================================= */

        $unidades =
            $this->request
            ->getPost(
                'unidades'
            );


        if (!is_array($unidades)) {

            $unidades = [];
        }


        /* =========================================================
        EVIDENCIAS A ELIMINAR
        ========================================================= */

        $evidenciasEliminadas =
            $this->request
            ->getPost(
                'evidencias_eliminadas'
            );


        if (!is_array($evidenciasEliminadas)) {

            $evidenciasEliminadas = [];
        }


        /* =========================================================
        EVIDENCIAS NUEVAS
        ========================================================= */

        $archivos =
            [];


        $files =
            $this->request
            ->getFiles();


        if (
            isset(
                $files['evidencia_fotografica']
            )
        ) {

            $archivos =
                $files['evidencia_fotografica'];


            if (!is_array($archivos)) {

                $archivos = [
                    $archivos,
                ];
            }
        }


        /* =========================================================
        ACTUALIZAR
        ========================================================= */

        try {

            $servicio =
                new ReporteService();


            $resultado =
                $servicio->actualizar(
                    $idReporte,
                    $datos,
                    $personal,
                    $unidades,
                    $archivos,
                    $evidenciasEliminadas,
                    $idUsuario
                );


            return $this->response
                ->setJSON([
                    'success' => true,

                    'message' =>
                    'El reporte fue actualizado correctamente.',

                    'id_reporte' =>
                    $resultado['id_reporte']
                        ?? $idReporte,

                    'folio' =>
                    $resultado['folio']
                        ?? null,
                ]);
        } catch (\InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    $e->getMessage(),
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error actualizando reporte {id}: {mensaje}',
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
                    'No fue posible actualizar el reporte.',
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
        /* =========================================================
        VALIDAR SESION
        ========================================================= */

        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'La sesion no es valida.',
                ]);
        }


        try {

            /* =====================================================
            CONEXION DATACORE
            ===================================================== */

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            /* =====================================================
            TODOS LOS REPORTES VIGENTES

            Esta exportacion NO depende de:
            - filtros del listado
            - fechas
            - busqueda
            - paginacion
            - IDs enviados desde JavaScript
            ===================================================== */

            $reportesDb =
                $db
                ->table('ai_reportes')
                ->where(
                    'eliminado',
                    0
                )
                ->orderBy(
                    'id_reporte',
                    'DESC'
                )
                ->get()
                ->getResultArray();


            /* =====================================================
            IDS DE TODOS LOS REPORTES
            ===================================================== */

            $idsReales =
                array_map(
                    static fn($reporte) =>
                    (int) $reporte['id_reporte'],
                    $reportesDb
                );


            /* =====================================================
            RELACIONES
            ===================================================== */

            $personalDb = [];
            $unidadesDb = [];
            $evidenciasDb = [];
            $seguimientosDb = [];


            if (!empty($idsReales)) {

                /* =================================================
                PERSONAL
                ================================================= */

                $personalDb =
                    $db
                    ->table('ai_reporte_personal')
                    ->whereIn(
                        'id_reporte',
                        $idsReales
                    )
                    ->orderBy(
                        'id_reporte',
                        'ASC'
                    )
                    ->orderBy(
                        'id_reporte_personal',
                        'ASC'
                    )
                    ->get()
                    ->getResultArray();


                /* =================================================
                UNIDADES
                ================================================= */

                $unidadesDb =
                    $db
                    ->table('ai_reporte_unidades u')
                    ->select([
                        'u.id_reporte_unidad',
                        'u.id_reporte',
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
                    ->whereIn(
                        'u.id_reporte',
                        $idsReales
                    )
                    ->orderBy(
                        'u.id_reporte',
                        'ASC'
                    )
                    ->orderBy(
                        'u.id_reporte_unidad',
                        'ASC'
                    )
                    ->get()
                    ->getResultArray();


                /* =================================================
                EVIDENCIAS
                ================================================= */

                $evidenciasDb =
                    $db
                    ->table('ai_reporte_evidencias')
                    ->whereIn(
                        'id_reporte',
                        $idsReales
                    )
                    ->where(
                        'eliminado',
                        0
                    )
                    ->orderBy(
                        'id_reporte',
                        'ASC'
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


                /* =================================================
                SEGUIMIENTOS

                Se consultan todos los seguimientos vigentes y se
                conserva solamente el ultimo de cada reporte.
                ================================================= */

                $seguimientosDb =
                    $db
                    ->table('ai_reporte_seguimientos')
                    ->select([
                        'id_seguimiento',
                        'id_reporte',
                        'fecha',
                        'tipo',
                        'estado_resultante',
                        'observaciones',
                    ])
                    ->whereIn(
                        'id_reporte',
                        $idsReales
                    )
                    ->where(
                        'eliminado',
                        0
                    )
                    ->orderBy(
                        'fecha',
                        'DESC'
                    )
                    ->orderBy(
                        'id_seguimiento',
                        'DESC'
                    )
                    ->get()
                    ->getResultArray();
            }


            /* =====================================================
            AGRUPAR PERSONAL
            ===================================================== */

            $personalPorReporte = [];


            foreach (
                $personalDb
                as $persona
            ) {

                $idReporte =
                    (int) (
                        $persona['id_reporte']
                        ?? 0
                    );


                if ($idReporte <= 0) {
                    continue;
                }


                $personalPorReporte[$idReporte][] =
                    $persona;
            }


            /* =====================================================
            AGRUPAR UNIDADES
            ===================================================== */

            $unidadesPorReporte = [];


            foreach (
                $unidadesDb
                as $unidad
            ) {

                $idReporte =
                    (int) (
                        $unidad['id_reporte']
                        ?? 0
                    );


                if ($idReporte <= 0) {
                    continue;
                }


                $unidadesPorReporte[$idReporte][] =
                    $unidad;
            }


            /* =====================================================
            AGRUPAR EVIDENCIAS
            ===================================================== */

            $evidenciasPorReporte = [];


            foreach (
                $evidenciasDb
                as $evidencia
            ) {

                $idReporte =
                    (int) (
                        $evidencia['id_reporte']
                        ?? 0
                    );


                if ($idReporte <= 0) {
                    continue;
                }


                $nombre =
                    trim(
                        (string) (
                            $evidencia['nombre_original']
                            ?? ''
                        )
                    );


                if ($nombre === '') {

                    $nombre =
                        trim(
                            (string) (
                                $evidencia['nombre_archivo']
                                ?? ''
                            )
                        );
                }


                $evidenciasPorReporte[$idReporte][] = [

                    'archivo' =>
                    $nombre,

                    'ruta' =>
                    $evidencia['ruta_archivo']
                        ?? '',
                ];
            }


            /* =====================================================
            ULTIMO SEGUIMIENTO POR REPORTE
            ===================================================== */

            $ultimoSeguimientoPorReporte = [];


            foreach (
                $seguimientosDb
                as $seguimiento
            ) {

                $idReporte =
                    (int) (
                        $seguimiento['id_reporte']
                        ?? 0
                    );


                if (
                    $idReporte <= 0
                    || isset(
                        $ultimoSeguimientoPorReporte[$idReporte]
                    )
                ) {
                    continue;
                }


                /*
                 * La consulta ya viene ordenada del seguimiento
                 * mas reciente al mas antiguo.
                 */
                $ultimoSeguimientoPorReporte[$idReporte] =
                    $seguimiento;
            }


            /* =====================================================
            PREPARAR REPORTES PARA EL EXCEL
            ===================================================== */

            $reportes = [];


            foreach (
                $reportesDb
                as $reporte
            ) {

                $idReporte =
                    (int) (
                        $reporte['id_reporte']
                        ?? 0
                    );


                $personal =
                    $personalPorReporte[$idReporte]
                    ?? [];


                $unidades =
                    $unidadesPorReporte[$idReporte]
                    ?? [];


                $ultimoSeguimiento =
                    $ultimoSeguimientoPorReporte[$idReporte]
                    ?? [];


                /* =================================================
                PERSONAL
                ================================================= */

                $nombresPersonal = [];
                $areasPersonal = [];
                $turnosPersonal = [];


                foreach (
                    $personal
                    as $persona
                ) {

                    $this->agregarValorExportacion(
                        $nombresPersonal,
                        $persona['nombre_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $areasPersonal,
                        $persona['area_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $turnosPersonal,
                        $persona['turno_snapshot']
                            ?? ''
                    );
                }


                /* =================================================
                UNIDADES
                ================================================= */

                $numerosUnidad = [];
                $placasUnidad = [];
                $marcasUnidad = [];
                $submarcasUnidad = [];
                $coloresUnidad = [];
                $estatusUnidad = [];
                $serviciosUnidad = [];
                $tiposUnidad = [];
                $origenesUnidad = [];


                foreach (
                    $unidades
                    as $unidad
                ) {

                    $this->agregarValorExportacion(
                        $numerosUnidad,
                        $unidad['no_economico_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $placasUnidad,
                        $unidad['placas_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $marcasUnidad,
                        $unidad['marca_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $submarcasUnidad,
                        $unidad['submarca_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $coloresUnidad,
                        $unidad['color_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $estatusUnidad,
                        $unidad['estatus_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $serviciosUnidad,
                        $unidad['servicio_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $tiposUnidad,
                        $unidad['tipo_snapshot']
                            ?? ''
                    );


                    $this->agregarValorExportacion(
                        $origenesUnidad,
                        $unidad['origen']
                            ?? ''
                    );
                }


                /* =================================================
                FOLIO
                ================================================= */

                $folio =
                    trim(
                        (string) (
                            $reporte['folio']
                            ?? ''
                        )
                    );


                $prefijo =
                    'QJ';


                $numeroFolio =
                    $folio;


                if (
                    str_starts_with(
                        $folio,
                        'QJ-'
                    )
                ) {

                    $numeroFolio =
                        substr(
                            $folio,
                            3
                        );
                }


                /* =================================================
                DATOS PARA ListadoExcelService
                ================================================= */

                $reportes[] = [

                    /* =============================================
                    DATOS DEL REPORTE
                    ============================================= */

                    'folio' =>
                    $folio,

                    'prefijo' =>
                    $prefijo,

                    'numero_folio' =>
                    $numeroFolio,

                    'fecha_registro' =>
                    $reporte['fecha_registro']
                        ?? '',


                    /* =============================================
                    IDENTIFICACION
                    ============================================= */

                    'folio_ip' =>
                    $reporte['folio_ip']
                        ?? '',

                    'fecha_queja' =>
                    $reporte['fecha_queja']
                        ?? '',

                    'fecha_acuerdo' =>
                    $reporte['fecha_acuerdo']
                        ?? '',

                    'expediente' =>
                    $reporte['expediente']
                        ?? '',

                    'nomenclatura' =>
                    $reporte['nomenclatura']
                        ?? '',

                    'no_oficio' =>
                    $reporte['numero_oficio']
                        ?? '',


                    /* =============================================
                    HECHOS
                    ============================================= */

                    'fecha_hechos' =>
                    $reporte['fecha_hechos']
                        ?? '',

                    'hora_hechos' =>
                    $reporte['hora_hechos']
                        ?? '',

                    'descripcion' =>
                    $reporte['descripcion_hechos']
                        ?? '',


                    /* =============================================
                    UBICACION
                    ============================================= */

                    'calle' =>
                    $reporte['calle']
                        ?? '',

                    'numero' =>
                    $reporte['numero_exterior']
                        ?? '',

                    'colonia' =>
                    $reporte['colonia']
                        ?? '',

                    'entre_calle' =>
                    $reporte['entre_calle']
                        ?? '',

                    'y_calle' =>
                    $reporte['y_calle']
                        ?? '',

                    'municipio' =>
                    $reporte['municipio']
                        ?? '',

                    'estado' =>
                    $reporte['estado']
                        ?? '',

                    'sector' =>
                    $reporte['sector']
                        ?? '',

                    'cuadrante' =>
                    $reporte['cuadrante']
                        ?? '',

                    'latitud' =>
                    $reporte['latitud']
                        ?? '',

                    'longitud' =>
                    $reporte['longitud']
                        ?? '',


                    /* =============================================
                    PERSONAL
                    ============================================= */

                    'oficial' =>
                    implode(
                        ' | ',
                        $nombresPersonal
                    ),

                    'area' =>
                    implode(
                        ' | ',
                        $areasPersonal
                    ),

                    'turno' =>
                    implode(
                        ' | ',
                        $turnosPersonal
                    ),


                    /* =============================================
                    UNIDADES
                    ============================================= */

                    'unidad' =>
                    implode(
                        ' | ',
                        $numerosUnidad
                    ),

                    'unidad_placas' =>
                    implode(
                        ' | ',
                        $placasUnidad
                    ),

                    'unidad_marca' =>
                    implode(
                        ' | ',
                        $marcasUnidad
                    ),

                    'unidad_submarca' =>
                    implode(
                        ' | ',
                        $submarcasUnidad
                    ),

                    'unidad_color' =>
                    implode(
                        ' | ',
                        $coloresUnidad
                    ),

                    'unidad_estatus' =>
                    implode(
                        ' | ',
                        $estatusUnidad
                    ),

                    'unidad_servicio_adscripcion' =>
                    implode(
                        ' | ',
                        $serviciosUnidad
                    ),

                    'unidad_tipo_vehiculo' =>
                    implode(
                        ' | ',
                        $tiposUnidad
                    ),

                    'unidad_origen' =>
                    implode(
                        ' | ',
                        $origenesUnidad
                    ),


                    /* =============================================
                    QUEJOSO
                    ============================================= */

                    'quejoso' =>
                    $reporte['nombre_quejoso']
                        ?? '',

                    'edad' =>
                    $reporte['edad_quejoso']
                        ?? '',

                    'genero' =>
                    $reporte['genero_quejoso']
                        ?? '',

                    'telefono' =>
                    $reporte['telefono_quejoso']
                        ?? '',

                    'correo' =>
                    $reporte['correo_quejoso']
                        ?? '',


                    /* =============================================
                    CLASIFICACION
                    ============================================= */

                    'clasificacion' =>
                    $reporte['clasificacion']
                        ?? '',

                    'inspector' =>
                    $reporte['inspector']
                        ?? '',

                    'investigador' =>
                    $reporte['investigador']
                        ?? '',

                    'quien_emite_resolucion' =>
                    $reporte['quien_emite_resolucion']
                        ?? '',

                    'resolucion' =>
                    trim(
                        (string) (
                            $reporte['resolucion']
                            ?? ''
                        )
                    ) !== ''
                        ? $reporte['resolucion']
                        : (
                            $reporte['estado_actual']
                            ?? ''
                        ),

                    'motivos' =>
                    $reporte['motivos']
                        ?? '',


                    /* =============================================
                    ADICIONAL
                    ============================================= */

                    'observaciones' =>
                    $reporte['observaciones']
                        ?? '',


                    /* =============================================
                    ULTIMO SEGUIMIENTO
                    ============================================= */

                    'seguimiento_fecha' =>
                    $ultimoSeguimiento['fecha']
                        ?? '',

                    'seguimiento_tipo' =>
                    $ultimoSeguimiento['tipo']
                        ?? '',

                    'seguimiento_estado' =>
                    $ultimoSeguimiento['estado_resultante']
                        ?? '',

                    'seguimiento_observaciones' =>
                    $ultimoSeguimiento['observaciones']
                        ?? '',


                    /* =============================================
                    EVIDENCIAS
                    ============================================= */

                    'evidencias' =>
                    $evidenciasPorReporte[$idReporte]
                        ?? [],
                ];
            }


            /* =====================================================
            GENERAR EXCEL
            ===================================================== */

            $servicio =
                new ListadoExcelService();


            $ruta =
                $servicio->generar(
                    $reportes
                );


            /* =====================================================
            DESCARGAR
            ===================================================== */

            return $this->response
                ->download(
                    $ruta,
                    null
                )
                ->setFileName(
                    basename(
                        $ruta
                    )
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error exportando listado completo de reportes: {mensaje}',
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

    public function verEvidencia(int $idEvidencia)
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


        if ($idEvidencia <= 0) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'La evidencia solicitada no es válida.',
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
            BUSCAR EVIDENCIA
            ===================================================== */

            $evidencia =
                $db
                ->table('ai_reporte_evidencias e')
                ->select([
                    'e.id_evidencia',
                    'e.id_reporte',
                    'e.nombre_original',
                    'e.nombre_archivo',
                    'e.ruta_archivo',
                    'e.extension',
                    'e.mime_type',
                ])
                ->join(
                    'ai_reportes r',
                    'r.id_reporte = e.id_reporte',
                    'inner'
                )
                ->where(
                    'e.id_evidencia',
                    $idEvidencia
                )
                ->where(
                    'e.eliminado',
                    0
                )
                ->where(
                    'r.eliminado',
                    0
                )
                ->get()
                ->getRowArray();


            if (!$evidencia) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'La evidencia no existe.',
                    ]);
            }


            /* =====================================================
            RESOLVER RUTA
            ===================================================== */

            $rutaGuardada =
                trim(
                    (string) (
                        $evidencia['ruta_archivo']
                        ?? ''
                    )
                );


            if ($rutaGuardada === '') {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'La evidencia no tiene un archivo asociado.',
                    ]);
            }


            /*
         * Actualmente la BD guarda rutas como:
         *
         * writable/uploads/asuntos_internos/reportes/2/archivo.png
         *
         * WRITEPATH ya apunta a:
         *
         * C:\laragon\www\DataCore\writable\
         *
         * Por eso quitamos "writable/" antes
         * de construir la ruta física.
         */

            $rutaRelativa =
                str_replace(
                    '\\',
                    '/',
                    $rutaGuardada
                );


            $rutaRelativa =
                ltrim(
                    $rutaRelativa,
                    '/'
                );


            if (
                str_starts_with(
                    strtolower($rutaRelativa),
                    'writable/'
                )
            ) {

                $rutaRelativa =
                    substr(
                        $rutaRelativa,
                        strlen('writable/')
                    );
            }


            /*
         * Convertimos nuevamente los separadores
         * al formato del sistema operativo.
         */

            $rutaRelativa =
                str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    $rutaRelativa
                );


            $rutaCompleta =
                rtrim(
                    WRITEPATH,
                    DIRECTORY_SEPARATOR
                )
                . DIRECTORY_SEPARATOR
                . $rutaRelativa;


            /* =====================================================
            SEGURIDAD DE RUTA
            ===================================================== */

            $rutaReal =
                realpath(
                    $rutaCompleta
                );


            $writableReal =
                realpath(
                    WRITEPATH
                );


            if (
                $rutaReal === false
                || $writableReal === false
                || !is_file($rutaReal)
            ) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El archivo de evidencia no fue encontrado.',
                    ]);
            }


            /*
         * Nos aseguramos de que el archivo esté
         * realmente dentro de writable/.
         */

            $prefijoWritable =
                rtrim(
                    $writableReal,
                    DIRECTORY_SEPARATOR
                )
                . DIRECTORY_SEPARATOR;


            if (
                !str_starts_with(
                    $rutaReal,
                    $prefijoWritable
                )
            ) {

                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'La ruta de la evidencia no es válida.',
                    ]);
            }


            /* =====================================================
            MIME TYPE
            ===================================================== */

            $mimeType =
                trim(
                    (string) (
                        $evidencia['mime_type']
                        ?? ''
                    )
                );


            /*
         * Si por alguna razón no está registrado,
         * lo detectamos directamente desde el archivo.
         */

            if ($mimeType === '') {

                $finfo =
                    new \finfo(
                        FILEINFO_MIME_TYPE
                    );


                $mimeType =
                    $finfo->file(
                        $rutaReal
                    )
                    ?: 'application/octet-stream';
            }


            /* =====================================================
            VALIDAR TIPO DE IMAGEN
            ===================================================== */

            $tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];


            if (
                !in_array(
                    strtolower($mimeType),
                    $tiposPermitidos,
                    true
                )
            ) {

                return $this->response
                    ->setStatusCode(415)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El archivo no es una imagen permitida.',
                    ]);
            }


            /* =====================================================
            LEER ARCHIVO
            ===================================================== */

            $contenido =
                file_get_contents(
                    $rutaReal
                );


            if ($contenido === false) {

                throw new \RuntimeException(
                    'No fue posible leer el archivo de evidencia.'
                );
            }


            /* =====================================================
            NOMBRE PARA EL NAVEGADOR
            ===================================================== */

            $nombreDescarga =
                trim(
                    (string) (
                        $evidencia['nombre_original']
                        ?? ''
                    )
                );


            if ($nombreDescarga === '') {

                $nombreDescarga =
                    trim(
                        (string) (
                            $evidencia['nombre_archivo']
                            ?? 'evidencia'
                        )
                    );
            }


            /*
         * Evitamos caracteres problemáticos
         * dentro del Content-Disposition.
         */

            $nombreDescarga =
                str_replace(
                    [
                        '"',
                        "\r",
                        "\n",
                    ],
                    '',
                    basename(
                        $nombreDescarga
                    )
                );


            /* =====================================================
            DEVOLVER IMAGEN
            ===================================================== */

            return $this->response
                ->setHeader(
                    'Content-Type',
                    $mimeType
                )
                ->setHeader(
                    'Content-Disposition',
                    'inline; filename="'
                        . $nombreDescarga
                        . '"'
                )
                ->setHeader(
                    'X-Content-Type-Options',
                    'nosniff'
                )
                ->setBody(
                    $contenido
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error mostrando evidencia {id}: {mensaje}',
                [
                    'id' =>
                    $idEvidencia,

                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible consultar la evidencia.',
                ]);
        }
    }

    /* =========================================================
    CONSULTAR SEGUIMIENTOS
    ========================================================= */

    public function obtenerSeguimientos(int $idReporte)
    {
        /* =====================================================
        VALIDAR SESIÓN
        ===================================================== */

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
                    'message' => 'El reporte proporcionado no es válido.',
                ]);
        }


        try {

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            /* =================================================
            REPORTE
            ================================================= */

            $reporte =
                $db
                ->table('ai_reportes')
                ->select([
                    'id_reporte',
                    'folio',
                    'expediente',
                    'estado_actual',
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
                        'message' => 'El reporte no existe.',
                    ]);
            }


            /* =================================================
            HISTORIAL
            ================================================= */

            $seguimientos =
                $db
                ->table('ai_reporte_seguimientos')
                ->select([
                    'id_seguimiento',
                    'id_reporte',
                    'fecha',
                    'tipo',
                    'estado_resultante',
                    'observaciones',
                    'created_by',
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
                    'fecha',
                    'DESC'
                )
                ->orderBy(
                    'id_seguimiento',
                    'DESC'
                )
                ->get()
                ->getResultArray();


            return $this->response
                ->setJSON([
                    'success' => true,

                    'reporte' => $reporte,

                    'seguimientos' => $seguimientos,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando seguimientos del reporte {id}: {mensaje}',
                [
                    'id' => $idReporte,
                    'mensaje' => $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible consultar el historial de seguimiento.',
                ]);
        }
    }


    /* =========================================================
    REGISTRAR SEGUIMIENTO
    ========================================================= */

    public function guardarSeguimiento(int $idReporte)
    {
        /* =====================================================
        VALIDAR SESIÓN
        ===================================================== */

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


        if ($idReporte <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El reporte proporcionado no es válido.',
                ]);
        }


        /* =====================================================
        DATOS
        ===================================================== */

        $fecha =
            trim(
                (string)
                $this->request->getPost(
                    'fecha'
                )
            );


        $tipo =
            trim(
                (string)
                $this->request->getPost(
                    'tipo'
                )
            );


        $estado =
            trim(
                (string)
                $this->request->getPost(
                    'estado'
                )
            );


        $observaciones =
            trim(
                (string)
                $this->request->getPost(
                    'observaciones'
                )
            );


        /* =====================================================
        VALIDACIONES
        ===================================================== */

        if ($fecha === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'La fecha del seguimiento es obligatoria.',
                ]);
        }


        $fechaObjeto =
            \DateTime::createFromFormat(
                'Y-m-d',
                $fecha
            );


        if (
            !$fechaObjeto
            || $fechaObjeto->format('Y-m-d') !== $fecha
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'La fecha del seguimiento no es válida.',
                ]);
        }


        if ($tipo === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El tipo de seguimiento es obligatorio.',
                ]);
        }


        $estadosPermitidos = [
            'Pendiente',
            'En proceso',
            'Finalizado',
        ];


        if (
            !in_array(
                $estado,
                $estadosPermitidos,
                true
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El estado seleccionado no es válido.',
                ]);
        }


        if ($observaciones === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Las observaciones del seguimiento son obligatorias.',
                ]);
        }


        /* =====================================================
        TRANSACCIÓN
        ===================================================== */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        $db->transBegin();


        try {

            /* =================================================
            VALIDAR REPORTE
            ================================================= */

            $reporte =
                $db
                ->table('ai_reportes')
                ->select([
                    'id_reporte',
                    'folio',
                    'expediente',
                    'estado_actual',
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

                throw new \InvalidArgumentException(
                    'El reporte no existe.'
                );
            }


            /* =================================================
            INSERTAR SEGUIMIENTO
            ================================================= */

            $insertado =
                $db
                ->table(
                    'ai_reporte_seguimientos'
                )
                ->insert([
                    'id_reporte' =>
                    $idReporte,

                    'fecha' =>
                    $fecha,

                    'tipo' =>
                    $tipo,

                    'estado_resultante' =>
                    $estado,

                    'observaciones' =>
                    $observaciones,

                    'created_by' =>
                    $idUsuario,

                    'eliminado' =>
                    0,
                ]);


            if ($insertado === false) {

                throw new \RuntimeException(
                    'No fue posible registrar el seguimiento.'
                );
            }


            $idSeguimiento =
                (int)
                $db->insertID();


            /* =================================================
            ACTUALIZAR ESTADO DEL REPORTE
            ================================================= */

            $actualizado =
                $db
                ->table('ai_reportes')
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'eliminado',
                    0
                )
                ->update([
                    'estado_actual' =>
                    $estado,

                    'updated_by' =>
                    $idUsuario,
                ]);


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar el estado del reporte.'
                );
            }


            /* =================================================
            VALIDAR TRANSACCIÓN
            ================================================= */

            if (
                $db->transStatus()
                === false
            ) {

                throw new \RuntimeException(
                    'No fue posible completar el seguimiento.'
                );
            }


            $db->transCommit();


            /* =================================================
            RESPUESTA
            ================================================= */

            return $this->response
                ->setJSON([
                    'success' => true,

                    'message' =>
                    'El seguimiento se registró correctamente.',

                    'seguimiento' => [
                        'id_seguimiento' =>
                        $idSeguimiento,

                        'id_reporte' =>
                        $idReporte,

                        'fecha' =>
                        $fecha,

                        'tipo' =>
                        $tipo,

                        'estado_resultante' =>
                        $estado,

                        'observaciones' =>
                        $observaciones,
                    ],

                    'estado_actual' =>
                    $estado,
                ]);
        } catch (\InvalidArgumentException $e) {

            $db->transRollback();


            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    $e->getMessage(),
                ]);
        } catch (\Throwable $e) {

            $db->transRollback();


            log_message(
                'error',
                'Error registrando seguimiento del reporte {id}: {mensaje}',
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
                    'No fue posible registrar el seguimiento.',
                ]);
        }
    }

    public function validarFolio()
    {
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


        $folio =
            trim(
                (string)
                $this->request->getGet(
                    'folio'
                )
            );


        if ($folio === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'El folio es obligatorio.',
                ]);
        }


        try {

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            $existe =
                $db
                ->table('ai_reportes')
                ->select('id_reporte')
                ->where(
                    'folio',
                    $folio
                )
                ->get()
                ->getRowArray();


            return $this->response
                ->setJSON([
                    'success' => true,

                    'existe' =>
                    !empty($existe),

                    'message' =>
                    !empty($existe)
                        ? 'Ya existe un reporte registrado con este folio.'
                        : 'El folio está disponible.',
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error validando folio de reporte: {mensaje}',
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
                    'No fue posible validar el folio.',
                ]);
        }
    }
}
