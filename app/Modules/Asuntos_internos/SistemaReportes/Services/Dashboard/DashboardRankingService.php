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


        /* =====================================================
        TIPO DE INFORMACIÓN DEL DASHBOARD
        ===================================================== */

        $filtros =
            $this->filtrosService
            ->obtenerFiltros();


        $tipoRegistro =
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
            $tipoRegistro === 'FELICITACION'
        ) {

            return $this->obtenerRankingFelicitaciones(
                $tipo
            );
        }


        /* =====================================================
        REPORTES
        TODOS / QUEJAS
        ===================================================== */

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
    OBTENER RANKING DE FELICITACIONES
    ========================================================= */

    private function obtenerRankingFelicitaciones(
        string $tipo
    ): array {

        return match ($tipo) {

            'area' =>
                $this->obtenerRankingFelicitacionesArea(),

            'unidad' =>
                $this->obtenerRankingFelicitacionesUnidad(),

            'personal' =>
                $this->obtenerRankingFelicitacionesPersonal(),

            default =>
                $this->obtenerRankingFelicitacionesSector(),
        };
    }

    /* =========================================================
    TOP 5 FELICITACIONES - SECTOR
    ========================================================= */

    private function obtenerRankingFelicitacionesSector(): array
    {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'p.area_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal p',
                'p.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [];

        $felicitacionesContadas = [];


        foreach (
            $registros
            as $registro
        ) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if (
                $idFelicitacion <= 0
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


            /*
            * Una felicitación solamente cuenta una vez
            * dentro del mismo sector.
            */

            $clave =
                $idFelicitacion
                . '|'
                . $sector;


            if (
                isset(
                    $felicitacionesContadas[$clave]
                )
            ) {

                continue;
            }


            $felicitacionesContadas[$clave] =
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
    TOP 5 FELICITACIONES - ÁREA
    ========================================================= */

    private function obtenerRankingFelicitacionesArea(): array
    {
        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'p.area_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal p',
                'p.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [];

        $felicitacionesContadas = [];


        foreach ($registros as $registro) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if ($idFelicitacion <= 0) {
                continue;
            }


            $area =
                $this->normalizarTexto(
                    (string) (
                        $registro['area_snapshot']
                        ?? ''
                    )
                );


            if ($area === '') {
                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . mb_strtoupper(
                    $area,
                    'UTF-8'
                );


            if (
                isset(
                    $felicitacionesContadas[$clave]
                )
            ) {

                continue;
            }


            $felicitacionesContadas[$clave] =
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
    TOP 5 FELICITACIONES - UNIDAD
    ========================================================= */

    private function obtenerRankingFelicitacionesUnidad(): array
    {
        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'u.no_economico_snapshot',
                'u.placas_snapshot',
            ])
            ->join(
                'ai_felicitacion_unidades u',
                'u.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $conteos = [];

        $felicitacionesContadas = [];


        foreach ($registros as $registro) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if ($idFelicitacion <= 0) {
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


            if ($unidad === '') {
                continue;
            }


            $clave =
                $idFelicitacion
                . '|'
                . mb_strtoupper(
                    $unidad,
                    'UTF-8'
                );


            if (
                isset(
                    $felicitacionesContadas[$clave]
                )
            ) {

                continue;
            }


            $felicitacionesContadas[$clave] =
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
    TOP 5 FELICITACIONES - PERSONAL
    ========================================================= */

    private function obtenerRankingFelicitacionesPersonal(): array
    {
        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'p.perscod',
                'p.plantilla_id',
                'p.nombre_snapshot',
            ])
            ->join(
                'ai_felicitacion_personal p',
                'p.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->filtrosService
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );


        $registros =
            $builder
            ->get()
            ->getResultArray();


        $personas = [];

        $felicitacionesContadas = [];


        foreach ($registros as $registro) {

            $idFelicitacion =
                (int) (
                    $registro['id_felicitacion']
                    ?? 0
                );


            if ($idFelicitacion <= 0) {
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


            /* =============================================
            IDENTIDAD ÚNICA
            ============================================== */

            if ($perscod !== '') {

                $identidad =
                    'P:'
                    . mb_strtoupper(
                        $perscod,
                        'UTF-8'
                    );

            } elseif ($plantillaId > 0) {

                $identidad =
                    'I:'
                    . $plantillaId;

            } else {

                continue;
            }


            if ($nombre === '') {

                $nombre =
                    $perscod !== ''
                        ? $perscod
                        : 'Personal ' . $plantillaId;
            }


            /* =============================================
            UNA PERSONA UNA VEZ POR FELICITACIÓN
            ============================================== */

            $clave =
                $idFelicitacion
                . '|'
                . $identidad;


            if (
                isset(
                    $felicitacionesContadas[$clave]
                )
            ) {

                continue;
            }


            $felicitacionesContadas[$clave] =
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


                if ($comparacion !== 0) {
                    return $comparacion;
                }


                return strcasecmp(
                    (string) ($a['nombre'] ?? ''),
                    (string) ($b['nombre'] ?? '')
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


        foreach ($personas as $persona) {

            $nombre =
                trim(
                    (string) (
                        $persona['nombre']
                        ?? ''
                    )
                );


            if ($nombre === '') {
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
            'Personal con mayor número de felicitaciones asociadas',
            $conteos
        );
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
                'r.folio',
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

        $reportesPorSector = [];

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


            $reportesPorSector[$sector][$idReporte] =
                $this->normalizarTexto(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                );
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


        $respuesta =
            $this->construirRespuesta(
                'sector',
                'Sectores',
                $conteos
            );


        $respuesta['detalles_ranking'] =
            $this->construirDetallesRanking(
                $conteos,
                $reportesPorSector
            );


        return $respuesta;
    }


    /* =========================================================
       TOP 5 - AREA
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
                'r.folio',
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

        $reportesPorArea = [];

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


            $reportesPorArea[$area][$idReporte] =
                $this->normalizarTexto(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                );
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


        $respuesta =
            $this->construirRespuesta(
                'area',
                'Áreas',
                $conteos
            );


        $respuesta['detalles_ranking'] =
            $this->construirDetallesRanking(
                $conteos,
                $reportesPorArea
            );


        return $respuesta;
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
                'r.folio',
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

        $reportesPorUnidad = [];

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


            $reportesPorUnidad[$unidad][$idReporte] =
                $this->normalizarTexto(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                );
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


        $respuesta =
            $this->construirRespuesta(
                'unidad',
                'Unidades',
                $conteos
            );


        $respuesta['detalles_ranking'] =
            $this->construirDetallesRanking(
                $conteos,
                $reportesPorUnidad
            );


        return $respuesta;
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
                'r.folio',
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

                    'identificador' =>
                        $perscod !== ''
                            ? $perscod
                            : (string) $plantillaId,

                    'reportes' =>
                        [],

                ];
            }


            $personas[$identidad]['total']++;


            $personas[$identidad]['reportes'][$idReporte] =
                $this->normalizarTexto(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                );
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

        $detalles = [];


        foreach (
            $personas
            as $identidad => $persona
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


            $reportes =
                is_array(
                    $persona['reportes']
                    ?? null
                )
                    ? $persona['reportes']
                    : [];


            $folios =
                array_values(
                    array_filter(
                        $reportes,
                        static function (
                            $folio
                        ): bool {

                            return trim(
                                (string) $folio
                            ) !== '';
                        }
                    )
                );


            $detalles[] = [

                'identidad' =>
                    (string) $identidad,

                'identificador' =>
                    (string) (
                        $persona['identificador']
                        ?? ''
                    ),

                'nombre' =>
                    $nombre,

                'total_quejas' =>
                    (int) (
                        $persona['total']
                        ?? 0
                    ),

                'folios' =>
                    $folios,

                'motivos' =>
                    $this->obtenerMotivosAgrupadosPorReportes(
                        $reportes
                    ),

            ];
        }


        $respuesta =
            $this->construirRespuesta(
                'personal',
                'Personal con mayor número de registros asociados',
                $conteos
            );


        $respuesta['detalles_personal'] =
            $detalles;

        $respuesta['detalles_ranking'] =
            $detalles;


        return $respuesta;
    }


    private function construirDetallesRanking(
        array $conteos,
        array $reportesPorElemento
    ): array {

        $detalles = [];


        foreach (
            $conteos
            as $nombre => $total
        ) {

            $nombre =
                (string) $nombre;

            $reportes =
                is_array(
                    $reportesPorElemento[$nombre]
                    ?? null
                )
                    ? $reportesPorElemento[$nombre]
                    : [];


            $folios =
                array_values(
                    array_filter(
                        $reportes,
                        static function (
                            $folio
                        ): bool {

                            return trim(
                                (string) $folio
                            ) !== '';
                        }
                    )
                );


            $detalles[] = [

                'nombre' =>
                    $nombre,

                'total_quejas' =>
                    (int) $total,

                'folios' =>
                    $folios,

                'motivos' =>
                    $this->obtenerMotivosAgrupadosPorReportes(
                        $reportes
                    ),

            ];
        }


        return $detalles;
    }


    private function obtenerMotivosAgrupadosPorReportes(
        array $reportes
    ): array {

        if (
            empty(
                $reportes
            )
        ) {

            return [];
        }


        $idsReportes =
            array_values(
                array_filter(
                    array_map(
                        'intval',
                        array_keys(
                            $reportes
                        )
                    ),
                    static fn (
                        int $idReporte
                    ): bool =>
                        $idReporte > 0
                )
            );


        if (
            empty(
                $idsReportes
            )
        ) {

            return [];
        }


        $registros =
            $this->db
            ->table(
                'ai_reporte_motivos rm'
            )
            ->select([
                'rm.id_reporte',
                'rm.motivo_personalizado',
                'm.motivo',
            ])
            ->join(
                'ai_cat_motivos m',
                'm.id_motivo = rm.id_motivo',
                'left'
            )
            ->whereIn(
                'rm.id_reporte',
                $idsReportes
            )
            ->where(
                'rm.eliminado',
                0
            )
            ->orderBy(
                'm.motivo',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $motivos = [];


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
                || !array_key_exists(
                    $idReporte,
                    $reportes
                )
            ) {

                continue;
            }


            $motivoPersonalizado =
                $this->normalizarTexto(
                    (string) (
                        $registro['motivo_personalizado']
                        ?? ''
                    )
                );


            $motivoCatalogo =
                $this->normalizarTexto(
                    (string) (
                        $registro['motivo']
                        ?? ''
                    )
                );


            $motivo =
                $motivoPersonalizado !== ''
                    ? $motivoPersonalizado
                    : $motivoCatalogo;


            if (
                $motivo === ''
            ) {

                continue;
            }


            $clave =
                mb_strtoupper(
                    $motivo,
                    'UTF-8'
                );


            if (
                !isset(
                    $motivos[$clave]
                )
            ) {

                $motivos[$clave] = [

                    'motivo' =>
                        $motivo,

                    'folios' =>
                        [],

                    '_reportes_contados' =>
                        [],

                ];
            }


            if (
                isset(
                    $motivos[$clave]['_reportes_contados'][$idReporte]
                )
            ) {

                continue;
            }


            $folio =
                $this->normalizarTexto(
                    (string) (
                        $reportes[$idReporte]
                        ?? ''
                    )
                );


            if (
                $folio === ''
            ) {

                continue;
            }


            $motivos[$clave]['_reportes_contados'][$idReporte] =
                true;

            $motivos[$clave]['folios'][] =
                $folio;
        }


        $resultado = [];


        foreach (
            $motivos
            as $motivo
        ) {

            $folios =
                array_values(
                    array_unique(
                        $motivo['folios']
                    )
                );


            $resultado[] = [

                'motivo' =>
                    (string) (
                        $motivo['motivo']
                        ?? ''
                    ),

                'cantidad_quejas' =>
                    count(
                        $folios
                    ),

                'folios' =>
                    $folios,

            ];
        }


        usort(
            $resultado,
            static function (
                array $a,
                array $b
            ): int {

                $comparacion =
                    ((int) ($b['cantidad_quejas'] ?? 0))
                    <=>
                    ((int) ($a['cantidad_quejas'] ?? 0));


                if (
                    $comparacion !== 0
                ) {

                    return $comparacion;
                }


                return strcasecmp(
                    (string) ($a['motivo'] ?? ''),
                    (string) ($b['motivo'] ?? '')
                );
            }
        );


        return $resultado;
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
