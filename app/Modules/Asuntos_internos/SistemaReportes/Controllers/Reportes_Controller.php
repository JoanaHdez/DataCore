<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ReporteService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardService;

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
                    'id_cuadra',
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
            SANCIÓN DISCIPLINARIA VIGENTE
            ===================================================== */

            $sancion =
                $db
                ->table('ai_reporte_sanciones')
                ->select([
                    'id_sancion',
                    'tipo',
                    'descripcion_otro',
                    'origen',
                    'id_seguimiento',
                    'created_at',
                    'updated_at',
                ])
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'es_actual',
                    1
                )
                ->where(
                    'eliminado',
                    0
                )
                ->orderBy(
                    'id_sancion',
                    'DESC'
                )
                ->limit(1)
                ->get()
                ->getRowArray();


            /* =====================================================
            PREPARAR SANCIÓN PARA LA VISTA
            ===================================================== */

            $sancionDetalle =
                null;


            if ($sancion) {

                $tipo =
                    trim(
                        (string) (
                            $sancion['tipo']
                            ?? ''
                        )
                    );


                $descripcionOtro =
                    trim(
                        (string) (
                            $sancion['descripcion_otro']
                            ?? ''
                        )
                    );


                $texto =
                    $tipo;


                if (
                    $tipo === 'Otro'
                    && $descripcionOtro !== ''
                ) {

                    $texto =
                        $descripcionOtro;
                }


                $origen =
                    trim(
                        (string) (
                            $sancion['origen']
                            ?? ''
                        )
                    );


                $fechaOrigen =
                    $sancion['updated_at']
                    ?? $sancion['created_at']
                    ?? null;


                $fechaFormateada =
                    null;


                if (!empty($fechaOrigen)) {

                    $timestamp =
                        strtotime(
                            (string) $fechaOrigen
                        );


                    if ($timestamp !== false) {

                        $fechaFormateada =
                            date(
                                'd/m/Y',
                                $timestamp
                            );
                    }
                }


                $sancionDetalle = [

                    'id_sancion' =>
                    (int) (
                        $sancion['id_sancion']
                        ?? 0
                    ),

                    'tipo' =>
                    $tipo,

                    'descripcion_otro' =>
                    $descripcionOtro,

                    'texto' =>
                    $texto !== ''
                        ? $texto
                        : 'Sin sanción registrada',

                    'origen' =>
                    $origen,

                    'id_seguimiento' =>
                    !empty($sancion['id_seguimiento'])
                        ? (int) $sancion['id_seguimiento']
                        : null,

                    'actualizada_desde_seguimiento' =>
                    $origen === 'seguimiento',

                    'fecha_actualizacion' =>
                    $fechaFormateada,

                ];
            }


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

                    'sancion' =>
                    $sancionDetalle,

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
        /* =========================================================
        VALIDAR SESIÓN
        ========================================================= */

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


        /* =========================================================
        USUARIO
        ========================================================= */

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


        /* =========================================================
        DATOS DEL DASHBOARD
        ========================================================= */

        try {

            $dashboardService =
                new DashboardService();


            /* =====================================================
            FILTROS DEL DASHBOARD

            Zona ya se encuentra habilitada y se deriva del
            sector institucional del personal involucrado.

            Clasificación permanece visible pero deshabilitada
            hasta contar con un catálogo institucional.

            Resolución ya no forma parte de los filtros porque
            es un dato abierto.
            ===================================================== */

            $filtrosDashboard = [

                /* =================================================
                FECHAS
                ================================================= */

                'fecha_inicio' =>
                trim(
                    (string) $this->request->getGet(
                        'fecha_inicio'
                    )
                ),

                'fecha_fin' =>
                trim(
                    (string) $this->request->getGet(
                        'fecha_fin'
                    )
                ),

                'periodo' =>
                trim(
                    (string) $this->request->getGet(
                        'periodo'
                    )
                ),

                'tipo_fecha' =>
                trim(
                    (string) (
                        $this->request->getGet(
                            'tipo_fecha'
                        )
                        ?? 'registro'
                    )
                ),


                /* =================================================
                REPORTE
                ================================================= */

                /*
             * El HTML / JS envía:
             *
             * estado_actual
             *
             * DashboardService utiliza internamente:
             *
             * estado
             */

                'estado' =>
                trim(
                    (string) $this->request->getGet(
                        'estado_actual'
                    )
                ),

                'seguimiento' =>
                trim(
                    (string) $this->request->getGet(
                        'seguimiento'
                    )
                ),

                'evidencia' =>
                trim(
                    (string) $this->request->getGet(
                        'evidencia'
                    )
                ),


                /* =================================================
                PERSONAL INVOLUCRADO
                ================================================= */

                'area_personal' =>
                trim(
                    (string) $this->request->getGet(
                        'area_personal'
                    )
                ),

                'turno' =>
                trim(
                    (string) $this->request->getGet(
                        'turno'
                    )
                ),

                'zona' =>
                trim(
                    (string) $this->request->getGet(
                        'zona'
                    )
                ),


                /* =================================================
                QUEJOSO
                ================================================= */

                'genero' =>
                trim(
                    (string) $this->request->getGet(
                        'genero'
                    )
                ),


                /* =================================================
                UNIDAD
                ================================================= */

                'unidad' =>
                trim(
                    (string) $this->request->getGet(
                        'unidad'
                    )
                ),

            ];


            /* =====================================================
            ESTABLECER FILTROS
            ===================================================== */

            $dashboardService
                ->establecerFiltros(
                    $filtrosDashboard
                );


            /* =====================================================
            OPCIONES DE LOS FILTROS
            ===================================================== */

            $opcionesFiltros =
                $dashboardService
                ->obtenerOpcionesFiltros();


            /* =====================================================
            INDICADORES
            ===================================================== */

            $indicadores =
                $dashboardService
                ->obtenerIndicadores();


            /* =====================================================
            QUEJAS POR SECTORES Y TURNOS
            ===================================================== */

            $sectoresTurnos =
                $dashboardService
                ->obtenerSectoresTurnos();


            /* =====================================================
            QUEJAS POR ÁREA
            ===================================================== */

            $quejasPorArea =
                $dashboardService
                ->obtenerQuejasPorArea();


            /* =====================================================
            QUEJAS POR ZONA
            ===================================================== */

            $quejasPorZona =
                $dashboardService
                ->obtenerQuejasPorZona();


            /* =====================================================
            QUEJAS POR TURNO
            ===================================================== */

            $quejasPorTurno =
                $dashboardService
                ->obtenerQuejasPorTurno();


            /* =====================================================
            SANCIONES DISCIPLINARIAS

            Se utiliza únicamente la sanción actual
            de cada reporte.
            ===================================================== */

            $sanciones =
                $dashboardService
                ->obtenerSanciones();


            /* =====================================================
            RESOLUCIONES

            IMPORTANTE:
            Se conserva la gráfica.

            Resolución solamente dejó de ser un filtro.
            ===================================================== */

            $resoluciones =
                $dashboardService
                ->obtenerResoluciones();


            /* =====================================================
            CLASIFICACIONES

            La gráfica continúa funcionando.

            Clasificación permanece pendiente como filtro.
            ===================================================== */

            $clasificaciones =
                $dashboardService
                ->obtenerClasificaciones();


            /* =====================================================
            REPORTES RECIENTES
            ===================================================== */

            $reportesRecientes =
                $dashboardService
                ->obtenerReportesRecientes(
                    6
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando datos del Dashboard: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            /*
         * No impedimos cargar el Dashboard si ocurre
         * un problema con las estadísticas.
         */


            /* =====================================================
            VALORES POR DEFECTO - OPCIONES DE FILTROS
            ===================================================== */

            $opcionesFiltros = [

                'areas' =>
                [],

                'generos' =>
                [],

                'unidades' =>
                [],

            ];


            /* =====================================================
            VALORES POR DEFECTO - INDICADORES
            ===================================================== */

            $indicadores = [

                'total' =>
                0,

                'pendientes' =>
                0,

                'en_proceso' =>
                0,

                'finalizados' =>
                0,

            ];


            /* =====================================================
            VALORES POR DEFECTO - SECTORES Y TURNOS
            ===================================================== */

            $sectoresTurnos = [

                'sectores' =>
                [],

                'turnos' =>
                [],

            ];


            /* =====================================================
            VALORES POR DEFECTO - ÁREAS
            ===================================================== */

            $quejasPorArea = [

                'areas' =>
                [],

                'totales' =>
                [],

            ];


            /* =====================================================
            VALORES POR DEFECTO - ZONAS
            ===================================================== */

            $quejasPorZona = [

                'zonas' => [
                    'Zona Norte',
                    'Zona Poniente',
                    'Zona Centro',
                    'Zona Oriente',
                ],

                'totales' => [
                    0,
                    0,
                    0,
                    0,
                ],

                'total' =>
                0,

            ];


            /* =====================================================
            VALORES POR DEFECTO - TURNOS
            ===================================================== */

            $quejasPorTurno = [

                'turnos' =>
                [],

                'totales' =>
                [],

                'total' =>
                0,

            ];


            /* =====================================================
            VALORES POR DEFECTO - SANCIONES
            ===================================================== */

            $sanciones = [

                'tipos' => [
                    'Arresto',
                    'Amonestación',
                    'Otro',
                ],

                'totales' => [
                    0,
                    0,
                    0,
                ],

                'total' =>
                0,

            ];


            /* =====================================================
            VALORES POR DEFECTO - RESOLUCIONES
            ===================================================== */

            $resoluciones = [

                'resoluciones' =>
                [],

                'totales' =>
                [],

                'total' =>
                0,

            ];


            /* =====================================================
            VALORES POR DEFECTO - CLASIFICACIONES
            ===================================================== */

            $clasificaciones = [

                'clasificaciones' =>
                [],

                'totales' =>
                [],

                'total' =>
                0,

            ];


            /* =====================================================
            REPORTES RECIENTES
            ===================================================== */

            $reportesRecientes =
                [];
        }


        /* =========================================================
        VISTA
        ========================================================= */

        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\index',
            [

                'requiereAutorizacionAdmin' =>
                $requiereAutorizacion,


                /* =================================================
                FILTROS
                ================================================= */

                'opcionesFiltros' =>
                $opcionesFiltros,


                /* =================================================
               DASHBOARD
            ================================================= */

                'indicadores' =>
                $indicadores,

                'sectoresTurnos' =>
                $sectoresTurnos,

                'quejasPorArea' =>
                $quejasPorArea,

                'quejasPorZona' =>
                $quejasPorZona,

                'quejasPorTurno' =>
                $quejasPorTurno,

                'sanciones' =>
                $sanciones,

                'resoluciones' =>
                $resoluciones,

                'clasificaciones' =>
                $clasificaciones,

                'reportesRecientes' =>
                $reportesRecientes,

            ]
        );
    }

    /* =========================================================
   AUTORIZAR DASHBOARD
========================================================= */

