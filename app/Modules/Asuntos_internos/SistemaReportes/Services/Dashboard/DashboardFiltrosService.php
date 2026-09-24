<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services\Dashboard;


class DashboardFiltrosService
{

    private $db;


    /**
     * Filtros activos del Dashboard.
     */
    private array $filtros = [];


    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {

        $this->db =
            \Config\Database::connect(
                'datacore'
            );
    }


    /* =========================================================
       FILTROS DEL DASHBOARD
    ========================================================= */

    /**
     * Define los filtros que utilizarán
     * las consultas del Dashboard.
     */
    public function establecerFiltros(
        array $filtros
    ): void {

        $this->filtros = [

            /* =================================================
               FECHA DE REGISTRO
            ================================================= */

            'fecha_registro_inicio' =>
                $this->limpiarFiltro(
                    $filtros['fecha_registro_inicio']
                    ?? null
                ),

            'fecha_registro_fin' =>
                $this->limpiarFiltro(
                    $filtros['fecha_registro_fin']
                    ?? null
                ),


            /* =================================================
               FECHA DE LA QUEJA
            ================================================= */

            'fecha_queja_inicio' =>
                $this->limpiarFiltro(
                    $filtros['fecha_queja_inicio']
                    ?? null
                ),

            'fecha_queja_fin' =>
                $this->limpiarFiltro(
                    $filtros['fecha_queja_fin']
                    ?? null
                ),


            /* =================================================
               REPORTE
            ================================================= */

            'estado' =>
                $this->limpiarFiltro(
                    $filtros['estado']
                    ?? null
                ),

            'seguimiento' =>
                $this->limpiarFiltro(
                    $filtros['seguimiento']
                    ?? null
                ),


            /* =================================================
               PERSONAL INVOLUCRADO
            ================================================= */

            'area_personal' =>
                $this->limpiarFiltro(
                    $filtros['area_personal']
                    ?? null
                ),

            'turno' =>
                $this->limpiarFiltro(
                    $filtros['turno']
                    ?? null
                ),

            'sector' =>
                $this->limpiarFiltro(
                    $filtros['sector']
                    ?? null
                ),


            /* =================================================
               UNIDAD
            ================================================= */

            'unidad' =>
                $this->limpiarFiltro(
                    $filtros['unidad']
                    ?? null
                ),

        ];
    }


    /* =========================================================
       OBTENER FILTROS ACTIVOS
    ========================================================= */

    /**
     * Permite consultar los filtros actualmente
     * establecidos en este servicio.
     */
    public function obtenerFiltros(): array
    {

        return $this->filtros;
    }


    /* =========================================================
       LIMPIAR FILTRO
    ========================================================= */

    /**
     * Los valores vacíos se convierten en null.
     */
    private function limpiarFiltro(
        mixed $valor
    ): ?string {

        if ($valor === null) {

            return null;
        }


        $valor =
            trim(
                (string) $valor
            );


        return (
            $valor !== ''
            ? $valor
            : null
        );
    }


    /* =========================================================
       OPCIONES DE FILTROS
    ========================================================= */

    public function obtenerOpcionesFiltros(): array
    {

        /* =====================================================
           ÁREAS INSTITUCIONALES DESDE PLANTILLA
        ===================================================== */

        $dbPlantilla =
            \Config\Database::connect(
                'plantilla'
            );


        $registrosAreas =
            $dbPlantilla
            ->table('plantilla')
            ->select('AREA')
            ->where(
                'ESTADO',
                'ACTIVO'
            )
            ->where(
                'AREA IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM(AREA) != ''",
                null,
                false
            )
            ->groupBy(
                'AREA'
            )
            ->orderBy(
                'AREA',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $areas = [];

        $areasRegistradas = [];


        foreach (
            $registrosAreas
            as $registro
        ) {

            $valor =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        (string) (
                            $registro['AREA']
                            ?? ''
                        )
                    )
                    ?? ''
                );


            if ($valor === '') {

                continue;
            }


            $clave =
                mb_strtoupper(
                    $valor,
                    'UTF-8'
                );


            if (
                isset(
                    $areasRegistradas[$clave]
                )
            ) {

                continue;
            }


            $areasRegistradas[$clave] =
                true;


            $areas[] =
                $valor;
        }


