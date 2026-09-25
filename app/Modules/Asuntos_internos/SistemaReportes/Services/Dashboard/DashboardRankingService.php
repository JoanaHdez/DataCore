<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardRankingService
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
       OBTENER RANKING
    ========================================================= */

    public function obtenerRanking(
        string $tipo = 'sector'
    ): array {

        $tipo =
            strtolower(
                trim(
                    $tipo
                )
            );


        return match ($tipo) {

            'area' =>
                $this->obtenerRankingArea(),

            'unidad' =>
                $this->obtenerRankingUnidad(),

            'personal' =>
                $this->obtenerRankingPersonal(),

            default =>
                $this->obtenerRankingSector(),
        };
    }


    /* =========================================================
       TOP 5 - SECTOR
    ========================================================= */

    private function obtenerRankingSector(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'p.area_snapshot',
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


            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );


            if (
                $sector === null
            ) {

                continue;
            }


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


            if (
                !isset(
                    $conteos[$sector]
                )
            ) {

                $conteos[$sector] =
                    0;
            }


            $conteos[$sector]++;
        }


        arsort(
            $conteos,
            SORT_NUMERIC
        );


        $conteos =
            array_slice(
                $conteos,
                0,
                5,
                true
            );


        return $this->construirRespuesta(
            'sector',
            'Sectores',
            $conteos
        );
    }


    /* =========================================================
       TOP 5 - ÁREA
    ========================================================= */

    private function obtenerRankingArea(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'p.area_snapshot',
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


        $registros =
            $builder
            ->get()
            ->getResultArray();


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


            $area =
                $this->normalizarTexto(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );


            if (
                $area === ''
            ) {

                continue;
            }


            $clave =
                $idReporte
                . '|'
                . mb_strtoupper(
                    $area,
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
                    $conteos[$area]
                )
            ) {

                $conteos[$area] =
                    0;
            }


            $conteos[$area]++;
        }


        arsort(
            $conteos,
            SORT_NUMERIC
        );


        $conteos =
            array_slice(
                $conteos,
                0,
                5,
                true
            );


        return $this->construirRespuesta(
            'area',
            'Áreas',
            $conteos
        );
    }


    /* =========================================================
       TOP 5 - UNIDAD
    ========================================================= */

    private function obtenerRankingUnidad(): array
    {

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


        $this->filtrosService
            ->aplicarFiltrosReportes(
                $builder,
                'r'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


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


            $unidad =
                $noEconomico !== ''
                    ? $noEconomico
                    : $placas;


            if (
                $unidad === ''
            ) {

                continue;
            }


            $clave =
                $idReporte
                . '|'
                . mb_strtoupper(
                    $unidad,
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
                    $conteos[$unidad]
                )
            ) {

                $conteos[$unidad] =
                    0;
            }


            $conteos[$unidad]++;
        }


        arsort(
            $conteos,
            SORT_NUMERIC
        );


        $conteos =
            array_slice(
                $conteos,
                0,
                5,
                true
            );


        return $this->construirRespuesta(
            'unidad',
            'Unidades',
            $conteos
        );
    }


    /* =========================================================
       TOP 5 - PERSONAL
    ========================================================= */

    private function obtenerRankingPersonal(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'p.perscod',
                'p.plantilla_id',
                'p.nombre_snapshot',
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


        $personas = [];

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


            $perscod =
                $this->normalizarTexto(
                    (string) (
                        $registro['perscod']
                        ?? ''
                    )
                );


            $plantillaId =
                (int) (
                    $registro['plantilla_id']
                    ?? 0
                );


            $nombre =
                $this->normalizarTexto(
                    (string) (
                        $registro['nombre_snapshot']
                        ?? ''
                    )
                );


            /* =================================================
               IDENTIDAD ÚNICA

               1. perscod
               2. plantilla_id
            ================================================= */

            if (
                $perscod !== ''
            ) {

                $identidad =
                    'P:'
                    . mb_strtoupper(
                        $perscod,
                        'UTF-8'
                    );

            } elseif (
                $plantillaId > 0
            ) {

                $identidad =
                    'I:'
                    . $plantillaId;

            } else {

                continue;
            }


            if (
                $nombre === ''
            ) {

                $nombre =
                    $perscod !== ''
                        ? $perscod
                        : 'Personal ' . $plantillaId;
            }


            /* =================================================
               UNA PERSONA SOLO CUENTA UNA VEZ
               POR REPORTE
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $identidad;


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
                    $personas[$identidad]
                )
            ) {

                $personas[$identidad] = [

                    'nombre' =>
                        $nombre,

                    'total' =>
                        0,

                ];
            }


            $personas[$identidad]['total']++;
        }


        uasort(
            $personas,
            static function (
                array $a,
                array $b
            ): int {

                $comparacion =
                    ((int) ($b['total'] ?? 0))
                    <=>
                    ((int) ($a['total'] ?? 0));


                if (
                    $comparacion !== 0
                ) {

                    return $comparacion;
                }


                return strcasecmp(
                    (string) (
                        $a['nombre']
                        ?? ''
                    ),
                    (string) (
                        $b['nombre']
                        ?? ''
                    )
                );
            }
        );


        $personas =
            array_slice(
                $personas,
                0,
                5,
                true
            );


        $conteos = [];


        foreach (
            $personas
            as $persona
        ) {

            $nombre =
                (string) (
                    $persona['nombre']
                    ?? ''
                );


            if (
                $nombre === ''
            ) {

                continue;
            }


            $conteos[$nombre] =
                (int) (
                    $persona['total']
                    ?? 0
                );
        }


        return $this->construirRespuesta(
            'personal',
            'Personal con mayor número de registros asociados',
            $conteos
        );
    }


    /* =========================================================
       CONSTRUIR RESPUESTA
    ========================================================= */

    private function construirRespuesta(
        string $tipo,
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


        $totalTop =
            array_sum(
                $totales
            );


        $porcentajes = [];


        foreach (
            $totales
            as $cantidad
        ) {

            $porcentajes[] =
                $totalTop > 0
                    ? round(
                        (
                            (int) $cantidad
                            / $totalTop
                        ) * 100,
                        1
                    )
                    : 0;
        }


        return [

            'tipo' =>
                $tipo,

            'titulo' =>
                $titulo,

            'etiquetas' =>
                $etiquetas,

            'totales' =>
                $totales,

            'porcentajes' =>
                $porcentajes,

            'total_top' =>
                $totalTop,

            'opciones' => [

                [
                    'valor' =>
                        'sector',

                    'texto' =>
                        'Sectores',
                ],

                [
                    'valor' =>
                        'area',

                    'texto' =>
                        'Áreas',
                ],

                [
                    'valor' =>
                        'unidad',

                    'texto' =>
                        'Unidades',
                ],

                [
                    'valor' =>
                        'personal',

                    'texto' =>
                        'Personal',
                ],

            ],

        ];
    }


    /* =========================================================
       OBTENER SECTOR DESDE ÁREA
    ========================================================= */

    private function obtenerSectorDesdeArea(
        string $area
    ): ?string {

        $area =
            mb_strtoupper(
                $this->normalizarTexto(
                    $area
                ),
                'UTF-8'
            );


        if (
            $area === ''
        ) {

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


        return
            'SECTOR '
            . $numero;
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