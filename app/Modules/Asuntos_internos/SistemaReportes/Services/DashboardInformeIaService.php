<?php

namespace App\Modules\Asuntos_internos\SistemaReportes\Services;

/* =========================================================
   GENERADOR DE ANALISIS DASHBOARD
   INTEGRACION IA PARA REPORTES
   =========================================================
   Este servicio prepara y envia a un proveedor IA unicamente
   datos sanitizados del Dashboard. No debe recibir folios,
   nombres, perscod, plantilla_id, fotografias, datos del
   quejoso ni detalles individuales.

   TODO IA:
   Pendiente validar respuestas reales con API key institucional
   autorizada y presupuesto disponible.
   ========================================================= */

class DashboardInformeIaService
{
    private const MENSAJE_ERROR =
        'No fue posible generar el analisis automatico.';

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

    private const TITULOS_SECCIONES = [
        'indicadores' =>
            'Indicadores generales',
        'catalogo' =>
            'Catalogo / clasificacion',
        'evolucion' =>
            'Evolucion temporal',
        'estado' =>
            'Estado de las Quejas',
        'zona' =>
            'Zona',
        'turno' =>
            'Turno',
        'sector' =>
            'Sector',
        'area' =>
            'Area',
        'unidad' =>
            'Unidad',
        'sanciones' =>
            'Sanciones disciplinarias',
        'cruce' =>
            'Analisis cruzado',
        'ranking_sector' =>
            'Ranking Sector',
        'ranking_area' =>
            'Ranking Area',
        'ranking_unidad' =>
            'Ranking Unidad',
        'hallazgos' =>
            'Hallazgos',
        'comparativa' =>
            'Comparativa temporal',
    ];

    private const INSTRUCCIONES_SECCION = [
        'indicadores' =>
            'Resume los indicadores generales y destaca cantidades principales sin repetir toda la tabla.',
        'catalogo' =>
            'Describe la distribucion por clasificacion y senala concentraciones relevantes.',
        'evolucion' =>
            'Describe la tendencia temporal observada y menciona periodos con mayor o menor concentracion.',
        'estado' =>
            'Explica la distribucion por estado usando cantidades y porcentajes disponibles.',
        'zona' =>
            'Describe concentraciones por zona y compara categorias cuando los datos lo permitan.',
        'turno' =>
            'Describe concentraciones por turno y compara categorias cuando los datos lo permitan.',
        'sector' =>
            'Describe concentraciones por sector y compara categorias cuando los datos lo permitan.',
        'area' =>
            'Describe concentraciones por area sin inferir responsabilidades.',
        'unidad' =>
            'Describe concentraciones por unidad sin inferir responsabilidades.',
        'sanciones' =>
            'Resume las sanciones disciplinarias vigentes y su distribucion.',
        'cruce' =>
            'Describe el cruce entre dimensiones y menciona patrones visibles sin inferir causas.',
        'ranking_sector' =>
            'Resume el ranking por sector destacando posiciones y diferencias visibles.',
        'ranking_area' =>
            'Resume el ranking por area destacando posiciones y diferencias visibles.',
        'ranking_unidad' =>
            'Resume el ranking por unidad destacando posiciones y diferencias visibles.',
        'hallazgos' =>
            'Redacta los hallazgos como observaciones institucionales breves.',
        'comparativa' =>
            'Describe la comparativa temporal usando diferencias y variaciones disponibles.',
    ];

    public function generarNarrativas(
        array $payloadSeguro
    ): array {

        $secciones =
            $payloadSeguro['secciones']
            ?? [];

        if (!is_array($secciones)) {
            return [];
        }

        $narrativas = [];

        foreach ($secciones as $seccion => $datos) {

            if (
                !is_string($seccion)
                || !is_array($datos)
                || !in_array(
                    $seccion,
                    self::SECCIONES_PERMITIDAS,
                    true
                )
            ) {
                continue;
            }

            $narrativas[$seccion] =
                $this->generarNarrativa(
                    $payloadSeguro,
                    $seccion,
                    $datos
                );
        }

        return $narrativas;
    }

    public function generarNarrativaSector(
        array $payloadSeguro
    ): array {

        $datosSector =
            $payloadSeguro['secciones']['sector']
            ?? null;

        if (!is_array($datosSector)) {
            return $this->respuestaError(
                'sector'
            );
        }

        return $this->generarNarrativa(
            $payloadSeguro,
            'sector',
            $datosSector
        );
    }

