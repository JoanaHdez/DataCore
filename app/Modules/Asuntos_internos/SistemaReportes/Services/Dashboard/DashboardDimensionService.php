<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardDimensionService
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
       OBTENER DIMENSIÓN
    ========================================================= */

    public function obtenerDimension(
        string $dimension = 'area'
    ): array {

        $dimension =
            strtolower(
                trim(
                    $dimension
                )
            );


        return match ($dimension) {

            'unidad' =>
                $this->obtenerPorUnidad(),

            /*
             * Grupo todavía no se habilita.
             *
             * Primero debemos comprobar que exista
             * una fuente de datos consistente.
             */

            default =>
                $this->obtenerPorArea(),
        };
    }


    /* =========================================================
       QUEJAS POR ÁREA
    ========================================================= */

    private function obtenerPorArea(): array
    {

        /* =====================================================
           CONSULTAR REPORTES + PERSONAL
        ===================================================== */

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'p.area_snapshot AS etiqueta',
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
           DESCARTAR ÁREAS VACÍAS
        ===================================================== */

        $builder
            ->where(
                'p.area_snapshot IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM(p.area_snapshot) != ''",
                null,
                false
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
           CONTABILIZAR
        ===================================================== */

        $conteos = [];

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


            if (
                $idReporte <= 0
            ) {

                continue;
            }


            $etiqueta =
                $this->normalizarTexto(
                    (string) (
                        $registro['etiqueta']
                        ?? ''
                    )
                );


            if (
                $etiqueta === ''
            ) {

                continue;
            }


            /*
             * Una misma queja solo cuenta una vez
             * dentro de la misma área.
             */

            $clave =
                $idReporte
                . '|AREA|'
                . mb_strtoupper(
                    $etiqueta,
                    'UTF-8'
                );


            if (
                isset(
                    $reportesContados[$clave]
                )
            ) {

                continue;
            }


            $reportesContados[$clave] =
                true;


            if (
                !isset(
                    $conteos[$etiqueta]
                )
            ) {

                $conteos[$etiqueta] =
                    0;
            }


            $conteos[$etiqueta]++;
        }


        /* =====================================================
           ORDENAR MAYOR → MENOR
        ===================================================== */

        arsort(
            $conteos,
            SORT_NUMERIC
        );


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return $this->construirRespuesta(
            'area',
            'Área',
            $conteos
        );
    }


    /* =========================================================
       QUEJAS POR UNIDAD
    ========================================================= */

    private function obtenerPorUnidad(): array
    {

        /* =====================================================
           CONSULTAR REPORTES + UNIDADES
        ===================================================== */

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'u.no_economico_snapshot',
                'u.placas_snapshot',
            ])
            ->join(
                'ai_reporte_unidades u',
                'u.id_reporte = r.id_reporte',
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
           DESCARTAR UNIDADES SIN IDENTIFICADOR
        ===================================================== */

        $builder
            ->groupStart()
                ->groupStart()
                    ->where(
                        'u.no_economico_snapshot IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        "TRIM(u.no_economico_snapshot) != ''",
                        null,
                        false
                    )
                ->groupEnd()
                ->orGroupStart()
                    ->where(
                        'u.placas_snapshot IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        "TRIM(u.placas_snapshot) != ''",
                        null,
                        false
                    )
                ->groupEnd()
            ->groupEnd();


        /* =====================================================
           CONSULTAR
        ===================================================== */

        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'u.no_economico_snapshot',
                'u.placas_snapshot',
            ])
            ->get()
            ->getResultArray();


        /* =====================================================
           CONTABILIZAR
        ===================================================== */

        $conteos = [];

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


            if (
                $idReporte <= 0
            ) {

                continue;
            }


            $noEconomico =
                $this->normalizarTexto(
                    (string) (
                        $registro['no_economico_snapshot']
                        ?? ''
                    )
                );


            $placas =
                $this->normalizarTexto(
                    (string) (
                        $registro['placas_snapshot']
                        ?? ''
                    )
                );


            /*
             * Misma regla que utilizamos en el filtro:
             *
             * 1. Número económico.
             * 2. Placas cuando no exista número económico.
             */

            $etiqueta =
                $noEconomico !== ''
                    ? $noEconomico
                    : $placas;


            if (
                $etiqueta === ''
            ) {

                continue;
            }


            /*
             * Una misma queja solo cuenta una vez
             * dentro de la misma unidad.
             */

            $clave =
                $idReporte
                . '|UNIDAD|'
                . mb_strtoupper(
                    $etiqueta,
                    'UTF-8'
                );


            if (
                isset(
                    $reportesContados[$clave]
                )
            ) {

                continue;
            }


            $reportesContados[$clave] =
                true;


            if (
                !isset(
                    $conteos[$etiqueta]
                )
            ) {

                $conteos[$etiqueta] =
                    0;
            }


            $conteos[$etiqueta]++;
        }


        /* =====================================================
           ORDENAR MAYOR → MENOR
        ===================================================== */

        arsort(
            $conteos,
            SORT_NUMERIC
        );


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return $this->construirRespuesta(
            'unidad',
            'Unidad',
            $conteos
        );
    }


    /* =========================================================
       CONSTRUIR RESPUESTA COMÚN
    ========================================================= */

    private function construirRespuesta(
        string $dimension,
        string $titulo,
        array $conteos
    ): array {

        $etiquetas =
            array_keys(
                $conteos
            );


        $totales =
            array_values(
                $conteos
            );


        $total =
            array_sum(
                $totales
            );


        $porcentajes = [];


        foreach (
            $totales
            as $cantidad
        ) {

            $porcentajes[] =
                $total > 0
                    ? round(
                        (
                            (int) $cantidad
                            / $total
                        ) * 100,
                        1
                    )
                    : 0;
        }


        return [

            'dimension' =>
                $dimension,

            'titulo' =>
                $titulo,

            'etiquetas' =>
                $etiquetas,

            'totales' =>
                $totales,

            'porcentajes' =>
                $porcentajes,

            'total' =>
                $total,

            /* =================================================
               DIMENSIONES HABILITADAS EN V1 ACTUAL
            ================================================= */

            'opciones' => [

                [
                    'valor' =>
                        'area',

                    'texto' =>
                        'Área',
                ],

                [
                    'valor' =>
                        'unidad',

                    'texto' =>
                        'Unidad',
                ],

            ],

        ];
    }


    /* =========================================================
       NORMALIZAR TEXTO
    ========================================================= */

    private function normalizarTexto(
        string $texto
    ): string {

        return trim(
            preg_replace(
                '/\s+/u',
                ' ',
                $texto
            )
            ?? ''
        );
    }

}