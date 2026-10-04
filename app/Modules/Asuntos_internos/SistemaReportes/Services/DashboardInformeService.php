<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

/* =========================================================
   GENERADOR DE ANALISIS DASHBOARD
   PREPARACION DE DATOS PARA IA / WORD FUTURO
   =========================================================
   Este servicio es la entrada tecnica del generador. Recibe
   filtros, secciones y configuracion del modal; reutiliza el
   DashboardService existente; y prepara datos agregados.

   Seguridad:
   - Oculta el filtro Personal de los filtros devueltos.
   - Expone solo el booleano filtro_personal_aplicado.
   - No incluye ranking_personal ni personal_individual.
   - No prepara folios ni detalles individuales.

   TODO IA/WORD:
   La generacion real de Word editable queda pendiente hasta
   confirmar dependencia PhpWord o mecanismo equivalente.
   ========================================================= */

class DashboardInformeService
{
    private const SECCIONES_PERMITIDAS = [
        'indicadores',
        'catalogo',
        'evolucion',
        'estado',
        'zona',
        'turno',
        'sector',
        'area',
        'unidad',
        'sanciones',
        'cruce',
        'ranking_sector',
        'ranking_area',
        'ranking_unidad',
        'hallazgos',
        'comparativa',
    ];

    private const SECCIONES_SOLO_QUEJAS = [
        'catalogo',
        'estado',
        'sanciones',
        'comparativa',
    ];

    private const FILTROS_PERMITIDOS = [
        'fecha_registro_inicio',
        'fecha_registro_fin',
        'tipo',
        'estado',
        'clasificacion',
        'seguimiento',
        'es_anonimo',
        'zona',
        'sector',
        'turno',
        'area_personal',
        'personal',
        'unidad',
    ];

    private const DIMENSIONES = [
        'area',
        'unidad',
    ];

    private const CRUCE_PRINCIPAL = [
        'sector',
        'zona',
        'area',
        'turno',
    ];

    private const CRUCE_SECUNDARIAS = [
        'sector' => [
            'turno',
            'estado',
        ],
        'zona' => [
            'turno',
            'estado',
        ],
        'area' => [
            'turno',
            'estado',
        ],
        'turno' => [
            'estado',
        ],
    ];

    public function preparar(
        array $filtros,
        array $secciones,
        array $configuracion = []
    ): array {

        $filtrosNormalizados =
            $this->normalizarFiltros(
                $filtros
            );

        $tipo =
            $this->obtenerTipoActivo(
                $filtrosNormalizados
            );

        $configuracionNormalizada =
            $this->normalizarConfiguracion(
                $configuracion,
                $tipo
            );

        $seccionesNormalizadas =
            $this->normalizarSecciones(
                $secciones,
                $tipo
            );

        if ($seccionesNormalizadas === []) {

            throw new \InvalidArgumentException(
                'Selecciona al menos una seccion valida para preparar el analisis.'
            );
        }

        $dashboardService =
            new DashboardService();

        $dashboardService->establecerFiltros(
            $filtrosNormalizados
        );

        $datosSecciones =
            $this->obtenerSecciones(
                $dashboardService,
                $seccionesNormalizadas,
                $configuracionNormalizada,
                $tipo
            );

        return [
            'ok' =>
                true,

            'filtros' =>
                $this->ocultarFiltroPersonal(
                    $filtrosNormalizados
                ),

            'filtros_activos' =>
                $this->obtenerFiltrosActivos(
                    $filtrosNormalizados
                ),

            'periodo' =>
                $this->construirPeriodo(
                    $filtrosNormalizados
                ),

            'tipo' =>
                $tipo,

            'filtro_personal_aplicado' =>
                $this->tieneFiltroPersonal(
                    $filtrosNormalizados
                ),

            'configuracion' =>
                $configuracionNormalizada,

            'secciones_solicitadas' =>
                $seccionesNormalizadas,

            'secciones' =>
                $datosSecciones,
        ];
    }

