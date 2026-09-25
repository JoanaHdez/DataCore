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

        /* =====================================================
           INDICADORES DE REPORTES / QUEJAS
        ===================================================== */

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([

                /* =============================================
                   TOTAL DE REPORTES EN AI_REPORTES

                   Por ahora esta tabla corresponde al bloque
                   que estamos cerrando para Quejas.
                ============================================== */

                'COUNT(*) AS total',


                /* =============================================
                   QUEJAS
                ============================================== */

                "SUM(
                    CASE
                        WHEN r.tipo_registro = 'QUEJA'
                        THEN 1
                        ELSE 0
                    END
                ) AS quejas",


                /* =============================================
                   PENDIENTES
                ============================================== */

                "SUM(
                    CASE
                        WHEN r.tipo_registro = 'QUEJA'
                        AND r.estado_actual = 'Pendiente'
                        THEN 1
                        ELSE 0
                    END
                ) AS pendientes",


                /* =============================================
                   EN PROCESO
                ============================================== */

                "SUM(
                    CASE
                        WHEN r.tipo_registro = 'QUEJA'
                        AND r.estado_actual = 'En proceso'
                        THEN 1
                        ELSE 0
                    END
                ) AS en_proceso",


                /* =============================================
                   FINALIZADOS
                ============================================== */

                "SUM(
                    CASE
                        WHEN r.tipo_registro = 'QUEJA'
                        AND r.estado_actual = 'Finalizado'
                        THEN 1
                        ELSE 0
                    END
                ) AS finalizados",


                /* =============================================
                   QUEJAS ANÓNIMAS
                ============================================== */

                "SUM(
                    CASE
                        WHEN r.tipo_registro = 'QUEJA'
                        AND r.es_anonimo = 1
                        THEN 1
                        ELSE 0
                    END
                ) AS anonimas",

            ], false);


        /* =====================================================
           FILTROS GLOBALES
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


        /* =====================================================
           PERSONAL INVOLUCRADO

           Se calcula aparte porque necesitamos contar
           personas distintas y no relaciones persona-reporte.
        ===================================================== */

        $personalInvolucrado =
            $this->obtenerTotalPersonalInvolucrado();


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [

            'total' =>
                (int) (
                    $resultado['total']
                    ?? 0
                ),


            'quejas' =>
                (int) (
                    $resultado['quejas']
                    ?? 0
                ),


            /*
             * FELICITACIONES
             *
             * Pendiente de conectar con ai_felicitaciones.
             *
             * No debemos calcularlas desde ai_reportes
             * porque ya comprobamos que los registros reales
             * viven en una tabla independiente.
             */

            'felicitaciones' =>
                0,


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


            'anonimas' =>
                (int) (
                    $resultado['anonimas']
                    ?? 0
                ),


            'personal_involucrado' =>
                $personalInvolucrado,

        ];
    }


    /* =========================================================
       TOTAL DE PERSONAL INVOLUCRADO
    ========================================================= */

    private function obtenerTotalPersonalInvolucrado(): int
    {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->join(
                'ai_reporte_personal p_indicador',
                'p_indicador.id_reporte = r.id_reporte',
                'inner'
            )
            ->select(
                "
                COUNT(
                    DISTINCT
                    CASE

                        /*
                         * PERSCOD tiene prioridad porque una misma
                         * persona puede aparecer con distintos
                         * plantilla_id.
                         */

                        WHEN
                            p_indicador.perscod IS NOT NULL
                            AND TRIM(
                                p_indicador.perscod
                            ) != ''

                        THEN
                            CONCAT(
                                'P:',
                                TRIM(
                                    p_indicador.perscod
                                )
                            )


                        /*
                         * Si no existe perscod utilizamos
                         * plantilla_id como respaldo.
                         */

                        WHEN
                            p_indicador.plantilla_id IS NOT NULL

                        THEN
                            CONCAT(
                                'I:',
                                p_indicador.plantilla_id
                            )


                        ELSE
                            NULL

                    END
                ) AS total_personal
                ",
                false
            );


        /* =====================================================
           APLICAR LOS MISMOS FILTROS DEL DASHBOARD
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


        return
            (int) (
                $resultado['total_personal']
                ?? 0
            );
    }

}