<?php

$comparativa =
    $comparativa
    ?? [];


$disponible =
    (bool) (
        $comparativa['disponible']
        ?? false
    );


$diasPeriodo =
    (int) (
        $comparativa['dias_periodo']
        ?? 0
    );


$periodoActual =
    $comparativa['periodo_actual']
    ?? [];


$periodoAnterior =
    $comparativa['periodo_anterior']
    ?? [];


$metricas =
    $comparativa['metricas']
    ?? [];


/* =========================================================
   PERIODOS
========================================================= */

$inicioActual =
    trim(
        (string) (
            $periodoActual['inicio']
            ?? ''
        )
    );


$finActual =
    trim(
        (string) (
            $periodoActual['fin']
            ?? ''
        )
    );


$inicioAnterior =
    trim(
        (string) (
            $periodoAnterior['inicio']
            ?? ''
        )
    );


$finAnterior =
    trim(
        (string) (
            $periodoAnterior['fin']
            ?? ''
        )
    );


/* =========================================================
   FORMATEAR FECHA
========================================================= */

$formatearFecha =
    static function (
        string $fecha
    ): string {

        if (
            $fecha === ''
        ) {

            return '';
        }


        $objeto =
            \DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $fecha
            );


        if (
            !$objeto
        ) {

            return $fecha;
        }


        return $objeto
            ->format(
                'd/m/Y'
            );
    };


/* =========================================================
   NORMALIZAR MÉTRICAS
========================================================= */

if (
    !is_array(
        $metricas
    )
) {

    $metricas = [];
}


/* =========================================================
   TOTAL PARA ESTADO SIN DATOS
========================================================= */

$totalComparativa =
    0;


foreach (
    $metricas
    as $metrica
) {

    if (
        !is_array(
            $metrica
        )
    ) {

        continue;
    }


    $totalComparativa +=
        (int) (
            $metrica['actual']
            ?? 0
        )
        +
        (int) (
            $metrica['anterior']
            ?? 0
        );
}


$sinDatosComparativa =
    $disponible
    && (
        empty($metricas)
        || $totalComparativa <= 0
    );


/* =========================================================
   SEPARAR MÉTRICA PRINCIPAL
========================================================= */

$metricaPrincipal =
    null;


$metricasSecundarias =
    [];


foreach (
    $metricas
    as $metrica
) {

    if (
        !is_array(
            $metrica
        )
    ) {

        continue;
    }


    $nombre =
        trim(
            (string) (
                $metrica['nombre']
                ?? ''
            )
        );


    if (
        $nombre === ''
    ) {

        continue;
    }


    $nombreNormalizado =
        mb_strtoupper(
            $nombre,
            'UTF-8'
        );


    if (
        $metricaPrincipal === null
        && in_array(
            $nombreNormalizado,
            [
                'TOTAL',
                'TOTAL DE REPORTES',
                'TOTAL REPORTES',
            ],
            true
        )
    ) {

        $metricaPrincipal =
            $metrica;

        continue;
    }


    $metricasSecundarias[] =
        $metrica;
}


/*
 * Si backend no utiliza exactamente ese nombre,
 * tomamos la primera métrica como principal.
 */

if (
    $metricaPrincipal === null
    && !empty(
        $metricasSecundarias
    )
) {

    $metricaPrincipal =
        array_shift(
            $metricasSecundarias
        );
}


/* =========================================================
   PREPARAR MÉTRICA
========================================================= */

$prepararMetrica =
    static function (
        array $metrica
    ): array {

        $nombre =
            trim(
                (string) (
                    $metrica['nombre']
                    ?? ''
                )
            );


        $actual =
            (int) (
                $metrica['actual']
                ?? 0
            );


        $anterior =
            (int) (
                $metrica['anterior']
                ?? 0
            );


        $diferencia =
            (int) (
                $metrica['diferencia']
                ?? 0
            );


        $variacionDisponible =
            (bool) (
                $metrica['variacion_disponible']
                ?? false
            );


        $variacion =
            $metrica['variacion']
            ?? null;


        $tendencia =
            trim(
                (string) (
                    $metrica['tendencia']
                    ?? 'sin_cambio'
                )
            );


        if (
            $diferencia > 0
        ) {

            $textoDiferencia =
                '+'
                . $diferencia;

        } else {

            $textoDiferencia =
                (string)
                $diferencia;
        }


        if (
            $variacionDisponible
            && $variacion !== null
        ) {

            $variacionNumero =
                (float)
                $variacion;


            $textoVariacion =
                (
                    $variacionNumero > 0
                        ? '+'
                        : ''
                )
                . $variacionNumero
                . '%';

        } else {

            $textoVariacion =
                'No disponible';
        }


        return [

            'nombre' =>
                $nombre,

            'actual' =>
                $actual,

            'anterior' =>
                $anterior,

            'diferencia' =>
                $diferencia,

            'texto_diferencia' =>
                $textoDiferencia,

            'texto_variacion' =>
                $textoVariacion,

            'tendencia' =>
                $tendencia,

        ];
    };

