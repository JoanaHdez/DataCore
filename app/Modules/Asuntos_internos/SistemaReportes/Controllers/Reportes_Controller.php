<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Controllers;

use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ListadoExcelService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\AuthService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\ReporteService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardInformeService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardInformeIaSanitizerService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardInformeIaService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\DashboardHallazgosService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\FolioService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\FelicitacionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\HistorialService;

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
            ARRESTO RELACIONADO

            Un reporte cuenta una sola vez si tiene al menos
            una sanción de tipo ARRESTO, sin importar cuántas
            sanciones de arresto tenga relacionadas.
            ===================================================== */

            $tieneArresto =
                $db
                ->table('ai_reporte_sanciones rs')
                ->join(
                    'ai_reporte_motivos rm',
                    'rm.id_reporte_motivo = rs.id_reporte_motivo',
                    'inner'
                )
                ->where(
                    'rm.id_reporte',
                    $reporte['id_reporte']
                )
                ->like(
                    'rs.tipo',
                    'ARRESTO',
                    'after'
                )
                ->countAllResults() > 0;


            $reporte['tiene_arresto'] =
                $tieneArresto
                ? 1
                : 0;


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
        SECTORES DISPONIBLES

        El catálogo se obtiene directamente desde plantilla
        para mostrar todos los sectores existentes, aunque
        todavía no tengan reportes relacionados.
        ========================================================= */

        $dbPlantilla =
            \Config\Database::connect(
                'plantilla'
            );


        $registrosSectores =
            $dbPlantilla
            ->table('plantilla')
            ->select('AREA')
            ->where(
                'ESTADO',
                'ACTIVO'
            )
            ->where(
                'AREA IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM(AREA) != ''",
                null,
                false
            )
            ->like(
                'AREA',
                'SECTOR',
                'after'
            )
            ->groupBy(
                'AREA'
            )
            ->get()
            ->getResultArray();


        $sectoresEncontrados =
            [];


        foreach (
            $registrosSectores
            as $registro
        ) {

            $area =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        mb_strtoupper(
                            (string) (
                                $registro['AREA']
                                ?? ''
                            ),
                            'UTF-8'
                        )
                    )
                        ?? ''
                );


            if (
                !preg_match(
                    '/^SECTOR\s+0*([0-9]+)/u',
                    $area,
                    $coincidencias
                )
            ) {
                continue;
            }


            $numeroSector =
                (int) (
                    $coincidencias[1]
                    ?? 0
                );


            if ($numeroSector <= 0) {
                continue;
            }


            $sector =
                'SECTOR '
                . str_pad(
                    (string) $numeroSector,
                    2,
                    '0',
                    STR_PAD_LEFT
                );


            $sectoresEncontrados[$numeroSector] =
                $sector;
        }


        /*
        * Ordenamos numéricamente:
        *
        * SECTOR 01
        * SECTOR 02
        * ...
        */

        ksort(
            $sectoresEncontrados,
            SORT_NUMERIC
        );


        $sectores =
            array_values(
                $sectoresEncontrados
            );


        /* =========================================================
        CATÁLOGO DE CANALIZACIÓN

        Se utiliza también en el modal Editar para mantener
        las mismas opciones que el formulario Nuevo.
        ========================================================= */

        $canalizaciones =
            $db
            ->table(
                'ai_cat_canalizacion_areas'
            )
            ->select([
                'id_area',
                'nombre',
            ])
            ->where(
                'activo',
                1
            )
            ->orderBy(
                'orden',
                'ASC'
            )
            ->orderBy(
                'nombre',
                'ASC'
            )
            ->get()
            ->getResultArray();


        /* =========================================================
        CATÁLOGO DE CLASIFICACIONES
        ========================================================= */

        $clasificaciones =
            $db
            ->table(
                'ai_cat_clasificaciones'
            )
            ->select([
                'id_clasificacion',
                'nombre',
            ])
            ->where(
                'activo',
                1
            )
            ->orderBy(
                'orden',
                'ASC'
            )
            ->orderBy(
                'nombre',
                'ASC'
            )
            ->get()
            ->getResultArray();


        /* =========================================================
        CATÁLOGO DE MOTIVOS
        ========================================================= */

        $motivos =
            $db
            ->table(
                'ai_cat_motivos'
            )
            ->select([
                'id_motivo',
                'motivo',
                'sancion',
            ])
            ->where(
                'activo',
                1
            )
            ->orderBy(
                'id_motivo',
                'ASC'
            )
            ->get()
            ->getResultArray();


        /* =========================================================
        CATALOGO DE TIPOS DE SEGUIMIENTO
        ========================================================= */

        $tiposSeguimiento =
            $this->obtenerTiposSeguimientoActivos(
                $db
            );


        /* =========================================================
        CATALOGO DE SANCIONES DE SEGUIMIENTO
        ========================================================= */

        $sancionesSeguimiento =
            $this->obtenerSancionesSeguimientoActivas(
                $db
            );


        /* =========================================================
        VISTA
        ========================================================= */

        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\index',
            [
                'reportes' =>
                $reportes,

                'sectores' =>
                $sectores,

                'canalizaciones' =>
                $canalizaciones,

                'clasificaciones' =>
                $clasificaciones,

                'motivos' =>
                $motivos,

                'tiposSeguimiento' =>
                $tiposSeguimiento,

                'sancionesSeguimiento' =>
                $sancionesSeguimiento,
            ]
        );
    }

    public function nuevo()
    {
        /* =========================================================
        VALORES POR DEFECTO
        ========================================================= */

        $folioVisual =
            'QJ- — Automático';


        $nomenclaturaVisual =
            'CGSC/CAI/QJ/—';


        $canalizaciones =
            [];


        $clasificaciones =
            [];


        $motivos =
            [];


        try {

            /* =====================================================
            CONEXIÓN DATACORE
            ===================================================== */

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            /* =====================================================
            FOLIO
            ===================================================== */

            $folioService =
                new FolioService(
                    $db
                );


            $previsualizacion =
                $folioService->previsualizar(
                    'QJ'
                );


            $numeroFolio =
                (int) (
                    $previsualizacion['numero_folio']
                    ?? 0
                );


            $folio =
                trim(
                    (string) (
                        $previsualizacion['folio']
                        ?? ''
                    )
                );


            $nomenclatura =
                trim(
                    (string) (
                        $previsualizacion['nomenclatura']
                        ?? ''
                    )
                );


            if (
                $folio !== ''
            ) {

                $folioVisual =
                    $folio;
            }


            if (
                $nomenclatura !== ''
            ) {

                $nomenclaturaVisual =
                    $nomenclatura;
            } elseif (
                $numeroFolio > 0
            ) {

                $nomenclaturaVisual =
                    'CGSC/CAI/QJ/'
                    . $numeroFolio;
            }


            /* =====================================================
            CATÁLOGO DE CANALIZACIÓN
            ===================================================== */

            $canalizaciones =
                $db
                ->table(
                    'ai_cat_canalizacion_areas'
                )
                ->select([
                    'id_area',
                    'nombre',
                ])
                ->where(
                    'activo',
                    1
                )
                ->orderBy(
                    'orden',
                    'ASC'
                )
                ->orderBy(
                    'nombre',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            /* =====================================================
            CATÁLOGO DE CLASIFICACIONES
            ===================================================== */

            $clasificaciones =
                $db
                ->table(
                    'ai_cat_clasificaciones'
                )
                ->select([
                    'id_clasificacion',
                    'nombre',
                ])
                ->where(
                    'activo',
                    1
                )
                ->orderBy(
                    'orden',
                    'ASC'
                )
                ->orderBy(
                    'nombre',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            /* =====================================================
            CATÁLOGO DE MOTIVOS
            ===================================================== */

            $motivos =
                $db
                ->table(
                    'ai_cat_motivos'
                )
                ->select([
                    'id_motivo',
                    'motivo',
                    'sancion',
                ])
                ->where(
                    'activo',
                    1
                )
                ->orderBy(
                    'id_motivo',
                    'ASC'
                )
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error preparando nuevo reporte: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );
        }


        return view(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\nuevo',
            [
                'folioVisual' =>
                $folioVisual,

                'nomenclaturaVisual' =>
                $nomenclaturaVisual,

                'canalizaciones' =>
                $canalizaciones,

                'clasificaciones' =>
                $clasificaciones,

                'motivos' =>
                $motivos,
            ]
        );
    }

    public function previsualizarFolio()
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


        /* =========================================================
        CLAVE DE FOLIO
        ========================================================= */

        $claveFolio =
            strtoupper(
                trim(
                    (string) (
                        $this->request
                        ->getGet(
                            'clave_folio'
                        )
                        ?? 'QJ'
                    )
                )
            );


        /* =========================================================
        PREVISUALIZAR FOLIO
        ========================================================= */

        try {

            $servicio =
                new FolioService();


            $resultado =
                $servicio->previsualizar(
                    $claveFolio
                );


            return $this->response
                ->setJSON([
                    'success' =>
                    true,

                    'tipo_registro' =>
                    $resultado['tipo_registro']
                        ?? null,

                    'clave_folio' =>
                    $resultado['clave_folio']
                        ?? $claveFolio,

                    'numero_folio' =>
                    $resultado['numero_folio']
                        ?? null,

                    'folio' =>
                    $resultado['folio']
                        ?? null,

                    'nomenclatura' =>
                    $resultado['nomenclatura']
                        ?? null,
                ]);
        } catch (\InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error previsualizando folio de Asuntos Internos: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'No fue posible consultar el siguiente folio.',
                ]);
        }
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
                    'message' =>
                    'La sesión no es válida.',
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


        if (
            $idUsuario <= 0
        ) {

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
            ->getPost(
                'personal'
            );


        $unidades =
            $this->request
            ->getPost(
                'unidades'
            );


        if (
            !is_array(
                $personal
            )
        ) {

            $personal = [];
        }


        if (
            !is_array(
                $unidades
            )
        ) {

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

            if (
                !is_array(
                    $archivos
                )
            ) {

                $archivos = [
                    $archivos,
                ];
            }
        }


        /* =========================================================
        GUARDAR REPORTE
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


            /* =====================================================
            IDENTIFICADOR DEL REPORTE CREADO
            ===================================================== */

            $idReporte =
                (int) (
                    $resultado['id_reporte']
                    ?? 0
                );


            $folio =
                trim(
                    (string) (
                        $resultado['folio']
                        ?? ''
                    )
                );


            /* =====================================================
            REGISTRAR HISTORIAL
            ===================================================== */

            if (
                $idReporte > 0
            ) {

                try {

                    $historialService =
                        new HistorialService();


                    $historialService
                        ->registrarCreacionReporte(
                            $idReporte,
                            $idUsuario
                        );
                } catch (\Throwable $e) {

                    /*
                    * El reporte YA fue creado correctamente.
                    *
                    * Si por alguna razón falla únicamente el registro
                    * de trazabilidad, no hacemos fallar toda la operación.
                    *
                    * Dejamos evidencia técnica en el log.
                    */

                    log_message(
                        'error',
                        'No fue posible registrar el historial de creación del reporte {idReporte}: {mensaje}',
                        [
                            'idReporte' =>
                            $idReporte,

                            'mensaje' =>
                            $e->getMessage(),
                        ]
                    );
                }
            }


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'success' =>
                    true,

                    'message' =>
                    'El reporte fue guardado correctamente.',

                    'id_reporte' =>
                    $idReporte > 0
                        ? $idReporte
                        : null,

                    'folio' =>
                    $folio !== ''
                        ? $folio
                        : null,
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

    public function guardarFelicitacion()
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
                    'message' =>
                    'La sesión no es válida.',
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


        if (
            $idUsuario <= 0
        ) {

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


        /* =========================================================
        PERSONAL
        ========================================================= */

        $personal =
            $this->request
            ->getPost(
                'personal'
            );


        if (
            !is_array(
                $personal
            )
        ) {

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


        if (
            !is_array(
                $unidades
            )
        ) {

            $unidades = [];
        }


        /* =========================================================
        GUARDAR
        ========================================================= */

        try {

            $servicio =
                new FelicitacionService();


            $resultado =
                $servicio->guardar(
                    $datos,
                    $personal,
                    $unidades,
                    $idUsuario
                );


            /* =====================================================
            IDENTIFICADOR DE LA FELICITACIÓN
            ===================================================== */

            $idFelicitacion =
                (int) (
                    $resultado['id_felicitacion']
                    ?? 0
                );


            /* =====================================================
            HISTORIAL
            ===================================================== */

            if (
                $idFelicitacion > 0
            ) {

                try {

                    $historialService =
                        new HistorialService();


                    $historialService
                        ->registrarCreacionFelicitacion(
                            $idFelicitacion,
                            $idUsuario
                        );
                } catch (\Throwable $e) {

                    /*
                 * La felicitación ya fue guardada.
                 *
                 * Si únicamente falla el historial,
                 * no hacemos fallar el alta.
                 */

                    log_message(
                        'error',
                        'No fue posible registrar el historial de creación de la felicitación {idFelicitacion}: {mensaje}',
                        [
                            'idFelicitacion' =>
                            $idFelicitacion,

                            'mensaje' =>
                            $e->getMessage(),
                        ]
                    );
                }
            }


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return $this->response
                ->setStatusCode(201)
                ->setJSON([
                    'success' =>
                    true,

                    'message' =>
                    'La felicitación fue guardada correctamente.',

                    'id_felicitacion' =>
                    $idFelicitacion > 0
                        ? $idFelicitacion
                        : null,

                    'numero_folio' =>
                    $resultado['numero_folio']
                        ?? null,

                    'folio' =>
                    $resultado['folio']
                        ?? null,

                    'nomenclatura' =>
                    $resultado['nomenclatura']
                        ?? null,
                ]);
        } catch (\InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    $e->getMessage(),
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error guardando felicitación de Asuntos Internos: {mensaje}',
                [
                    'mensaje' =>
                    $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'No fue posible guardar la felicitación.',
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
                    'message' =>
                    'La sesión no es válida.',
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


        if (
            $idUsuario <= 0
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible identificar al usuario.',
                ]);
        }


        if (
            $idReporte <= 0
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El reporte proporcionado no es válido.',
                ]);
        }


        /* =========================================================
        CONEXIÓN BD
        ========================================================= */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =========================================================
        OBTENER ESTADO ANTERIOR
        ========================================================= */

        $reporteAnterior =
            $db
            ->table(
                'ai_reportes'
            )
            ->select([
                'id_reporte',
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


        if (
            !$reporteAnterior
        ) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible localizar el reporte.',
                ]);
        }


        $estadoAnterior =
            trim(
                (string) (
                    $reporteAnterior['estado_actual']
                    ?? ''
                )
            );


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


        if (
            !is_array(
                $personal
            )
        ) {

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


        if (
            !is_array(
                $unidades
            )
        ) {

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


        if (
            !is_array(
                $evidenciasEliminadas
            )
        ) {

            $evidenciasEliminadas = [];
        }


        /* =========================================================
        EVIDENCIAS NUEVAS
        ========================================================= */

        $archivos = [];


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


            if (
                !is_array(
                    $archivos
                )
            ) {

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


            /* =====================================================
            OBTENER ESTADO RESULTANTE
            ===================================================== */

            $reporteActualizado =
                $db
                ->table(
                    'ai_reportes'
                )
                ->select([
                    'id_reporte',
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


            $estadoNuevo =
                trim(
                    (string) (
                        $reporteActualizado['estado_actual']
                        ?? ''
                    )
                );


            /* =====================================================
            REGISTRAR HISTORIAL
            ===================================================== */

            try {

                $historialService =
                    new HistorialService();


                /* =============================================
                EDICIÓN GENERAL
                ============================================== */

                $historialService
                    ->registrarEdicionReporte(
                        $idReporte,
                        $idUsuario
                    );


                /* =============================================
                CAMBIO DE ESTADO
                ============================================== */

                if (
                    $estadoAnterior !== ''
                    && $estadoNuevo !== ''
                    && $estadoAnterior !== $estadoNuevo
                ) {

                    $historialService
                        ->registrarCambioEstadoReporte(
                            $idReporte,
                            $idUsuario,
                            $estadoAnterior,
                            $estadoNuevo
                        );
                }
            } catch (\Throwable $e) {

                /*
                * El reporte ya fue actualizado.
                *
                * Si únicamente falla la trazabilidad,
                * no hacemos fallar la edición.
                */

                log_message(
                    'error',
                    'No fue posible registrar el historial de edición del reporte {idReporte}: {mensaje}',
                    [
                        'idReporte' =>
                        $idReporte,

                        'mensaje' =>
                        $e->getMessage(),
                    ]
                );
            }


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return $this->response
                ->setJSON([
                    'success' =>
                    true,

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
                    'success' =>
                    false,

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
                    'success' =>
                    false,

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
                    'folio_imp',
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

                    'calle_quejoso',
                    'numero_quejoso',
                    'colonia_quejoso',
                    'municipio_quejoso',
                    'estado_quejoso',

                    'es_anonimo',
                    'numero_anonimo',

                    'canalizacion_area',
                    'canalizacion_otro',

                    'clasificacion',
                    'inspector',
                    'investigador',

                    'sin_sanciones',
                    'baja_voluntaria',
                    'desistir',

                    'quien_emite_resolucion',
                    'resolucion',
                    'motivos',
                    'estado_actual',
                    'origen_estado',
                    'observaciones',
                    'modalidad_unidad',

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
            DIRECCIÓN PARA NOTIFICACIÓN
            ===================================================== */

            $direccionNotificacion =
                $db
                ->table(
                    'ai_reporte_direccion_notificacion'
                )
                ->select([
                    'id_direccion_notificacion',
                    'id_reporte',
                    'pertenece_neza',
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


            /*
            * La dirección para notificación es opcional.
            *
            * Si el reporte no tiene una registrada,
            * devolvemos null.
            */

            if (!$direccionNotificacion) {

                $direccionNotificacion =
                    null;
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
                    'alias_snapshot',
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


            $limpiarTextoDetalle =
                static function ($valor): string {

                    $texto =
                        trim(
                            (string) (
                                $valor
                                ?? ''
                            )
                        );


                    if ($texto === '') {
                        return '';
                    }


                    if (
                        mb_check_encoding(
                            $texto,
                            'UTF-8'
                        )
                    ) {

                        return $texto;
                    }


                    $textoConvertido =
                        mb_convert_encoding(
                            $texto,
                            'UTF-8',
                            'ISO-8859-1'
                        );


                    return mb_check_encoding(
                        $textoConvertido,
                        'UTF-8'
                    )
                        ? $textoConvertido
                        : '';
                };


            $folioReporte =
                strtoupper(
                    trim(
                        (string) (
                            $reporte['folio']
                            ?? ''
                        )
                    )
                );


            $nomenclaturaReporte =
                strtoupper(
                    trim(
                        (string) (
                            $reporte['nomenclatura']
                            ?? ''
                        )
                    )
                );


            $esQjf =
                substr(
                    $folioReporte,
                    0,
                    4
                ) === 'QJF-'
                || strpos(
                    $nomenclaturaReporte,
                    '/QJF/'
                ) !== false;


            $resolverResponsableDetalle =
                static function (
                    string $nombre,
                    bool $esQjf
                ) use (
                    $dbPlantilla,
                    $limpiarTextoDetalle
                ): ?array {

                    $nombreLimpio =
                        $limpiarTextoDetalle(
                            $nombre
                        );


                    if (
                        $esQjf
                        || strtoupper(
                            $nombreLimpio
                        ) === 'NO APLICA'
                    ) {

                        return [
                            'no_aplica' =>
                                true,
                        ];
                    }


                    if ($nombreLimpio === '') {
                        return null;
                    }


                    $persona =
                        $dbPlantilla
                        ->table(
                            'plantilla'
                        )
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
                        )
                        ->where(
                            'TIPO_NOMINA',
                            'RAMO 33'
                        )
                        ->where(
                            'AREA',
                            'COORDINACION DE ASUNTOS INTERNOS'
                        )
                        ->where(
                            'NOMBRE_COMPLETO',
                            $nombreLimpio
                        )
                        ->limit(1)
                        ->get()
                        ->getRowArray();


                    if (!$persona) {

                        return [
                            'no_aplica' =>
                                false,

                            'id' =>
                                0,

                            'perscod' =>
                                '',

                            'nombre' =>
                                $nombreLimpio,

                            'nomina' =>
                                '',

                            'area' =>
                                '',

                            'turno' =>
                                '',

                            'foto' =>
                                null,
                        ];
                    }


                    $perscod =
                        $limpiarTextoDetalle(
                            $persona['PERSCOD']
                            ?? ''
                        );


                    $foto =
                        null;


                    if ($perscod !== '') {

                        $foto =
                            'http://10.8.6.2:8083/dgsc/images/fotos/'
                            . rawurlencode(
                                $perscod
                            )
                            . '/F.F.R.E.jpg';
                    }


                    return [
                        'no_aplica' =>
                            false,

                        'id' =>
                            (int) (
                                $persona['ID']
                                ?? 0
                            ),

                        'perscod' =>
                            $perscod,

                        'nombre' =>
                            $limpiarTextoDetalle(
                                $persona['NOMBRE_COMPLETO']
                                ?? $nombreLimpio
                            ),

                        'nomina' =>
                            $limpiarTextoDetalle(
                                $persona['NO_NOMINA']
                                ?? ''
                            ),

                        'area' =>
                            $limpiarTextoDetalle(
                                $persona['AREA']
                                ?? ''
                            ),

                        'turno' =>
                            $limpiarTextoDetalle(
                                $persona['TURNO']
                                ?? ''
                            ),

                        'foto' =>
                            $foto,
                    ];
                };


            $reporte['inspector_detalle'] =
                $resolverResponsableDetalle(
                    (string) (
                        $reporte['inspector']
                        ?? ''
                    ),
                    $esQjf
                );


            $reporte['investigador_detalle'] =
                $resolverResponsableDetalle(
                    (string) (
                        $reporte['investigador']
                        ?? ''
                    ),
                    $esQjf
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

                    'alias' =>
                    trim(
                        (string) (
                            $persona['alias_snapshot']
                            ?? ''
                        )
                    ),

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
                ])
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
            MOTIVOS RELACIONADOS
            ===================================================== */

            $motivos =
                $db
                ->table('ai_reporte_motivos rm')
                ->select([
                    'rm.id_reporte_motivo',
                    'rm.id_motivo',
                    'rm.motivo_personalizado',

                    'm.motivo',
                    'm.sancion AS sancion',

                    's.id_sancion',
                    's.tipo AS sancion_registrada',
                    's.folio_sancion',
                    's.origen AS sancion_origen',
                ])
                ->join(
                    'ai_cat_motivos m',
                    'm.id_motivo = rm.id_motivo',
                    'left'
                )
                ->join(
                    'ai_reporte_sanciones s',
                    's.id_reporte_motivo = rm.id_reporte_motivo
            AND s.eliminado = 0',
                    'left'
                )
                ->where(
                    'rm.id_reporte',
                    $idReporte
                )
                ->where(
                    'rm.eliminado',
                    0
                )
                ->orderBy(
                    'rm.id_reporte_motivo',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            /* =====================================================
            PREPARAR TEXTO DEL MOTIVO
            ===================================================== */

            foreach (
                $motivos
                as &$motivo
            ) {

                $motivoPersonalizado =
                    trim(
                        (string) (
                            $motivo['motivo_personalizado']
                            ?? ''
                        )
                    );


                $motivoCatalogo =
                    trim(
                        (string) (
                            $motivo['motivo']
                            ?? ''
                        )
                    );


                $motivo['motivo_mostrar'] =
                    $motivoPersonalizado !== ''
                    ? $motivoPersonalizado
                    : $motivoCatalogo;
            }

            unset($motivo);


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

                    'direccion_notificacion' =>
                    $direccionNotificacion,

                    'personal' =>
                    $personal,

                    'unidades' =>
                    $unidades,

                    'evidencias' =>
                    $evidencias,

                    'motivos' =>
                    $motivos,

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


    /* =========================================================
    CONSTRUIR HALLAZGOS DEL DASHBOARD
    ========================================================= */

    private function construirHallazgosDashboard(
        array $estadosQuejas,
        array $quejasPorSector,
        array $quejasPorZona,
        array $quejasPorTurno,
        array $dimensionDashboard,
        bool $esFelicitacion
    ): array {

        return (new DashboardHallazgosService())
            ->construir(
                $estadosQuejas,
                $quejasPorSector,
                $quejasPorZona,
                $quejasPorTurno,
                $dimensionDashboard,
                $esFelicitacion
            );

        $hallazgos = [];


        /* =====================================================
        HELPER
        OBTENER MÁXIMOS

        Devuelve todas las categorías que comparten
        el valor máximo para evitar interpretar un empate
        como si existiera un único primer lugar.
        ===================================================== */

        $obtenerMayores =
            static function (
                array $etiquetas,
                array $totales
            ): ?array {

                if (
                    empty($etiquetas)
                    || empty($totales)
                ) {

                    return null;
                }


                $mayorTotal =
                    0;


                foreach (
                    $totales
                    as $total
                ) {

                    $total =
                        (int) $total;


                    if (
                        $total > $mayorTotal
                    ) {

                        $mayorTotal =
                            $total;
                    }
                }


                if (
                    $mayorTotal <= 0
                ) {

                    return null;
                }


                $mayores = [];


                foreach (
                    $totales
                    as $indice => $total
                ) {

                    if (
                        (int) $total
                        !== $mayorTotal
                    ) {

                        continue;
                    }


                    $etiqueta =
                        trim(
                            (string) (
                                $etiquetas[$indice]
                                ?? ''
                            )
                        );


                    if (
                        $etiqueta === ''
                    ) {

                        continue;
                    }


                    $mayores[] =
                        $etiqueta;
                }


                if (
                    empty($mayores)
                ) {

                    return null;
                }


                return [

                    'etiquetas' =>
                    $mayores,

                    'total' =>
                    $mayorTotal,

                    'empate' =>
                    count(
                        $mayores
                    ) > 1,

                ];
            };


        /* =====================================================
        HELPER
        MOSTRAR ETIQUETAS
        ===================================================== */

        $formatearEtiquetas =
            static function (
                array $etiquetas
            ): string {

                $cantidad =
                    count(
                        $etiquetas
                    );


                if (
                    $cantidad === 0
                ) {

                    return '';
                }


                if (
                    $cantidad === 1
                ) {

                    return
                        (string)
                        $etiquetas[0];
                }


                if (
                    $cantidad === 2
                ) {

                    return
                        $etiquetas[0]
                        . ' y '
                        . $etiquetas[1];
                }


                $ultima =
                    array_pop(
                        $etiquetas
                    );


                return
                    implode(
                        ', ',
                        $etiquetas
                    )
                    . ' y '
                    . $ultima;
            };


        /* =====================================================
        1. SECTOR
        ===================================================== */

        $sectorMayor =
            $obtenerMayores(
                $quejasPorSector['sectores']
                    ?? [],
                $quejasPorSector['totales']
                    ?? []
            );


        if (
            $sectorMayor !== null
        ) {

            $textoSector =
                $formatearEtiquetas(
                    $sectorMayor['etiquetas']
                );


            $total =
                (int)
                $sectorMayor['total'];


            if (
                $esFelicitacion
            ) {

                $descripcion =
                    $sectorMayor['empate']
                    ? $textoSector
                    . ' comparten la mayor cantidad, con '
                    . $total
                    . (
                        $total === 1
                        ? ' felicitación cada uno.'
                        : ' felicitaciones cada uno.'
                    )
                    : $textoSector
                    . ' registra '
                    . $total
                    . (
                        $total === 1
                        ? ' felicitación'
                        : ' felicitaciones'
                    )
                    . ' en el periodo seleccionado.';
            } else {

                $descripcion =
                    $sectorMayor['empate']
                    ? $textoSector
                    . ' comparten la mayor concentración, con '
                    . $total
                    . (
                        $total === 1
                        ? ' registro cada uno.'
                        : ' registros cada uno.'
                    )
                    : $textoSector
                    . ' concentra '
                    . $total
                    . (
                        $total === 1
                        ? ' registro'
                        : ' registros'
                    )
                    . ' en el periodo seleccionado.';
            }


            $hallazgos[] = [

                'tipo' =>
                'sector',

                'titulo' =>
                $sectorMayor['empate']
                    ? (
                        $esFelicitacion
                        ? 'Sectores con más felicitaciones'
                        : 'Sectores con mayor concentración'
                    )
                    : (
                        $esFelicitacion
                        ? 'Sector con más felicitaciones'
                        : 'Mayor concentración por sector'
                    ),

                'valor' =>
                $textoSector,

                'descripcion' =>
                $descripcion,

            ];
        }


        /* =====================================================
        2. ZONA
        ===================================================== */

        $zonaMayor =
            $obtenerMayores(
                $quejasPorZona['zonas']
                    ?? [],
                $quejasPorZona['totales']
                    ?? []
            );


        if (
            $zonaMayor !== null
        ) {

            $textoZona =
                $formatearEtiquetas(
                    $zonaMayor['etiquetas']
                );


            $total =
                (int)
                $zonaMayor['total'];


            $hallazgos[] = [

                'tipo' =>
                'zona',

                'titulo' =>
                $zonaMayor['empate']
                    ? (
                        $esFelicitacion
                        ? 'Zonas con más felicitaciones'
                        : 'Zonas con mayor concentración'
                    )
                    : (
                        $esFelicitacion
                        ? 'Zona con más felicitaciones'
                        : 'Zona con mayor concentración'
                    ),

                'valor' =>
                $textoZona,

                'descripcion' =>
                $zonaMayor['empate']
                    ? $textoZona
                    . ' comparten el valor máximo con '
                    . $total
                    . (
                        $esFelicitacion
                        ? (
                            $total === 1
                            ? ' felicitación cada una.'
                            : ' felicitaciones cada una.'
                        )
                        : (
                            $total === 1
                            ? ' registro cada una.'
                            : ' registros cada una.'
                        )
                    )
                    : $textoZona
                    . ' presenta '
                    . $total
                    . (
                        $esFelicitacion
                        ? (
                            $total === 1
                            ? ' felicitación.'
                            : ' felicitaciones.'
                        )
                        : (
                            $total === 1
                            ? ' registro.'
                            : ' registros.'
                        )
                    ),

            ];
        }


        /* =====================================================
        3. TURNO
        ===================================================== */

        $turnoMayor =
            $obtenerMayores(
                $quejasPorTurno['turnos']
                    ?? [],
                $quejasPorTurno['totales']
                    ?? []
            );


        if (
            $turnoMayor !== null
        ) {

            $textoTurno =
                $formatearEtiquetas(
                    $turnoMayor['etiquetas']
                );


            $total =
                (int)
                $turnoMayor['total'];


            $hallazgos[] = [

                'tipo' =>
                'turno',

                'titulo' =>
                $turnoMayor['empate']
                    ? (
                        $esFelicitacion
                        ? 'Turnos con más felicitaciones'
                        : 'Turnos con mayor concentración'
                    )
                    : (
                        $esFelicitacion
                        ? 'Turno con más felicitaciones'
                        : 'Turno con mayor concentración'
                    ),

                'valor' =>
                $textoTurno,

                'descripcion' =>
                $turnoMayor['empate']
                    ? $textoTurno
                    . ' comparten el valor máximo con '
                    . $total
                    . (
                        $esFelicitacion
                        ? (
                            $total === 1
                            ? ' felicitación cada uno.'
                            : ' felicitaciones cada uno.'
                        )
                        : (
                            $total === 1
                            ? ' registro cada uno.'
                            : ' registros cada uno.'
                        )
                    )
                    : $textoTurno
                    . ' reúne '
                    . $total
                    . (
                        $esFelicitacion
                        ? (
                            $total === 1
                            ? ' felicitación.'
                            : ' felicitaciones.'
                        )
                        : (
                            $total === 1
                            ? ' registro.'
                            : ' registros.'
                        )
                    ),

            ];
        }


        /* =====================================================
        4. ESTADO
        SOLO REPORTES
        ===================================================== */

        if (
            !$esFelicitacion
        ) {

            $estadoMayor =
                $obtenerMayores(
                    $estadosQuejas['estados']
                        ?? [],
                    $estadosQuejas['totales']
                        ?? []
                );


            if (
                $estadoMayor !== null
            ) {

                $textoEstado =
                    $formatearEtiquetas(
                        $estadoMayor['etiquetas']
                    );


                $total =
                    (int)
                    $estadoMayor['total'];


                $hallazgos[] = [

                    'tipo' =>
                    'estado',

                    'titulo' =>
                    $estadoMayor['empate']
                        ? 'Estados predominantes'
                        : 'Estado predominante',

                    'valor' =>
                    $textoEstado,

                    'descripcion' =>
                    $estadoMayor['empate']
                        ? $textoEstado
                        . ' comparten el mayor número de registros, con '
                        . $total
                        . ' cada uno.'
                        : $total
                        . (
                            $total === 1
                            ? ' registro se encuentra actualmente en estado '
                            : ' registros se encuentran actualmente en estado '
                        )
                        . $textoEstado
                        . '.',

                ];
            }
        }


        /* =====================================================
        5. DIMENSIÓN
        ÁREA / UNIDAD
        ===================================================== */

        $dimensionMayor =
            $obtenerMayores(
                $dimensionDashboard['etiquetas']
                    ?? [],
                $dimensionDashboard['totales']
                    ?? []
            );


        if (
            $dimensionMayor !== null
        ) {

            $textoDimension =
                $formatearEtiquetas(
                    $dimensionMayor['etiquetas']
                );


            $total =
                (int)
                $dimensionMayor['total'];


            $tituloDimension =
                trim(
                    (string) (
                        $dimensionDashboard['titulo']
                        ?? 'Dimensión'
                    )
                );


            $hallazgos[] = [

                'tipo' =>
                'dimension',

                'titulo' =>
                $dimensionMayor['empate']
                    ? $tituloDimension
                    . ' — mayor concentración compartida'
                    : $tituloDimension
                    . ' con mayor concentración',

                'valor' =>
                $textoDimension,

                'descripcion' =>
                $dimensionMayor['empate']
                    ? $textoDimension
                    . ' comparten el valor máximo con '
                    . $total
                    . (
                        $esFelicitacion
                        ? (
                            $total === 1
                            ? ' asociación con felicitaciones cada uno.'
                            : ' asociaciones con felicitaciones cada uno.'
                        )
                        : (
                            $total === 1
                            ? ' asociación con reportes cada uno.'
                            : ' asociaciones con reportes cada uno.'
                        )
                    )
                    : $textoDimension
                    . ' registra '
                    . $total
                    . (
                        $esFelicitacion
                        ? (
                            $total === 1
                            ? ' asociación con felicitaciones.'
                            : ' asociaciones con felicitaciones.'
                        )
                        : (
                            $total === 1
                            ? ' asociación con reportes.'
                            : ' asociaciones con reportes.'
                        )
                    ),

            ];
        }


        return array_slice(
            $hallazgos,
            0,
            5
        );
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


        $requiereAutorizacion =
            !$esAdmin
            && !$autorizacionTemporal;


        /* =========================================================
        DIMENSIÓN DEL DASHBOARD
        ÁREA / UNIDAD
        ========================================================= */

        $dimension =
            strtolower(
                trim(
                    (string)
                    $this->request->getGet(
                        'dimension'
                    )
                )
            );


        if (
            !in_array(
                $dimension,
                [
                    'area',
                    'unidad',
                ],
                true
            )
        ) {

            $dimension =
                'area';
        }


        /* =========================================================
        ANÁLISIS CRUZADO
        ========================================================= */

        $crucePrincipal =
            strtolower(
                trim(
                    (string)
                    $this->request->getGet(
                        'cruce_principal'
                    )
                )
            );


        $cruceSecundaria =
            strtolower(
                trim(
                    (string)
                    $this->request->getGet(
                        'cruce_secundaria'
                    )
                )
            );


        if (
            $crucePrincipal === ''
        ) {

            $crucePrincipal =
                'sector';
        }


        if (
            $cruceSecundaria === ''
        ) {

            $cruceSecundaria =
                'turno';
        }


        /* =========================================================
        RANKING TOP 5
        ========================================================= */

        $rankingTipo =
            strtolower(
                trim(
                    (string)
                    $this->request->getGet(
                        'ranking'
                    )
                )
            );


        if (
            !in_array(
                $rankingTipo,
                [
                    'sector',
                    'area',
                    'unidad',
                    'personal',
                ],
                true
            )
        ) {

            $rankingTipo =
                'sector';
        }


        $personalIndividualSeleccionado =
            trim(
                (string)
                $this->request->getGet(
                    'personal_individual'
                )
            );


        $personalIndividual = [

            'personal' => [

                'identificador' =>
                    $personalIndividualSeleccionado,

                'perscod' =>
                    null,

                'plantilla_id' =>
                    null,

                'nombre' =>
                    null,

                'area' =>
                    null,

                'turno' =>
                    null,

            ],

            'total_quejas' =>
                0,

            'ultimas_quejas' =>
                [],

        ];


        /* =========================================================
        DATOS DEL DASHBOARD
        ========================================================= */

        try {

            $dashboardService =
                new DashboardService();


            /* =====================================================
            FILTROS DEL DASHBOARD
            ===================================================== */

            $filtrosDashboard = [

                /* =================================================
                PERIODO
                ================================================= */

                'fecha_registro_inicio' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'fecha_registro_inicio'
                    )
                ),

                'fecha_registro_fin' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'fecha_registro_fin'
                    )
                ),


                /* =================================================
                TIPO
                ================================================= */

                'tipo' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'tipo'
                    )
                ),


                /* =================================================
                FILTROS DE QUEJA
                ================================================= */

                'estado' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'estado'
                    )
                ),

                'clasificacion' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'clasificacion'
                    )
                ),

                'seguimiento' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'seguimiento'
                    )
                ),

                'es_anonimo' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'es_anonimo'
                    )
                ),


                /* =================================================
                UBICACIÓN OPERATIVA
                ================================================= */

                'zona' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'zona'
                    )
                ),

                'sector' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'sector'
                    )
                ),

                'turno' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'turno'
                    )
                ),


                /* =================================================
                PERSONAL INVOLUCRADO
                ================================================= */

                'area_personal' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'area_personal'
                    )
                ),

                'personal' =>
                trim(
                    (string)
                    $this->request->getGet(
                        'personal'
                    )
                ),


                /* =================================================
                UNIDAD
                ================================================= */

                'unidad' =>
                trim(
                    (string)
                    $this->request->getGet(
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


            if (
                $personalIndividualSeleccionado !== ''
            ) {

                $personalIndividual =
                    $dashboardService
                    ->obtenerPersonalIndividual(
                        $personalIndividualSeleccionado
                    );
            }


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
            EVOLUCIÓN TEMPORAL
            ===================================================== */

            $evolucion =
                $dashboardService
                ->obtenerEvolucionTemporal();


            /* =====================================================
            ESTADO DE LAS QUEJAS
            ===================================================== */

            $estadosQuejas =
                $dashboardService
                ->obtenerEstadosQuejas();


            /* =====================================================
            QUEJAS POR SECTOR
            ===================================================== */

            $quejasPorSector =
                $dashboardService
                ->obtenerQuejasPorSector();


            /* =====================================================
            QUEJAS POR SECTORES Y TURNOS
            BLOQUE ANTERIOR
            ===================================================== */

            $sectoresTurnos =
                $dashboardService
                ->obtenerSectoresTurnos();


            /* =====================================================
            QUEJAS POR ÁREA
            BLOQUE ANTERIOR
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
            DIMENSIÓN DINÁMICA
            ÁREA / UNIDAD
            ===================================================== */

            $dimensionDashboard =
                $dashboardService
                ->obtenerDimension(
                    $dimension
                );


            /* =====================================================
            ANÁLISIS CRUZADO
            ===================================================== */

            $cruceDashboard =
                $dashboardService
                ->obtenerCruce(
                    $crucePrincipal,
                    $cruceSecundaria
                );


            /* =====================================================
            COMPARATIVAS
            ===================================================== */

            $comparativa =
                $dashboardService
                ->obtenerComparativa();


            /* =====================================================
            RANKING TOP 5
            ===================================================== */

            $rankingDashboard =
                $dashboardService
                ->obtenerRanking(
                    $rankingTipo
                );


            /* =====================================================
            SANCIONES DISCIPLINARIAS
            ===================================================== */

            $sanciones =
                $dashboardService
                ->obtenerSanciones();


            /* =====================================================
            CLASIFICACIONES
            ===================================================== */

            $clasificaciones =
                $dashboardService
                ->obtenerClasificaciones();

            /* =====================================================
                HALLAZGOS AUTOMÁTICOS
                ===================================================== */

            $tipoActivo =
                strtoupper(
                    trim(
                        (string) (
                            $filtrosDashboard['tipo']
                            ?? ''
                        )
                    )
                );


            $esFelicitacionDashboard =
                $tipoActivo === 'FELICITACION';


            $hallazgosDashboard =
                $this->construirHallazgosDashboard(
                    $estadosQuejas,
                    $quejasPorSector,
                    $quejasPorZona,
                    $quejasPorTurno,
                    $dimensionDashboard,
                    $esFelicitacionDashboard
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


            /* =====================================================
            OPCIONES DE FILTROS
            ===================================================== */

            $opcionesFiltros = [

                'areas' =>
                [],

                'clasificaciones' =>
                [],

                'unidades' =>
                [],

            ];


            /* =====================================================
            INDICADORES
            ===================================================== */

            $indicadores = [

                'total' =>
                0,

                'quejas' =>
                0,

                'felicitaciones' =>
                0,

                'pendientes' =>
                0,

                'en_proceso' =>
                0,

                'finalizados' =>
                0,

                'anonimas' =>
                0,

                'personal_involucrado' =>
                0,

            ];


            /* =====================================================
            EVOLUCIÓN TEMPORAL
            ===================================================== */

            $evolucion = [

                'agrupacion' =>
                'dia',

                'datos' =>
                [],

                'total' =>
                0,

            ];


            /* =====================================================
            ESTADO DE LAS QUEJAS
            ===================================================== */

            $estadosQuejas = [

                'estados' => [
                    'Pendiente',
                    'En proceso',
                    'Finalizado',
                ],

                'totales' => [
                    0,
                    0,
                    0,
                ],

                'porcentajes' => [
                    0,
                    0,
                    0,
                ],

                'total' =>
                0,

            ];


            /* =====================================================
            QUEJAS POR SECTOR
            ===================================================== */

            $quejasPorSector = [

                'sectores' => [
                    'SECTOR 1',
                    'SECTOR 2',
                    'SECTOR 3',
                    'SECTOR 4',
                    'SECTOR 5',
                    'SECTOR 6',
                    'SECTOR 7',
                    'SECTOR 8',
                    'SECTOR 9',
                    'SECTOR 10',
                    'SECTOR 11',
                    'SECTOR 12',
                    'SECTOR 13',
                    'SECTOR 14',
                    'SECTOR 15',
                ],

                'totales' => [
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                ],

                'total' =>
                0,

            ];


            /* =====================================================
            SECTORES Y TURNOS
            ===================================================== */

            $sectoresTurnos = [

                'sectores' =>
                [],

                'turnos' =>
                [],

            ];


            /* =====================================================
            ÁREAS
            ===================================================== */

            $quejasPorArea = [

                'areas' =>
                [],

                'totales' =>
                [],

            ];


            /* =====================================================
            ZONAS
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
            TURNOS
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
            DIMENSIÓN DINÁMICA
            ===================================================== */

            $dimensionDashboard = [

                'dimension' =>
                $dimension,

                'titulo' =>
                $dimension === 'unidad'
                    ? 'Unidad'
                    : 'Área',

                'etiquetas' =>
                [],

                'totales' =>
                [],

                'porcentajes' =>
                [],

                'total' =>
                0,

                'opciones' => [

                    [
                        'valor' =>
                        'area',

                        'texto' =>
                        'Área',
                    ],

                    [
                        'valor' =>
                        'unidad',

                        'texto' =>
                        'Unidad',
                    ],

                ],

            ];


            /* =====================================================
            ANÁLISIS CRUZADO
            ===================================================== */

            $cruceDashboard = [

                'principal' =>
                'sector',

                'secundaria' =>
                'turno',

                'categorias' =>
                [],

                'series' =>
                [],

                'total' =>
                0,

                'opciones_principal' => [

                    [
                        'valor' =>
                        'sector',

                        'texto' =>
                        'Sector',
                    ],

                    [
                        'valor' =>
                        'zona',

                        'texto' =>
                        'Zona',
                    ],

                    [
                        'valor' =>
                        'area',

                        'texto' =>
                        'Área',
                    ],

                    [
                        'valor' =>
                        'turno',

                        'texto' =>
                        'Turno',
                    ],

                ],

                'opciones_secundaria' => [

                    [
                        'valor' =>
                        'turno',

                        'texto' =>
                        'Turno',
                    ],

                    [
                        'valor' =>
                        'estado',

                        'texto' =>
                        'Estado',
                    ],

                ],

            ];


            /* =====================================================
            COMPARATIVAS
            ===================================================== */

            $comparativa = [

                'disponible' =>
                false,

                'dias_periodo' =>
                0,

                'periodo_actual' => [

                    'inicio' =>
                    null,

                    'fin' =>
                    null,

                ],

                'periodo_anterior' => [

                    'inicio' =>
                    null,

                    'fin' =>
                    null,

                ],

                'metricas' =>
                [],

            ];


            /* =====================================================
            RANKING TOP 5
            ===================================================== */

            $rankingDashboard = [

                'tipo' =>
                $rankingTipo,

                'titulo' =>
                match ($rankingTipo) {

                    'area' =>
                    'Áreas',

                    'unidad' =>
                    'Unidades',

                    'personal' =>
                    'Personal con mayor número de registros asociados',

                    default =>
                    'Sectores',
                },

                'etiquetas' =>
                [],

                'totales' =>
                [],

                'porcentajes' =>
                [],

                'total_top' =>
                0,

                'opciones' => [

                    [
                        'valor' =>
                        'sector',

                        'texto' =>
                        'Sectores',
                    ],

                    [
                        'valor' =>
                        'area',

                        'texto' =>
                        'Áreas',
                    ],

                    [
                        'valor' =>
                        'unidad',

                        'texto' =>
                        'Unidades',
                    ],

                    [
                        'valor' =>
                        'personal',

                        'texto' =>
                        'Personal',
                    ],

                ],

            ];


            /* =====================================================
            SANCIONES
            ===================================================== */

            $sanciones = [

                'tipos' => [
                    'Arresto',
                    'Amonestación',
                    'Llamada de atención',
                    'FALTA',
                    'Otro',
                ],

                'totales' => [
                    0,
                    0,
                    0,
                    0,
                    0,
                ],

                'total' =>
                0,

            ];


            /* =====================================================
            CLASIFICACIONES
            ===================================================== */

            $clasificaciones = [

                'clasificaciones' =>
                [],

                'totales' =>
                [],

                'total' =>
                0,

            ];
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
                DIMENSIÓN
                ================================================= */

                'dimensionSeleccionada' =>
                $dimension,


                /* =================================================
                RANKING
                ================================================= */

                'rankingSeleccionado' =>
                $rankingTipo,


                /* =================================================
                PERSONAL INDIVIDUAL
                ================================================= */

                'personalIndividualSeleccionado' =>
                $personalIndividualSeleccionado,

                'personalIndividual' =>
                $personalIndividual,


                /* =================================================
                DASHBOARD
                ================================================= */

                'indicadores' =>
                $indicadores,

                'evolucion' =>
                $evolucion,

                'estadosQuejas' =>
                $estadosQuejas,

                'quejasPorSector' =>
                $quejasPorSector,

                'sectoresTurnos' =>
                $sectoresTurnos,

                'quejasPorArea' =>
                $quejasPorArea,

                'quejasPorZona' =>
                $quejasPorZona,

                'quejasPorTurno' =>
                $quejasPorTurno,

                'dimensionDashboard' =>
                $dimensionDashboard,

                'cruceDashboard' =>
                $cruceDashboard,

                'comparativa' =>
                $comparativa,

                'rankingDashboard' =>
                $rankingDashboard,

                'hallazgosDashboard' =>
                $hallazgosDashboard,

                'sanciones' =>
                $sanciones,

                'clasificaciones' =>
                $clasificaciones,

            ]
        );
    }

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


            $administrador =
                $authService
                ->validarAutorizacionAdministradores(
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

        if (!$administrador) {

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
            FECHA DE REGISTRO
            ===================================================== */

            'fecha_registro_inicio' =>
            trim(
                (string) $this->request->getPost(
                    'fecha_registro_inicio'
                )
            ),

            'fecha_registro_fin' =>
            trim(
                (string) $this->request->getPost(
                    'fecha_registro_fin'
                )
            ),


            /* =====================================================
            FECHA DE LA QUEJA
            ===================================================== */

            'fecha_queja_inicio' =>
            trim(
                (string) $this->request->getPost(
                    'fecha_queja_inicio'
                )
            ),

            'fecha_queja_fin' =>
            trim(
                (string) $this->request->getPost(
                    'fecha_queja_fin'
                )
            ),


            /* =====================================================
            REPORTE
            ===================================================== */

            /*
            * El frontend envía:
            *
            * estado_actual
            *
            * DashboardService utiliza:
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

            'sector' =>
            trim(
                (string) $this->request->getPost(
                    'sector'
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
            * DashboardExcelService los pasa a DashboardService.
            *
            * De esta manera el Excel utiliza los mismos filtros
            * que actualmente están aplicados en el Dashboard.
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
            VALIDAR ARCHIVO
            ===================================================== */

            if (
                !$ruta
                || !is_file($ruta)
            ) {

                throw new \RuntimeException(
                    'El archivo de Excel no fue generado correctamente.'
                );
            }


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

            /* =====================================================
            LOG
            ===================================================== */

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


            /* =====================================================
            RESPUESTA
            ===================================================== */

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,

                    'message' =>
                    'No fue posible generar el archivo de Excel.',
                ]);
        }
    }

    public function prepararInformeDashboard()
    {
        /*
         * GENERADOR DE ANALISIS DASHBOARD
         * -----------------------------------------------------
         * Endpoint tecnico de preparacion. Devuelve payload
         * sanitizado y, si existe API key autorizada, narrativas
         * IA por seccion. No genera Word ni guarda historial.
         *
         * TODO IA/HISTORIAL:
         * Antes de persistir analisis generados se debe aprobar
         * una tabla que guarde solo filtros sanitizados, secciones,
         * configuracion, narrativa y metadata no sensible.
         */
        if (
            session()->get('reportes_autenticado') !== true
            || !session()->has('usuario_reportes')
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'ok' => false,

                    'message' =>
                    'La sesión no es válida.',
                ]);
        }

        $usuario =
            session()->get(
                'usuario_reportes'
            );

        $esAdmin =
            ($usuario['rol'] ?? null)
            === 'admin';

        $dashboardAutorizado =
            session()->get(
                'reportes_dashboard_autorizado'
            ) === true;

        if (
            !$esAdmin
            && !$dashboardAutorizado
        ) {

            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'ok' => false,

                    'message' =>
                    'Se requiere autorización administrativa para preparar el informe.',
                ]);
        }

        $payload =
            $this->request->getJSON(
                true
            );

        if (!is_array($payload)) {

            $payload =
                $this->request->getPost();
        }

        $filtros =
            $payload['filtros']
            ?? [];

        $secciones =
            $payload['secciones']
            ?? [];

        $configuracion =
            $payload['configuracion']
            ?? [];

        if (!is_array($filtros)) {

            $filtros = [];
        }

        if (!is_array($secciones)) {

            $secciones = [];
        }

        if (!is_array($configuracion)) {

            $configuracion = [];
        }

        try {

            $servicio =
                new DashboardInformeService();

            $resultado =
                $servicio->preparar(
                    $filtros,
                    $secciones,
                    $configuracion
                );

            $sanitizador =
                new DashboardInformeIaSanitizerService();

            $payloadSeguro =
                $sanitizador->construirPayloadSeguro(
                    $resultado
                );

            if (!empty($payloadSeguro['secciones'])) {

                $iaService =
                    new DashboardInformeIaService();

                $payloadSeguro['narrativas'] =
                    $iaService->generarNarrativas(
                        $payloadSeguro
                    );
            }

            return $this->response
                ->setJSON(
                    $payloadSeguro
                );

        } catch (\InvalidArgumentException | \RuntimeException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'ok' => false,

                    'message' =>
                    $e->getMessage(),
                ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error preparando informe de Dashboard: {mensaje} en {archivo}:{linea}',
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
                    'ok' => false,

                    'message' =>
                    'No fue posible preparar el informe del Dashboard.',
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
                    'message' =>
                    'La sesión no es válida.',
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


        $rol =
            $usuario['rol']
            ?? 'usuario';


        if (
            $idUsuario <= 0
        ) {

            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'No fue posible identificar al usuario.',
                ]);
        }


        if (
            $idReporte <= 0
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El reporte proporcionado no es válido.',
                ]);
        }


        /* =========================================================
        CONEXIÓN DATACORE
        ========================================================= */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =========================================================
        BUSCAR REPORTE
        ========================================================= */

        $reporte =
            $db
            ->table(
                'ai_reportes'
            )
            ->where(
                'id_reporte',
                $idReporte
            )
            ->get()
            ->getRowArray();


        if (
            !$reporte
        ) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El reporte no existe.',
                ]);
        }


        /*
        * Si ya estaba eliminado,
        * no volvemos a procesarlo.
        */

        if (
            (int) (
                $reporte['eliminado']
                ?? 0
            ) === 1
        ) {

            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El reporte ya fue eliminado.',
                ]);
        }


        /* =========================================================
        AUTORIZACIÓN
        ========================================================= */

        $idAdministradorAutorizador =
            null;


        /*
        * ADMIN
        *
        * Puede eliminar directamente.
        */

        if (
            $rol === 'admin'
        ) {

            $idAdministradorAutorizador =
                $idUsuario;
        } else {

            /*
            * USUARIO NORMAL
            *
            * El backend vuelve a exigir la contraseña administrativa
            * para ejecutar la operación real.
            */

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


            if (
                $passwordAdmin === ''
            ) {

                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Se requiere autorización administrativa.',
                    ]);
            }


            try {

                $authService =
                    new AuthService();


                $administrador =
                    $authService
                    ->validarAutorizacionAdministradores(
                        $passwordAdmin
                    );
            } catch (\Throwable $e) {

                log_message(
                    'error',
                    'Error validando autorización administrativa para eliminar reporte.'
                );


                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'No fue posible validar la autorización.',
                    ]);
            }


            if (
                !$administrador
            ) {

                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Contraseña de administrador incorrecta.',
                    ]);
            }


            $idAdministradorAutorizador =
                (int) (
                    $administrador['id_usuario']
                    ?? 0
                );


            if ($idAdministradorAutorizador <= 0) {

                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'No fue posible identificar al administrador autorizador.',
                    ]);
            }
        }


        /* =========================================================
        TRANSACCIÓN
        ========================================================= */

        $db->transBegin();


        try {

            $ahora =
                date(
                    'Y-m-d H:i:s'
                );


            /* =====================================================
            BORRADO LÓGICO
            ===================================================== */

            $db
                ->table(
                    'ai_reportes'
                )
                ->where(
                    'id_reporte',
                    $idReporte
                )
                ->update([
                    'eliminado' =>
                    1,

                    'eliminado_at' =>
                    $ahora,

                    'eliminado_por' =>
                    $idUsuario,

                    'updated_at' =>
                    $ahora,
                ]);


            /* =====================================================
            REGISTRO DE ELIMINACIÓN
            ===================================================== */

            $db
                ->table(
                    'ai_reporte_eliminaciones'
                )
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


            /* =====================================================
            HISTORIAL GENERAL
            ===================================================== */

            $historialService =
                new HistorialService();


            $historialService
                ->registrarEliminacionReporte(
                    $idReporte,
                    $idUsuario
                );


            /* =====================================================
            VALIDAR TRANSACCIÓN
            ===================================================== */

            if (
                $db->transStatus() === false
            ) {

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
                    'No fue posible eliminar el reporte.',
                ]);
        }


        /* =========================================================
        RESPUESTA
        ========================================================= */

        return $this->response
            ->setJSON([
                'success' =>
                true,

                'message' =>
                'El reporte fue eliminado correctamente.',
            ]);
    }

    public function buscarPersonal()
    {
        $termino =
            trim(
                (string)
                $this->request->getGet('q')
            );


        /*
        |--------------------------------------------------------------------------
        | NO BUSCAR SI EL CAMPO ESTÁ VACÍO
        |--------------------------------------------------------------------------
        */

        if (
            mb_strlen($termino) === 0
        ) {

            return $this->response
                ->setJSON([
                    'success' => true,
                    'personal' => [],
                ]);
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | CONEXIÓN A PLANTILLA
            |--------------------------------------------------------------------------
            */

            $db =
                \Config\Database::connect(
                    'plantilla'
                );


            /*
            |--------------------------------------------------------------------------
            | CONSULTA DE PERSONAL
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | BÚSQUEDA PARCIAL
            |--------------------------------------------------------------------------
            |
            | Permite buscar por:
            |
            | - Nombre
            | - Apellido
            | - Nombre completo
            | - Número de nómina
            |
            */

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
                ->orLike(
                    'PERSCOD',
                    $termino
                );


            if (
                ctype_digit($termino)
            ) {

                $builder
                    ->orWhere(
                        'ID',
                        (int) $termino
                    );
            }


            $builder
                ->groupEnd();


            /*
            |--------------------------------------------------------------------------
            | OBTENER RESULTADOS
            |--------------------------------------------------------------------------
            */

            $personal =
                $builder
                ->orderBy(
                    'NOMBRE_COMPLETO',
                    'ASC'
                )
                ->limit(10)
                ->get()
                ->getResultArray();


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR TEXTO PARA JSON
            |--------------------------------------------------------------------------
            |
            | Algunos registros de la BD pueden contener caracteres que no están
            | codificados correctamente como UTF-8.
            |
            | Esto evita:
            |
            | Malformed UTF-8 characters, possibly incorrectly encoded
            |
            */

            $limpiarTexto =
                static function ($valor): string {

                    $texto =
                        trim(
                            (string)
                            ($valor ?? '')
                        );


                    if ($texto === '') {

                        return '';
                    }


                    /*
                    * Si ya es UTF-8 válido,
                    * no modificamos nada.
                    */

                    if (
                        mb_check_encoding(
                            $texto,
                            'UTF-8'
                        )
                    ) {

                        return $texto;
                    }


                    /*
                    * Intentamos convertir los registros antiguos.
                    */

                    $textoConvertido =
                        mb_convert_encoding(
                            $texto,
                            'UTF-8',
                            'ISO-8859-1'
                        );


                    /*
                    * Última protección.
                    */

                    if (
                        ! mb_check_encoding(
                            $textoConvertido,
                            'UTF-8'
                        )
                    ) {

                        return '';
                    }


                    return $textoConvertido;
                };


            /*
            |--------------------------------------------------------------------------
            | PREPARAR RESPUESTA
            |--------------------------------------------------------------------------
            */

            $resultado = [];


            foreach (
                $personal as $persona
            ) {

                /*
                * PERSCOD
                */

                $perscod =
                    $limpiarTexto(
                        $persona['PERSCOD']
                            ?? ''
                    );


                /*
                * FOTO
                */

                $foto =
                    null;


                if (
                    $perscod !== ''
                ) {

                    $foto =
                        'http://10.8.6.2:8083/dgsc/images/fotos/'
                        . rawurlencode($perscod)
                        . '/F.F.R.E.jpg';
                }


                /*
                * RESULTADO
                */

                $resultado[] = [

                    'id' =>
                    (int)
                    ($persona['ID'] ?? 0),

                    'perscod' =>
                    $perscod,

                    'nombre' =>
                    $limpiarTexto(
                        $persona['NOMBRE_COMPLETO']
                            ?? ''
                    ),

                    'nomina' =>
                    $limpiarTexto(
                        $persona['NO_NOMINA']
                            ?? ''
                    ),

                    'area' =>
                    $limpiarTexto(
                        $persona['AREA']
                            ?? ''
                    ),

                    'turno' =>
                    $limpiarTexto(
                        $persona['TURNO']
                            ?? ''
                    ),

                    'foto' =>
                    $foto,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | RESPUESTA
            |--------------------------------------------------------------------------
            */

            return $this->response
                ->setJSON([
                    'success' => true,
                    'personal' => $resultado,
                ]);
        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

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

    public function buscarPersonalAsuntosInternos()
    {
        $termino =
            trim(
                (string)
                $this->request->getGet('q')
            );


        /* =========================================================
        NO BUSCAR SI EL CAMPO ESTÁ VACÍO
        ========================================================= */

        if (
            mb_strlen($termino) === 0
        ) {

            return $this->response
                ->setJSON([
                    'success' => true,
                    'personal' => [],
                ]);
        }


        try {

            /* =========================================================
            CONEXIÓN A PLANTILLA
            ========================================================= */

            $db =
                \Config\Database::connect(
                    'plantilla'
                );


            /* =========================================================
            CONSULTA
            ========================================================= */

            $builder =
                $db
                ->table(
                    'plantilla'
                )
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
                )
                ->where(
                    'TIPO_NOMINA',
                    'RAMO 33'
                )
                ->where(
                    'AREA',
                    'COORDINACION DE ASUNTOS INTERNOS'
                );


            /* =========================================================
            BÚSQUEDA
            ========================================================= */

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
                ->orLike(
                    'PERSCOD',
                    $termino
                )
                ->groupEnd();


            /* =========================================================
            RESULTADOS
            ========================================================= */

            $personal =
                $builder
                ->orderBy(
                    'NOMBRE_COMPLETO',
                    'ASC'
                )
                ->limit(10)
                ->get()
                ->getResultArray();


            /* =========================================================
            LIMPIAR TEXTO
            ========================================================= */

            $limpiarTexto =
                static function ($valor): string {

                    $texto =
                        trim(
                            (string)
                            ($valor ?? '')
                        );


                    if ($texto === '') {

                        return '';
                    }


                    if (
                        mb_check_encoding(
                            $texto,
                            'UTF-8'
                        )
                    ) {

                        return $texto;
                    }


                    $textoConvertido =
                        mb_convert_encoding(
                            $texto,
                            'UTF-8',
                            'ISO-8859-1'
                        );


                    if (
                        !mb_check_encoding(
                            $textoConvertido,
                            'UTF-8'
                        )
                    ) {

                        return '';
                    }


                    return $textoConvertido;
                };


            /* =========================================================
            PREPARAR RESPUESTA
            ========================================================= */

            $resultado = [];


            foreach (
                $personal
                as $persona
            ) {

                $perscod =
                    $limpiarTexto(
                        $persona['PERSCOD']
                            ?? ''
                    );


                $foto =
                    null;


                if (
                    $perscod !== ''
                ) {

                    $foto =
                        'http://10.8.6.2:8083/dgsc/images/fotos/'
                        . rawurlencode(
                            $perscod
                        )
                        . '/F.F.R.E.jpg';
                }


                $resultado[] = [

                    'id' =>
                    (int)
                    (
                        $persona['ID']
                        ?? 0
                    ),

                    'perscod' =>
                    $perscod,

                    'nombre' =>
                    $limpiarTexto(
                        $persona['NOMBRE_COMPLETO']
                            ?? ''
                    ),

                    'nomina' =>
                    $limpiarTexto(
                        $persona['NO_NOMINA']
                            ?? ''
                    ),

                    'area' =>
                    $limpiarTexto(
                        $persona['AREA']
                            ?? ''
                    ),

                    'turno' =>
                    $limpiarTexto(
                        $persona['TURNO']
                            ?? ''
                    ),

                    'foto' =>
                    $foto,
                ];
            }


            /* =========================================================
            RESPUESTA
            ========================================================= */

            return $this->response
                ->setJSON([
                    'success' => true,
                    'personal' => $resultado,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error buscando personal de Asuntos Internos: {mensaje}',
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
                    'No fue posible consultar el personal de Asuntos Internos.',
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
                    'folio_ip',
                    'expediente',
                    'nomenclatura',
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
                    'id_sancion_seguimiento',
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


                $origen =
                    trim(
                        (string) (
                            $filaSancion['origen']
                            ?? ''
                        )
                    );


                if (
                    $origen === 'seguimiento'
                    && !empty($filaSancion['id_sancion_seguimiento'])
                ) {

                    $texto =
                        $this->obtenerNombreSancionSeguimientoPorId(
                            $db,
                            (int) $filaSancion['id_sancion_seguimiento']
                        )
                        ?: $tipo;
                } elseif (
                    $tipo === 'Otro'
                    && $descripcionOtro !== ''
                ) {

                    $texto =
                        $descripcionOtro;
                }


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
                    $texto,

                    'id_sancion_seguimiento' =>
                    !empty($filaSancion['id_sancion_seguimiento'])
                        ? (int) $filaSancion['id_sancion_seguimiento']
                        : null,

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
                ->table('ai_reporte_seguimientos s')
                ->select([
                    's.id_seguimiento',
                    's.id_reporte',
                    's.fecha',
                    's.folio_ip',
                    's.tipo AS tipo_legacy',
                    's.id_tipo_seguimiento',
                    's.tipo_otro',
                    'ts.nombre AS tipo_catalogo',
                    's.estado_resultante',
                    's.observaciones',
                    's.created_by',
                    's.created_at',
                ])
                ->join(
                    'ai_cat_tipos_seguimiento ts',
                    'ts.id_tipo_seguimiento = s.id_tipo_seguimiento',
                    'left'
                )
                ->where(
                    's.id_reporte',
                    $idReporte
                )
                ->where(
                    's.eliminado',
                    0
                )
                ->orderBy(
                    's.fecha',
                    'DESC'
                )
                ->orderBy(
                    's.id_seguimiento',
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

                $tipoCatalogo =
                    trim(
                        (string) (
                            $seguimiento['tipo_catalogo']
                            ?? ''
                        )
                    );


                $tipoOtro =
                    trim(
                        (string) (
                            $seguimiento['tipo_otro']
                            ?? ''
                        )
                    );


                $tipoLegacy =
                    trim(
                        (string) (
                            $seguimiento['tipo_legacy']
                            ?? ''
                        )
                    );


                $seguimiento['tipo'] =
                    $this->formatearTipoSeguimiento(
                        $tipoCatalogo,
                        $tipoOtro,
                        $tipoLegacy
                    );


                $seguimiento['tipo_texto'] =
                    $seguimiento['tipo'];

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
                    ->table('ai_reporte_sanciones rs')
                    ->select([
                        'rs.id_sancion',
                        'rs.tipo',
                        'rs.id_sancion_seguimiento',
                        'ss.nombre AS sancion_catalogo',
                        'rs.descripcion_otro',
                        'rs.es_actual',
                    ])
                    ->join(
                        'ai_cat_sanciones_seguimiento ss',
                        'ss.id_sancion_seguimiento = rs.id_sancion_seguimiento',
                        'left'
                    )
                    ->where(
                        'rs.id_reporte',
                        $idReporte
                    )
                    ->where(
                        'rs.id_seguimiento',
                        $idSeguimiento
                    )
                    ->where(
                        'rs.eliminado',
                        0
                    )
                    ->orderBy(
                        'rs.id_sancion',
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
                            $sancionSeguimiento['sancion_catalogo']
                            ?? $sancionSeguimiento['tipo']
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

                    'id_sancion_seguimiento' =>
                    !empty($sancionSeguimiento['id_sancion_seguimiento'])
                        ? (int) $sancionSeguimiento['id_sancion_seguimiento']
                        : null,

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

                    'tipos_seguimiento' =>
                    $this->obtenerTiposSeguimientoActivos(
                        $db
                    ),

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


        $idTipoSeguimiento =
            (int)
            $this->request->getPost(
                'id_tipo_seguimiento'
            );


        $tipoOtro =
            trim(
                (string)
                $this->request->getPost(
                    'tipo_otro'
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
        FOLIO IP
        ===================================================== */

        $folioIp =
            trim(
                (string)
                $this->request->getPost(
                    'folio_ip'
                )
            );


        if (
            mb_strlen(
                $folioIp
            ) > 100
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El Folio IP no puede exceder 100 caracteres.',
                ]);
        }


        /* =====================================================
        SANCIÓN
        ===================================================== */

        $idSancionSeguimiento =
            (int)
            $this->request->getPost(
                'id_sancion_seguimiento'
            );


        $sancionTipo =
            '';


        $sancionOtro =
            null;


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

        $db =
            \Config\Database::connect(
                'datacore'
            );


        $tipoSeguimiento =
            $this->obtenerTipoSeguimientoActivo(
                $db,
                $idTipoSeguimiento
            );


        if (!$tipoSeguimiento) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El tipo de seguimiento seleccionado no es válido.',
                ]);
        }


        $tipo =
            trim(
                (string) (
                    $tipoSeguimiento['nombre']
                    ?? ''
                )
            );


        if ($tipo === 'OTRO') {

            if ($tipoOtro === '') {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Debes especificar el tipo de seguimiento.',
                    ]);
            }


            if (
                mb_strlen(
                    $tipoOtro
                ) > 255
            ) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El tipo de seguimiento especificado no puede exceder 255 caracteres.',
                    ]);
            }
        } else {

            $tipoOtro =
                null;
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

        if ($idSancionSeguimiento > 0) {

            $sancionCatalogo =
                $this->obtenerSancionSeguimientoActiva(
                    $db,
                    $idSancionSeguimiento
                );


            if (!$sancionCatalogo) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'La sanción disciplinaria seleccionada no es válida.',
                    ]);
            }


            $sancionTipo =
                trim(
                    (string) (
                        $sancionCatalogo['nombre']
                        ?? ''
                    )
                );
        }

        /* =====================================================
        VALIDAR REPORTE
        ===================================================== */

        $reporte =
            $db
            ->table('ai_reportes')
            ->select([
                'id_reporte',
                'folio',
                'folio_ip',
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
        VALIDAR FOLIO IP DUPLICADO
        ===================================================== */

        if ($folioIp !== '') {

            $folioIpExistente =
                $db
                ->table('ai_reportes')
                ->select([
                    'id_reporte',
                    'folio',
                ])
                ->where(
                    'folio_ip',
                    $folioIp
                )
                ->where(
                    'eliminado',
                    0
                )
                ->where(
                    'id_reporte !=',
                    $idReporte
                )
                ->limit(1)
                ->get()
                ->getRowArray();


            if ($folioIpExistente) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El Folio IP ya se encuentra registrado en otro reporte. Debes ingresar uno diferente para continuar.',
                    ]);
            }
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
                'id_sancion_seguimiento',
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


        if ($idSancionSeguimiento > 0) {

            if (!$sancionActual) {

                $hayCambioSancion =
                    true;
            } else {

                $tipoActual =
                    (int) (
                        $sancionActual['id_sancion_seguimiento']
                        ?? 0
                    );


                if (
                    $tipoActual !== $idSancionSeguimiento
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

                    'id_tipo_seguimiento' =>
                    $idTipoSeguimiento,

                    'tipo_otro' =>
                    $tipoOtro,

                    'folio_ip' =>
                    $folioIp !== ''
                        ? $folioIp
                        : null,

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

                        'id_sancion_seguimiento' =>
                        $idSancionSeguimiento,

                        'descripcion_otro' =>
                        null,

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
            ACTUALIZAR REPORTE PRINCIPAL
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

                    'folio_ip' =>
                    $folioIp !== ''
                        ? $folioIp
                        : (
                            $reporte['folio_ip']
                            ?? null
                        ),

                    'estado_actual' =>
                    $estado,

                    'origen_estado' =>
                    'seguimiento',

                    'updated_by' =>
                    $idUsuario,

                    'updated_at' =>
                    date(
                        'Y-m-d H:i:s'
                    ),

                ]);


            if ($actualizado === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar el reporte.'
                );
            }


            /* =================================================
            HISTORIAL GENERAL
            ================================================= */

            $historialService =
                new HistorialService();


            /* =================================================
            SEGUIMIENTO CREADO
            ================================================= */

            $historialService
                ->registrarCreacionSeguimiento(
                    $idSeguimiento,
                    $idReporte,
                    $idUsuario
                );


            /* =================================================
            CAMBIO DE ESTADO
            ================================================= */

            $estadoAnterior =
                trim(
                    (string) (
                        $reporte['estado_actual']
                        ?? ''
                    )
                );


            if (
                $estadoAnterior !== ''
                && $estadoAnterior !== $estado
            ) {

                $historialService
                    ->registrarCambioEstadoReporte(
                        $idReporte,
                        $idUsuario,
                        $estadoAnterior,
                        $estado
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
                        $this->formatearTipoSeguimiento(
                            $tipo,
                            $tipoOtro,
                            $tipo
                        ),

                        'tipo_catalogo' =>
                        $tipo,

                        'id_tipo_seguimiento' =>
                        $idTipoSeguimiento,

                        'tipo_otro' =>
                        $tipoOtro,

                        'tipo_texto' =>
                        $this->formatearTipoSeguimiento(
                            $tipo,
                            $tipoOtro,
                            $tipo
                        ),

                        'estado_resultante' =>
                        $estado,

                        'observaciones' =>
                        $observaciones,

                    ],

                    'folio_ip' =>
                    $folioIp !== ''
                        ? $folioIp
                        : (
                            $reporte['folio_ip']
                            ?? null
                        ),

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
                    'message' =>
                    'No fue posible identificar al usuario.',
                ]);
        }


        if ($idSeguimiento <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El seguimiento proporcionado no es válido.',
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


        $idTipoSeguimiento =
            (int) (
                $datos['id_tipo_seguimiento']
                ?? 0
            );


        $tipoOtro =
            trim(
                (string) (
                    $datos['tipo_otro']
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


        /* =====================================================
        FOLIO IP
        ===================================================== */

        $folioIp =
            trim(
                (string) (
                    $datos['folio_ip']
                    ?? ''
                )
            );


        if (
            mb_strlen(
                $folioIp
            ) > 100
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'El Folio IP no puede exceder 100 caracteres.',
                ]);
        }


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


        $idSancionSeguimiento =
            (int) (
                $datos['id_sancion_seguimiento']
                ?? 0
            );


        $sancionTipo =
            '';


        $sancionOtro =
            null;


        /* =====================================================
        VALIDACIONES GENERALES
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
                    'message' =>
                    'La acción de sanción no es válida.',
                ]);
        }


        /* =====================================================
        CONEXIÓN
        ===================================================== */

        $db =
            \Config\Database::connect(
                'datacore'
            );


        /* =====================================================
        VALIDAR SANCIÓN DE SEGUIMIENTO
        ===================================================== */

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

            if ($idSancionSeguimiento > 0) {

                $sancionCatalogo =
                    $this->obtenerSancionSeguimientoActiva(
                        $db,
                        $idSancionSeguimiento
                    );


                if (!$sancionCatalogo) {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' =>
                            'La sanción disciplinaria seleccionada no es válida.',
                        ]);
                }


                $sancionTipo =
                    trim(
                        (string) (
                            $sancionCatalogo['nombre']
                            ?? ''
                        )
                    );
            } elseif ($sancionAccion === 'cambiar') {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'La sanción disciplinaria seleccionada no es válida.',
                    ]);
            }
        }


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
                    'message' =>
                    'El seguimiento solicitado no existe.',
                ]);
        }


        /* =====================================================
        VALIDAR TIPO DE SEGUIMIENTO
        ===================================================== */

        $tipo =
            '';


        if ($idTipoSeguimiento > 0) {

            $tipoSeguimiento =
                $this->obtenerTipoSeguimientoActivo(
                    $db,
                    $idTipoSeguimiento
                );


            if (!$tipoSeguimiento) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El tipo de seguimiento seleccionado no es válido.',
                    ]);
            }


            $tipo =
                trim(
                    (string) (
                        $tipoSeguimiento['nombre']
                        ?? ''
                    )
                );


            if ($tipo === 'OTRO') {

                if ($tipoOtro === '') {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' =>
                            'Debes especificar el tipo de seguimiento.',
                        ]);
                }


                if (
                    mb_strlen(
                        $tipoOtro
                    ) > 255
                ) {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' =>
                            'El tipo de seguimiento especificado no puede exceder 255 caracteres.',
                        ]);
                }
            } else {

                $tipoOtro =
                    null;
            }
        } else {

            $idTipoActual =
                (int) (
                    $seguimiento['id_tipo_seguimiento']
                    ?? 0
                );


            $tipoLegacy =
                trim(
                    (string) (
                        $seguimiento['tipo']
                        ?? ''
                    )
                );


            if (
                $idTipoActual > 0
                || $tipoLegacy === ''
            ) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El tipo de seguimiento seleccionado no es válido.',
                    ]);
            }


            $idTipoSeguimiento =
                null;


            $tipo =
                $tipoLegacy;


            $tipoOtro =
                $seguimiento['tipo_otro']
                ?? null;
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
                    'message' =>
                    'El seguimiento no está relacionado con un reporte válido.',
                ]);
        }


        /* =====================================================
        CONSULTAR REPORTE PRINCIPAL
        ===================================================== */

        $reporte =
            $db
            ->table('ai_reportes')
            ->select([
                'id_reporte',
                'folio',
                'folio_ip',
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
                    'El reporte relacionado no existe.',
                ]);
        }


        /* =====================================================
        VALIDAR FOLIO IP DUPLICADO
        ===================================================== */

        if ($folioIp !== '') {

            $folioIpExistente =
                $db
                ->table('ai_reportes')
                ->select([
                    'id_reporte',
                    'folio',
                ])
                ->where(
                    'folio_ip',
                    $folioIp
                )
                ->where(
                    'eliminado',
                    0
                )
                ->where(
                    'id_reporte !=',
                    $idReporte
                )
                ->limit(1)
                ->get()
                ->getRowArray();


            if ($folioIpExistente) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'El Folio IP ya se encuentra registrado en otro reporte. Debes ingresar uno diferente para continuar.',
                    ]);
            }
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

                    'id_tipo_seguimiento' =>
                    $idTipoSeguimiento,

                    'tipo_otro' =>
                    $tipoOtro,

                    'folio_ip' =>
                    $folioIp !== ''
                        ? $folioIp
                        : null,

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

                        'id_sancion_seguimiento' =>
                        $idSancionSeguimiento > 0
                            ? $idSancionSeguimiento
                            : (
                                $sancionSeguimiento['id_sancion_seguimiento']
                                ?? null
                            ),

                        'descripcion_otro' =>
                        null,

                        /*
                            * NO modificamos:
                            *
                            * origen
                            * id_seguimiento
                            * es_actual
                            *
                            * porque estamos corrigiendo
                            * el mismo evento histórico.
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

                        'id_sancion_seguimiento' =>
                        $idSancionSeguimiento,

                        'descripcion_otro' =>
                        null,

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
            * seguimiento cronológico, no necesariamente
            * el seguimiento que acabamos de editar.
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


            /* =================================================
            ACTUALIZAR REPORTE PRINCIPAL
            ================================================= */

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

                    'folio_ip' =>
                    $folioIp !== ''
                        ? $folioIp
                        : (
                            $reporte['folio_ip']
                            ?? null
                        ),

                    'estado_actual' =>
                    $estadoActualReporte,

                    'origen_estado' =>
                    'seguimiento',

                    'updated_by' =>
                    $idUsuario,

                    'updated_at' =>
                    date('Y-m-d H:i:s'),

                ]);


            if ($actualizarReporte === false) {

                throw new \RuntimeException(
                    'No fue posible actualizar el reporte.'
                );
            }


            /* =================================================
            HISTORIAL GENERAL
            ================================================= */

            $historialService =
                new HistorialService();


            /* =================================================
            SEGUIMIENTO EDITADO
            ================================================= */

            $historialService
                ->registrarEdicionSeguimiento(
                    $idSeguimiento,
                    $idReporte,
                    $idUsuario
                );


            /* =================================================
            CAMBIO REAL DEL ESTADO DEL REPORTE
            ================================================= */

            $estadoAnteriorReporte =
                trim(
                    (string) (
                        $reporte['estado_actual']
                        ?? ''
                    )
                );


            if (
                $estadoAnteriorReporte !== ''
                && $estadoActualReporte !== ''
                && $estadoAnteriorReporte !== $estadoActualReporte
            ) {

                $historialService
                    ->registrarCambioEstadoReporte(
                        $idReporte,
                        $idUsuario,
                        $estadoAnteriorReporte,
                        $estadoActualReporte
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
                    'No fue posible completar la actualización.'
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
                    'El seguimiento se actualizó correctamente.',

                    'id_reporte' =>
                    $idReporte,

                    'id_seguimiento' =>
                    $idSeguimiento,

                    'folio_ip' =>
                    $folioIp !== ''
                        ? $folioIp
                        : (
                            $reporte['folio_ip']
                            ?? null
                        ),

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
                    'message' =>
                    'No fue posible actualizar el seguimiento.',
                ]);
        }
    }


    private function obtenerTiposSeguimientoActivos(
        $db
    ): array {

        return $db
            ->table(
                'ai_cat_tipos_seguimiento'
            )
            ->select([
                'id_tipo_seguimiento',
                'nombre',
                'orden',
            ])
            ->where(
                'activo',
                1
            )
            ->orderBy(
                'orden',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }


    private function obtenerTipoSeguimientoActivo(
        $db,
        int $idTipoSeguimiento
    ): ?array {

        if ($idTipoSeguimiento <= 0) {

            return null;
        }


        $tipo =
            $db
            ->table(
                'ai_cat_tipos_seguimiento'
            )
            ->select([
                'id_tipo_seguimiento',
                'nombre',
                'orden',
            ])
            ->where(
                'id_tipo_seguimiento',
                $idTipoSeguimiento
            )
            ->where(
                'activo',
                1
            )
            ->get()
            ->getRowArray();


        return $tipo
            ?: null;
    }


    private function formatearTipoSeguimiento(
        string $tipoCatalogo,
        ?string $tipoOtro = null,
        ?string $tipoFallback = null
    ): string {

        $tipoCatalogo =
            trim(
                $tipoCatalogo
            );


        $tipoOtro =
            trim(
                (string) $tipoOtro
            );


        $tipoFallback =
            trim(
                (string) $tipoFallback
            );


        if ($tipoCatalogo !== '') {

            if (
                $tipoCatalogo === 'OTRO'
                && $tipoOtro !== ''
            ) {

                return $tipoOtro;
            }


            return $tipoCatalogo;
        }


        return $tipoFallback !== ''
            ? $tipoFallback
            : 'Seguimiento';
    }


    private function obtenerSancionesSeguimientoActivas(
        $db
    ): array {

        return $db
            ->table(
                'ai_cat_sanciones_seguimiento'
            )
            ->select([
                'id_sancion_seguimiento',
                'nombre',
                'orden',
            ])
            ->where(
                'activo',
                1
            )
            ->orderBy(
                'orden',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }


    private function obtenerSancionSeguimientoActiva(
        $db,
        int $idSancionSeguimiento
    ): ?array {

        if ($idSancionSeguimiento <= 0) {

            return null;
        }


        $sancion =
            $db
            ->table(
                'ai_cat_sanciones_seguimiento'
            )
            ->select([
                'id_sancion_seguimiento',
                'nombre',
                'orden',
            ])
            ->where(
                'id_sancion_seguimiento',
                $idSancionSeguimiento
            )
            ->where(
                'activo',
                1
            )
            ->get()
            ->getRowArray();


        return $sancion
            ?: null;
    }


    private function obtenerNombreSancionSeguimientoPorId(
        $db,
        int $idSancionSeguimiento
    ): string {

        if ($idSancionSeguimiento <= 0) {

            return '';
        }


        $sancion =
            $db
            ->table(
                'ai_cat_sanciones_seguimiento'
            )
            ->select([
                'nombre',
            ])
            ->where(
                'id_sancion_seguimiento',
                $idSancionSeguimiento
            )
            ->get()
            ->getRowArray();


        return trim(
            (string) (
                $sancion['nombre']
                ?? ''
            )
        );
    }


    public function validarFolio()
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


        /* =========================================================
        PARÁMETROS
        ========================================================= */

        $folio =
            trim(
                (string) (
                    $this->request->getGet('folio')
                    ?? ''
                )
            );


        $folioIp =
            trim(
                (string) (
                    $this->request->getGet('folio_ip')
                    ?? ''
                )
            );


        $folioImp =
            trim(
                (string) (
                    $this->request->getGet('folio_imp')
                    ?? ''
                )
            );


        $idReporte =
            (int) (
                $this->request->getGet('id_reporte')
                ?? 0
            );


        /* =========================================================
        VALIDAR QUE EXISTA ALGO QUE REVISAR
        ========================================================= */

        if (
            $folio === ''
            && $folioIp === ''
            && $folioImp === ''
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'No se recibió ningún folio para validar.',
                ]);
        }


        try {

            $db =
                \Config\Database::connect(
                    'datacore'
                );


            /* =====================================================
            RESPUESTA BASE
            ===================================================== */

            $resultado = [

                'success' =>
                true,

                'folio' => [
                    'valor' => $folio,
                    'existe' => false,
                ],

                'folio_ip' => [
                    'valor' => $folioIp,
                    'existe' => false,
                ],

                'folio_imp' => [
                    'valor' => $folioImp,
                    'existe' => false,
                ],

            ];


            /* =====================================================
            FOLIO GENERAL
            ===================================================== */

            if ($folio !== '') {

                $builder =
                    $db
                    ->table('ai_reportes')
                    ->select('id_reporte')
                    ->where(
                        'folio',
                        $folio
                    )
                    ->where(
                        'eliminado',
                        0
                    );


                if ($idReporte > 0) {

                    $builder->where(
                        'id_reporte !=',
                        $idReporte
                    );
                }


                $existeFolio =
                    $builder
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                $resultado['folio']['existe'] =
                    !empty($existeFolio);
            }


            /* =====================================================
            FOLIO IP
            ===================================================== */

            if ($folioIp !== '') {

                $builder =
                    $db
                    ->table('ai_reportes')
                    ->select('id_reporte')
                    ->where(
                        'folio_ip',
                        $folioIp
                    )
                    ->where(
                        'eliminado',
                        0
                    );


                if ($idReporte > 0) {

                    $builder->where(
                        'id_reporte !=',
                        $idReporte
                    );
                }


                $existeFolioIp =
                    $builder
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                $resultado['folio_ip']['existe'] =
                    !empty($existeFolioIp);
            }


            /* =====================================================
            FOLIO IMP
            ===================================================== */

            if ($folioImp !== '') {

                $builder =
                    $db
                    ->table('ai_reportes')
                    ->select('id_reporte')
                    ->where(
                        'folio_imp',
                        $folioImp
                    )
                    ->where(
                        'eliminado',
                        0
                    );


                if ($idReporte > 0) {

                    $builder->where(
                        'id_reporte !=',
                        $idReporte
                    );
                }


                $existeFolioImp =
                    $builder
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                $resultado['folio_imp']['existe'] =
                    !empty($existeFolioImp);
            }


            /* =====================================================
            MENSAJE GENERAL
            ===================================================== */

            if (
                $resultado['folio_ip']['existe']
                || $resultado['folio_imp']['existe']
                || $resultado['folio']['existe']
            ) {

                $resultado['message'] =
                    'Se encontró al menos un folio ya registrado.';
            } else {

                $resultado['message'] =
                    'Los folios están disponibles.';
            }


            return $this->response
                ->setJSON(
                    $resultado
                );
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error validando folios de reporte: {mensaje}',
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
                    'No fue posible validar los folios.',
                ]);
        }
    }

    /* =========================================================
    HISTORIAL GENERAL DEL DASHBOARD
    ========================================================= */

    public function historialDashboard()
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
                    'success' =>
                        false,

                    'message' =>
                        'La sesión no es válida.',
                ]);
        }


        $usuario =
            session()->get(
                'usuario_reportes'
            );


        $esAdmin =
            ($usuario['rol'] ?? null)
            === 'admin';


        $dashboardAutorizado =
            session()->get(
                'reportes_dashboard_autorizado'
            ) === true;


        if (
            !$esAdmin
            && !$dashboardAutorizado
        ) {

            $accept =
                strtolower(
                    (string) $this->request
                    ->getHeaderLine(
                        'Accept'
                    )
                );


            $esPeticionJson =
                str_contains(
                    $accept,
                    'application/json'
                );


            $esAjax =
                strtolower(
                    (string) $this->request
                    ->getHeaderLine(
                        'X-Requested-With'
                    )
                ) === 'xmlhttprequest';


            if (
                !$esPeticionJson
                && !$esAjax
            ) {

                return redirect()
                    ->to(
                        base_url(
                            'asuntos-internos/reportes/dashboard?autorizar=historial'
                        )
                    );
            }


            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'Se requiere autorización administrativa para consultar el historial.',
                ]);
        }


        /* =====================================================
        CONSULTAR HISTORIAL
        ===================================================== */

        try {

            $historialService =
                new HistorialService();


            $historial =
                $historialService
                ->obtenerHistorial();


            /* =================================================
            RESPUESTA
            ================================================= */

            return $this->response
                ->setJSON([
                    'success' =>
                        true,

                    'historial' =>
                        $historial,
                ]);

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error consultando historial general del dashboard: {mensaje}',
                [
                    'mensaje' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' =>
                        false,

                    'message' =>
                        'No fue posible consultar el historial.',
                ]);
        }
    }
    
}