    private function generarNarrativa(
        array $payloadSeguro,
        string $seccion,
        array $datosSeccion
    ): array {

        $apiKey =
            trim(
                (string) (
                    env('DASHBOARD_INFORME_IA_API_KEY')
                    ?: env('OPENAI_API_KEY')
                    ?: ''
                )
            );

        if ($apiKey === '') {
            return $this->respuestaError(
                $seccion
            );
        }

        $endpoint =
            trim(
                (string) (
                    env('DASHBOARD_INFORME_IA_URL')
                    ?: 'https://api.openai.com/v1/responses'
                )
            );

        $modelo =
            trim(
                (string) (
                    env('DASHBOARD_INFORME_IA_MODEL')
                    ?: 'gpt-4.1-mini'
                )
            );

        $datosParaIa =
            $this->construirDatosSeccion(
                $payloadSeguro,
                $seccion,
                $datosSeccion
            );

        $datosParaIaJson =
            json_encode(
                $datosParaIa,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

        if (!is_string($datosParaIaJson)) {
            return $this->respuestaError(
                $seccion
            );
        }

        try {

            $cliente =
                \Config\Services::curlrequest([
                    'timeout' =>
                        30,
                ]);

            $respuesta =
                $cliente->request(
                    'POST',
                    $endpoint,
                    [
                        'headers' => [
                            'Authorization' =>
                                'Bearer ' . $apiKey,

                            'Content-Type' =>
                                'application/json',

                            'Accept' =>
                                'application/json',
                        ],

                        'json' =>
                            [
                                'model' =>
                                    $modelo,

                                'input' =>
                                    [
                                        [
                                            'role' =>
                                                'system',

                                            'content' =>
                                                [
                                                    [
                                                        'type' =>
                                                            'input_text',

                                                        'text' =>
                                                            $this->instrucciones(
                                                                $seccion
                                                            ),
                                                    ],
                                                ],
                                        ],

                                        [
                                            'role' =>
                                                'user',

                                            'content' =>
                                                [
                                                    [
                                                        'type' =>
                                                            'input_text',

                                                        'text' =>
                                                            $datosParaIaJson,
                                                    ],
                                                ],
                                        ],
                                    ],

                                'temperature' =>
                                    0.2,

                                'max_output_tokens' =>
                                    500,
                            ],

                        'http_errors' =>
                            false,
                    ]
                );

            if (
                $respuesta->getStatusCode() < 200
                || $respuesta->getStatusCode() >= 300
            ) {
                return $this->respuestaError(
                    $seccion
                );
            }

            $contenido =
                json_decode(
                    $respuesta->getBody(),
                    true
                );

            if (!is_array($contenido)) {
                return $this->respuestaError(
                    $seccion
                );
            }

            $narrativa =
                $this->extraerTextoRespuesta(
                    $contenido
                );

            if ($narrativa === '') {
                return $this->respuestaError(
                    $seccion
                );
            }

            return [
                'ok' =>
                    true,

                'seccion' =>
                    $seccion,

                'titulo' =>
                    self::TITULOS_SECCIONES[$seccion]
                    ?? $seccion,

                'narrativa' =>
                    $narrativa,
            ];

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Error generando narrativa IA de {seccion}: {mensaje}',
                [
                    'seccion' =>
                        $seccion,

                    'mensaje' =>
                        $e->getMessage(),
                ]
            );

            return $this->respuestaError(
                $seccion
            );
        }
    }

    private function construirDatosSeccion(
        array $payloadSeguro,
        string $seccion,
        array $datosSeccion
    ): array {

        return [
            'seccion' =>
                $seccion,

            'titulo' =>
                self::TITULOS_SECCIONES[$seccion]
                ?? $seccion,

            'tipo' =>
                $payloadSeguro['tipo']
                ?? '',

            'periodo' =>
                $payloadSeguro['periodo']
                ?? [],

            'filtros' =>
                $payloadSeguro['filtros']
                ?? [],

            'filtro_personal_aplicado' =>
                (bool) (
                    $payloadSeguro['filtro_personal_aplicado']
                    ?? false
                ),

            'datos' =>
                $datosSeccion,
        ];
    }

    private function instrucciones(
        string $seccion
    ): string {

        return implode(
            "\n",
            [
                'Eres un analista institucional de Asuntos Internos.',
                'Redacta una narrativa breve en espanol para un informe editable del Dashboard.',
                'Usa un tono institucional, claro y objetivo.',
                'Utiliza unicamente los datos sanitizados proporcionados.',
                'Describe cifras, concentraciones, comparaciones, porcentajes o tendencias solo cuando existan en los datos.',
                'No inventes cifras, causas, nombres, identidades, folios ni recomendaciones.',
                'No identifiques personas ni intentes inferir identidades.',
                'No acuses, no atribuyas responsabilidades y evita lenguaje alarmista.',
                'Si filtro_personal_aplicado es true, puedes decir "Para el personal seleccionado", sin agregar datos de identidad.',
                'Si los datos son insuficientes, indicalo brevemente.',
                'Devuelve solo texto plano, sin HTML, Markdown, listas largas ni titulo.',
                'Extension sugerida: entre 80 y 150 palabras.',
                'Instruccion especifica de seccion: '
                    . (
                        self::INSTRUCCIONES_SECCION[$seccion]
                        ?? 'Resume la informacion agregada disponible.'
                    ),
            ]
        );
    }

    private function extraerTextoRespuesta(
        array $respuesta
    ): string {

        if (is_string($respuesta['output_text'] ?? null)) {
            return $this->limpiarNarrativa(
                $respuesta['output_text']
            );
        }

        foreach (($respuesta['output'] ?? []) as $salida) {

            if (!is_array($salida)) {
                continue;
            }

            foreach (($salida['content'] ?? []) as $contenido) {

                if (!is_array($contenido)) {
                    continue;
                }

                if (is_string($contenido['text'] ?? null)) {
                    return $this->limpiarNarrativa(
                        $contenido['text']
                    );
                }
            }
        }

        return '';
    }

    private function limpiarNarrativa(
        string $texto
    ): string {

        $texto =
            trim(
                strip_tags(
                    $texto
                )
            );

        $texto =
            preg_replace(
                "/[ \t]+/",
                ' ',
                $texto
            ) ?? $texto;

        $texto =
            preg_replace(
                "/\n{3,}/",
                "\n\n",
                $texto
            ) ?? $texto;

        return mb_substr(
            trim($texto),
            0,
            2500,
            'UTF-8'
        );
    }

    private function respuestaError(
        string $seccion
    ): array {

        return [
            'ok' =>
                false,

            'seccion' =>
                $seccion,

            'titulo' =>
                self::TITULOS_SECCIONES[$seccion]
                ?? $seccion,

            'message' =>
                self::MENSAJE_ERROR,
        ];
    }
}
