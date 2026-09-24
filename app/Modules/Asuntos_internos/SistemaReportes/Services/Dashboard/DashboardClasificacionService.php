<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardClasificacionService
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
       QUEJAS POR CLASIFICACIÓN
    ========================================================= */

    public function obtenerClasificaciones(): array
    {

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.clasificacion',
            ]);


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
            ->get()
            ->getResultArray();


        /* =====================================================
           AGRUPAR CLASIFICACIONES
        ===================================================== */

        $conteos = [];


        foreach (
            $registros
            as $registro
        ) {

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
             * Una clasificación vacía
             * no representa una categoría real.
             */

            if ($clasificacion === '') {

                continue;
            }


            /*
             * Utilizamos una clave normalizada
             * para evitar separar:
             *
             * Extorsión
             * EXTORSIÓN
             * extorsión
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


        foreach (
            $conteos
            as $dato
        ) {

            $clasificaciones[] =
                $dato['nombre'];


            $totales[] =
                (int) $dato['total'];
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

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

}