?>


<section class="dashboard-comparativa" id="dashboard-comparativa">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-comparativa__encabezado">

        <div class="dashboard-comparativa__encabezado-info">

            <span class="dashboard-comparativa__eyebrow">
                Comparativa temporal
            </span>


            <h2 class="dashboard-comparativa__titulo">
                Periodo actual vs. periodo anterior
            </h2>


            <p class="dashboard-comparativa__descripcion">
                Comparación automática con el periodo inmediatamente
                anterior de la misma duración.
            </p>

        </div>


        <?php if ($disponible): ?>

        <div class="dashboard-comparativa__duracion">

            <span>
                Duración
            </span>


            <strong>
                <?= esc($diasPeriodo) ?>
            </strong>


            <small>
                días
            </small>

        </div>

        <?php endif; ?>

    </div>


    <!-- =====================================================
         SIN PERIODO COMPLETO
    ====================================================== -->

    <?php if (!$disponible): ?>

    <div class="dashboard-comparativa__estado">

        <div class="dashboard-comparativa__estado-icono">

            <svg viewBox="0 0 24 24" aria-hidden="true">

                <rect x="4" y="5" width="16" height="15" rx="2" />

                <path d="M8 3v4" />
                <path d="M16 3v4" />
                <path d="M4 10h16" />

            </svg>

        </div>


        <div>

            <strong>
                Selecciona un periodo completo
            </strong>


            <span>
                Ingresa fecha inicial y fecha final para comparar
                contra el periodo anterior equivalente.
            </span>

        </div>

    </div>


    <?php elseif ($sinDatosComparativa): ?>

    <div class="dashboard-comparativa__vacio">

        <div class="dashboard-grafica__placeholder">

            <strong>
                Sin datos
            </strong>


            <span>
                Sin datos para los filtros seleccionados.
            </span>

        </div>

    </div>


    <?php else: ?>


    <!-- =================================================
             PERIODOS
        ================================================== -->

    <div class="dashboard-comparativa__periodos">

        <article class="
                    dashboard-comparativa__periodo
                    dashboard-comparativa__periodo--actual
                ">

            <div class="dashboard-comparativa__periodo-icono">

                <svg viewBox="0 0 24 24" aria-hidden="true">

                    <rect x="4" y="5" width="16" height="15" rx="2" />

                    <path d="M8 3v4" />
                    <path d="M16 3v4" />
                    <path d="M4 10h16" />

                </svg>

            </div>


            <div>

                <span class="dashboard-comparativa__periodo-etiqueta">
                    Periodo actual
                </span>


                <strong class="dashboard-comparativa__periodo-fecha">

                    <?= esc(
                            $formatearFecha(
                                $inicioActual
                            )
                        ) ?>

                    <span>
                        →
                    </span>

                    <?= esc(
                            $formatearFecha(
                                $finActual
                            )
                        ) ?>

                </strong>

            </div>

        </article>


        <article class="
                    dashboard-comparativa__periodo
                    dashboard-comparativa__periodo--anterior
                ">

            <div class="dashboard-comparativa__periodo-icono">

                <svg viewBox="0 0 24 24" aria-hidden="true">

                    <rect x="4" y="5" width="16" height="15" rx="2" />

                    <path d="M8 3v4" />
                    <path d="M16 3v4" />
                    <path d="M4 10h16" />

                </svg>

            </div>


            <div>

                <span class="dashboard-comparativa__periodo-etiqueta">
                    Periodo anterior
                </span>


                <strong class="dashboard-comparativa__periodo-fecha">

                    <?= esc(
                            $formatearFecha(
                                $inicioAnterior
                            )
                        ) ?>

                    <span>
                        →
                    </span>

                    <?= esc(
                            $formatearFecha(
                                $finAnterior
                            )
                        ) ?>

                </strong>

            </div>

        </article>

    </div>


    <!-- =================================================
             MÉTRICA PRINCIPAL
        ================================================== -->

    <?php if ($metricaPrincipal !== null): ?>

    <?php

            $principal =
                $prepararMetrica(
                    $metricaPrincipal
                );

            ?>


    <article class="
                    dashboard-comparativa__principal
                    dashboard-comparativa__principal--<?= esc(
                        $principal['tendencia']
                    ) ?>
                ">

        <div class="dashboard-comparativa__principal-info">

            <span class="dashboard-comparativa__principal-eyebrow">
                Indicador principal
            </span>


            <h3 class="dashboard-comparativa__principal-titulo">
                <?= esc(
                            $principal['nombre']
                        ) ?>
            </h3>

        </div>


        <div class="dashboard-comparativa__principal-valores">

            <div class="dashboard-comparativa__principal-valor">

                <span>
                    Actual
                </span>


                <strong>
                    <?= esc(
                                $principal['actual']
                            ) ?>
                </strong>

            </div>


            <div class="
                            dashboard-comparativa__principal-separador
                        " aria-hidden="true"></div>


            <div class="dashboard-comparativa__principal-valor">

                <span>
                    Anterior
                </span>


                <strong>
                    <?= esc(
                                $principal['anterior']
                            ) ?>
                </strong>

            </div>


            <div class="
                            dashboard-comparativa__principal-separador
                        " aria-hidden="true"></div>


            <div class="dashboard-comparativa__principal-cambio">

                <span>
                    Cambio
                </span>


                <strong>
                    <?= esc(
                                $principal['texto_diferencia']
                            ) ?>
                </strong>


                <small>
                    <?= esc(
                                $principal['texto_variacion']
                            ) ?>
                </small>

            </div>

        </div>

    </article>

    <?php endif; ?>


    <!-- =================================================
             MÉTRICAS SECUNDARIAS
        ================================================== -->

    <div class="dashboard-comparativa__grid">

        <?php foreach ($metricasSecundarias as $metrica): ?>

        <?php

                $datosMetrica =
                    $prepararMetrica(
                        $metrica
                    );


                if (
                    $datosMetrica['nombre'] === ''
                ) {

                    continue;
                }

                ?>


        <article class="
                        dashboard-comparativa__tarjeta
                        dashboard-comparativa__tarjeta--<?= esc(
                            $datosMetrica['tendencia']
                        ) ?>
                    ">

            <div class="dashboard-comparativa__tarjeta-header">

                <span class="dashboard-comparativa__tarjeta-etiqueta">
                    <?= esc(
                                $datosMetrica['nombre']
                            ) ?>
                </span>


                <span class="
                                dashboard-comparativa__tendencia
                                dashboard-comparativa__tendencia--<?= esc(
                                    $datosMetrica['tendencia']
                                ) ?>
                            " aria-hidden="true">

                    <?php if ($datosMetrica['tendencia'] === 'aumento'): ?>

                    ↑

                    <?php elseif ($datosMetrica['tendencia'] === 'disminucion'): ?>

                    ↓

                    <?php else: ?>

                    →

                    <?php endif; ?>

                </span>

            </div>


            <div class="dashboard-comparativa__valores">

                <div>

                    <span>
                        Actual
                    </span>


                    <strong>
                        <?= esc(
                                    $datosMetrica['actual']
                                ) ?>
                    </strong>

                </div>


                <div>

                    <span>
                        Anterior
                    </span>


                    <strong>
                        <?= esc(
                                    $datosMetrica['anterior']
                                ) ?>
                    </strong>

                </div>

            </div>


            <div class="dashboard-comparativa__tarjeta-footer">

                <div class="dashboard-comparativa__cambio">

                    <span>
                        Diferencia
                    </span>


                    <strong>
                        <?= esc(
                                    $datosMetrica['texto_diferencia']
                                ) ?>
                    </strong>

                </div>


                <div class="dashboard-comparativa__variacion">

                    <span>
                        Variación
                    </span>


                    <strong>
                        <?= esc(
                                    $datosMetrica['texto_variacion']
                                ) ?>
                    </strong>

                </div>

            </div>

        </article>

        <?php endforeach; ?>

    </div>


    <!-- =================================================
             DATOS
        ================================================== -->

    <script type="application/json" id="dashboard-comparativa-datos">
    <?= json_encode(
                $comparativa,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_HEX_TAG
                | JSON_HEX_AMP
                | JSON_HEX_APOS
                | JSON_HEX_QUOT
            ) ?>
    </script>

    <?php endif; ?>

</section>