    private function obtenerSecciones(
        DashboardService $dashboardService,
        array $secciones,
        array $configuracion,
        string $tipo
    ): array {

        $datos = [];

        foreach ($secciones as $seccion) {

            $datos[$seccion] =
                match ($seccion) {
                    'indicadores' =>
                        $dashboardService->obtenerIndicadores(),

                    'catalogo' =>
                        $dashboardService->obtenerClasificaciones(),

                    'evolucion' =>
                        $dashboardService->obtenerEvolucionTemporal(),

                    'estado' => [
                        'compatible' =>
                            $tipo !== 'FELICITACION',

                        'datos' =>
                            $dashboardService->obtenerEstadosQuejas(),
                    ],

                    'zona' =>
                        $dashboardService->obtenerQuejasPorZona(),

                    'turno' =>
                        $dashboardService->obtenerQuejasPorTurno(),

                    'sector' =>
                        $dashboardService->obtenerQuejasPorSector(),

                    'area' =>
                        $dashboardService->obtenerDimension(
                            'area'
                        ),

                    'unidad' =>
                        $dashboardService->obtenerDimension(
                            'unidad'
                        ),

                    'sanciones' =>
                        $dashboardService->obtenerSanciones(),

                    'cruce' =>
                        $dashboardService->obtenerCruce(
                            $configuracion['cruce_principal'],
                            $configuracion['cruce_secundaria']
                        ),

                    'ranking_sector' =>
                        $dashboardService->obtenerRanking(
                            'sector'
                        ),

                    'ranking_area' =>
                        $dashboardService->obtenerRanking(
                            'area'
                        ),

                    'ranking_unidad' =>
                        $dashboardService->obtenerRanking(
                            'unidad'
                        ),

                    'hallazgos' =>
                        $this->obtenerHallazgos(
                            $dashboardService,
                            $configuracion,
                            $tipo
                        ),

                    'comparativa' =>
                        $dashboardService->obtenerComparativa(),

                    default =>
                        [],
                };
        }

        return $datos;
    }

    private function obtenerHallazgos(
        DashboardService $dashboardService,
        array $configuracion,
        string $tipo
    ): array {

        $servicio =
            new DashboardHallazgosService();

        return $servicio->construir(
            $dashboardService->obtenerEstadosQuejas(),
            $dashboardService->obtenerQuejasPorSector(),
            $dashboardService->obtenerQuejasPorZona(),
            $dashboardService->obtenerQuejasPorTurno(),
            $dashboardService->obtenerDimension(
                $configuracion['dimension']
            ),
            $tipo === 'FELICITACION'
        );
    }

    private function normalizarConfiguracion(
        array $configuracion,
        string $tipo
    ): array {

        $dimension =
            $this->normalizarOpcion(
                $configuracion['dimension']
                ?? 'area',
                self::DIMENSIONES,
                'area'
            );

        $crucePrincipal =
            $this->normalizarOpcion(
                $configuracion['cruce_principal']
                ?? 'sector',
                self::CRUCE_PRINCIPAL,
                'sector'
            );

        if (
            $tipo === 'FELICITACION'
            && $crucePrincipal === 'turno'
        ) {

            $crucePrincipal =
                'sector';
        }

        $secundariasPermitidas =
            self::CRUCE_SECUNDARIAS[$crucePrincipal]
            ?? [
                'turno',
            ];

        $cruceSecundaria =
            $this->normalizarOpcion(
                $configuracion['cruce_secundaria']
                ?? $secundariasPermitidas[0],
                $secundariasPermitidas,
                $secundariasPermitidas[0]
            );

        if (
            $tipo === 'FELICITACION'
            && $cruceSecundaria === 'estado'
        ) {

            $cruceSecundaria =
                'turno';
        }

        return [
            'dimension' =>
                $dimension,

            'cruce_principal' =>
                $crucePrincipal,

            'cruce_secundaria' =>
                $cruceSecundaria,
        ];
    }

