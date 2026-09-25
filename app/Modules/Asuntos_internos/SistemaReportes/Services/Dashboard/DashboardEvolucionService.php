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

        $filtros =
            $this->filtrosService
            ->obtenerFiltros();


        $tipo =
            strtoupper(
                trim(
                    (string) (
                        $filtros['tipo']
                        ?? ''
                    )
                )
            );


        /* =====================================================
           FELICITACIONES
        ===================================================== */

        if (
            $tipo === 'FELICITACION'
        ) {

            return $this->obtenerEvolucionFelicitaciones();
        }


        /* =====================================================
           REPORTES
           TODOS / QUEJAS
        ===================================================== */

        return $this->obtenerEvolucionReportes();
    }


    /* =========================================================
       EVOLUCIÓN DE REPORTES
    ========================================================= */

    private function obtenerEvolucionReportes(): array
    {

        /*
         * Para esta primera versión utilizamos fecha_registro.
         *
         * La unidad base es un día.
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


        return $this->construirRespuesta(
            $registros
        );
    }


    /* =========================================================
       EVOLUCIÓN DE FELICITACIONES
    ========================================================= */

    private function obtenerEvolucionFelicitaciones(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select(
                "
                DATE(f.fecha_registro) AS fecha,
                COUNT(DISTINCT f.id_felicitacion) AS total
                ",
                false
            );


        /* =====================================================
           FILTROS GLOBALES DE FELICITACIONES
        ===================================================== */

        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        /* =====================================================
           SOLO REGISTROS CON FECHA
        ===================================================== */

        $builder
            ->where(
                'f.fecha_registro IS NOT NULL',
                null,
                false
            );


        /* =====================================================
           AGRUPAR POR DÍA
        ===================================================== */

        $builder
            ->groupBy(
                'DATE(f.fecha_registro)',
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


        return $this->construirRespuesta(
            $registros
        );
    }


    /* =========================================================
       CONSTRUIR RESPUESTA
    ========================================================= */

    private function construirRespuesta(
        array $registros
    ): array {

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