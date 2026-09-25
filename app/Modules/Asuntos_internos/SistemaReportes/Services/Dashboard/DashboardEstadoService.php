<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardEstadoService
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
       ESTADO DE LAS QUEJAS
    ========================================================= */

    public function obtenerEstadosQuejas(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select(
                "
                r.estado_actual AS estado,
                COUNT(DISTINCT r.id_reporte) AS total
                ",
                false
            );


        /* =====================================================
           SOLO QUEJAS
        ===================================================== */

        $builder
            ->where(
                'r.tipo_registro',
                'QUEJA'
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
           ESTADOS VÁLIDOS
        ===================================================== */

        $builder
            ->whereIn(
                'r.estado_actual',
                [
                    'Pendiente',
                    'En proceso',
                    'Finalizado',
                ]
            );


        /* =====================================================
           AGRUPAR
        ===================================================== */

        $builder
            ->groupBy(
                'r.estado_actual'
            );


        /* =====================================================
           CONSULTAR
        ===================================================== */

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
           ESTRUCTURA BASE

           Conservamos siempre los tres estados para que
           posteriormente la gráfica mantenga una estructura
           estable incluso cuando alguno tenga cero registros.
        ===================================================== */

        $totalesPorEstado = [

            'Pendiente' =>
                0,

            'En proceso' =>
                0,

            'Finalizado' =>
                0,

        ];


        /* =====================================================
           ASIGNAR RESULTADOS
        ===================================================== */

        foreach (
            $registros
            as $registro
        ) {

            $estado =
                trim(
                    (string) (
                        $registro['estado']
                        ?? ''
                    )
                );


            if (
                !array_key_exists(
                    $estado,
                    $totalesPorEstado
                )
            ) {

                continue;
            }


            $totalesPorEstado[$estado] =
                (int) (
                    $registro['total']
                    ?? 0
                );
        }


        /* =====================================================
           TOTAL DE QUEJAS
        ===================================================== */

        $total =
            array_sum(
                $totalesPorEstado
            );


        /* =====================================================
           PORCENTAJES
        ===================================================== */

        $porcentajes =
            [];


        foreach (
            $totalesPorEstado
            as $estado => $cantidad
        ) {

            $porcentajes[$estado] =
                $total > 0
                    ? round(
                        ($cantidad / $total) * 100,
                        1
                    )
                    : 0;
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'estados' =>
                array_keys(
                    $totalesPorEstado
                ),

            'totales' =>
                array_values(
                    $totalesPorEstado
                ),

            'porcentajes' =>
                array_values(
                    $porcentajes
                ),

            'total' =>
                $total,

        ];
    }

}