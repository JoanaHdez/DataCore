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
        return $this->estadoService
            ->obtenerEstadosQuejas();
    }


    /* =========================================================
    QUEJAS POR SECTOR
    ========================================================= */

    public function obtenerQuejasPorSector(): array
    {
        return $this->ubicacionService
            ->obtenerQuejasPorSector();
    }


    /* =========================================================
       QUEJAS POR SECTORES Y TURNOS
    ========================================================= */

    public function obtenerSectoresTurnos(): array
    {
        return $this->ubicacionService
            ->obtenerSectoresTurnos();
    }

    /* =========================================================
    ANÁLISIS CRUZADO
    ========================================================= */

    public function obtenerCruce(
        string $principal = 'sector',
        string $secundaria = 'turno'
    ): array {

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

        return $this->dimensionService
            ->obtenerDimension(
                $dimension
            );
    }


    /* =========================================================
    QUEJAS POR ZONA

    La zona NO se almacena en ai_reportes.

    Se obtiene a partir del sector institucional registrado
    históricamente en:

    ai_reporte_personal.area_snapshot

    Mapeo institucional:

    SECTOR 01 - 03 → Zona Norte
    SECTOR 04 - 07 → Zona Poniente
    SECTOR 08 - 10 → Zona Centro
    SECTOR 11 - 15 → Zona Oriente

    IMPORTANTE:
    Se cuentan REPORTES / QUEJAS, no personas.
    ========================================================= */

    public function obtenerQuejasPorZona(): array
    {
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
    QUEJAS POR TURNO
    ========================================================= */

    public function obtenerQuejasPorTurno(): array
    {
        return $this->ubicacionService
            ->obtenerQuejasPorTurno();
    }


    /* =========================================================
    CATÁLOGO GENERAL
    QUEJAS POR CLASIFICACIÓN
    ========================================================= */

    public function obtenerClasificaciones(): array
    {
        return $this->clasificacionService
            ->obtenerClasificaciones();
    }
}
