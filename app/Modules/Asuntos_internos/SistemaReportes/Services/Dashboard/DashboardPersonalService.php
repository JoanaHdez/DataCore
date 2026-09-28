<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardPersonalService
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
       PERSONAL INDIVIDUAL
    ========================================================= */

    public function obtenerAnalisis(
        string $identificador
    ): array {

        $identificador =
            $this->normalizarTexto(
                $identificador
            );


        if (
            $identificador === ''
        ) {

            return $this->respuestaVacia(
                ''
            );
        }


        $quejas =
            $this->obtenerUltimasQuejas(
                $identificador
            );


        $motivos =
            $this->obtenerMotivosPorReporte(
                array_column(
                    $quejas,
                    'id_reporte'
                )
            );


        foreach (
            $quejas
            as &$queja
        ) {

            $idReporte =
                (int) (
                    $queja['id_reporte']
                    ?? 0
                );


            $queja['motivos'] =
                $motivos[$idReporte]
                ?? [];
        }

        unset($queja);


        return [

            'personal' =>
                $this->obtenerDatosPersona(
                    $identificador
                ),

            'total_quejas' =>
                $this->obtenerTotalQuejas(
                    $identificador
                ),

            'ultimas_quejas' =>
                $quejas,

        ];
    }


    /* =========================================================
       TOTAL DE QUEJAS UNICAS
    ========================================================= */

    private function obtenerTotalQuejas(
        string $identificador
    ): int {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->join(
                'ai_reporte_personal p_individual',
                'p_individual.id_reporte = r.id_reporte',
                'inner'
            )
            ->select(
                'COUNT(DISTINCT r.id_reporte) AS total',
                false
            );


        $this->aplicarFiltrosSinPersonal(
            $builder
        );


        $this->aplicarFiltroPersonaIndividual(
            $builder,
            $identificador
        );


        $resultado =
            $builder
            ->get()
            ->getRowArray();


        return (int) (
            $resultado['total']
            ?? 0
        );
    }


    /* =========================================================
       ULTIMAS 10 QUEJAS
    ========================================================= */

    private function obtenerUltimasQuejas(
        string $identificador
    ): array {

        $builder =
            $this->db
            ->table(
                'ai_reportes r'
            )
            ->select([
                'r.id_reporte',
                'r.folio',
                'r.fecha_registro',
                'r.estado_actual',
                'r.clasificacion',
            ])
            ->join(
                'ai_reporte_personal p_individual',
                'p_individual.id_reporte = r.id_reporte',
                'inner'
            );


        $this->aplicarFiltrosSinPersonal(
            $builder
        );


        $this->aplicarFiltroPersonaIndividual(
            $builder,
            $identificador
        );


        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'r.folio',
                'r.fecha_registro',
                'r.estado_actual',
                'r.clasificacion',
            ])
            ->orderBy(
                'r.fecha_registro',
                'DESC'
            )
            ->orderBy(
                'r.id_reporte',
                'DESC'
            )
            ->limit(10)
            ->get()
            ->getResultArray();


        $quejas = [];


        foreach (
            $registros
            as $registro
        ) {

            $quejas[] = [

                'id_reporte' =>
                    (int) (
                        $registro['id_reporte']
                        ?? 0
                    ),

                'folio' =>
                    $this->normalizarNullable(
                        $registro['folio']
                        ?? null
                    ),

                'fecha' =>
                    $this->normalizarNullable(
                        $registro['fecha_registro']
                        ?? null
                    ),

                'estado' =>
                    $this->normalizarNullable(
                        $registro['estado_actual']
                        ?? null
                    ),

                'clasificacion' =>
                    $this->normalizarNullable(
                        $registro['clasificacion']
                        ?? null
                    ),

                'motivos' =>
                    [],

            ];
        }


        return $quejas;
    }


    /* =========================================================
       MOTIVOS POR REPORTE
    ========================================================= */

    private function obtenerMotivosPorReporte(
        array $idsReporte
    ): array {

        $idsReporte =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            'intval',
                            $idsReporte
                        )
                    )
                )
            );


        if (
            empty($idsReporte)
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
                'rm.id_motivo',
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
                $idsReporte
            )
            ->where(
                'rm.eliminado',
                0
            )
            ->orderBy(
                'rm.id_reporte',
                'ASC'
            )
            ->orderBy(
                'rm.id_reporte_motivo',
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
            ) {

                continue;
            }


            $motivoPersonalizado =
                $this->normalizarTexto(
                    $registro['motivo_personalizado']
                    ?? ''
                );


            $motivoCatalogo =
                $this->normalizarTexto(
                    $registro['motivo']
                    ?? ''
                );


            $textoMotivo =
                $motivoPersonalizado !== ''
                ? $motivoPersonalizado
                : $motivoCatalogo;


            if (
                $textoMotivo === ''
            ) {

                continue;
            }


            $motivos[$idReporte][] = [

                'id_motivo' =>
                    (int) (
                        $registro['id_motivo']
                        ?? 0
                    ),

                'motivo' =>
                    $textoMotivo,

            ];
        }


        return $motivos;
    }


    /* =========================================================
       DATOS BASICOS DE LA PERSONA
    ========================================================= */

    private function obtenerDatosPersona(
        string $identificador
    ): array {

        $builder =
            $this->db
            ->table(
                'ai_reporte_personal p_individual'
            )
            ->select([
                'p_individual.perscod',
                'p_individual.plantilla_id',
                'p_individual.nombre_snapshot',
                'p_individual.area_snapshot',
                'p_individual.turno_snapshot',
            ])
            ->join(
                'ai_reportes r',
                'r.id_reporte = p_individual.id_reporte',
                'inner'
            );


        $this->aplicarFiltrosSinPersonal(
            $builder
        );


        $this->aplicarFiltroPersonaIndividual(
            $builder,
            $identificador
        );


        $persona =
            $builder
            ->orderBy(
                'r.fecha_registro',
                'DESC'
            )
            ->orderBy(
                'r.id_reporte',
                'DESC'
            )
            ->limit(1)
            ->get()
            ->getRowArray();


        if (
            !$persona
        ) {

            $persona =
                $this->obtenerDatosPersonaSinFiltros(
                    $identificador
                );
        }


        return [

            'identificador' =>
                $identificador,

            'perscod' =>
                $this->normalizarNullable(
                    $persona['perscod']
                    ?? null
                ),

            'plantilla_id' =>
                isset($persona['plantilla_id'])
                ? (int) $persona['plantilla_id']
                : null,

            'nombre' =>
                $this->normalizarNullable(
                    $persona['nombre_snapshot']
                    ?? null
                ),

            'area' =>
                $this->normalizarNullable(
                    $persona['area_snapshot']
                    ?? null
                ),

            'turno' =>
                $this->normalizarNullable(
                    $persona['turno_snapshot']
                    ?? null
                ),

        ];
    }


    /* =========================================================
       DATOS BASICOS SIN FILTROS
    ========================================================= */

    private function obtenerDatosPersonaSinFiltros(
        string $identificador
    ): array {

        $builder =
            $this->db
            ->table(
                'ai_reporte_personal p_individual'
            )
            ->select([
                'p_individual.perscod',
                'p_individual.plantilla_id',
                'p_individual.nombre_snapshot',
                'p_individual.area_snapshot',
                'p_individual.turno_snapshot',
            ]);


        $this->aplicarFiltroPersonaIndividual(
            $builder,
            $identificador
        );


        return $builder
            ->orderBy(
                'p_individual.id_reporte_personal',
                'DESC'
            )
            ->limit(1)
            ->get()
            ->getRowArray()
            ?? [];
    }


    /* =========================================================
       FILTROS GLOBALES SIN PERSONAL
    ========================================================= */

    private function aplicarFiltrosSinPersonal(
        $builder
    ): void {

        $filtros =
            $this->filtrosService
            ->obtenerFiltros();


        $filtros['personal'] =
            null;


        $filtrosSinPersonal =
            new DashboardFiltrosService();


        $filtrosSinPersonal
            ->establecerFiltros(
                $filtros
            );


        $filtrosSinPersonal
            ->aplicarFiltrosReportes(
                $builder,
                'r'
            );
    }


    /* =========================================================
       RESTRICCION DE PERSONA INDIVIDUAL
    ========================================================= */

    private function aplicarFiltroPersonaIndividual(
        $builder,
        string $identificador
    ): void {

        $identificadorEscapado =
            $this->db
            ->escape(
                $identificador
            );


        $builder
            ->where(
                "(
                    p_individual.perscod = {$identificadorEscapado}
                    OR CAST(
                        p_individual.plantilla_id
                        AS CHAR
                    ) = {$identificadorEscapado}
                )",
                null,
                false
            );
    }


    /* =========================================================
       RESPUESTA VACIA
    ========================================================= */

    private function respuestaVacia(
        string $identificador
    ): array {

        return [

            'personal' => [

                'identificador' =>
                    $identificador,

                'perscod' =>
                    null,

                'plantilla_id' =>
                    null,

                'nombre' =>
                    null,

                'area' =>
                    null,

                'turno' =>
                    null,

            ],

            'total_quejas' =>
                0,

            'ultimas_quejas' =>
                [],

        ];
    }


    /* =========================================================
       NORMALIZAR TEXTO
    ========================================================= */

    private function normalizarTexto(
        mixed $valor
    ): string {

        return trim(
            (string) (
                $valor
                ?? ''
            )
        );
    }


    /* =========================================================
       NORMALIZAR NULLABLE
    ========================================================= */

    private function normalizarNullable(
        mixed $valor
    ): ?string {

        $texto =
            $this->normalizarTexto(
                $valor
            );


        return $texto !== ''
            ? $texto
            : null;
    }
}
