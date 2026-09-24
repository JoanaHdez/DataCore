<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardFiltrosService;
use App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard\DashboardIndicadoresService;

class DashboardService
{
    private $db;

    private DashboardFiltrosService $filtrosService;
    private DashboardIndicadoresService $indicadoresService;

    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );

        $this->filtrosService =
            new DashboardFiltrosService();

        $this->indicadoresService =
            new DashboardIndicadoresService(
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
       QUEJAS POR SECTORES Y TURNOS
    ========================================================= */

    public function obtenerSectoresTurnos(): array
    {
        /* =====================================================
           SECTORES INSTITUCIONALES
        ===================================================== */

        $sectores = [];


        for (
            $numero = 1;
            $numero <= 15;
            $numero++
        ) {

            $sectores[] =
                'SECTOR ' . $numero;
        }


        /* =====================================================
           TURNOS DEL DASHBOARD
        ===================================================== */

        $turnos = [
            'Primer turno',
            'Segundo turno',
            'Tercer turno',
            'Alfa',
            'Beta',
            'Diario',
            'No refiere ni fecha ni horario',
        ];


        /* =====================================================
           INICIALIZAR CONTEOS
        ===================================================== */

        $conteos = [];


        foreach (
            $turnos
            as $turno
        ) {

            $conteos[$turno] =
                array_fill(
                    0,
                    count($sectores),
                    0
                );
        }


        /* =====================================================
           CONSULTAR REPORTES + PERSONAL
        ===================================================== */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.area_snapshot AS area',
                'p.turno_snapshot AS turno',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'p.area_snapshot',
                'p.turno_snapshot',
            ])
            ->get()
            ->getResultArray();


        /* =====================================================
           EVITAR DUPLICADOS

           Una misma queja puede tener varias personas.

           Si varias pertenecen al mismo sector
           y misma familia de turno, contamos
           la queja una sola vez.
        ===================================================== */

        $combinacionesContadas =
            [];


        /* =====================================================
           CONTABILIZAR
        ===================================================== */

        foreach (
            $registros
            as $registro
        ) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            if ($idReporte <= 0) {
                continue;
            }


            /* =================================================
               OBTENER SECTOR DESDE EL ÁREA
            ================================================= */

            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area']
                        ?? ''
                    )
                );


            /*
             * Si la persona no pertenece a uno
             * de los 15 sectores, esta queja
             * no forma parte de esta gráfica.
             */

            if ($sector === null) {
                continue;
            }


            /* =================================================
               CLASIFICAR TURNO
            ================================================= */

            $turno =
                $this->clasificarTurnoDashboard(
                    (string) (
                        $registro['turno']
                        ?? ''
                    )
                );


            /*
             * Si el turno no pertenece a una
             * categoría contemplada, no lo
             * clasificamos arbitrariamente.
             */

            if ($turno === null) {
                continue;
            }


            if (
                !isset(
                    $conteos[$turno]
                )
            ) {
                continue;
            }


            /* =================================================
               BUSCAR ÍNDICE DEL SECTOR
            ================================================= */

            $indiceSector =
                array_search(
                    $sector,
                    $sectores,
                    true
                );


            if (
                $indiceSector === false
            ) {
                continue;
            }


            /* =================================================
               EVITAR CONTAR DOS VECES

               reporte + sector + turno
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $sector
                . '|'
                . $turno;


            if (
                isset(
                    $combinacionesContadas[$clave]
                )
            ) {
                continue;
            }


            $combinacionesContadas[$clave] = true;


            /* =================================================
               SUMAR
            ================================================= */

            $conteos[$turno][$indiceSector]++;
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [
            'sectores' =>
            $sectores,

            'turnos' =>
            $conteos,
        ];
    }


    /* =========================================================
       OBTENER SECTOR DESDE AREA_SNAPSHOT

       Ejemplos:

       SECTOR 01 CAMPESTRE
           → SECTOR 1

       SECTOR 02 ARAGON
           → SECTOR 2

       SECTOR 15 REFORMA
           → SECTOR 15
    ========================================================= */

    private function obtenerSectorDesdeArea(
        string $area
    ): ?string {

        $area =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    mb_strtoupper(
                        $area,
                        'UTF-8'
                    )
                )
                    ?? ''
            );


        if ($area === '') {
            return null;
        }


        if (
            !preg_match(
                '/^SECTOR\s+0*([0-9]+)\b/u',
                $area,
                $coincidencias
            )
        ) {

            return null;
        }


        $numero =
            (int) (
                $coincidencias[1]
                ?? 0
            );


        if (
            $numero < 1
            || $numero > 15
        ) {

            return null;
        }


        return (
            'SECTOR '
            . $numero
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
        /* =====================================================
        ZONAS INSTITUCIONALES
        ===================================================== */

        $conteos = [
            'Zona Norte' => 0,
            'Zona Poniente' => 0,
            'Zona Centro' => 0,
            'Zona Oriente' => 0,
        ];


        /* =====================================================
        CONSULTAR REPORTES + PERSONAL
        ===================================================== */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.area_snapshot AS area',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );


        /*
     * Aplicamos todos los filtros comunes que ya tenga
     * activos el Dashboard.
     */

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );


        /*
     * Una misma queja puede tener varias personas.
     *
     * Agrupamos inicialmente por reporte + área para reducir
     * duplicados antes de realizar la clasificación.
     */

        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'p.area_snapshot',
            ])
            ->get()
            ->getResultArray();


        /* =====================================================
        EVITAR DUPLICADOS

        Si una queja tiene:

        Persona A → SECTOR 01
        Persona B → SECTOR 01
        Persona C → SECTOR 02

        Todos pertenecen a Zona Norte.

        La queja cuenta solamente UNA VEZ para Zona Norte.
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
            OBTENER SECTOR
            ================================================= */

            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area']
                        ?? ''
                    )
                );


            /*
         * Las áreas que no sean uno de los sectores 1-15
         * no pertenecen a ninguna de estas cuatro zonas.
         */

            if ($sector === null) {
                continue;
            }


            /* =================================================
            OBTENER ZONA
            ================================================= */

            $zona =
                $this->obtenerZonaDesdeSector(
                    $sector
                );


            if (
                $zona === null
                || !array_key_exists(
                    $zona,
                    $conteos
                )
            ) {
                continue;
            }


            /* =================================================
            EVITAR CONTAR DOS VECES

            reporte + zona
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $zona;


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
            SUMAR QUEJA
            ================================================= */

            $conteos[$zona]++;
        }


        /* =====================================================
        RESPUESTA
        ===================================================== */

        return [
            'zonas' => [
                'Zona Norte',
                'Zona Poniente',
                'Zona Centro',
                'Zona Oriente',
            ],

            'totales' => [
                $conteos['Zona Norte'],
                $conteos['Zona Poniente'],
                $conteos['Zona Centro'],
                $conteos['Zona Oriente'],
            ],

            'total' =>
            array_sum(
                $conteos
            ),
        ];
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
       CLASIFICAR TURNO

       IMPORTANTE:
       La plantilla tiene muchas variantes.

       Las agrupamos por familia.
    ========================================================= */

    /* =========================================================
    OBTENER ZONA DESDE SECTOR
    ========================================================= */

    private function obtenerZonaDesdeSector(
        string $sector
    ): ?string {

        /*
     * obtenerSectorDesdeArea() nos entrega valores como:
     *
     * SECTOR 1
     * SECTOR 2
     * ...
     * SECTOR 15
     */

        if (
            !preg_match(
                '/^SECTOR\s+([0-9]+)$/u',
                trim($sector),
                $coincidencias
            )
        ) {

            return null;
        }


        $numero =
            (int) (
                $coincidencias[1]
                ?? 0
            );


        /* =====================================================
        ZONA NORTE
        Sectores 1 - 3
        ===================================================== */

        if (
            $numero >= 1
            && $numero <= 3
        ) {

            return 'Zona Norte';
        }


        /* =====================================================
        ZONA PONIENTE
        Sectores 4 - 7
        ===================================================== */

        if (
            $numero >= 4
            && $numero <= 7
        ) {

            return 'Zona Poniente';
        }


        /* =====================================================
        ZONA CENTRO
        Sectores 8 - 10
        ===================================================== */

        if (
            $numero >= 8
            && $numero <= 10
        ) {

            return 'Zona Centro';
        }


        /* =====================================================
        ZONA ORIENTE
        Sectores 11 - 15
        ===================================================== */

        if (
            $numero >= 11
            && $numero <= 15
        ) {

            return 'Zona Oriente';
        }


        return null;
    }


    private function clasificarTurnoDashboard(
        string $turno
    ): ?string {

        $turno =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    mb_strtoupper(
                        $turno,
                        'UTF-8'
                    )
                )
                    ?? ''
            );


        /* =====================================================
           SIN TURNO
        ===================================================== */

        if ($turno === '') {

            return (
                'No refiere ni fecha ni horario'
            );
        }


        /* =====================================================
           PRIMER TURNO
        ===================================================== */

        if (
            preg_match(
                '/\bPRIMERO\b/u',
                $turno
            )
            || preg_match(
                '/\bPRIMER\b/u',
                $turno
            )
        ) {

            return 'Primer turno';
        }


        /* =====================================================
           SEGUNDO TURNO
        ===================================================== */

        if (
            preg_match(
                '/\bSEGUNDO\b/u',
                $turno
            )
        ) {

            return 'Segundo turno';
        }


        /* =====================================================
           TERCER TURNO
        ===================================================== */

        if (
            preg_match(
                '/\bTERCERO\b/u',
                $turno
            )
            || preg_match(
                '/\bTERCER\b/u',
                $turno
            )
        ) {

            return 'Tercer turno';
        }


        /* =====================================================
           ALFA
        ===================================================== */

        if (
            preg_match(
                '/\bALFA\b/u',
                $turno
            )
        ) {

            return 'Alfa';
        }


        /* =====================================================
           BETA
        ===================================================== */

        if (
            preg_match(
                '/\bBETA\b/u',
                $turno
            )
        ) {

            return 'Beta';
        }


        /* =====================================================
           DIARIO
        ===================================================== */

        if (
            preg_match(
                '/\bDIARIO\b/u',
                $turno
            )
        ) {

            return 'Diario';
        }


        /* =====================================================
           NO REFIERE
        ===================================================== */

        if (
            str_contains(
                $turno,
                'NO REFIERE'
            )
            || str_contains(
                $turno,
                'SIN TURNO'
            )
        ) {

            return (
                'No refiere ni fecha ni horario'
            );
        }


        /* =====================================================
           OTROS

           DELTA
           GAMA
           UNICO
           24 HRS. POR 48 HRS.
           etc.

           No se asignan arbitrariamente.
        ===================================================== */

        return null;
    }

    /* =========================================================
    QUEJAS POR ÁREA
    ========================================================= */

    public function obtenerQuejasPorArea(): array
    {
        /*
     * Contamos reportes, no personas.
     *
     * Si una queja tiene varias personas pertenecientes
     * a la misma área, solamente cuenta una vez para
     * dicha área.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.area_snapshot AS area',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            )
            ->where(
                'p.area_snapshot IS NOT NULL',
                null,
                false
            )
            ->where(
                'p.area_snapshot !=',
                ''
            );

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'p.area_snapshot',
            ])
            ->get()
            ->getResultArray();


        /* =====================================================
        AGRUPAR
        ===================================================== */

        $conteos = [];


        foreach ($registros as $registro) {

            $area =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        mb_strtoupper(
                            (string) (
                                $registro['area']
                                ?? ''
                            ),
                            'UTF-8'
                        )
                    )
                        ?? ''
                );


            if ($area === '') {
                continue;
            }


            /*
         * Los sectores conservan también su nombre
         * institucional completo:
         *
         * SECTOR 01 CAMPESTRE
         * SECTOR 02 ARAGON
         * etc.
         *
         * Aquí NO los reducimos a "SECTOR 1",
         * porque esta gráfica analiza áreas.
         */

            if (
                !isset(
                    $conteos[$area]
                )
            ) {

                $conteos[$area] = 0;
            }


            $conteos[$area]++;
        }


        /* =====================================================
        ORDENAR DE MAYOR A MENOR
        ===================================================== */

        arsort(
            $conteos,
            SORT_NUMERIC
        );


        /* =====================================================
        FORMATO PARA CHART.JS
        ===================================================== */

        $areas = [];
        $totales = [];


        foreach (
            $conteos
            as $area => $total
        ) {

            $areas[] =
                $area;


            $totales[] =
                (int) $total;
        }


        return [
            'areas' =>
            $areas,

            'totales' =>
            $totales,
        ];
    }

    /* =========================================================
    QUEJAS POR TURNO
    ========================================================= */

    public function obtenerQuejasPorTurno(): array
    {
        /*
     * Esta gráfica cuenta QUEJAS / REPORTES,
     * no cantidad de personas.
     *
     * A diferencia de "Sectores y turnos",
     * aquí NO importa el área del personal.
     * Analizamos todos los reportes vigentes
     * que tengan personal relacionado.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.turno_snapshot AS turno',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
        CATEGORÍAS DEL DASHBOARD
        ===================================================== */

        $conteos = [
            'Primer turno' => 0,
            'Segundo turno' => 0,
            'Tercer turno' => 0,
            'Alfa' => 0,
            'Beta' => 0,
            'Diario' => 0,
            'No refiere ni fecha ni horario' => 0,
        ];


        /*
     * Nos permite evitar que una misma queja
     * se cuente varias veces cuando tiene varios
     * elementos pertenecientes al mismo turno.
     *
     * Ejemplo:
     *
     * QJ-001
     * - Oficial A → SEGUNDO
     * - Oficial B → SEGUNDO
     * - Oficial C → SEGUNDO
     *
     * Resultado:
     * Segundo turno +1
     *
     * No +3.
     */

        $reportesContados = [];


        foreach ($registros as $registro) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            if ($idReporte <= 0) {
                continue;
            }


            $turno =
                $this->clasificarTurnoDashboard(
                    (string) (
                        $registro['turno']
                        ?? ''
                    )
                );


            /*
         * Algunos turnos reales todavía no tienen
         * una regla institucional definida:
         *
         * DELTA
         * GAMA
         * UNICO
         * 24x48
         * etc.
         *
         * No los asignamos arbitrariamente.
         */

            if (
                $turno === null
                || !array_key_exists(
                    $turno,
                    $conteos
                )
            ) {
                continue;
            }


            $clave =
                $idReporte
                . '|'
                . $turno;


            if (
                isset(
                    $reportesContados[$clave]
                )
            ) {
                continue;
            }


            $reportesContados[$clave] =
                true;


            $conteos[$turno]++;
        }


        /* =====================================================
        RESPUESTA
        ===================================================== */

        return [
            'turnos' => [
                'Primer turno',
                'Segundo turno',
                'Tercer turno',
                'Alfa',
                'Beta',
                'Diario',
                'No refiere ni fecha ni horario',
            ],

            'totales' => [
                $conteos['Primer turno'],
                $conteos['Segundo turno'],
                $conteos['Tercer turno'],
                $conteos['Alfa'],
                $conteos['Beta'],
                $conteos['Diario'],
                $conteos['No refiere ni fecha ni horario'],
            ],

            'total' =>
            array_sum(
                $conteos
            ),
        ];
    }

    /* =========================================================
    RESOLUCIÓN GENERAL
    ========================================================= */

    public function obtenerResoluciones(): array
    {
        /*
     * Contamos reportes vigentes agrupados por
     * la resolución registrada en ai_reportes.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.resolucion',
            ]);

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->get()
            ->getResultArray();

        /* =====================================================
        AGRUPAR RESOLUCIONES
        ===================================================== */

        $conteos = [];


        foreach ($registros as $registro) {

            $resolucion =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        (string) (
                            $registro['resolucion']
                            ?? ''
                        )
                    )
                        ?? ''
                );


            /*
         * Si todavía no existe una resolución,
         * no inventamos una categoría.
         *
         * Los reportes pendientes o en proceso
         * pueden legítimamente no tenerla todavía.
         */

            if ($resolucion === '') {
                continue;
            }


            /*
         * Normalizamos únicamente para evitar
         * duplicados provocados por diferencias
         * de mayúsculas/minúsculas.
         */

            $clave =
                mb_strtoupper(
                    $resolucion,
                    'UTF-8'
                );


            if (
                !isset(
                    $conteos[$clave]
                )
            ) {

                $conteos[$clave] = [
                    'nombre' =>
                    $resolucion,

                    'total' =>
                    0,
                ];
            }


            $conteos[$clave]['total']++;
        }


        /* =====================================================
        ORDENAR DE MAYOR A MENOR
        ===================================================== */

        uasort(
            $conteos,
            static function (
                array $a,
                array $b
            ): int {

                return (
                    $b['total']
                    <=> $a['total']
                );
            }
        );


        /* =====================================================
        PREPARAR RESPUESTA
        ===================================================== */

        $resoluciones = [];
        $totales = [];


        foreach ($conteos as $dato) {

            $resoluciones[] =
                $dato['nombre'];


            $totales[] =
                (int) $dato['total'];
        }


        return [
            'resoluciones' =>
            $resoluciones,

            'totales' =>
            $totales,

            'total' =>
            array_sum(
                $totales
            ),
        ];
    }

    /* =========================================================
    CATÁLOGO GENERAL
    QUEJAS POR CLASIFICACIÓN
    ========================================================= */

    public function obtenerClasificaciones(): array
    {
        /*
     * Contamos reportes vigentes agrupados por
     * la clasificación registrada en ai_reportes.
     *
     * No utilizamos las categorías temporales del Excel:
     * el Dashboard mostrará las clasificaciones que
     * realmente existan en la base de datos.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.clasificacion',
            ]);

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
        AGRUPAR CLASIFICACIONES
        ===================================================== */

        $conteos = [];


        foreach ($registros as $registro) {

            $clasificacion =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        (string) (
                            $registro['clasificacion']
                            ?? ''
                        )
                    )
                        ?? ''
                );


            /*
         * Una clasificación vacía no representa
         * una categoría real.
         */

            if ($clasificacion === '') {
                continue;
            }


            /*
         * Utilizamos una clave normalizada para evitar
         * separar valores únicamente por diferencias
         * entre mayúsculas y minúsculas.
         *
         * Ejemplo:
         *
         * Extorsión
         * EXTORSIÓN
         * extorsión
         *
         * se contabilizan como una sola clasificación.
         */

            $clave =
                mb_strtoupper(
                    $clasificacion,
                    'UTF-8'
                );


            if (
                !isset(
                    $conteos[$clave]
                )
            ) {

                $conteos[$clave] = [
                    'nombre' =>
                    $clasificacion,

                    'total' =>
                    0,
                ];
            }


            $conteos[$clave]['total']++;
        }


        /* =====================================================
        ORDENAR DE MAYOR A MENOR
        ===================================================== */

        uasort(
            $conteos,
            static function (
                array $a,
                array $b
            ): int {

                return (
                    $b['total']
                    <=> $a['total']
                );
            }
        );


        /* =====================================================
        PREPARAR RESPUESTA
        ===================================================== */

        $clasificaciones = [];
        $totales = [];


        foreach ($conteos as $dato) {

            $clasificaciones[] =
                $dato['nombre'];


            $totales[] =
                (int) $dato['total'];
        }


        return [
            'clasificaciones' =>
            $clasificaciones,

            'totales' =>
            $totales,

            'total' =>
            array_sum(
                $totales
            ),
        ];
    }

    /* =========================================================
    REPORTES RECIENTES
    ========================================================= */

    public function obtenerReportesRecientes(
        int $limite = 6
    ): array {

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.folio',
                'r.fecha_registro',
                'r.expediente',
                'r.clasificacion',
                'r.estado_actual',
            ]);

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->orderBy(
                'r.fecha_registro',
                'DESC'
            )
            ->orderBy(
                'r.id_reporte',
                'DESC'
            )
            ->limit(
                $limite
            )
            ->get()
            ->getResultArray();


        $reportes = [];


        foreach ($registros as $registro) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            /* =====================================================
            ÁREA DEL PERSONAL RELACIONADO
            ===================================================== */

            $area =
                '—';


            if ($idReporte > 0) {

                $personal =
                    $this->db
                    ->table('ai_reporte_personal')
                    ->select(
                        'area_snapshot'
                    )
                    ->where(
                        'id_reporte',
                        $idReporte
                    )
                    ->where(
                        'area_snapshot IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        'area_snapshot !=',
                        ''
                    )
                    ->orderBy(
                        'id_reporte_personal',
                        'ASC'
                    )
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                if (
                    !empty($personal['area_snapshot'])
                ) {

                    $area =
                        trim(
                            (string) $personal['area_snapshot']
                        );
                }
            }


            /* =====================================================
            FECHA
            ===================================================== */

            $fecha =
                $registro['fecha_registro']
                ?? null;


            $fechaFormateada =
                '—';


            if (!empty($fecha)) {

                $timestamp =
                    strtotime(
                        (string) $fecha
                    );


                if ($timestamp !== false) {

                    $fechaFormateada =
                        date(
                            'd/m/Y',
                            $timestamp
                        );
                }
            }


            /* =====================================================
            RESPUESTA
            ===================================================== */

            $reportes[] = [

                'id_reporte' =>
                $idReporte,

                'folio' =>
                trim(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                ),

                'fecha' =>
                $fechaFormateada,

                'expediente' =>
                trim(
                    (string) (
                        $registro['expediente']
                        ?? ''
                    )
                ),

                'clasificacion' =>
                trim(
                    (string) (
                        $registro['clasificacion']
                        ?? ''
                    )
                ),

                'area' =>
                $area,

                'estado' =>
                trim(
                    (string) (
                        $registro['estado_actual']
                        ?? ''
                    )
                ),
            ];
        }


        return $reportes;
    }
}
