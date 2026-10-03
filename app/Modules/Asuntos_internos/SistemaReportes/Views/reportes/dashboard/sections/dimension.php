<?php

$dimensionDashboard =
    $dimensionDashboard
    ?? [];


$dimensionSeleccionada =
    trim(
        (string) (
            $dimensionDashboard['dimension']
            ?? 'area'
        )
    );


$tituloDimension =
    trim(
        (string) (
            $dimensionDashboard['titulo']
            ?? 'Área'
        )
    );


$etiquetas =
    $dimensionDashboard['etiquetas']
    ?? [];


$totales =
    $dimensionDashboard['totales']
    ?? [];


$porcentajes =
    $dimensionDashboard['porcentajes']
    ?? [];


$totalDimension =
    (int) (
        $dimensionDashboard['total']
        ?? 0
    );


$opciones =
    $dimensionDashboard['opciones']
    ?? [];


/* =========================================================
   TIPO ACTIVO
========================================================= */

$tipoDashboard =
    strtoupper(
        trim(
            (string) (
                $_GET['tipo']
                ?? ''
            )
        )
    );


$esFelicitacion =
    $tipoDashboard === 'FELICITACION';


$descripcionDimension =
    $esFelicitacion
        ? 'Distribución de las felicitaciones según la dimensión seleccionada.'
        : 'Distribución de las quejas según la dimensión seleccionada.';


$ariaDimension =
    $esFelicitacion
        ? 'Distribución de felicitaciones por dimensión'
        : 'Distribución de quejas por dimensión';


$textoRegistros =
    $esFelicitacion
        ? 'felicitaciones'
        : 'quejas';


/* =========================================================
   NORMALIZAR ARREGLOS
========================================================= */

if (!is_array($etiquetas)) {
    $etiquetas = [];
}


if (!is_array($totales)) {
    $totales = [];
}


if (!is_array($porcentajes)) {
    $porcentajes = [];
}


if (!is_array($opciones)) {
    $opciones = [];
}


$sinDatosDimension =
    empty($etiquetas)
    || $totalDimension <= 0;

?>


<section class="dashboard-dimension" id="dashboard-dimension">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-dimension__encabezado">

        <div class="dashboard-dimension__encabezado-info">

            <span class="dashboard-dimension__eyebrow">
                Distribución institucional
            </span>


            <h2 class="dashboard-dimension__titulo">
                Análisis por <?= esc($tituloDimension) ?>
            </h2>


            <p class="dashboard-dimension__descripcion">
                <?= esc($descripcionDimension) ?>
            </p>

        </div>


        <div class="dashboard-dimension__resumen">

            <span class="dashboard-dimension__resumen-etiqueta">
                Total asociado
            </span>


            <strong class="dashboard-dimension__resumen-valor" id="dashboard-dimension-total">
                <?= esc($totalDimension) ?>
            </strong>


            <small class="dashboard-dimension__resumen-texto">
                <?= esc($textoRegistros) ?>
            </small>

        </div>

    </div>


    <!-- =====================================================
         SELECTOR DE DIMENSIÓN
    ====================================================== -->

    <div class="dashboard-dimension__selector">

        <span class="dashboard-dimension__selector-label">
            Analizar por
        </span>


        <div class="dashboard-dimension__tabs" role="group" aria-label="Seleccionar dimensión de análisis">

            <?php foreach ($opciones as $opcion): ?>

            <?php

                $valor =
                    trim(
                        (string) (
                            $opcion['valor']
                            ?? ''
                        )
                    );


                $texto =
                    trim(
                        (string) (
                            $opcion['texto']
                            ?? ''
                        )
                    );


                if (
                    $valor === ''
                    || $texto === ''
                ) {
                    continue;
                }


                $activo =
                    $valor === $dimensionSeleccionada;

                ?>


            <button type="button" class="
                        dashboard-dimension__tab
                        <?= $activo
                            ? 'dashboard-dimension__tab--activo'
                            : '' ?>
                    " data-dimension="<?= esc($valor) ?>" aria-pressed="<?= $activo ? 'true' : 'false' ?>">
                <?= esc($texto) ?>
            </button>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <?php if ($sinDatosDimension): ?>

    <div class="dashboard-dimension__vacio">

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

    <div class="dashboard-dimension__panel">

        <!-- =============================================
                 DISTRIBUCIÓN
            ============================================== -->

        <div class="dashboard-dimension__bloque">

            <div class="dashboard-dimension__bloque-header">

                <div>

                    <span class="dashboard-dimension__bloque-eyebrow">
                        Distribución
                    </span>

                    <h3 class="dashboard-dimension__bloque-titulo">
                        <?= esc($tituloDimension) ?>
                    </h3>

                </div>


                <span class="dashboard-dimension__cantidad-categorias">
                    <?= esc(count($etiquetas)) ?>
                    <?= count($etiquetas) === 1
                            ? 'registro'
                            : 'registros' ?>
                </span>

            </div>


            <div class="dashboard-dimension__grafica-scroll">

                <div class="dashboard-dimension__grafica" id="dashboard-dimension-chart-container">

                    <canvas id="dashboard-dimension-chart" aria-label="<?= esc($ariaDimension) ?>" role="img"></canvas>

                </div>

            </div>

        </div>


        <!-- =============================================
                 DETALLE
            ============================================== -->

        <div class="
                dashboard-dimension__bloque
                dashboard-dimension__bloque--detalle
            ">

            <div class="dashboard-dimension__bloque-header">

                <div>

                    <span class="dashboard-dimension__bloque-eyebrow">
                        Detalle
                    </span>

                    <h3 class="dashboard-dimension__bloque-titulo">
                        Todos los resultados
                    </h3>

                </div>

            </div>


            <div class="dashboard-dimension__lista">

                <?php foreach ($etiquetas as $indice => $etiqueta): ?>

                <?php

                        $cantidad =
                            (int) (
                                $totales[$indice]
                                ?? 0
                            );


                        $porcentaje =
                            (float) (
                                $porcentajes[$indice]
                                ?? 0
                            );

                        ?>


                <div class="dashboard-dimension__item">

                    <div class="dashboard-dimension__item-principal">

                        <span class="dashboard-dimension__item-etiqueta">
                            <?= esc($etiqueta) ?>
                        </span>


                        <span class="dashboard-dimension__item-porcentaje">
                            <?= esc($porcentaje) ?>%
                        </span>

                    </div>


                    <div class="dashboard-dimension__item-secundario">

                        <strong class="dashboard-dimension__item-total">
                            <?= esc($cantidad) ?>
                        </strong>


                        <span>
                            <?= $cantidad === 1
                                        ? esc(
                                            $esFelicitacion
                                                ? 'felicitación'
                                                : 'queja'
                                        )
                                        : esc($textoRegistros) ?>
                        </span>

                    </div>


                    <div class="dashboard-dimension__item-barra" aria-hidden="true">

                        <span style="width: <?= esc(
                                        min(
                                            100,
                                            max(
                                                0,
                                                $porcentaje
                                            )
                                        )
                                    ) ?>%;"></span>

                    </div>

                </div>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

    <?php endif; ?>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script type="application/json" id="dashboard-dimension-datos">
    <?= json_encode(
            [
                'tipo' =>
                    $esFelicitacion
                        ? 'felicitacion'
                        : 'queja',

                'dimension' =>
                    $dimensionSeleccionada,

                'titulo' =>
                    $tituloDimension,

                'etiquetas' =>
                    $etiquetas,

                'totales' =>
                    $totales,

                'porcentajes' =>
                    $porcentajes,

                'total' =>
                    $totalDimension,
            ],
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        ) ?>
    </script>

</section>