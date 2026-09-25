<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardFiltrosService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardIndicadoresService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardUbicacionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardAreaService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardClasificacionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardEvolucionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardEstadoService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardDimensionService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardCruceService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardComparativaService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardRankingService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardFelicitacionesService;

class DashboardService
{
    private $db;

    private DashboardFiltrosService $filtrosService;
    private DashboardIndicadoresService $indicadoresService;
    private DashboardUbicacionService $ubicacionService;
    private DashboardAreaService $areaService;
    private DashboardClasificacionService $clasificacionService;
    private DashboardEvolucionService $evolucionService;
    private DashboardEstadoService $estadoService;
    private DashboardDimensionService $dimensionService;
    private DashboardCruceService $cruceService;
    private DashboardComparativaService $comparativaService;
    private DashboardRankingService $rankingService;
    private DashboardFelicitacionesService $felicitacionesService;

    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );


        /* =====================================================
        FILTROS
        ===================================================== */

        $this->filtrosService =
            new DashboardFiltrosService();


        /* =====================================================
        INDICADORES
        ===================================================== */

        $this->indicadoresService =
            new DashboardIndicadoresService(
                $this->filtrosService
            );


        /* =====================================================
        UBICACIÓN
        ===================================================== */

        $this->ubicacionService =
            new DashboardUbicacionService(
                $this->filtrosService
            );


        /* =====================================================
        ÁREA
        ===================================================== */

        $this->areaService =
            new DashboardAreaService(
                $this->filtrosService
            );


        /* =====================================================
        CLASIFICACIÓN
        ===================================================== */

        $this->clasificacionService =
            new DashboardClasificacionService(
                $this->filtrosService
            );


        /* =====================================================
        EVOLUCIÓN TEMPORAL
        ===================================================== */

        $this->evolucionService =
            new DashboardEvolucionService(
                $this->filtrosService
            );


        /* =====================================================
        ESTADO DE LAS QUEJAS
        ===================================================== */

        $this->estadoService =
            new DashboardEstadoService(
                $this->filtrosService
            );


        /* =====================================================
        DIMENSIÓN
        ÁREA / UNIDAD
        ===================================================== */

        $this->dimensionService =
            new DashboardDimensionService(
                $this->filtrosService
            );

        $this->cruceService =
            new DashboardCruceService(
                $this->filtrosService
            );

        $this->comparativaService =
            new DashboardComparativaService(
                $this->filtrosService,
                $this->indicadoresService
            );

        $this->rankingService =
            new DashboardRankingService(
                $this->filtrosService
            );

        $this->felicitacionesService =
            new DashboardFelicitacionesService(
                $this->filtrosService
            );
    }

    /* =========================================================
    FILTROS DEL DASHBOARD
    ========================================================= */

    /**
     * Define los filtros que utilizarán todas las consultas
     * del Dashboard.
     */
    public function establecerFiltros(
        array $filtros
    ): void {

        $this->filtrosService
            ->establecerFiltros(
                $filtros
            );
    }


    /* =========================================================
       OPCIONES DE FILTROS
    ========================================================= */

    public function obtenerOpcionesFiltros(): array
    {
        return $this->filtrosService
            ->obtenerOpcionesFiltros();
    }


    /* =========================================================
    APLICAR FILTROS COMUNES A AI_REPORTES
    ========================================================= */

    private function aplicarFiltrosReportes(
        $builder,
        string $alias = ''
    ) {
        return $this->filtrosService
            ->aplicarFiltrosReportes(
                $builder,
                $alias
            );
    }


    /* =========================================================
    INDICADORES GENERALES
    ========================================================= */

    public function obtenerIndicadores(): array
    {
        $tipo =
            $this->obtenerTipoActivo();


        /* =====================================================
        SOLO FELICITACIONES
        ===================================================== */

        if (
            $tipo === 'FELICITACION'
        ) {

            return $this->felicitacionesService
                ->obtenerIndicadores();
        }


        /* =====================================================
        TODOS / QUEJAS

        "Todos" utiliza únicamente ai_reportes.
        Felicitaciones se maneja por separado.
        ===================================================== */

        return $this->indicadoresService
            ->obtenerIndicadores();
    }

    /* =========================================================
    EVOLUCIÓN TEMPORAL
    ========================================================= */

    public function obtenerEvolucionTemporal(): array
    {

        return $this->evolucionService
            ->obtenerEvolucionTemporal();
    }


    /* =========================================================
    ESTADO DE LAS QUEJAS
    ========================================================= */

    public function obtenerEstadosQuejas(): array
    {
        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return [

                'estados' =>
                [],

                'totales' =>
                [],

                'porcentajes' =>
                [],

                'total' =>
                0,

            ];
        }


        return $this->estadoService
            ->obtenerEstadosQuejas();
    }


    /* =========================================================
    DISTRIBUCIÓN POR SECTOR
    ========================================================= */

    public function obtenerQuejasPorSector(): array
    {
        $tipo =
            $this->obtenerTipoActivo();


        if (
            $tipo === 'FELICITACION'
        ) {

            return $this->felicitacionesService
                ->obtenerPorSector();
        }


        return $this->ubicacionService
            ->obtenerQuejasPorSector();
    }


    /* =========================================================
       QUEJAS POR SECTORES Y TURNOS
    ========================================================= */

    public function obtenerSectoresTurnos(): array
    {
        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return [

                'sectores' =>
                    [],

                'turnos' =>
                    [],

            ];
        }


        return $this->ubicacionService
            ->obtenerSectoresTurnos();
    }


    /* =========================================================
    COMPARATIVAS
    ========================================================= */

    public function obtenerComparativa(): array
    {
        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return [

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
        }


        return $this->comparativaService
            ->obtenerComparativa();
    }

    /* =========================================================
    ANÁLISIS CRUZADO
    ========================================================= */

    public function obtenerCruce(
        string $principal = 'sector',
        string $secundaria = 'turno'
    ): array {

        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return [

                'principal' =>
                $principal,

                'secundaria' =>
                $secundaria,

                'categorias' =>
                [],

                'series' =>
                [],

                'total' =>
                0,

                'opciones_principal' =>
                [],

                'opciones_secundaria' =>
                [],

            ];
        }


        return $this->cruceService
            ->obtenerCruce(
                $principal,
                $secundaria
            );
    }


    /* =========================================================
    ÁREA / GRUPO / UNIDAD
    ========================================================= */

    public function obtenerDimension(
        string $dimension = 'area'
    ): array {

        $tipo =
            $this->obtenerTipoActivo();


        if (
            $tipo === 'FELICITACION'
        ) {

            return $this->felicitacionesService
                ->obtenerDimension(
                    $dimension
                );
        }


        return $this->dimensionService
            ->obtenerDimension(
                $dimension
            );
    }

    /* =========================================================
    RANKINGS DINÁMICOS TOP 5
    ========================================================= */

    public function obtenerRanking(
        string $tipo = 'sector'
    ): array {

        return $this->rankingService
            ->obtenerRanking(
                $tipo
            );
    }

    /* =========================================================
    FELICITACIONES - INDICADORES
    ========================================================= */

    public function obtenerIndicadoresFelicitaciones(): array
    {
        return $this->felicitacionesService
            ->obtenerIndicadores();
    }


    /* =========================================================
    FELICITACIONES - SECTOR
    ========================================================= */

    public function obtenerFelicitacionesPorSector(): array
    {
        return $this->felicitacionesService
            ->obtenerPorSector();
    }


    /* =========================================================
    FELICITACIONES - ZONA
    ========================================================= */

    public function obtenerFelicitacionesPorZona(): array
    {
        return $this->felicitacionesService
            ->obtenerPorZona();
    }


    /* =========================================================
    FELICITACIONES - TURNO
    ========================================================= */

    public function obtenerFelicitacionesPorTurno(): array
    {
        return $this->felicitacionesService
            ->obtenerPorTurno();
    }


    /* =========================================================
    FELICITACIONES - ÁREA / UNIDAD
    ========================================================= */

    public function obtenerDimensionFelicitaciones(
        string $dimension = 'area'
    ): array {

        return $this->felicitacionesService
            ->obtenerDimension(
                $dimension
            );
    }

    /* =========================================================
    DISTRIBUCIÓN POR ZONA
    ========================================================= */

    public function obtenerQuejasPorZona(): array
    {
        $tipo =
            $this->obtenerTipoActivo();


        if (
            $tipo === 'FELICITACION'
        ) {

            return $this->felicitacionesService
                ->obtenerPorZona();
        }


        return $this->ubicacionService
            ->obtenerQuejasPorZona();
    }

    /* =========================================================
    SANCIONES DISCIPLINARIAS

    Se contabiliza únicamente la sanción ACTUAL
    de cada reporte.

    No se incluye el historial de sanciones.

    Fuente:
    ai_reporte_sanciones

    Reglas:
    - es_actual = 1
    - eliminado = 0
    - se cuentan reportes / sanciones actuales
    - se respetan todos los filtros del Dashboard
    ========================================================= */

    public function obtenerSanciones(): array
    {
        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return [

                'tipos' =>
                [],

                'totales' =>
                [],

                'total' =>
                0,

            ];
        }

        /* =====================================================
        CATEGORÍAS
        ===================================================== */

        $conteos = [
            'Arresto' => 0,
            'Amonestación' => 0,
            'Otro' => 0,
        ];


        /* =====================================================
        CONSULTAR REPORTES + SANCIÓN ACTUAL
        ===================================================== */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                's.id_sancion',
                's.tipo',
            ])
            ->join(
                'ai_reporte_sanciones s',
                's.id_reporte = r.id_reporte',
                'inner'
            )
            ->where(
                's.es_actual',
                1
            )
            ->where(
                's.eliminado',
                0
            );


        /* =====================================================
        FILTROS DEL DASHBOARD

        De esta forma la gráfica de sanciones responde
        exactamente a los mismos filtros que las demás.
        ===================================================== */

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );


        /* =====================================================
        CONSULTAR
        ===================================================== */

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
        EVITAR DUPLICADOS

        Aunque la regla de negocio establece una sola
        sanción actual por reporte, protegemos el Dashboard
        ante posibles inconsistencias históricas.

        La unidad de conteo será:

        reporte + tipo de sanción
        ===================================================== */

        $reportesContados = [];


        /* =====================================================
        CONTABILIZAR
        ===================================================== */

        foreach ($registros as $registro) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            if ($idReporte <= 0) {
                continue;
            }


            /* =================================================
            NORMALIZAR TIPO
            ================================================= */

            $tipoOriginal =
                trim(
                    (string) (
                        $registro['tipo']
                        ?? ''
                    )
                );


            if ($tipoOriginal === '') {
                continue;
            }


            $tipoNormalizado =
                mb_strtoupper(
                    $tipoOriginal,
                    'UTF-8'
                );


            /* =================================================
            CLASIFICAR
            ================================================= */

            $tipo =
                match ($tipoNormalizado) {

                    'ARRESTO' =>
                    'Arresto',

                    'AMONESTACIÓN',
                    'AMONESTACION' =>
                    'Amonestación',

                    'OTRO' =>
                    'Otro',

                    default =>
                    null,
                };


            /*
         * No inventamos categorías para valores
         * que no pertenezcan al catálogo oficial.
         */

            if (
                $tipo === null
                || !array_key_exists(
                    $tipo,
                    $conteos
                )
            ) {
                continue;
            }


            /* =================================================
            EVITAR DUPLICADOS
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $tipo;


            if (
                isset(
                    $reportesContados[$clave]
                )
            ) {
                continue;
            }


            $reportesContados[$clave] =
                true;


            /* =================================================
            SUMAR
            ================================================= */

            $conteos[$tipo]++;
        }


        /* =====================================================
        RESPUESTA
        ===================================================== */

        return [
            'tipos' => [
                'Arresto',
                'Amonestación',
                'Otro',
            ],

            'totales' => [
                $conteos['Arresto'],
                $conteos['Amonestación'],
                $conteos['Otro'],
            ],

            'total' =>
            array_sum(
                $conteos
            ),
        ];
    }

    /* =========================================================
    QUEJAS POR ÁREA
    ========================================================= */

    public function obtenerQuejasPorArea(): array
    {
        return $this->areaService
            ->obtenerQuejasPorArea();
    }

    /* =========================================================
    DISTRIBUCIÓN POR TURNO
    ========================================================= */

    public function obtenerQuejasPorTurno(): array
    {
        $tipo =
            $this->obtenerTipoActivo();


        if (
            $tipo === 'FELICITACION'
        ) {

            return $this->felicitacionesService
                ->obtenerPorTurno();
        }


        return $this->ubicacionService
            ->obtenerQuejasPorTurno();
    }


    /* =========================================================
    CATÁLOGO GENERAL
    QUEJAS POR CLASIFICACIÓN
    ========================================================= */

    public function obtenerClasificaciones(): array
    {
        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return [

                'clasificaciones' =>
                    [],

                'totales' =>
                    [],

                'total' =>
                    0,

            ];
        }


        return $this->clasificacionService
            ->obtenerClasificaciones();
    }

    /* =========================================================
    OBTENER TIPO ACTIVO
    ========================================================= */

    private function obtenerTipoActivo(): string
    {
        $filtros =
            $this->filtrosService
            ->obtenerFiltros();


        $tipo =
            strtoupper(
                trim(
                    (string) (
                        $filtros['tipo']
                        ?? ''
                    )
                )
            );


        return match ($tipo) {

            'QUEJA' =>
            'QUEJA',

            'FELICITACION' =>
            'FELICITACION',

            default =>
            'TODOS',
        };
    }
}
