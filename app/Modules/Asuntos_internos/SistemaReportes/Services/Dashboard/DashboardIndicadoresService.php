<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardIndicadoresService
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
       INDICADORES GENERALES
    ========================================================= */

    public function obtenerIndicadores(): array
    {

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'COUNT(*) AS total',

                "SUM(
                    CASE
                        WHEN r.estado_actual = 'Pendiente'
                        THEN 1
                        ELSE 0
                    END
                ) AS pendientes",

                "SUM(
                    CASE
                        WHEN r.estado_actual = 'En proceso'
                        THEN 1
                        ELSE 0
                    END
                ) AS en_proceso",

                "SUM(
                    CASE
                        WHEN r.estado_actual = 'Finalizado'
                        THEN 1
                        ELSE 0
                    END
                ) AS finalizados",
            ], false);


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

        $resultado =
            $builder
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

}