public function autorizarDashboard()
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


    /* =====================================================
       USUARIO ACTUAL
    ===================================================== */

    $usuario =
        session()->get(
            'usuario_reportes'
        );


    $rol =
        $usuario['rol']
        ?? 'usuario';


    /*
     * Si ya es administrador,
     * no necesita autorización adicional.
     */
    if ($rol === 'admin') {

        session()->set(
            'reportes_dashboard_autorizado',
            true
        );


        return $this->response
            ->setJSON([
                'success' => true,
                'message' => 'Acceso autorizado.',
            ]);
    }


    /* =====================================================
       CONTRASEÑA ADMINISTRATIVA
    ===================================================== */

    $passwordAdmin =
        strtoupper(
            trim(
                (string)
                $this->request
                    ->getPost(
                        'password_admin'
                    )
            )
        );


    if ($passwordAdmin === '') {

        return $this->response
            ->setStatusCode(422)
            ->setJSON([
                'success' => false,
                'message' =>
                    'Ingresa la contraseña del administrador.',
            ]);
    }


    /* =====================================================
       VALIDAR AUTORIZACIÓN
    ===================================================== */

    try {

        $authService =
            new AuthService();


        $autorizado =
            $authService
                ->validarAutorizacionAdmin(
                    $passwordAdmin
                );


    } catch (\Throwable $e) {

        log_message(
            'error',
            'Error validando autorización administrativa para Dashboard: {mensaje}',
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


    /* =====================================================
       CONTRASEÑA INCORRECTA
    ===================================================== */

    if (!$autorizado) {

        return $this->response
            ->setStatusCode(403)
            ->setJSON([
                'success' => false,
                'message' =>
                    'Contraseña de administrador incorrecta.',
            ]);
    }


    /* =====================================================
       AUTORIZACIÓN CORRECTA
    ===================================================== */

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

    public function exportarDashboard()
    {
        /* =========================================================
        SECCIONES A EXPORTAR
        ========================================================= */

        $secciones =
            $this->request->getPost(
                'secciones'
            );


        if (
            !is_array($secciones)
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


        /* =========================================================
        FILTROS ACTIVOS DEL DASHBOARD

        Deben corresponder con los mismos filtros utilizados
        por el método dashboard().

        El JS los envía junto con las secciones seleccionadas.
        ========================================================= */

        $filtrosDashboard = [

            /* =====================================================
            FECHAS
            ===================================================== */

            'fecha_inicio' =>
            trim(
                (string) $this->request->getPost(
                    'fecha_inicio'
                )
            ),

            'fecha_fin' =>
            trim(
                (string) $this->request->getPost(
                    'fecha_fin'
                )
            ),

            'periodo' =>
            trim(
                (string) $this->request->getPost(
                    'periodo'
                )
            ),

            'tipo_fecha' =>
            trim(
                (string) (
                    $this->request->getPost(
                        'tipo_fecha'
                    )
                    ?? 'registro'
                )
            ),


            /* =====================================================
            REPORTE
            ===================================================== */

            /*
         * El frontend utiliza:
         *
         * estado_actual
         *
         * DashboardService utiliza internamente:
         *
         * estado
         */

            'estado' =>
            trim(
                (string) $this->request->getPost(
                    'estado_actual'
                )
            ),

            'seguimiento' =>
            trim(
                (string) $this->request->getPost(
                    'seguimiento'
                )
            ),

            'evidencia' =>
            trim(
                (string) $this->request->getPost(
                    'evidencia'
                )
            ),


            /* =====================================================
            PERSONAL INVOLUCRADO
            ===================================================== */

            'area_personal' =>
            trim(
                (string) $this->request->getPost(
                    'area_personal'
                )
            ),

            'turno' =>
            trim(
                (string) $this->request->getPost(
                    'turno'
                )
            ),

            'zona' =>
            trim(
                (string) $this->request->getPost(
                    'zona'
                )
            ),


            /* =====================================================
            QUEJOSO
            ===================================================== */

            'genero' =>
            trim(
                (string) $this->request->getPost(
                    'genero'
                )
            ),


            /* =====================================================
            UNIDAD
            ===================================================== */

            'unidad' =>
            trim(
                (string) $this->request->getPost(
                    'unidad'
                )
            ),

        ];


        /* =========================================================
        GENERAR ARCHIVO
        ========================================================= */

        try {

            /*
         * Los filtros se entregan a DashboardExcelService.
         *
         * DashboardExcelService los pasa posteriormente a
         * DashboardService, por lo que el Excel debe utilizar
         * exactamente la misma consulta filtrada que el
         * Dashboard mostrado en pantalla.
         */

            $servicio =
                new DashboardExcelService(
                    $filtrosDashboard
                );


            $ruta =
                $servicio->generar(
                    $secciones
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
                'Error exportando Dashboard: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error exportando Dashboard: {mensaje} en {archivo}:{linea}',
                [
                    'mensaje' =>
                    $e->getMessage(),

                    'archivo' =>
                    $e->getFile(),

                    'linea' =>
                    $e->getLine(),
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
            SECCIONES SOLICITADAS
            ===================================================== */

            $seccionesPermitidas = [

                'datos_reporte',

                'identificacion',

                'hechos',

                'ubicacion',

                'personal',

                'unidades',

                'quejoso',

                'clasificacion',

                'observaciones',

                'seguimientos',

                'evidencias',

            ];


            $seccionesSolicitadas =
                $this->request
                ->getPost(
                    'secciones'
                );


            if (
                !is_array(
                    $seccionesSolicitadas
                )
            ) {

                $seccionesSolicitadas =
                    [];
            }


            $secciones = [];


            foreach (
                $seccionesSolicitadas
                as $seccion
            ) {

                $seccion =
                    trim(
                        (string)
                        $seccion
                    );


                if (
                    $seccion !== ''
                    && in_array(
                        $seccion,
                        $seccionesPermitidas,
                        true
                    )
                    && !in_array(
                        $seccion,
                        $secciones,
                        true
                    )
                ) {

                    $secciones[] =
                        $seccion;
                }
            }


            if (
                empty($secciones)
            ) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Selecciona al menos una sección para exportar.',
                    ]);
            }


            /* =====================================================
            SECCIONES QUE REQUIEREN RELACIONES
            ===================================================== */

            $incluirPersonal =
                in_array(
                    'personal',
                    $secciones,
                    true
                );


            $incluirUnidades =
                in_array(
                    'unidades',
                    $secciones,
                    true
                );


            $incluirSeguimientos =
                in_array(
                    'seguimientos',
                    $secciones,
                    true
                );


            $incluirEvidencias =
                in_array(
                    'evidencias',
                    $secciones,
                    true
                );


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
                ->table(
                    'ai_reportes'
                )
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

            $personalDb =
                [];

            $unidadesDb =
                [];

            $evidenciasDb =
                [];

            $seguimientosDb =
                [];


            if (
                !empty($idsReales)
            ) {

                /* =================================================
                PERSONAL
                ================================================= */

                if (
                    $incluirPersonal
                ) {

                    $personalDb =
                        $db
                        ->table(
                            'ai_reporte_personal'
                        )
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
                }


                /* =================================================
                UNIDADES
                ================================================= */

                if (
                    $incluirUnidades
                ) {

                    $unidadesDb =
                        $db
                        ->table(
                            'ai_reporte_unidades u'
                        )
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
                }


                /* =================================================
                EVIDENCIAS
                ================================================= */

                if (
                    $incluirEvidencias
                ) {

                    $evidenciasDb =
                        $db
                        ->table(
                            'ai_reporte_evidencias'
                        )
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
                }


                /* =================================================
                SEGUIMIENTOS
                ================================================= */

                if (
                    $incluirSeguimientos
                ) {

                    $seguimientosDb =
                        $db
                        ->table(
                            'ai_reporte_seguimientos'
                        )
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
            }


            /* =====================================================
            AGRUPAR PERSONAL
            ===================================================== */

            $personalPorReporte =
                [];


            foreach (
                $personalDb
                as $persona
            ) {

                $idReporte =
                    (int) (
                        $persona['id_reporte']
                        ?? 0
                    );


                if (
                    $idReporte <= 0
                ) {
                    continue;
                }


                $personalPorReporte[$idReporte][] =
                    $persona;
            }


            /* =====================================================
            AGRUPAR UNIDADES
            ===================================================== */

            $unidadesPorReporte =
                [];


            foreach (
                $unidadesDb
                as $unidad
            ) {

                $idReporte =
                    (int) (
                        $unidad['id_reporte']
                        ?? 0
                    );


                if (
                    $idReporte <= 0
                ) {
                    continue;
                }


                $unidadesPorReporte[$idReporte][] =
                    $unidad;
            }


            /* =====================================================
            AGRUPAR EVIDENCIAS
            ===================================================== */

            $evidenciasPorReporte =
                [];


            foreach (
                $evidenciasDb
                as $evidencia
            ) {

                $idReporte =
                    (int) (
                        $evidencia['id_reporte']
                        ?? 0
                    );


                if (
                    $idReporte <= 0
                ) {
                    continue;
                }


                $nombre =
                    trim(
                        (string) (
                            $evidencia['nombre_original']
                            ?? ''
                        )
                    );


                if (
                    $nombre === ''
                ) {

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
            AGRUPAR SEGUIMIENTOS
            ===================================================== */

            $seguimientosPorReporte =
                [];


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
                ) {
                    continue;
                }


                $seguimientosPorReporte[$idReporte][] =
                    $seguimiento;
            }


            /* =====================================================
            PREPARAR REPORTES PARA EL EXCEL
            ===================================================== */

            $reportes =
                [];


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


                /* =================================================
                PERSONAL
                ================================================= */

                $nombresPersonal =
                    [];

                $areasPersonal =
                    [];

                $turnosPersonal =
                    [];


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

                $numerosUnidad =
                    [];

                $placasUnidad =
                    [];

                $marcasUnidad =
                    [];

                $submarcasUnidad =
                    [];

                $coloresUnidad =
                    [];

                $estatusUnidad =
                    [];

                $serviciosUnidad =
                    [];

                $tiposUnidad =
                    [];

                $origenesUnidad =
                    [];


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

                    'id_cuadra' =>
                    $reporte['id_cuadra']
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
                    SEGUIMIENTOS
                    ============================================= */

                    'seguimientos' =>
                    $seguimientosPorReporte[$idReporte]
                        ?? [],


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
                    $reportes,
                    $secciones
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

            /* =================================================
            CONEXIÓN DATACORE
            ================================================= */

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
            SANCIÓN VIGENTE
            ================================================= */

            $filaSancion =
                $db
                ->table('ai_reporte_sanciones')
                ->select([
                    'id_sancion',
                    'id_reporte',
                    'tipo',
                    'descripcion_otro',
                    'origen',
                    'id_seguimiento',
                    'es_actual',
                    'created_at',
                    'updated_at',
                ])
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->where(
                    'es_actual',
                    1
                )
                ->where(
                    'eliminado',
                    0
                )
                ->orderBy(
                    'id_sancion',
                    'DESC'
                )
                ->limit(1)
                ->get()
                ->getRowArray();


            $sancion =
                null;


            if ($filaSancion) {

                $tipo =
                    trim(
                        (string) (
                            $filaSancion['tipo']
                            ?? ''
                        )
                    );


                $descripcionOtro =
                    trim(
                        (string) (
                            $filaSancion['descripcion_otro']
                            ?? ''
                        )
                    );


                $texto =
                    $tipo;


                if (
                    $tipo === 'Otro'
                    && $descripcionOtro !== ''
                ) {

                    $texto =
                        $descripcionOtro;
                }


                $origen =
                    trim(
                        (string) (
                            $filaSancion['origen']
                            ?? ''
                        )
                    );


                $fechaActualizacion =
                    $filaSancion['updated_at']
                    ?? $filaSancion['created_at']
                    ?? null;


                $sancion = [

                    'id_sancion' =>
                    (int) (
                        $filaSancion['id_sancion']
                        ?? 0
                    ),

                    'tipo' =>
                    $tipo,

                    'descripcion_otro' =>
                    $descripcionOtro,

                    'texto' =>
                    $texto !== ''
                        ? $texto
                        : 'Sin sanción registrada',

                    'origen' =>
                    $origen,

                    'id_seguimiento' =>
                    !empty($filaSancion['id_seguimiento'])
                        ? (int) $filaSancion['id_seguimiento']
                        : null,

                    'actualizada_desde_seguimiento' =>
                    $origen === 'seguimiento',

                    'fecha_actualizacion' =>
                    $fechaActualizacion,

                    'es_actual' =>
                    true,

                ];
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


            /* =================================================
            SANCIÓN RELACIONADA CON CADA SEGUIMIENTO
            ================================================= */

            foreach (
                $seguimientos
                as &$seguimiento
            ) {

                $idSeguimiento =
                    (int) (
                        $seguimiento['id_seguimiento']
                        ?? 0
                    );


                $seguimiento['sancion'] =
                    null;


                if ($idSeguimiento <= 0) {
                    continue;
                }


                $sancionSeguimiento =
                    $db
                    ->table('ai_reporte_sanciones')
                    ->select([
                        'id_sancion',
                        'tipo',
                        'descripcion_otro',
                        'es_actual',
                    ])
                    ->where(
                        'id_reporte',
                        $idReporte
                    )
                    ->where(
                        'id_seguimiento',
                        $idSeguimiento
                    )
                    ->where(
                        'eliminado',
                        0
                    )
                    ->orderBy(
                        'id_sancion',
                        'DESC'
                    )
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                if (!$sancionSeguimiento) {
                    continue;
                }


                $tipoSeguimiento =
                    trim(
                        (string) (
                            $sancionSeguimiento['tipo']
                            ?? ''
                        )
                    );


                $otroSeguimiento =
                    trim(
                        (string) (
                            $sancionSeguimiento['descripcion_otro']
                            ?? ''
                        )
                    );


                $textoSeguimiento =
                    $tipoSeguimiento;


                if (
                    $tipoSeguimiento === 'Otro'
                    && $otroSeguimiento !== ''
                ) {

                    $textoSeguimiento =
                        $otroSeguimiento;
                }


                $seguimiento['sancion'] = [

                    'id_sancion' =>
                    (int) (
                        $sancionSeguimiento['id_sancion']
                        ?? 0
                    ),

                    'tipo' =>
                    $tipoSeguimiento,

                    'descripcion_otro' =>
                    $otroSeguimiento,

                    'texto' =>
                    $textoSeguimiento,

                    'es_actual' =>
                    (int) (
                        $sancionSeguimiento['es_actual']
                        ?? 0
                    ) === 1,

                ];
            }


            unset(
                $seguimiento
            );


            /* =================================================
            RESPUESTA
            ================================================= */

            return $this->response
                ->setJSON([

                    'success' =>
                    true,

                    'reporte' =>
                    $reporte,

                    'sancion' =>
                    $sancion,

                    'seguimientos' =>
                    $seguimientos,

                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando seguimientos del reporte {id}: {mensaje}',
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
                    'No fue posible consultar los seguimientos del reporte.',
                ]);
        }
    }

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
        DATOS DEL SEGUIMIENTO
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
        SANCIÓN
        ===================================================== */

        $sancionTipo =
            trim(
                (string)
                $this->request->getPost(
                    'sancion_disciplinaria'
                )
            );


        $sancionOtro =
            trim(
                (string)
                $this->request->getPost(
                    'sancion_otro'
                )
            );


        /* =====================================================
        VALIDAR FECHA
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


        /* =====================================================
        VALIDAR TIPO DE SEGUIMIENTO
        ===================================================== */

        $tiposPermitidos = [
            'Actualización',
            'Investigación',
            'Turnado',
            'Resolución',
            'Otro',
        ];


        if (
            !in_array(
                $tipo,
                $tiposPermitidos,
                true
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El tipo de seguimiento seleccionado no es válido.',
                ]);
        }


        /* =====================================================
        VALIDAR ESTADO
        ===================================================== */

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


        /* =====================================================
        VALIDAR OBSERVACIONES
        ===================================================== */

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
        VALIDAR SANCIÓN
        ===================================================== */

        $sancionesPermitidas = [
            '',
            'Arresto',
            'Amonestación',
            'Otro',
        ];


        if (
            !in_array(
                $sancionTipo,
                $sancionesPermitidas,
                true
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'La sanción disciplinaria seleccionada no es válida.',
                ]);
        }


        if ($sancionTipo === 'Otro') {

            if ($sancionOtro === '') {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Debes especificar la sanción disciplinaria.',
                    ]);
            }


            if (
                mb_strlen(
                    $sancionOtro
                ) > 255
            ) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'La descripción de la sanción no puede exceder 255 caracteres.',
                    ]);
            }
        } else {

            $sancionOtro =
                null;
        }


        /* =====================================================
        CONEXIÓN DATACORE
        ===================================================== */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =====================================================
        VALIDAR REPORTE
        ===================================================== */

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
                    'message' =>
                    'El reporte no existe.',
                ]);
        }


        /* =====================================================
        CONSULTAR SANCIÓN VIGENTE
        ===================================================== */

        $sancionActual =
            $db
            ->table('ai_reporte_sanciones')
            ->select([
                'id_sancion',
                'tipo',
                'descripcion_otro',
                'origen',
                'id_seguimiento',
                'es_actual',
            ])
            ->where(
                'id_reporte',
                $idReporte
            )
            ->where(
                'es_actual',
                1
            )
            ->where(
                'eliminado',
                0
            )
            ->orderBy(
                'id_sancion',
                'DESC'
            )
            ->limit(1)
            ->get()
            ->getRowArray();


        /* =====================================================
        DETERMINAR SI REALMENTE CAMBIA LA SANCIÓN
        ===================================================== */

        $hayCambioSancion =
            false;


        if ($sancionTipo !== '') {

            if (!$sancionActual) {

                $hayCambioSancion =
                    true;
            } else {

                $tipoActual =
                    trim(
                        (string) (
                            $sancionActual['tipo']
                            ?? ''
                        )
                    );


                $otroActual =
                    trim(
                        (string) (
                            $sancionActual['descripcion_otro']
                            ?? ''
                        )
                    );


                if (
                    $tipoActual !== $sancionTipo
                ) {

                    $hayCambioSancion =
                        true;
                } elseif (
                    $sancionTipo === 'Otro'
                    && mb_strtolower(
                        trim($otroActual)
                    ) !== mb_strtolower(
                        trim((string) $sancionOtro)
                    )
                ) {

                    $hayCambioSancion =
                        true;
                }
            }
        }


        /* =====================================================
        TRANSACCIÓN
        ===================================================== */

        $db->transBegin();


        try {

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


            if ($idSeguimiento <= 0) {

                throw new \RuntimeException(
                    'No fue posible identificar el seguimiento registrado.'
                );
            }


            /* =================================================
            CAMBIO DE SANCIÓN
            ================================================= */

            if ($hayCambioSancion) {

                /* =============================================
                SANCIÓN ANTERIOR DEJA DE SER ACTUAL
                ============================================= */

                if ($sancionActual) {

                    $desactivada =
                        $db
                        ->table(
                            'ai_reporte_sanciones'
                        )
                        ->where(
                            'id_sancion',
                            (int) $sancionActual['id_sancion']
                        )
                        ->where(
                            'id_reporte',
                            $idReporte
                        )
                        ->where(
                            'es_actual',
                            1
                        )
                        ->where(
                            'eliminado',
                            0
                        )
                        ->update([

                            'es_actual' =>
                            0,

                            'updated_by' =>
                            $idUsuario,

                            'updated_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),

                        ]);


                    if ($desactivada === false) {

                        throw new \RuntimeException(
                            'No fue posible actualizar la sanción anterior.'
                        );
                    }
                }


                /* =============================================
                NUEVA SANCIÓN VIGENTE
                ============================================= */

                $nuevaSancion =
                    $db
                    ->table(
                        'ai_reporte_sanciones'
                    )
                    ->insert([

                        'id_reporte' =>
                        $idReporte,

                        'tipo' =>
                        $sancionTipo,

                        'descripcion_otro' =>
                        $sancionOtro,

                        'origen' =>
                        'seguimiento',

                        'id_seguimiento' =>
                        $idSeguimiento,

                        'es_actual' =>
                        1,

                        'created_by' =>
                        $idUsuario,

                        'eliminado' =>
                        0,

                    ]);


                if ($nuevaSancion === false) {

                    throw new \RuntimeException(
                        'No fue posible registrar la nueva sanción disciplinaria.'
                    );
                }
            }


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

                    'updated_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

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

                    'success' =>
                    true,

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

                    'sancion_modificada' =>
                    $hayCambioSancion,

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


    public function actualizarSeguimiento(int $idSeguimiento)
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
                    'message' => 'No fue posible identificar al usuario.',
                ]);
        }


        if ($idSeguimiento <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'El seguimiento proporcionado no es válido.',
                ]);
        }


        /* =====================================================
        LEER DATOS PUT
        ===================================================== */

        $datos =
            $this->request->getRawInput();


        $fecha =
            trim(
                (string) (
                    $datos['fecha']
                    ?? ''
                )
            );


        $tipo =
            trim(
                (string) (
                    $datos['tipo']
                    ?? ''
                )
            );


        $estado =
            trim(
                (string) (
                    $datos['estado']
                    ?? ''
                )
            );


        $observaciones =
            trim(
                (string) (
                    $datos['observaciones']
                    ?? ''
                )
            );


        /*
     * En edición utilizaremos:
     *
     * sancion_accion:
     * - sin_cambio
     * - mantener
     * - cambiar
     * - quitar
     *
     * "mantener" significa que el seguimiento ya tenía
     * una sanción asociada y no fue modificada.
     */

        $sancionAccion =
            trim(
                (string) (
                    $datos['sancion_accion']
                    ?? 'sin_cambio'
                )
            );


        $sancionTipo =
            trim(
                (string) (
                    $datos['sancion_disciplinaria']
                    ?? ''
                )
            );


        $sancionOtro =
            trim(
                (string) (
                    $datos['sancion_otro']
                    ?? ''
                )
            );


        /* =====================================================
        VALIDACIONES GENERALES
        ===================================================== */

        if ($fecha === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'La fecha del seguimiento es obligatoria.',
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
                    'message' => 'La fecha del seguimiento no es válida.',
                ]);
        }


        $tiposPermitidos = [
            'Actualización',
            'Investigación',
            'Turnado',
            'Resolución',
            'Otro',
        ];


        if (
            !in_array(
                $tipo,
                $tiposPermitidos,
                true
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'El tipo de seguimiento seleccionado no es válido.',
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
                    'message' => 'El estado seleccionado no es válido.',
                ]);
        }


        if ($observaciones === '') {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Las observaciones del seguimiento son obligatorias.',
                ]);
        }


        $accionesPermitidas = [
            'sin_cambio',
            'mantener',
            'cambiar',
            'quitar',
        ];


        if (
            !in_array(
                $sancionAccion,
                $accionesPermitidas,
                true
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'La acción de sanción no es válida.',
                ]);
        }


        if (
            in_array(
                $sancionAccion,
                [
                    'mantener',
                    'cambiar',
                ],
                true
            )
        ) {

            $sancionesPermitidas = [
                'Arresto',
                'Amonestación',
                'Otro',
            ];


            if (
                !in_array(
                    $sancionTipo,
                    $sancionesPermitidas,
                    true
                )
            ) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'La sanción disciplinaria seleccionada no es válida.',
                    ]);
            }


            if ($sancionTipo === 'Otro') {

                if ($sancionOtro === '') {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' => 'Debes especificar la sanción disciplinaria.',
                        ]);
                }


                if (
                    mb_strlen(
                        $sancionOtro
                    ) > 255
                ) {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' => 'La descripción de la sanción no puede exceder 255 caracteres.',
                        ]);
                }
            } else {

                $sancionOtro =
                    null;
            }
        }


        /* =====================================================
        CONEXIÓN
        ===================================================== */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =====================================================
        CONSULTAR SEGUIMIENTO
        ===================================================== */

        $seguimiento =
            $db
            ->table('ai_reporte_seguimientos')
            ->where(
                'id_seguimiento',
                $idSeguimiento
            )
            ->where(
                'eliminado',
                0
            )
            ->get()
            ->getRowArray();


        if (!$seguimiento) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'El seguimiento solicitado no existe.',
                ]);
        }


        $idReporte =
            (int) (
                $seguimiento['id_reporte']
                ?? 0
            );


        if ($idReporte <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'El seguimiento no está relacionado con un reporte válido.',
                ]);
        }


        /* =====================================================
        SANCIÓN ASOCIADA A ESTE SEGUIMIENTO
        ===================================================== */

        $sancionSeguimiento =
            $db
            ->table('ai_reporte_sanciones')
            ->where(
                'id_reporte',
                $idReporte
            )
            ->where(
                'id_seguimiento',
                $idSeguimiento
            )
            ->where(
                'eliminado',
                0
            )
            ->orderBy(
                'id_sancion',
                'DESC'
            )
            ->limit(1)
            ->get()
            ->getRowArray();


        /*
     * Guardamos si esta sanción ES la vigente.
     *
     * Esto es fundamental:
     * editar una sanción histórica NO debe volverla actual.
     */

        $sancionSeguimientoEsActual =
            $sancionSeguimiento
            && (int) (
                $sancionSeguimiento['es_actual']
                ?? 0
            ) === 1;


        /* =====================================================
        TRANSACCIÓN
        ===================================================== */

        $db->transBegin();


        try {

            /* =================================================
            ACTUALIZAR EL MISMO SEGUIMIENTO
            ================================================= */

            $actualizado =
                $db
                ->table('ai_reporte_seguimientos')
                ->where(
                    'id_seguimiento',
                    $idSeguimiento
                )
                ->where(
                    'eliminado',
                    0
                )
                ->update([

                    'fecha' =>
                    $fecha,

                    'tipo' =>
                    $tipo,

                    'estado_resultante' =>
                    $estado,

                    'observaciones' =>
                    $observaciones,

                    'updated_by' =>
                    $idUsuario,

                    'updated_at' =>
                    date('Y-m-d H:i:s'),

                ]);


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar el seguimiento.'
                );
            }


            /* =================================================
            CORREGIR SANCIÓN EXISTENTE
            ================================================= */

            if (
                $sancionSeguimiento
                && $sancionAccion === 'cambiar'
            ) {

                $actualizarSancion =
                    $db
                    ->table('ai_reporte_sanciones')
                    ->where(
                        'id_sancion',
                        (int) $sancionSeguimiento['id_sancion']
                    )
                    ->where(
                        'id_reporte',
                        $idReporte
                    )
                    ->update([

                        'tipo' =>
                        $sancionTipo,

                        'descripcion_otro' =>
                        $sancionOtro,

                        /*
                     * NO modificamos:
                     *
                     * origen
                     * id_seguimiento
                     * es_actual
                     *
                     * porque estamos corrigiendo el mismo
                     * evento histórico.
                     */

                        'updated_by' =>
                        $idUsuario,

                        'updated_at' =>
                        date('Y-m-d H:i:s'),

                    ]);


                if ($actualizarSancion === false) {

                    throw new \RuntimeException(
                        'No fue posible corregir la sanción del seguimiento.'
                    );
                }
            }


            /* =================================================
            QUITAR SANCIÓN DE ESTE SEGUIMIENTO
            ================================================= */

            if (
                $sancionSeguimiento
                && $sancionAccion === 'quitar'
            ) {

                /*
             * Estamos diciendo que este seguimiento
             * realmente NO debió haber producido el cambio
             * de sanción.
             *
             * La fila se conserva como auditoría mediante
             * soft delete.
             */

                $eliminarSancion =
                    $db
                    ->table('ai_reporte_sanciones')
                    ->where(
                        'id_sancion',
                        (int) $sancionSeguimiento['id_sancion']
                    )
                    ->update([

                        'es_actual' =>
                        0,

                        'eliminado' =>
                        1,

                        'updated_by' =>
                        $idUsuario,

                        'updated_at' =>
                        date('Y-m-d H:i:s'),

                        'eliminado_at' =>
                        date('Y-m-d H:i:s'),

                        'eliminado_por' =>
                        $idUsuario,

                    ]);


                if ($eliminarSancion === false) {

                    throw new \RuntimeException(
                        'No fue posible corregir la sanción del seguimiento.'
                    );
                }


                /*
             * Si precisamente eliminamos la sanción que era
             * vigente, recuperamos la sanción activa anterior.
             */

                if ($sancionSeguimientoEsActual) {

                    $anterior =
                        $db
                        ->table('ai_reporte_sanciones')
                        ->where(
                            'id_reporte',
                            $idReporte
                        )
                        ->where(
                            'eliminado',
                            0
                        )
                        ->where(
                            'id_sancion <',
                            (int) $sancionSeguimiento['id_sancion']
                        )
                        ->orderBy(
                            'id_sancion',
                            'DESC'
                        )
                        ->limit(1)
                        ->get()
                        ->getRowArray();


                    if ($anterior) {

                        $db
                            ->table('ai_reporte_sanciones')
                            ->where(
                                'id_sancion',
                                (int) $anterior['id_sancion']
                            )
                            ->update([

                                'es_actual' =>
                                1,

                                'updated_by' =>
                                $idUsuario,

                                'updated_at' =>
                                date('Y-m-d H:i:s'),

                            ]);
                    }
                }
            }


            /* =================================================
            AGREGAR SANCIÓN A UN SEGUIMIENTO QUE NO TENÍA
            ================================================= */

            if (
                !$sancionSeguimiento
                && $sancionAccion === 'cambiar'
            ) {

                /*
             * Necesitamos saber si este seguimiento es el
             * movimiento más reciente del caso.
             *
             * Solamente en ese caso la sanción agregada
             * mediante la corrección puede convertirse
             * automáticamente en la sanción vigente.
             */

                $seguimientoMasReciente =
                    $db
                    ->table('ai_reporte_seguimientos')
                    ->select([
                        'id_seguimiento',
                        'fecha',
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
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                $esSeguimientoMasReciente =
                    $seguimientoMasReciente
                    && (int) (
                        $seguimientoMasReciente['id_seguimiento']
                        ?? 0
                    ) === $idSeguimiento;


                if ($esSeguimientoMasReciente) {

                    /*
                 * La sanción vigente anterior deja de ser
                 * actual, pero sigue en el historial.
                 */

                    $db
                        ->table('ai_reporte_sanciones')
                        ->where(
                            'id_reporte',
                            $idReporte
                        )
                        ->where(
                            'es_actual',
                            1
                        )
                        ->where(
                            'eliminado',
                            0
                        )
                        ->update([

                            'es_actual' =>
                            0,

                            'updated_by' =>
                            $idUsuario,

                            'updated_at' =>
                            date('Y-m-d H:i:s'),

                        ]);
                }


                $insertarSancion =
                    $db
                    ->table('ai_reporte_sanciones')
                    ->insert([

                        'id_reporte' =>
                        $idReporte,

                        'tipo' =>
                        $sancionTipo,

                        'descripcion_otro' =>
                        $sancionOtro,

                        'origen' =>
                        'seguimiento',

                        'id_seguimiento' =>
                        $idSeguimiento,

                        'es_actual' =>
                        $esSeguimientoMasReciente
                            ? 1
                            : 0,

                        'created_by' =>
                        $idUsuario,

                        'eliminado' =>
                        0,

                    ]);


                if ($insertarSancion === false) {

                    throw new \RuntimeException(
                        'No fue posible registrar la sanción corregida.'
                    );
                }
            }


            /* =================================================
            ESTADO ACTUAL DEL REPORTE
            ================================================= */

            /*
         * El estado_actual debe representar el último
         * seguimiento cronológico, no necesariamente el
         * seguimiento que acabamos de editar.
         */

            $ultimoSeguimiento =
                $db
                ->table('ai_reporte_seguimientos')
                ->select([
                    'id_seguimiento',
                    'estado_resultante',
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
                ->limit(1)
                ->get()
                ->getRowArray();


            $estadoActualReporte =
                trim(
                    (string) (
                        $ultimoSeguimiento['estado_resultante']
                        ?? $estado
                    )
                );


            $actualizarReporte =
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
                    $estadoActualReporte,

                    'updated_by' =>
                    $idUsuario,

                    'updated_at' =>
                    date('Y-m-d H:i:s'),

                ]);


            if ($actualizarReporte === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar el estado del reporte.'
                );
            }


            if (
                $db->transStatus()
                === false
            ) {

                throw new \RuntimeException(
                    'No fue posible completar la actualización.'
                );
            }


            $db->transCommit();


            return $this->response
                ->setJSON([

                    'success' =>
                    true,

                    'message' =>
                    'El seguimiento se actualizó correctamente.',

                    'id_reporte' =>
                    $idReporte,

                    'id_seguimiento' =>
                    $idSeguimiento,

                    'estado_actual' =>
                    $estadoActualReporte,

                ]);
        } catch (\Throwable $e) {

            $db->transRollback();


            log_message(
                'error',
                'Error actualizando seguimiento {id}: {mensaje}',
                [
                    'id' =>
                    $idSeguimiento,

                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'No fue posible actualizar el seguimiento.',
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