        /* =====================================================
           GÉNEROS REGISTRADOS
        ===================================================== */

        $registrosGeneros =
            $this->db
            ->table('ai_reportes')
            ->select(
                'genero_quejoso'
            )
            ->where(
                'eliminado',
                0
            )
            ->where(
                'genero_quejoso IS NOT NULL',
                null,
                false
            )
            ->where(
                "TRIM(genero_quejoso) != ''",
                null,
                false
            )
            ->groupBy(
                'genero_quejoso'
            )
            ->orderBy(
                'genero_quejoso',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $generos = [];


        foreach (
            $registrosGeneros
            as $registro
        ) {

            $valor =
                trim(
                    (string) (
                        $registro['genero_quejoso']
                        ?? ''
                    )
                );


            if ($valor !== '') {

                $generos[] =
                    $valor;
            }
        }


        /* =====================================================
           UNIDADES INSTITUCIONALES
        ===================================================== */

        $dbUnidades =
            \Config\Database::connect(
                'unidades'
            );


        $registrosUnidades =
            $dbUnidades
            ->table(
                'parque_vehicular'
            )
            ->select([
                'no_economico',
                'placas',
            ])
            ->groupStart()
                ->where(
                    'no_economico IS NOT NULL',
                    null,
                    false
                )
                ->where(
                    "TRIM(no_economico) != ''",
                    null,
                    false
                )
                ->orGroupStart()
                    ->where(
                        'placas IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        "TRIM(placas) != ''",
                        null,
                        false
                    )
                ->groupEnd()
            ->groupEnd()
            ->orderBy(
                'no_economico',
                'ASC'
            )
            ->orderBy(
                'placas',
                'ASC'
            )
            ->get()
            ->getResultArray();


        $unidades = [];

        $valoresUnidad = [];


        foreach (
            $registrosUnidades
            as $registro
        ) {

            $noEconomico =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        (string) (
                            $registro['no_economico']
                            ?? ''
                        )
                    )
                    ?? ''
                );


