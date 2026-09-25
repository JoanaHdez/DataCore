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

?>


<section
    class="dashboard-comparativa"
    id="dashboard-comparativa"
>

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-comparativa__encabezado">

        <div>

            <span class="dashboard-comparativa__eyebrow">
                Comparativa temporal
            </span>

            <h2 class="dashboard-comparativa__titulo">
                Periodo actual vs. periodo anterior
            </h2>

            <p class="dashboard-comparativa__descripcion">
                Comparación automática con el periodo inmediatamente anterior
                de la misma duración.
            </p>

        </div>


        <?php if ($disponible): ?>

            <div class="dashboard-comparativa__resumen">

                <span class="dashboard-comparativa__resumen-etiqueta">
                    Duración
                </span>

                <strong class="dashboard-comparativa__resumen-valor">
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

        <div class="dashboard-comparativa__vacio">

            <strong>
                Selecciona un periodo completo
            </strong>

            <span>
                Ingresa fecha inicial y fecha final para comparar
                contra el periodo anterior equivalente.
            </span>

        </div>


    <?php else: ?>


        <!-- =================================================
             PERIODOS
        ================================================== -->

        <div class="dashboard-comparativa__periodos">

            <div class="dashboard-comparativa__periodo">

                <span>
                    Periodo actual
                </span>

                <strong>
                    <?= esc(
                        $formatearFecha(
                            $inicioActual
                        )
                    ) ?>

                    →

                    <?= esc(
                        $formatearFecha(
                            $finActual
                        )
                    ) ?>
                </strong>

            </div>


            <div class="dashboard-comparativa__periodo">

                <span>
                    Periodo anterior
                </span>

                <strong>
                    <?= esc(
                        $formatearFecha(
                            $inicioAnterior
                        )
                    ) ?>

                    →

                    <?= esc(
                        $formatearFecha(
                            $finAnterior
                        )
                    ) ?>
                </strong>

            </div>

        </div>


        <!-- =================================================
             MÉTRICAS
        ================================================== -->

        <div class="dashboard-comparativa__grid">

            <?php foreach ($metricas as $metrica): ?>

                <?php

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
                    $nombre === ''
                ) {

                    continue;
                }


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
                        (float) $variacion;


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

                ?>


                <article
                    class="
                        dashboard-comparativa__tarjeta
                        dashboard-comparativa__tarjeta--<?= esc($tendencia) ?>
                    "
                >

                    <span class="dashboard-comparativa__tarjeta-etiqueta">
                        <?= esc($nombre) ?>
                    </span>


                    <div class="dashboard-comparativa__valores">

                        <div>

                            <span>
                                Actual
                            </span>

                            <strong>
                                <?= esc($actual) ?>
                            </strong>

                        </div>


                        <div>

                            <span>
                                Anterior
                            </span>

                            <strong>
                                <?= esc($anterior) ?>
                            </strong>

                        </div>

                    </div>


                    <div class="dashboard-comparativa__cambio">

                        <span>
                            Diferencia
                        </span>

                        <strong>
                            <?= esc($textoDiferencia) ?>
                        </strong>

                    </div>


                    <div class="dashboard-comparativa__variacion">

                        <span>
                            Variación
                        </span>

                        <strong>
                            <?= esc($textoVariacion) ?>
                        </strong>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>


        <!-- =================================================
             DATOS PARA JAVASCRIPT
        ================================================== -->

        <script
            type="application/json"
            id="dashboard-comparativa-datos"
        ><?= json_encode(
            $comparativa,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        ) ?></script>

    <?php endif; ?>

</section>