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


        if (
            $this->obtenerTipoActivo()
            === 'FELICITACION'
        ) {

            return $this->obtenerAnalisisFelicitaciones(
                $identificador
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


        $totalQuejas =
            $this->obtenerTotalQuejas(
                $identificador
            );


        return [

            'personal' =>
                $this->obtenerDatosPersona(
                    $identificador,
                    'queja'
                ),

            'total_quejas' =>
                $totalQuejas,

            'ultimas_quejas' =>
                $quejas,

            'tipo_analisis' =>
                'queja',

            'total_registros' =>
                $totalQuejas,

            'ultimos_registros' =>
                $quejas,

        ];
    }


    /* =========================================================
       PERSONAL INDIVIDUAL - FELICITACIONES
    ========================================================= */

    private function obtenerAnalisisFelicitaciones(
        string $identificador
    ): array {

        $felicitaciones =
            $this->obtenerUltimasFelicitaciones(
                $identificador
            );


        $totalFelicitaciones =
            $this->obtenerTotalFelicitaciones(
                $identificador
            );


        return [

            'personal' =>
                $this->obtenerDatosPersona(
                    $identificador,
                    'felicitacion'
                ),

            'total_quejas' =>
                0,

            'ultimas_quejas' =>
                [],

            'total_felicitaciones' =>
                $totalFelicitaciones,

            'ultimas_felicitaciones' =>
                $felicitaciones,

            'tipo_analisis' =>
                'felicitacion',

            'total_registros' =>
                $totalFelicitaciones,

            'ultimos_registros' =>
                $felicitaciones,

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
       TOTAL DE FELICITACIONES UNICAS
    ========================================================= */

    private function obtenerTotalFelicitaciones(
        string $identificador
    ): int {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->join(
                'ai_felicitacion_personal p_individual',
                'p_individual.id_felicitacion = f.id_felicitacion',
                'inner'
            )
            ->select(
                'COUNT(DISTINCT f.id_felicitacion) AS total',
                false
            );


        $this->aplicarFiltrosFelicitacionesSinPersonal(
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
       ULTIMAS 10 FELICITACIONES
    ========================================================= */

    private function obtenerUltimasFelicitaciones(
        string $identificador
    ): array {

        $builder =
            $this->db
            ->table(
                'ai_felicitaciones f'
            )
            ->select([
                'f.id_felicitacion',
                'f.folio',
                'f.fecha_registro',
                'f.nombre_felicitante',
                'f.razon_felicitacion',
            ])
            ->join(
                'ai_felicitacion_personal p_individual',
                'p_individual.id_felicitacion = f.id_felicitacion',
                'inner'
            );


        $this->aplicarFiltrosFelicitacionesSinPersonal(
            $builder
        );


        $this->aplicarFiltroPersonaIndividual(
            $builder,
            $identificador
        );


        $registros =
            $builder
            ->groupBy([
                'f.id_felicitacion',
                'f.folio',
                'f.fecha_registro',
                'f.nombre_felicitante',
                'f.razon_felicitacion',
            ])
            ->orderBy(
                'f.fecha_registro',
                'DESC'
            )
            ->orderBy(
                'f.id_felicitacion',
                'DESC'
            )
            ->limit(10)
            ->get()
            ->getResultArray();


        $felicitaciones = [];


        foreach (
            $registros
            as $registro
        ) {

            $felicitaciones[] = [

                'id_felicitacion' =>
                    (int) (
                        $registro['id_felicitacion']
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

                'felicitante' =>
                    $this->normalizarNullable(
                        $registro['nombre_felicitante']
                        ?? null
                    ),

                'razon' =>
                    $this->normalizarNullable(
                        $registro['razon_felicitacion']
                        ?? null
                    ),

            ];
        }


        return $felicitaciones;
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
        string $identificador,
        string $tipoAnalisis = 'queja'
    ): array {

        if (
            $tipoAnalisis === 'felicitacion'
        ) {

            return $this->obtenerDatosPersonaFelicitaciones(
                $identificador
            );
        }

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
                $this->obtenerDatosPersonaQuejasSinFiltros(
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

        return $this->obtenerDatosPersonaQuejasSinFiltros(
            $identificador
        );
    }


    /* =========================================================
       DATOS BASICOS DE PERSONA EN QUEJAS SIN FILTROS
    ========================================================= */

    private function obtenerDatosPersonaQuejasSinFiltros(
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
       DATOS BASICOS DE PERSONA EN FELICITACIONES
    ========================================================= */

    private function obtenerDatosPersonaFelicitaciones(
        string $identificador
    ): array {

        $builder =
            $this->db
            ->table(
                'ai_felicitacion_personal p_individual'
            )
            ->select([
                'p_individual.perscod',
                'p_individual.plantilla_id',
                'p_individual.nombre_snapshot',
                'p_individual.area_snapshot',
                'p_individual.turno_snapshot',
            ])
            ->join(
                'ai_felicitaciones f',
                'f.id_felicitacion = p_individual.id_felicitacion',
                'inner'
            );


        $this->aplicarFiltrosFelicitacionesSinPersonal(
            $builder
        );


        $this->aplicarFiltroPersonaIndividual(
            $builder,
            $identificador
        );


        $persona =
            $builder
            ->orderBy(
                'f.fecha_registro',
                'DESC'
            )
            ->orderBy(
                'f.id_felicitacion',
                'DESC'
            )
            ->limit(1)
            ->get()
            ->getRowArray();


        if (
            !$persona
        ) {

            $persona =
                $this->obtenerDatosPersonaFelicitacionesSinFiltros(
                    $identificador
                );
        }


        if (
            !$persona
        ) {

            $persona =
                $this->obtenerDatosPersonaQuejasSinFiltros(
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
       DATOS BASICOS DE PERSONA EN FELICITACIONES SIN FILTROS
    ========================================================= */

    private function obtenerDatosPersonaFelicitacionesSinFiltros(
        string $identificador
    ): array {

        $builder =
            $this->db
            ->table(
                'ai_felicitacion_personal p_individual'
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
                'p_individual.id_felicitacion_personal',
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
       FILTROS DE FELICITACIONES SIN PERSONAL
    ========================================================= */

    private function aplicarFiltrosFelicitacionesSinPersonal(
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
            ->aplicarFiltrosFelicitaciones(
                $builder,
                'f'
            );
    }


    /* =========================================================
       TIPO ACTIVO
    ========================================================= */

    private function obtenerTipoActivo(): string
    {

        $filtros =
            $this->filtrosService
            ->obtenerFiltros();


        return strtoupper(
            trim(
                (string) (
                    $filtros['tipo']
                    ?? ''
                )
            )
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
