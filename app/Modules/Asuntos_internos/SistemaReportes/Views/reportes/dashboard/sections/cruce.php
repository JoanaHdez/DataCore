<?php

$cruceDashboard =
    $cruceDashboard
    ?? [];


$principalSeleccionado =
    trim(
        (string) (
            $cruceDashboard['principal']
            ?? 'sector'
        )
    );


$secundariaSeleccionada =
    trim(
        (string) (
            $cruceDashboard['secundaria']
            ?? 'turno'
        )
    );


$categorias =
    $cruceDashboard['categorias']
    ?? [];


$series =
    $cruceDashboard['series']
    ?? [];


$totalCruce =
    (int) (
        $cruceDashboard['total']
        ?? 0
    );


$opcionesPrincipal =
    $cruceDashboard['opciones_principal']
    ?? [];


$opcionesSecundaria =
    $cruceDashboard['opciones_secundaria']
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


$descripcionCruce =
    $esFelicitacion
        ? 'Relación entre dos dimensiones de las felicitaciones registradas.'
        : 'Relación entre dos dimensiones de las quejas registradas.';


$ariaCruce =
    $esFelicitacion
        ? 'Gráfica de análisis cruzado de felicitaciones'
        : 'Gráfica de análisis cruzado de quejas';


/* =========================================================
   NORMALIZAR
========================================================= */

if (!is_array($categorias)) {
    $categorias = [];
}


if (!is_array($series)) {
    $series = [];
}


if (!is_array($opcionesPrincipal)) {
    $opcionesPrincipal = [];
}


if (!is_array($opcionesSecundaria)) {
    $opcionesSecundaria = [];
}


/* =========================================================
   TÍTULOS
========================================================= */

$textoPrincipal =
    ucfirst(
        $principalSeleccionado
    );


$textoSecundaria =
    ucfirst(
        $secundariaSeleccionada
    );


$sinDatosCruce =
    empty($categorias)
    || empty($series)
    || $totalCruce <= 0;

?>


<section class="dashboard-cruce" id="dashboard-cruce">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-cruce__encabezado">

        <div class="dashboard-cruce__encabezado-info">

            <span class="dashboard-cruce__eyebrow">
                Análisis cruzado
            </span>


            <h2 class="dashboard-cruce__titulo">
                <?= esc($textoPrincipal) ?>
                ×
                <?= esc($textoSecundaria) ?>
            </h2>


            <p class="dashboard-cruce__descripcion">
                <?= esc($descripcionCruce) ?>
            </p>

        </div>


        <div class="dashboard-cruce__resumen">

            <span class="dashboard-cruce__resumen-etiqueta">
                Asociaciones mostradas
            </span>


            <strong class="dashboard-cruce__resumen-valor">
                <?= esc($totalCruce) ?>
            </strong>

        </div>

    </div>


    <!-- =====================================================
         SELECTORES
    ====================================================== -->

    <div class="dashboard-cruce__selectores">

        <div class="dashboard-cruce__selector">

            <label for="dashboard-cruce-principal">
                Analizar por
            </label>


            <select id="dashboard-cruce-principal" class="dashboard-cruce__selector-control">

                <?php foreach ($opcionesPrincipal as $opcion): ?>

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

                    ?>

                <option value="<?= esc($valor) ?>" <?= $valor === $principalSeleccionado
                            ? 'selected'
                            : '' ?>>
                    <?= esc($texto) ?>
                </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div class="dashboard-cruce__selector">

            <label for="dashboard-cruce-secundaria">
                Comparar con
            </label>


            <select id="dashboard-cruce-secundaria" class="dashboard-cruce__selector-control">

                <?php foreach ($opcionesSecundaria as $opcion): ?>

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

                    ?>

                <option value="<?= esc($valor) ?>" <?= $valor === $secundariaSeleccionada
                            ? 'selected'
                            : '' ?>>
                    <?= esc($texto) ?>
                </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <?php if ($sinDatosCruce): ?>

    <div class="dashboard-cruce__vacio">

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

    <div class="dashboard-cruce__panel">

        <div class="dashboard-cruce__panel-header">

            <div>

                <span class="dashboard-cruce__panel-eyebrow">
                    Distribución combinada
                </span>

                <h3 class="dashboard-cruce__panel-titulo">
                    <?= esc($textoPrincipal) ?>
                    ×
                    <?= esc($textoSecundaria) ?>
                </h3>

            </div>


            <span class="dashboard-cruce__categorias-total">
                <?= esc(count($categorias)) ?>
                <?= count($categorias) === 1
                        ? 'categoría'
                        : 'categorías' ?>
            </span>

        </div>


        <div class="dashboard-cruce__grafica-scroll">

            <div class="dashboard-cruce__grafica" id="dashboard-cruce-chart-container">

                <canvas id="dashboard-cruce-chart" aria-label="<?= esc($ariaCruce) ?>" role="img"></canvas>

            </div>

        </div>

    </div>

    <?php endif; ?>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script type="application/json" id="dashboard-cruce-datos">
    <?= json_encode(
            [
                'tipo' =>
                    $esFelicitacion
                        ? 'felicitacion'
                        : 'queja',

                'principal' =>
                    $principalSeleccionado,

                'secundaria' =>
                    $secundariaSeleccionada,

                'categorias' =>
                    $categorias,

                'series' =>
                    $series,

                'total' =>
                    $totalCruce,
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