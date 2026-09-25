<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardEvolucionService
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
       EVOLUCIÓN TEMPORAL
    ========================================================= */

    public function obtenerEvolucionTemporal(): array
    {

        /*
         * Para esta primera versión utilizamos fecha_registro.
         *
         * La unidad base es un día.
         *
         * Esto nos permite conservar el dato real y decidir
         * posteriormente en frontend si conviene representar
         * días, semanas o meses según el periodo consultado.
         */

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select(
                "
                DATE(r.fecha_registro) AS fecha,
                COUNT(DISTINCT r.id_reporte) AS total
                ",
                false
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
           SOLO REGISTROS CON FECHA
        ===================================================== */

        $builder
            ->where(
                'r.fecha_registro IS NOT NULL',
                null,
                false
            );


        /* =====================================================
           AGRUPAR POR DÍA
        ===================================================== */

        $builder
            ->groupBy(
                'DATE(r.fecha_registro)',
                false
            )
            ->orderBy(
                'fecha',
                'ASC'
            );


        /* =====================================================
           CONSULTAR
        ===================================================== */

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
           NORMALIZAR RESPUESTA
        ===================================================== */

        $datos = [];


        foreach (
            $registros
            as $registro
        ) {

            $fecha =
                trim(
                    (string) (
                        $registro['fecha']
                        ?? ''
                    )
                );


            if (
                $fecha === ''
            ) {

                continue;
            }


            $datos[] = [

                'fecha' =>
                    $fecha,

                'total' =>
                    (int) (
                        $registro['total']
                        ?? 0
                    ),

            ];
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'agrupacion' =>
                'dia',

            'datos' =>
                $datos,

            'total' =>
                array_sum(
                    array_column(
                        $datos,
                        'total'
                    )
                ),

        ];
    }

}