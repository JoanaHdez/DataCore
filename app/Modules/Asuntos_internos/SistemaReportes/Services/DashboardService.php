<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

class DashboardService
{
    private $db;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        $this->db =
            \Config\Database::connect(
                'datacore'
            );
    }


    /* =========================================================
       INDICADORES GENERALES
    ========================================================= */

    public function obtenerIndicadores(): array
    {
        $resultado =
            $this->db
            ->table('ai_reportes')
            ->select([
                'COUNT(*) AS total',

                "SUM(
                    CASE
                        WHEN estado_actual = 'Pendiente'
                        THEN 1
                        ELSE 0
                    END
                ) AS pendientes",

                "SUM(
                    CASE
                        WHEN estado_actual = 'En proceso'
                        THEN 1
                        ELSE 0
                    END
                ) AS en_proceso",

                "SUM(
                    CASE
                        WHEN estado_actual = 'Finalizado'
                        THEN 1
                        ELSE 0
                    END
                ) AS finalizados",
            ], false)
            ->where(
                'eliminado',
                0
            )
            ->get()
            ->getRowArray();


        return [
            'total' =>
                (int) (
                    $resultado['total']
                    ?? 0
                ),

            'pendientes' =>
                (int) (
                    $resultado['pendientes']
                    ?? 0
                ),

            'en_proceso' =>
                (int) (
                    $resultado['en_proceso']
                    ?? 0
                ),

            'finalizados' =>
                (int) (
                    $resultado['finalizados']
                    ?? 0
                ),
        ];
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

        $registros =
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
            )
            ->where(
                'r.eliminado',
                0
            )
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
                    $combinacionesContadas[
                        $clave
                    ]
                )
            ) {
                continue;
            }


            $combinacionesContadas[
                $clave
            ] = true;


            /* =================================================
               SUMAR
            ================================================= */

            $conteos[
                $turno
            ][
                $indiceSector
            ]++;
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
       CLASIFICAR TURNO

       IMPORTANTE:
       La plantilla tiene muchas variantes.

       Las agrupamos por familia.
    ========================================================= */

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

    $registros =
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
            'r.eliminado',
            0
        )
        ->where(
            'p.area_snapshot IS NOT NULL',
            null,
            false
        )
        ->where(
            'p.area_snapshot !=',
            ''
        )
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

    $registros =
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
        )
        ->where(
            'r.eliminado',
            0
        )
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

    $registros =
        $this->db
        ->table('ai_reportes')
        ->select([
            'id_reporte',
            'resolucion',
        ])
        ->where(
            'eliminado',
            0
        )
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
}