    private function normalizarFiltros(
        array $filtros
    ): array {

        $normalizados = [];

        foreach (self::FILTROS_PERMITIDOS as $filtro) {

            $normalizados[$filtro] =
                $filtro === 'personal'
                    ? $this->limpiarValorPersonal(
                        $filtros[$filtro]
                        ?? null
                    )
                    : $this->limpiarValor(
                        $filtros[$filtro]
                        ?? null
                    );
        }

        if (
            $this->obtenerTipoActivo(
                $normalizados
            ) === 'FELICITACION'
        ) {

            $normalizados['estado'] = null;
            $normalizados['clasificacion'] = null;
            $normalizados['seguimiento'] = null;
            $normalizados['es_anonimo'] = null;
        }

        return $normalizados;
    }

    private function normalizarSecciones(
        array $secciones,
        string $tipo
    ): array {

        $normalizadas = [];

        foreach ($secciones as $seccion) {

            $seccion =
                strtolower(
                    trim(
                        (string) $seccion
                    )
                );

            if (
                $seccion === ''
                || !in_array(
                    $seccion,
                    self::SECCIONES_PERMITIDAS,
                    true
                )
            ) {

                continue;
            }

            if (
                $tipo === 'FELICITACION'
                && in_array(
                    $seccion,
                    self::SECCIONES_SOLO_QUEJAS,
                    true
                )
            ) {

                continue;
            }

            $normalizadas[$seccion] =
                true;
        }

        return array_keys(
            $normalizadas
        );
    }

    private function obtenerFiltrosActivos(
        array $filtros
    ): array {

        $activos = [];

        foreach ($filtros as $campo => $valor) {

            if ($valor === null || $valor === '') {

                continue;
            }

            $activos[$campo] =
                $valor;
        }

        return $this->ocultarFiltroPersonal(
            $activos
        );
    }

    private function ocultarFiltroPersonal(
        array $filtros
    ): array {

        unset(
            $filtros['personal']
        );

        return $filtros;
    }

    private function tieneFiltroPersonal(
        array $filtros
    ): bool {

        return $this->limpiarValorPersonal(
            $filtros['personal']
            ?? null
        ) !== null;
    }

    private function construirPeriodo(
        array $filtros
    ): array {

        $inicio =
            $filtros['fecha_registro_inicio']
            ?? null;

        $fin =
            $filtros['fecha_registro_fin']
            ?? null;

        if ($inicio && $fin) {

            $texto =
                $this->formatearFecha(
                    $inicio
                )
                . ' al '
                . $this->formatearFecha(
                    $fin
                );

        } elseif ($inicio) {

            $texto =
                'Desde '
                . $this->formatearFecha(
                    $inicio
                );

        } elseif ($fin) {

            $texto =
                'Hasta '
                . $this->formatearFecha(
                    $fin
                );

        } else {

            $texto =
                'Todo el periodo disponible';
        }

        return [
            'inicio' =>
                $inicio,

            'fin' =>
                $fin,

            'texto' =>
                $texto,
        ];
    }

    private function normalizarOpcion(
        mixed $valor,
        array $permitidos,
        string $predeterminado
    ): string {

        $valor =
            strtolower(
                trim(
                    (string) $valor
                )
            );

        return in_array(
            $valor,
            $permitidos,
            true
        )
            ? $valor
            : $predeterminado;
    }

    private function limpiarValor(
        mixed $valor
    ): ?string {

        if ($valor === null) {

            return null;
        }

        $valor =
            trim(
                (string) $valor
            );

        return $valor !== ''
            ? $valor
            : null;
    }

    private function limpiarValorPersonal(
        mixed $valor
    ): ?string {

        $valor =
            $this->limpiarValor(
                $valor
            );

        if ($valor === null) {

            return null;
        }

        $normalizado =
            strtolower(
                $valor
            );

        return in_array(
            $normalizado,
            [
                '0',
                'null',
                'undefined',
            ],
            true
        )
            ? null
            : $valor;
    }

    private function formatearFecha(
        string $fecha
    ): string {

        $timestamp =
            strtotime(
                $fecha
            );

        if ($timestamp === false) {

            return $fecha;
        }

        return date(
            'd/m/Y',
            $timestamp
        );
    }

    private function obtenerTipoActivo(
        array $filtros
    ): string {

        return strtoupper(
            trim(
                (string) (
                    $filtros['tipo']
                    ?? ''
                )
            )
        );
    }
}
