<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardAreaService
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


        /* =====================================================
           FILTROS DEL DASHBOARD
        ===================================================== */

        $this->filtrosService
            ->aplicarFiltrosReportes(
                $builder,
                'r'
            );


        /* =====================================================
           CONSULTAR
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
           AGRUPAR
        ===================================================== */

        $conteos = [];


        foreach (
            $registros
            as $registro
        ) {

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

                $conteos[$area] =
                    0;
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


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'areas' =>
                $areas,

            'totales' =>
                $totales,

        ];
    }

}