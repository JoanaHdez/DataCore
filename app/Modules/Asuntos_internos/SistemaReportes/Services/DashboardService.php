<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

class DashboardService
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
     * Define los filtros que utilizarán todas las consultas
     * del Dashboard.
     */
    public function establecerFiltros(
        array $filtros
    ): void {

        $this->filtros = [

            /* =================================================
               FECHAS
            ================================================= */

            'fecha_inicio' =>
                $this->limpiarFiltro(
                    $filtros['fecha_inicio']
                    ?? null
                ),

            'fecha_fin' =>
                $this->limpiarFiltro(
                    $filtros['fecha_fin']
                    ?? null
                ),

            'periodo' =>
                $this->limpiarFiltro(
                    $filtros['periodo']
                    ?? null
                ),

            'tipo_fecha' =>
                $this->limpiarFiltro(
                    $filtros['tipo_fecha']
                    ?? 'registro'
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

            'evidencia' =>
                $this->limpiarFiltro(
                    $filtros['evidencia']
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


            /* =================================================
               QUEJOSO
            ================================================= */

            'genero' =>
                $this->limpiarFiltro(
                    $filtros['genero']
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


    /**
     * Limpia valores provenientes de GET.
     *
     * Los valores vacíos se convierten en null para que
     * posteriormente sea sencillo saber si un filtro está
     * realmente activo.
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

           La plantilla es la fuente oficial del catálogo de
           áreas. El filtro se aplica posteriormente contra
           area_snapshot para conservar el dato histórico del
           reporte.
        ===================================================== */

        $dbPlantilla =
            \Config\Database::connect(
                'plantilla'
            );


        $registrosAreas =
            $dbPlantilla
            ->table('plantilla')
            ->select('AREA')
            ->where('ESTADO', 'ACTIVO')
            ->where('AREA IS NOT NULL', null, false)
            ->where("TRIM(AREA) != ''", null, false)
            ->groupBy('AREA')
            ->orderBy('AREA', 'ASC')
            ->get()
            ->getResultArray();


        $areas = [];
        $areasRegistradas = [];


        foreach ($registrosAreas as $registro) {

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


            if (isset($areasRegistradas[$clave])) {
                continue;
            }


            $areasRegistradas[$clave] = true;
            $areas[] = $valor;
        }


        /* =====================================================
           GÉNEROS REGISTRADOS
        ===================================================== */

        $registrosGeneros =
            $this->db
            ->table('ai_reportes')
            ->select('genero_quejoso')
            ->where('eliminado', 0)
            ->where('genero_quejoso IS NOT NULL', null, false)
            ->where("TRIM(genero_quejoso) != ''", null, false)
            ->groupBy('genero_quejoso')
            ->orderBy('genero_quejoso', 'ASC')
            ->get()
            ->getResultArray();


        $generos = [];


        foreach ($registrosGeneros as $registro) {

            $valor =
                trim(
                    (string) (
                        $registro['genero_quejoso']
                        ?? ''
                    )
                );


            if ($valor !== '') {
                $generos[] = $valor;
            }
        }


        /* =====================================================
           UNIDADES INVOLUCRADAS
        /* =====================================================
           UNIDADES INSTITUCIONALES DESDE PARQUE VEHICULAR

           parque_vehicular es la fuente oficial del catálogo
           de unidades disponibles para el filtro.

           IMPORTANTE:
           Este catálogo solamente llena el SELECT.

           Cuando el usuario aplica el filtro, la búsqueda
           continúa realizándose contra:

           ai_reporte_unidades.no_economico_snapshot
           ai_reporte_unidades.placas_snapshot

           De esta manera conservamos el dato histórico que
           tenía la unidad cuando fue relacionada al reporte.
        ===================================================== */

        $dbUnidades =
            \Config\Database::connect(
                'unidades'
            );


        $registrosUnidades =
            $dbUnidades
            ->table('parque_vehicular')
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

        /*
         * Evita repetir unidades cuando existan registros
         * duplicados en parque_vehicular.
         *
         * La comparación se realiza sin distinguir
         * mayúsculas/minúsculas.
         */

        $valoresUnidad =
            [];


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
             * El valor que viaja en el filtro será:
             *
             * 1. Número económico, cuando exista.
             * 2. Placas, cuando no exista número económico.
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


            /*
             * Texto mostrado al usuario.
             *
             * Si existen ambos datos:
             *
             * 1234 · ABC-123
             *
             * Si solamente existe uno:
             *
             * 1234
             *
             * o
             *
             * ABC-123
             */

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
            'areas' => $areas,
            'generos' => $generos,
            'unidades' => $unidades,
        ];
    }


    /* =========================================================
   APLICAR FILTROS COMUNES A AI_REPORTES
========================================================= */

    private function aplicarFiltrosReportes(
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
           FECHA A ANALIZAR
        ===================================================== */

        $tipoFecha =
            $this->filtros['tipo_fecha']
            ?? 'registro';


        $campoFecha =
            match ($tipoFecha) {

                'queja' =>
                    $prefijo . 'fecha_queja',

                'hechos' =>
                    $prefijo . 'fecha_hechos',

                'acuerdo' =>
                    $prefijo . 'fecha_acuerdo',

                default =>
                    $prefijo . 'fecha_registro',
            };


        if (
            !empty($this->filtros['fecha_inicio'])
        ) {

            $builder->where(
                $campoFecha . ' >=',
                $this->filtros['fecha_inicio']
            );
        }


        if (
            !empty($this->filtros['fecha_fin'])
        ) {

            $builder->where(
                $campoFecha . ' <=',
                $this->filtros['fecha_fin']
            );
        }


        /* =====================================================
           ESTADO ACTUAL
        ===================================================== */

        if (
            !empty($this->filtros['estado'])
        ) {

            $builder->where(
                $prefijo . 'estado_actual',
                $this->filtros['estado']
            );
        }


        /* =====================================================
           GÉNERO DEL QUEJOSO
        ===================================================== */

        if (
            !empty($this->filtros['genero'])
        ) {

            $builder->where(
                $prefijo . 'genero_quejoso',
                $this->filtros['genero']
            );
        }


        /* =====================================================
           SEGUIMIENTO
        ===================================================== */

        if (
            !empty($this->filtros['seguimiento'])
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
           EVIDENCIA
        ===================================================== */

        if (
            !empty($this->filtros['evidencia'])
        ) {

            if (
                $this->filtros['evidencia']
                === 'con'
            ) {

                $builder->where(
                    "EXISTS (
                        SELECT 1
                        FROM ai_reporte_evidencias e
                        WHERE e.id_reporte = {$prefijo}id_reporte
                        AND e.eliminado = 0
                    )",
                    null,
                    false
                );
            }


            if (
                $this->filtros['evidencia']
                === 'sin'
            ) {

                $builder->where(
                    "NOT EXISTS (
                        SELECT 1
                        FROM ai_reporte_evidencias e
                        WHERE e.id_reporte = {$prefijo}id_reporte
                        AND e.eliminado = 0
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
            !empty($this->filtros['area_personal'])
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

           El SELECT usa categorías analíticas del Dashboard,
           no los textos crudos almacenados en turno_snapshot.
        ===================================================== */

        if (
            !empty($this->filtros['turno'])
        ) {

            $condicionTurno =
                $this->obtenerCondicionSqlTurno(
                    $this->filtros['turno'],
                    'p_turno.turno_snapshot'
                );


            if ($condicionTurno !== null) {

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
           UNIDAD
        ===================================================== */

        if (
            !empty($this->filtros['unidad'])
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
       INDICADORES GENERALES
    ========================================================= */

    public function obtenerIndicadores(): array
    {
        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'COUNT(*) AS total',

                "SUM(
                CASE
                    WHEN r.estado_actual = 'Pendiente'
                    THEN 1
                    ELSE 0
                END
            ) AS pendientes",

                "SUM(
                CASE
                    WHEN r.estado_actual = 'En proceso'
                    THEN 1
                    ELSE 0
                END
            ) AS en_proceso",

                "SUM(
                CASE
                    WHEN r.estado_actual = 'Finalizado'
                    THEN 1
                    ELSE 0
                END
            ) AS finalizados",
            ], false);


        /* =====================================================
       FILTROS DEL DASHBOARD
    ===================================================== */

        $this->aplicarFiltrosReportes(
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


        return [

            'total' =>
            (int) (
                $resultado['total']
                ?? 0
            ),

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
        ];
    }


    /* =========================================================
       QUEJAS POR SECTORES Y TURNOS
    ========================================================= */

    public function obtenerSectoresTurnos(): array
    {
        /* =====================================================
           SECTORES INSTITUCIONALES
        ===================================================== */

        $sectores = [];


        for (
            $numero = 1;
            $numero <= 15;
            $numero++
        ) {

            $sectores[] =
                'SECTOR ' . $numero;
        }


        /* =====================================================
           TURNOS DEL DASHBOARD
        ===================================================== */

        $turnos = [
            'Primer turno',
            'Segundo turno',
            'Tercer turno',
            'Alfa',
            'Beta',
            'Diario',
            'No refiere ni fecha ni horario',
        ];


        /* =====================================================
           INICIALIZAR CONTEOS
        ===================================================== */

        $conteos = [];


        foreach (
            $turnos
            as $turno
        ) {

            $conteos[$turno] =
                array_fill(
                    0,
                    count($sectores),
                    0
                );
        }


        /* =====================================================
           CONSULTAR REPORTES + PERSONAL
        ===================================================== */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.area_snapshot AS area',
                'p.turno_snapshot AS turno',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'p.area_snapshot',
                'p.turno_snapshot',
            ])
            ->get()
            ->getResultArray();


        /* =====================================================
           EVITAR DUPLICADOS

           Una misma queja puede tener varias personas.

           Si varias pertenecen al mismo sector
           y misma familia de turno, contamos
           la queja una sola vez.
        ===================================================== */

        $combinacionesContadas =
            [];


        /* =====================================================
           CONTABILIZAR
        ===================================================== */

        foreach (
            $registros
            as $registro
        ) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            if ($idReporte <= 0) {
                continue;
            }


            /* =================================================
               OBTENER SECTOR DESDE EL ÁREA
            ================================================= */

            $sector =
                $this->obtenerSectorDesdeArea(
                    (string) (
                        $registro['area']
                        ?? ''
                    )
                );


            /*
             * Si la persona no pertenece a uno
             * de los 15 sectores, esta queja
             * no forma parte de esta gráfica.
             */

            if ($sector === null) {
                continue;
            }


            /* =================================================
               CLASIFICAR TURNO
            ================================================= */

            $turno =
                $this->clasificarTurnoDashboard(
                    (string) (
                        $registro['turno']
                        ?? ''
                    )
                );


            /*
             * Si el turno no pertenece a una
             * categoría contemplada, no lo
             * clasificamos arbitrariamente.
             */

            if ($turno === null) {
                continue;
            }


            if (
                !isset(
                    $conteos[$turno]
                )
            ) {
                continue;
            }


            /* =================================================
               BUSCAR ÍNDICE DEL SECTOR
            ================================================= */

            $indiceSector =
                array_search(
                    $sector,
                    $sectores,
                    true
                );


            if (
                $indiceSector === false
            ) {
                continue;
            }


            /* =================================================
               EVITAR CONTAR DOS VECES

               reporte + sector + turno
            ================================================= */

            $clave =
                $idReporte
                . '|'
                . $sector
                . '|'
                . $turno;


            if (
                isset(
                    $combinacionesContadas[$clave]
                )
            ) {
                continue;
            }


            $combinacionesContadas[$clave] = true;


            /* =================================================
               SUMAR
            ================================================= */

            $conteos[$turno][$indiceSector]++;
        }


        /* =====================================================
           RESPUESTA
        ===================================================== */

        return [
            'sectores' =>
            $sectores,

            'turnos' =>
            $conteos,
        ];
    }


    /* =========================================================
       OBTENER SECTOR DESDE AREA_SNAPSHOT

       Ejemplos:

       SECTOR 01 CAMPESTRE
           → SECTOR 1

       SECTOR 02 ARAGON
           → SECTOR 2

       SECTOR 15 REFORMA
           → SECTOR 15
    ========================================================= */

    private function obtenerSectorDesdeArea(
        string $area
    ): ?string {

        $area =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    mb_strtoupper(
                        $area,
                        'UTF-8'
                    )
                )
                    ?? ''
            );


        if ($area === '') {
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


        return (
            'SECTOR '
            . $numero
        );
    }


    /* =========================================================
       CLASIFICAR TURNO

       IMPORTANTE:
       La plantilla tiene muchas variantes.

       Las agrupamos por familia.
    ========================================================= */

    private function clasificarTurnoDashboard(
        string $turno
    ): ?string {

        $turno =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    mb_strtoupper(
                        $turno,
                        'UTF-8'
                    )
                )
                    ?? ''
            );


        /* =====================================================
           SIN TURNO
        ===================================================== */

        if ($turno === '') {

            return (
                'No refiere ni fecha ni horario'
            );
        }


        /* =====================================================
           PRIMER TURNO
        ===================================================== */

        if (
            preg_match(
                '/\bPRIMERO\b/u',
                $turno
            )
            || preg_match(
                '/\bPRIMER\b/u',
                $turno
            )
        ) {

            return 'Primer turno';
        }


        /* =====================================================
           SEGUNDO TURNO
        ===================================================== */

        if (
            preg_match(
                '/\bSEGUNDO\b/u',
                $turno
            )
        ) {

            return 'Segundo turno';
        }


        /* =====================================================
           TERCER TURNO
        ===================================================== */

        if (
            preg_match(
                '/\bTERCERO\b/u',
                $turno
            )
            || preg_match(
                '/\bTERCER\b/u',
                $turno
            )
        ) {

            return 'Tercer turno';
        }


        /* =====================================================
           ALFA
        ===================================================== */

        if (
            preg_match(
                '/\bALFA\b/u',
                $turno
            )
        ) {

            return 'Alfa';
        }


        /* =====================================================
           BETA
        ===================================================== */

        if (
            preg_match(
                '/\bBETA\b/u',
                $turno
            )
        ) {

            return 'Beta';
        }


        /* =====================================================
           DIARIO
        ===================================================== */

        if (
            preg_match(
                '/\bDIARIO\b/u',
                $turno
            )
        ) {

            return 'Diario';
        }


        /* =====================================================
           NO REFIERE
        ===================================================== */

        if (
            str_contains(
                $turno,
                'NO REFIERE'
            )
            || str_contains(
                $turno,
                'SIN TURNO'
            )
        ) {

            return (
                'No refiere ni fecha ni horario'
            );
        }


        /* =====================================================
           OTROS

           DELTA
           GAMA
           UNICO
           24 HRS. POR 48 HRS.
           etc.

           No se asignan arbitrariamente.
        ===================================================== */

        return null;
    }

    /* =========================================================
   QUEJAS POR ÁREA
========================================================= */

    public function obtenerQuejasPorArea(): array
    {
        /*
     * Contamos reportes, no personas.
     *
     * Si una queja tiene varias personas pertenecientes
     * a la misma área, solamente cuenta una vez para
     * dicha área.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.area_snapshot AS area',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            )
            ->where(
                'p.area_snapshot IS NOT NULL',
                null,
                false
            )
            ->where(
                'p.area_snapshot !=',
                ''
            );

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->groupBy([
                'r.id_reporte',
                'p.area_snapshot',
            ])
            ->get()
            ->getResultArray();


        /* =====================================================
       AGRUPAR
    ===================================================== */

        $conteos = [];


        foreach ($registros as $registro) {

            $area =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        mb_strtoupper(
                            (string) (
                                $registro['area']
                                ?? ''
                            ),
                            'UTF-8'
                        )
                    )
                        ?? ''
                );


            if ($area === '') {
                continue;
            }


            /*
         * Los sectores conservan también su nombre
         * institucional completo:
         *
         * SECTOR 01 CAMPESTRE
         * SECTOR 02 ARAGON
         * etc.
         *
         * Aquí NO los reducimos a "SECTOR 1",
         * porque esta gráfica analiza áreas.
         */

            if (
                !isset(
                    $conteos[$area]
                )
            ) {

                $conteos[$area] = 0;
            }


            $conteos[$area]++;
        }


        /* =====================================================
       ORDENAR DE MAYOR A MENOR
    ===================================================== */

        arsort(
            $conteos,
            SORT_NUMERIC
        );


        /* =====================================================
       FORMATO PARA CHART.JS
    ===================================================== */

        $areas = [];
        $totales = [];


        foreach (
            $conteos
            as $area => $total
        ) {

            $areas[] =
                $area;


            $totales[] =
                (int) $total;
        }


        return [
            'areas' =>
            $areas,

            'totales' =>
            $totales,
        ];
    }

    /* =========================================================
   QUEJAS POR TURNO
========================================================= */

    public function obtenerQuejasPorTurno(): array
    {
        /*
     * Esta gráfica cuenta QUEJAS / REPORTES,
     * no cantidad de personas.
     *
     * A diferencia de "Sectores y turnos",
     * aquí NO importa el área del personal.
     * Analizamos todos los reportes vigentes
     * que tengan personal relacionado.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'p.turno_snapshot AS turno',
            ])
            ->join(
                'ai_reporte_personal p',
                'p.id_reporte = r.id_reporte',
                'inner'
            );

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
       CATEGORÍAS DEL DASHBOARD
    ===================================================== */

        $conteos = [
            'Primer turno' => 0,
            'Segundo turno' => 0,
            'Tercer turno' => 0,
            'Alfa' => 0,
            'Beta' => 0,
            'Diario' => 0,
            'No refiere ni fecha ni horario' => 0,
        ];


        /*
     * Nos permite evitar que una misma queja
     * se cuente varias veces cuando tiene varios
     * elementos pertenecientes al mismo turno.
     *
     * Ejemplo:
     *
     * QJ-001
     * - Oficial A → SEGUNDO
     * - Oficial B → SEGUNDO
     * - Oficial C → SEGUNDO
     *
     * Resultado:
     * Segundo turno +1
     *
     * No +3.
     */

        $reportesContados = [];


        foreach ($registros as $registro) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            if ($idReporte <= 0) {
                continue;
            }


            $turno =
                $this->clasificarTurnoDashboard(
                    (string) (
                        $registro['turno']
                        ?? ''
                    )
                );


            /*
         * Algunos turnos reales todavía no tienen
         * una regla institucional definida:
         *
         * DELTA
         * GAMA
         * UNICO
         * 24x48
         * etc.
         *
         * No los asignamos arbitrariamente.
         */

            if (
                $turno === null
                || !array_key_exists(
                    $turno,
                    $conteos
                )
            ) {
                continue;
            }


            $clave =
                $idReporte
                . '|'
                . $turno;


            if (
                isset(
                    $reportesContados[$clave]
                )
            ) {
                continue;
            }


            $reportesContados[$clave] =
                true;


            $conteos[$turno]++;
        }


        /* =====================================================
       RESPUESTA
    ===================================================== */

        return [
            'turnos' => [
                'Primer turno',
                'Segundo turno',
                'Tercer turno',
                'Alfa',
                'Beta',
                'Diario',
                'No refiere ni fecha ni horario',
            ],

            'totales' => [
                $conteos['Primer turno'],
                $conteos['Segundo turno'],
                $conteos['Tercer turno'],
                $conteos['Alfa'],
                $conteos['Beta'],
                $conteos['Diario'],
                $conteos['No refiere ni fecha ni horario'],
            ],

            'total' =>
            array_sum(
                $conteos
            ),
        ];
    }

    /* =========================================================
   RESOLUCIÓN GENERAL
========================================================= */

    public function obtenerResoluciones(): array
    {
        /*
     * Contamos reportes vigentes agrupados por
     * la resolución registrada en ai_reportes.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.resolucion',
            ]);

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->get()
            ->getResultArray();

        /* =====================================================
       AGRUPAR RESOLUCIONES
    ===================================================== */

        $conteos = [];


        foreach ($registros as $registro) {

            $resolucion =
                trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        (string) (
                            $registro['resolucion']
                            ?? ''
                        )
                    )
                        ?? ''
                );


            /*
         * Si todavía no existe una resolución,
         * no inventamos una categoría.
         *
         * Los reportes pendientes o en proceso
         * pueden legítimamente no tenerla todavía.
         */

            if ($resolucion === '') {
                continue;
            }


            /*
         * Normalizamos únicamente para evitar
         * duplicados provocados por diferencias
         * de mayúsculas/minúsculas.
         */

            $clave =
                mb_strtoupper(
                    $resolucion,
                    'UTF-8'
                );


            if (
                !isset(
                    $conteos[$clave]
                )
            ) {

                $conteos[$clave] = [
                    'nombre' =>
                    $resolucion,

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

        $resoluciones = [];
        $totales = [];


        foreach ($conteos as $dato) {

            $resoluciones[] =
                $dato['nombre'];


            $totales[] =
                (int) $dato['total'];
        }


        return [
            'resoluciones' =>
            $resoluciones,

            'totales' =>
            $totales,

            'total' =>
            array_sum(
                $totales
            ),
        ];
    }

    /* =========================================================
   CATÁLOGO GENERAL
   QUEJAS POR CLASIFICACIÓN
========================================================= */

    public function obtenerClasificaciones(): array
    {
        /*
     * Contamos reportes vigentes agrupados por
     * la clasificación registrada en ai_reportes.
     *
     * No utilizamos las categorías temporales del Excel:
     * el Dashboard mostrará las clasificaciones que
     * realmente existan en la base de datos.
     */

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.clasificacion',
            ]);

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->get()
            ->getResultArray();


        /* =====================================================
       AGRUPAR CLASIFICACIONES
    ===================================================== */

        $conteos = [];


        foreach ($registros as $registro) {

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
         * Una clasificación vacía no representa
         * una categoría real.
         */

            if ($clasificacion === '') {
                continue;
            }


            /*
         * Utilizamos una clave normalizada para evitar
         * separar valores únicamente por diferencias
         * entre mayúsculas y minúsculas.
         *
         * Ejemplo:
         *
         * Extorsión
         * EXTORSIÓN
         * extorsión
         *
         * se contabilizan como una sola clasificación.
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


        foreach ($conteos as $dato) {

            $clasificaciones[] =
                $dato['nombre'];


            $totales[] =
                (int) $dato['total'];
        }


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

    /* =========================================================
   REPORTES RECIENTES
========================================================= */

    /* =========================================================
   REPORTES RECIENTES
========================================================= */

    public function obtenerReportesRecientes(
        int $limite = 6
    ): array {

        $builder =
            $this->db
            ->table('ai_reportes r')
            ->select([
                'r.id_reporte',
                'r.folio',
                'r.fecha_registro',
                'r.expediente',
                'r.clasificacion',
                'r.estado_actual',
            ]);

        $this->aplicarFiltrosReportes(
            $builder,
            'r'
        );

        $registros =
            $builder
            ->orderBy(
                'r.fecha_registro',
                'DESC'
            )
            ->orderBy(
                'r.id_reporte',
                'DESC'
            )
            ->limit(
                $limite
            )
            ->get()
            ->getResultArray();


        $reportes = [];


        foreach ($registros as $registro) {

            $idReporte =
                (int) (
                    $registro['id_reporte']
                    ?? 0
                );


            /* =====================================================
           ÁREA DEL PERSONAL RELACIONADO
        ===================================================== */

            $area =
                '—';


            if ($idReporte > 0) {

                $personal =
                    $this->db
                    ->table('ai_reporte_personal')
                    ->select(
                        'area_snapshot'
                    )
                    ->where(
                        'id_reporte',
                        $idReporte
                    )
                    ->where(
                        'area_snapshot IS NOT NULL',
                        null,
                        false
                    )
                    ->where(
                        'area_snapshot !=',
                        ''
                    )
                    ->orderBy(
                        'id_reporte_personal',
                        'ASC'
                    )
                    ->limit(1)
                    ->get()
                    ->getRowArray();


                if (
                    !empty($personal['area_snapshot'])
                ) {

                    $area =
                        trim(
                            (string) $personal['area_snapshot']
                        );
                }
            }


            /* =====================================================
           FECHA
        ===================================================== */

            $fecha =
                $registro['fecha_registro']
                ?? null;


            $fechaFormateada =
                '—';


            if (!empty($fecha)) {

                $timestamp =
                    strtotime(
                        (string) $fecha
                    );


                if ($timestamp !== false) {

                    $fechaFormateada =
                        date(
                            'd/m/Y',
                            $timestamp
                        );
                }
            }


            /* =====================================================
           RESPUESTA
        ===================================================== */

            $reportes[] = [

                'id_reporte' =>
                $idReporte,

                'folio' =>
                trim(
                    (string) (
                        $registro['folio']
                        ?? ''
                    )
                ),

                'fecha' =>
                $fechaFormateada,

                'expediente' =>
                trim(
                    (string) (
                        $registro['expediente']
                        ?? ''
                    )
                ),

                'clasificacion' =>
                trim(
                    (string) (
                        $registro['clasificacion']
                        ?? ''
                    )
                ),

                'area' =>
                $area,

                'estado' =>
                trim(
                    (string) (
                        $registro['estado_actual']
                        ?? ''
                    )
                ),
            ];
        }


        return $reportes;
    }
}
