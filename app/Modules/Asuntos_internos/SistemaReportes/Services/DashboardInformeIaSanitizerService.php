<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

/* =========================================================
   GENERADOR DE ANALISIS DASHBOARD
   SANITIZADOR DE PAYLOAD PARA IA
   =========================================================
   Este servicio reconstruye un payload seguro desde listas
   permitidas. Su objetivo es impedir que la IA reciba datos
   personales, folios, identificadores de personal, evidencias,
   observaciones o detalles individuales.

   La validacion recursiva es una defensa adicional: si una
   clave prohibida aparece en cualquier nivel, se detiene la
   preparacion del informe.
   ========================================================= */

class DashboardInformeIaSanitizerService
{
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
        'unidad',
    ];

    private const CLAVES_PROHIBIDAS = [
        'nombre_persona',
        'nombre_completo',
        'personal_nombre',
        'nomina',
        'nómina',
        'perscod',
        'plantilla_id',
        'foto',
        'fotografia',
        'fotografía',
        'telefono',
        'teléfono',
        'celular',
        'correo',
        'email',
        'domicilio',
        'direccion',
        'dirección',
        'quejoso',
        'nombre_quejoso',
        'folio',
        'folios',
        'hechos',
        'evidencia',
        'evidencias',
        'observacion',
        'observación',
        'observaciones',
        'detalles_personal',
        'detalles_ranking',
        'id_personal',
        'personal',
        'personal_individual',
        'ranking_personal',
        'identificador',
        'identidad',
        'reportes',
    ];

    public function construirPayloadSeguro(
        array $preparado
    ): array {

        $payload = [
            'ok' =>
                true,

            'seguro_para_ia' =>
                true,

            'filtro_personal_aplicado' =>
                (bool) (
                    $preparado['filtro_personal_aplicado']
                    ?? false
                ),

            'tipo' =>
                $this->texto(
                    $preparado['tipo']
                    ?? ''
                ),

            'periodo' =>
                $this->sanitizarPeriodo(
                    $preparado['periodo']
                    ?? []
                ),

            'filtros' =>
                $this->sanitizarFiltros(
                    $preparado['filtros_activos']
                    ?? []
                ),

            'configuracion' =>
                $this->sanitizarConfiguracion(
                    $preparado['configuracion']
                    ?? []
                ),

            'secciones_solicitadas' =>
                $this->sanitizarSeccionesSolicitadas(
                    $preparado['secciones_solicitadas']
                    ?? []
                ),

            'secciones' =>
                $this->sanitizarSecciones(
                    $preparado['secciones']
                    ?? []
                ),
        ];

        $this->validarPayloadSeguro(
            $payload
        );

        return $payload;
    }

    private function sanitizarSecciones(
        array $secciones
    ): array {

        $seguras = [];

        foreach ($secciones as $clave => $datos) {

            if (!is_array($datos)) {
                continue;
            }

            $clave =
                (string) $clave;

            $sanitizada =
                match ($clave) {
                    'indicadores' =>
                        $this->sanitizarIndicadores(
                            $datos
                        ),

                    'catalogo' =>
                        $this->sanitizarSerieSimple(
                            $datos,
                            'clasificaciones',
                            'totales',
                            'clasificacion'
                        ),

                    'evolucion' =>
                        $this->sanitizarEvolucion(
                            $datos
                        ),

                    'estado' =>
                        $this->sanitizarEstado(
                            $datos
                        ),

                    'zona' =>
                        $this->sanitizarSerieSimple(
                            $datos,
                            'zonas',
                            'totales',
                            'zona'
                        ),

                    'turno' =>
                        $this->sanitizarSerieSimple(
                            $datos,
                            'turnos',
                            'totales',
                            'turno'
                        ),

                    'sector' =>
                        $this->sanitizarSerieSimple(
                            $datos,
                            'sectores',
                            'totales',
                            'sector'
                        ),

                    'area' =>
                        $this->sanitizarDimension(
                            $datos,
                            'area'
                        ),

                    'unidad' =>
                        $this->sanitizarDimension(
                            $datos,
                            'unidad'
                        ),

                    'sanciones' =>
                        $this->sanitizarSerieSimple(
                            $datos,
                            'tipos',
                            'totales',
                            'tipo_sancion'
                        ),

                    'cruce' =>
                        $this->sanitizarCruce(
                            $datos
                        ),

                    'ranking_sector',
                    'ranking_area',
                    'ranking_unidad' =>
                        $this->sanitizarRanking(
                            $datos,
                            $clave
                        ),

                    'hallazgos' =>
                        $this->sanitizarHallazgos(
                            $datos
                        ),

                    'comparativa' =>
                        $this->sanitizarComparativa(
                            $datos
                        ),

                    default =>
                        null,
                };

            if ($sanitizada !== null) {
                $seguras[$clave] =
                    $sanitizada;
            }
        }

        return $seguras;
    }

    private function sanitizarIndicadores(
        array $datos
    ): array {

        $metricas = [];

        foreach ($datos as $clave => $valor) {

            if (!is_numeric($valor)) {
                continue;
            }

            $metricas[] = [
                'etiqueta' =>
                    $this->texto(
                        (string) $clave
                    ),

                'cantidad' =>
                    $this->numero(
                        $valor
                    ),
            ];
        }

        return [
            'metricas' =>
                $metricas,
        ];
    }

    private function sanitizarEvolucion(
        array $datos
    ): array {

        $filas = [];

        foreach (($datos['datos'] ?? []) as $fila) {

            if (!is_array($fila)) {
                continue;
            }

            $filas[] = [
                'periodo' =>
                    $this->texto(
                        $fila['fecha']
                        ?? ''
                    ),

                'cantidad' =>
                    $this->numero(
                        $fila['total']
                        ?? 0
                    ),
            ];
        }

        return [
            'agrupacion' =>
                $this->texto(
                    $datos['agrupacion']
                    ?? ''
                ),

            'datos' =>
                $filas,

            'total' =>
                $this->numero(
                    $datos['total']
                    ?? 0
                ),
        ];
    }

    private function sanitizarEstado(
        array $datos
    ): array {

        $datosEstado =
            is_array($datos['datos'] ?? null)
                ? $datos['datos']
                : $datos;

        return [
            'compatible' =>
                (bool) (
                    $datos['compatible']
                    ?? true
                ),

            'datos' =>
                $this->construirElementos(
                    $datosEstado['estados']
                    ?? [],
                    $datosEstado['totales']
                    ?? [],
                    $datosEstado['porcentajes']
                    ?? [],
                    'estado'
                ),

            'total' =>
                $this->numero(
                    $datosEstado['total']
                    ?? 0
                ),
        ];
    }

    private function sanitizarSerieSimple(
        array $datos,
        string $claveEtiquetas,
        string $claveTotales,
        string $nombreEtiqueta
    ): array {

        return [
            'datos' =>
                $this->construirElementos(
                    $datos[$claveEtiquetas]
                    ?? [],
                    $datos[$claveTotales]
                    ?? [],
                    $datos['porcentajes']
                    ?? [],
                    $nombreEtiqueta
                ),

            'total' =>
                $this->numero(
                    $datos['total']
                    ?? 0
                ),
        ];
    }

    private function sanitizarDimension(
        array $datos,
        string $dimension
    ): array {

        return [
            'dimension' =>
                $dimension,

            'titulo' =>
                $this->texto(
                    $datos['titulo']
                    ?? ''
                ),

            'datos' =>
                $this->construirElementos(
                    $datos['etiquetas']
                    ?? [],
                    $datos['totales']
                    ?? [],
                    $datos['porcentajes']
                    ?? [],
                    $dimension
                ),

            'total' =>
                $this->numero(
                    $datos['total']
                    ?? 0
                ),
        ];
    }

    private function sanitizarCruce(
        array $datos
    ): array {

        $series = [];

        foreach (($datos['series'] ?? []) as $serie) {

            if (!is_array($serie)) {
                continue;
            }

            $series[] = [
                'etiqueta' =>
                    $this->texto(
                        $serie['nombre']
                        ?? ''
                    ),

                'cantidades' =>
                    $this->numeros(
                        $serie['datos']
                        ?? []
                    ),
            ];
        }

        return [
            'principal' =>
                $this->texto(
                    $datos['principal']
                    ?? ''
                ),

            'secundaria' =>
                $this->texto(
                    $datos['secundaria']
                    ?? ''
                ),

            'categorias' =>
                $this->textos(
                    $datos['categorias']
                    ?? []
                ),

            'series' =>
                $series,

            'total' =>
                $this->numero(
                    $datos['total']
                    ?? 0
                ),
        ];
    }

    private function sanitizarRanking(
        array $datos,
        string $clave
    ): array {

        return [
            'tipo' =>
                $clave,

            'titulo' =>
                $this->texto(
                    $datos['titulo']
                    ?? ''
                ),

            'datos' =>
                $this->construirElementos(
                    $datos['etiquetas']
                    ?? [],
                    $datos['totales']
                    ?? [],
                    $datos['porcentajes']
                    ?? [],
                    'categoria'
                ),

            'total' =>
                $this->numero(
                    $datos['total_top']
                    ?? $datos['total']
                    ?? 0
                ),
        ];
    }

    private function sanitizarHallazgos(
        array $datos
    ): array {

        $hallazgos = [];

        foreach ($datos as $hallazgo) {

            if (!is_array($hallazgo)) {
                continue;
            }

            $hallazgos[] = [
                'tipo' =>
                    $this->texto(
                        $hallazgo['tipo']
                        ?? ''
                    ),

                'titulo' =>
                    $this->texto(
                        $hallazgo['titulo']
                        ?? ''
                    ),

                'etiqueta' =>
                    $this->texto(
                        $hallazgo['valor']
                        ?? ''
                    ),

                'descripcion' =>
                    $this->texto(
                        $hallazgo['descripcion']
                        ?? ''
                    ),
            ];
        }

        return [
            'hallazgos' =>
                $hallazgos,
        ];
    }

    private function sanitizarComparativa(
        array $datos
    ): array {

        $metricas = [];

        foreach (($datos['metricas'] ?? []) as $metrica) {

            if (!is_array($metrica)) {
                continue;
            }

            $metricas[] = [
                'clave' =>
                    $this->texto(
                        $metrica['clave']
                        ?? ''
                    ),

                'etiqueta' =>
                    $this->texto(
                        $metrica['nombre']
                        ?? ''
                    ),

                'actual' =>
                    $this->numero(
                        $metrica['actual']
                        ?? 0
                    ),

                'anterior' =>
                    $this->numero(
                        $metrica['anterior']
                        ?? 0
                    ),

                'diferencia' =>
                    $this->numero(
                        $metrica['diferencia']
                        ?? 0
                    ),

                'variacion' =>
                    ($metrica['variacion_disponible'] ?? false)
                        ? $this->numero(
                            $metrica['variacion']
                            ?? 0
                        )
                        : null,

                'tendencia' =>
                    $this->texto(
                        $metrica['tendencia']
                        ?? ''
                    ),
            ];
        }

        return [
            'disponible' =>
                (bool) (
                    $datos['disponible']
                    ?? false
                ),

            'dias_periodo' =>
                $this->numero(
                    $datos['dias_periodo']
                    ?? 0
                ),

            'periodo_actual' =>
                $this->sanitizarPeriodoSimple(
                    $datos['periodo_actual']
                    ?? []
                ),

            'periodo_anterior' =>
                $this->sanitizarPeriodoSimple(
                    $datos['periodo_anterior']
                    ?? []
                ),

            'metricas' =>
                $metricas,
        ];
    }

    private function sanitizarFiltros(
        array $filtros
    ): array {

        $seguros = [];

        foreach (self::FILTROS_PERMITIDOS as $filtro) {

            $valor =
                $filtros[$filtro]
                ?? null;

            if ($valor === null || $valor === '') {
                continue;
            }

            $seguros[$filtro] =
                $this->texto(
                    $valor
                );
        }

        return $seguros;
    }

    private function sanitizarConfiguracion(
        array $configuracion
    ): array {

        return [
            'dimension' =>
                $this->texto(
                    $configuracion['dimension']
                    ?? ''
                ),

            'cruce_principal' =>
                $this->texto(
                    $configuracion['cruce_principal']
                    ?? ''
                ),

            'cruce_secundaria' =>
                $this->texto(
                    $configuracion['cruce_secundaria']
                    ?? ''
                ),
        ];
    }

    private function sanitizarPeriodo(
        array $periodo
    ): array {

        return [
            'inicio' =>
                $this->texto(
                    $periodo['inicio']
                    ?? ''
                ),

            'fin' =>
                $this->texto(
                    $periodo['fin']
                    ?? ''
                ),

            'texto' =>
                $this->texto(
                    $periodo['texto']
                    ?? ''
                ),
        ];
    }

    private function sanitizarPeriodoSimple(
        array $periodo
    ): array {

        return [
            'inicio' =>
                $this->texto(
                    $periodo['inicio']
                    ?? ''
                ),

            'fin' =>
                $this->texto(
                    $periodo['fin']
                    ?? ''
                ),
        ];
    }

    private function sanitizarSeccionesSolicitadas(
        array $secciones
    ): array {

        return array_values(
            array_filter(
                array_map(
                    fn ($seccion): string =>
                        $this->texto(
                            $seccion
                        ),
                    $secciones
                ),
                static fn (string $seccion): bool =>
                    $seccion !== ''
            )
        );
    }

    private function construirElementos(
        array $etiquetas,
        array $totales,
        array $porcentajes,
        string $claveEtiqueta
    ): array {

        $elementos = [];

        foreach ($etiquetas as $indice => $etiqueta) {

            $elemento = [
                $claveEtiqueta =>
                    $this->texto(
                        $etiqueta
                    ),

                'cantidad' =>
                    $this->numero(
                        $totales[$indice]
                        ?? 0
                    ),
            ];

            if (array_key_exists($indice, $porcentajes)) {
                $elemento['porcentaje'] =
                    $this->numero(
                        $porcentajes[$indice]
                    );
            }

            $elementos[] =
                $elemento;
        }

        return $elementos;
    }

    private function validarPayloadSeguro(
        mixed $valor,
        string $ruta = 'payload'
    ): void {

        if (!is_array($valor)) {
            return;
        }

        foreach ($valor as $clave => $contenido) {

            $claveTexto =
                mb_strtolower(
                    trim(
                        (string) $clave
                    ),
                    'UTF-8'
                );

            if (
                in_array(
                    $claveTexto,
                    self::CLAVES_PROHIBIDAS,
                    true
                )
            ) {

                throw new \RuntimeException(
                    'Payload inseguro para IA: clave prohibida en '
                    . $ruta
                    . '.'
                );
            }

            $this->validarPayloadSeguro(
                $contenido,
                $ruta . '.' . $claveTexto
            );
        }
    }

    private function textos(
        mixed $valores
    ): array {

        if (!is_array($valores)) {
            return [];
        }

        return array_values(
            array_map(
                fn ($valor): string =>
                    $this->texto(
                        $valor
                    ),
                $valores
            )
        );
    }

    private function numeros(
        mixed $valores
    ): array {

        if (!is_array($valores)) {
            return [];
        }

        return array_values(
            array_map(
                fn ($valor): int|float =>
                    $this->numero(
                        $valor
                    ),
                $valores
            )
        );
    }

    private function texto(
        mixed $valor
    ): string {

        return trim(
            strip_tags(
                (string) $valor
            )
        );
    }

    private function numero(
        mixed $valor
    ): int|float {

        if (!is_numeric($valor)) {
            return 0;
        }

        $numero =
            (float) $valor;

        return floor($numero) === $numero
            ? (int) $numero
            : $numero;
    }
}
