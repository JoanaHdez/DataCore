<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardUbicacionService
{

    private $db;

    private DashboardFiltrosService $filtrosService;


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct(
        DashboardFiltrosService $filtrosService
    ) {

        $this->db =
            \Config\Database::connect(
                'datacore'
            );

        $this->filtrosService =
            $filtrosService;
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


        $this->filtrosService
            ->aplicarFiltrosReportes(
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
        ===================================================== */

        $combinacionesContadas = [];


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


            $combinacionesContadas[$clave] =
                true;


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
    QUEJAS POR SECTOR
    ========================================================= */

    public function obtenerQuejasPorSector(): array
    {
        /* =====================================================
        SECTORES INSTITUCIONALES
        ===================================================== */

        $conteos = [];


        for (
            $numero = 1;
            $numero <= 15;
            $numero++
        ) {

            $conteos[
                'SECTOR ' . $numero
            ] = 0;
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
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );


        /* =====================================================
        FILTROS GLOBALES
        ===================================================== */

        $this->filtrosService
            ->aplicarFiltrosReportes(
                $builder,
                'r'
            );


        /* =====================================================
        OBTENER REGISTROS
        ===================================================== */

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

        Una misma queja solamente debe contabilizarse una vez
        dentro del mismo sector.
        ===================================================== */

        $reportesContados = [];


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


            if (
                $idReporte <= 0
            ) {

                continue;
            }


            /* =================================================
            OBTENER SECTOR DESDE AREA_SNAPSHOT
            ================================================= */

            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area']
                        ?? ''
                    )
                );


            if (
                $sector === null
                || !array_key_exists(
                    $sector,
                    $conteos
                )
            ) {

                continue;
            }


            /* =================================================
            EVITAR CONTAR LA MISMA QUEJA DOS VECES
            EN EL MISMO SECTOR
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $sector;


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

            $conteos[$sector]++;
        }


        /* =====================================================
        RESPUESTA
        ===================================================== */

        return [

            'sectores' =>
                array_keys(
                    $conteos
                ),

            'totales' =>
                array_values(
                    $conteos
                ),

            'total' =>
                array_sum(
                    $conteos
                ),

        ];
    }


    /* =========================================================
       QUEJAS POR ZONA
    ========================================================= */

    public function obtenerQuejasPorZona(): array
    {
        /* =====================================================
           ZONAS INSTITUCIONALES
        ===================================================== */

        $conteos = [

            'Zona Norte' =>
                0,

            'Zona Poniente' =>
                0,

            'Zona Centro' =>
                0,

            'Zona Oriente' =>
                0,

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


        $this->filtrosService
            ->aplicarFiltrosReportes(
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
           EVITAR DUPLICADOS
        ===================================================== */

        $reportesContados = [];


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
               OBTENER SECTOR
            ================================================= */

            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area']
                        ?? ''
                    )
                );


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
       OBTENER ZONA DESDE SECTOR
    ========================================================= */

    private function obtenerZonaDesdeSector(
        string $sector
    ): ?string {

        if (
            !preg_match(
                '/^SECTOR\s+([0-9]+)$/u',
                trim(
                    $sector
                ),
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


    /* =========================================================
       CLASIFICAR TURNO
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


        /*
         * Otros valores como:
         *
         * DELTA
         * GAMA
         * UNICO
         * 24 HRS. POR 48 HRS.
         *
         * No se clasifican arbitrariamente.
         */

        return null;
    }


    /* =========================================================
       QUEJAS POR TURNO
    ========================================================= */

    public function obtenerQuejasPorTurno(): array
    {
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


        $this->filtrosService
            ->aplicarFiltrosReportes(
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

            'Primer turno' =>
                0,

            'Segundo turno' =>
                0,

            'Tercer turno' =>
                0,

            'Alfa' =>
                0,

            'Beta' =>
                0,

            'Diario' =>
                0,

            'No refiere ni fecha ni horario' =>
                0,

        ];


        /* =====================================================
           EVITAR DUPLICADOS
        ===================================================== */

        $reportesContados = [];


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


            $turno =
                $this->clasificarTurnoDashboard(
                    (string) (
                        $registro['turno']
                        ?? ''
                    )
                );


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

}