            $placas =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        (string) (
                            $registro['placas']
                            ?? ''
                        )
                    )
                    ?? ''
                );


            /*
             * El valor enviado por el filtro será:
             *
             * 1. Número económico.
             * 2. Placas cuando no exista número económico.
             */

            $valor =
                $noEconomico !== ''
                ? $noEconomico
                : $placas;


            if ($valor === '') {

                continue;
            }


            $clave =
                mb_strtoupper(
                    $valor,
                    'UTF-8'
                );


            if (
                isset(
                    $valoresUnidad[$clave]
                )
            ) {

                continue;
            }


            $valoresUnidad[$clave] =
                true;


            if (
                $noEconomico !== ''
                && $placas !== ''
            ) {

                $texto =
                    $noEconomico
                    . ' · '
                    . $placas;

            } else {

                $texto =
                    $valor;
            }


            $unidades[] = [

                'valor' =>
                    $valor,

                'texto' =>
                    $texto,

                'no_economico' =>
                    $noEconomico,

                'placas' =>
                    $placas,

            ];
        }


        return [

            'areas' =>
                $areas,

            'generos' =>
                $generos,

            'unidades' =>
                $unidades,

        ];
    }


    /* =========================================================
       APLICAR FILTROS COMUNES A AI_REPORTES
    ========================================================= */

    public function aplicarFiltrosReportes(
        $builder,
        string $alias = ''
    ) {

        $prefijo =
            $alias !== ''
            ? rtrim(
                $alias,
                '.'
            ) . '.'
            : '';


        /* =====================================================
           SIEMPRE EXCLUIR ELIMINADOS
        ===================================================== */

        $builder->where(
            $prefijo . 'eliminado',
            0
        );


        /* =====================================================
           FECHA DE REGISTRO
        ===================================================== */

        if (
            !empty(
                $this->filtros['fecha_registro_inicio']
            )
        ) {

            $builder->where(
                $prefijo . 'fecha_registro >=',
                $this->filtros['fecha_registro_inicio']
            );
        }


        if (
            !empty(
                $this->filtros['fecha_registro_fin']
            )
        ) {

            $builder->where(
                $prefijo . 'fecha_registro <=',
                $this->filtros['fecha_registro_fin']
            );
        }


        /* =====================================================
           FECHA DE LA QUEJA
        ===================================================== */

        if (
            !empty(
                $this->filtros['fecha_queja_inicio']
            )
        ) {

            $builder->where(
                $prefijo . 'fecha_queja >=',
                $this->filtros['fecha_queja_inicio']
            );
        }


        if (
            !empty(
                $this->filtros['fecha_queja_fin']
            )
        ) {

            $builder->where(
                $prefijo . 'fecha_queja <=',
                $this->filtros['fecha_queja_fin']
            );
        }


        /* =====================================================
           ESTADO ACTUAL
        ===================================================== */

        if (
            !empty(
                $this->filtros['estado']
            )
        ) {

            $builder->where(
                $prefijo . 'estado_actual',
                $this->filtros['estado']
            );
        }


        /* =====================================================
           SEGUIMIENTO
        ===================================================== */

        if (
            !empty(
                $this->filtros['seguimiento']
            )
        ) {

            if (
                $this->filtros['seguimiento']
                === 'con'
            ) {

                $builder->where(
                    "EXISTS (
                        SELECT 1
                        FROM ai_reporte_seguimientos s
                        WHERE s.id_reporte = {$prefijo}id_reporte
                        AND s.eliminado = 0
                    )",
                    null,
                    false
                );
            }


            if (
                $this->filtros['seguimiento']
                === 'sin'
            ) {

                $builder->where(
                    "NOT EXISTS (
                        SELECT 1
                        FROM ai_reporte_seguimientos s
                        WHERE s.id_reporte = {$prefijo}id_reporte
                        AND s.eliminado = 0
                    )",
                    null,
                    false
                );
            }
        }


        /* =====================================================
           ÁREA DEL PERSONAL INVOLUCRADO
        ===================================================== */

        if (
            !empty(
                $this->filtros['area_personal']
            )
        ) {

            $areaPersonal =
                $this->db->escape(
                    $this->filtros['area_personal']
                );


            $builder->where(
                "EXISTS (
                    SELECT 1
                    FROM ai_reporte_personal p_area
                    WHERE p_area.id_reporte = {$prefijo}id_reporte
                    AND p_area.area_snapshot = {$areaPersonal}
                )",
                null,
                false
            );
        }


        /* =====================================================
           TURNO DEL PERSONAL
        ===================================================== */

        if (
            !empty(
                $this->filtros['turno']
            )
        ) {

            $condicionTurno =
                $this->obtenerCondicionSqlTurno(
                    $this->filtros['turno'],
                    'p_turno.turno_snapshot'
                );


            if (
                $condicionTurno !== null
            ) {

                $builder->where(
                    "EXISTS (
                        SELECT 1
                        FROM ai_reporte_personal p_turno
                        WHERE p_turno.id_reporte = {$prefijo}id_reporte
                        AND ({$condicionTurno})
                    )",
                    null,
                    false
                );
            }
        }


        /* =====================================================
           SECTOR DEL PERSONAL INVOLUCRADO
        ===================================================== */

        if (
            !empty(
                $this->filtros['sector']
            )
        ) {

            $sector =
                trim(
                    (string)
                    $this->filtros['sector']
                );


            if (
                preg_match(
                    '/^SECTOR\s+0*([0-9]+)$/i',
                    $sector,
                    $coincidencias
                )
            ) {

                $numeroSector =
                    (int) (
                        $coincidencias[1]
                        ?? 0
                    );


                if (
                    $numeroSector >= 1
                    && $numeroSector <= 15
                ) {

                    $builder->where(
                        "EXISTS (
                            SELECT 1
                            FROM ai_reporte_personal p_sector
                            WHERE p_sector.id_reporte = {$prefijo}id_reporte
                            AND UPPER(
                                TRIM(
                                    COALESCE(
                                        p_sector.area_snapshot,
                                        ''
                                    )
                                )
                            ) REGEXP '^SECTOR[[:space:]]+0*{$numeroSector}([^0-9]|$)'
                        )",
                        null,
                        false
                    );
                }
            }
        }


        /* =====================================================
           UNIDAD
        ===================================================== */

        if (
            !empty(
                $this->filtros['unidad']
            )
        ) {

            $unidad =
                $this->db->escape(
                    $this->filtros['unidad']
                );


            $builder->where(
                "EXISTS (
                    SELECT 1
                    FROM ai_reporte_unidades u_filtro
                    WHERE u_filtro.id_reporte = {$prefijo}id_reporte
                    AND (
                        u_filtro.no_economico_snapshot = {$unidad}
                        OR u_filtro.placas_snapshot = {$unidad}
                    )
                )",
                null,
                false
            );
        }


        return $builder;
    }


    /* =========================================================
       CONDICIÓN SQL PARA TURNO ANALÍTICO
    ========================================================= */

    private function obtenerCondicionSqlTurno(
        string $turno,
        string $campo
    ): ?string {

        return match ($turno) {

            'Primer turno' =>
                "(
                    UPPER(COALESCE({$campo}, '')) LIKE '%PRIMERO%'
                    OR UPPER(COALESCE({$campo}, '')) LIKE '%PRIMER %'
                    OR UPPER(TRIM(COALESCE({$campo}, ''))) = 'PRIMER'
                )",

            'Segundo turno' =>
                "UPPER(COALESCE({$campo}, '')) LIKE '%SEGUNDO%'",

            'Tercer turno' =>
                "(
                    UPPER(COALESCE({$campo}, '')) LIKE '%TERCERO%'
                    OR UPPER(COALESCE({$campo}, '')) LIKE '%TERCER %'
                    OR UPPER(TRIM(COALESCE({$campo}, ''))) = 'TERCER'
                )",

            'Alfa' =>
                "UPPER(COALESCE({$campo}, '')) LIKE '%ALFA%'",

            'Beta' =>
                "UPPER(COALESCE({$campo}, '')) LIKE '%BETA%'",

            'Diario' =>
                "UPPER(COALESCE({$campo}, '')) LIKE '%DIARIO%'",

            'No refiere ni fecha ni horario' =>
                "(
                    {$campo} IS NULL
                    OR TRIM(COALESCE({$campo}, '')) = ''
                    OR UPPER(COALESCE({$campo}, '')) LIKE '%NO REFIERE%'
                    OR UPPER(COALESCE({$campo}, '')) LIKE '%SIN TURNO%'
                )",

            default =>
                null,
        };
    }


    /* =========================================================
       CONDICIÓN SQL PARA ZONA
    ========================================================= */

    /**
     * La zona no se almacena directamente.
     * Se obtiene a partir del sector institucional.
     */
    private function obtenerCondicionSqlZona(
        string $zona,
        string $campo
    ): ?string {

        return match ($zona) {

            /* =================================================
               ZONA NORTE
               Sectores 1 - 3
            ================================================= */

            'Zona Norte' =>
                "UPPER(TRIM(COALESCE({$campo}, '')))
                 REGEXP '^SECTOR[[:space:]]+0*(1|2|3)([^0-9]|$)'",


            /* =================================================
               ZONA PONIENTE
               Sectores 4 - 7
            ================================================= */

            'Zona Poniente' =>
                "UPPER(TRIM(COALESCE({$campo}, '')))
                 REGEXP '^SECTOR[[:space:]]+0*(4|5|6|7)([^0-9]|$)'",


            /* =================================================
               ZONA CENTRO
               Sectores 8 - 10
            ================================================= */

            'Zona Centro' =>
                "UPPER(TRIM(COALESCE({$campo}, '')))
                 REGEXP '^SECTOR[[:space:]]+0*(8|9|10)([^0-9]|$)'",


            /* =================================================
               ZONA ORIENTE
               Sectores 11 - 15
            ================================================= */

            'Zona Oriente' =>
                "UPPER(TRIM(COALESCE({$campo}, '')))
                 REGEXP '^SECTOR[[:space:]]+0*(11|12|13|14|15)([^0-9]|$)'",


            default =>
                null,
        };
    }

}