<?php

/* =========================================================
   EVOLUCIÓN TEMPORAL
========================================================= */

$evolucion =
    $evolucion
    ?? [];


$agrupacion =
    trim(
        (string) (
            $evolucion['agrupacion']
            ?? 'dia'
        )
    );


$datosEvolucion =
    $evolucion['datos']
    ?? [];


if (
    !is_array(
        $datosEvolucion
    )
) {

    $datosEvolucion =
        [];
}


$totalEvolucion =
    (int) (
        $evolucion['total']
        ?? 0
    );


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


$descripcionEvolucion =
    $esFelicitacion
        ? 'Comportamiento de las felicitaciones registradas durante el periodo consultado.'
        : 'Comportamiento de las quejas registradas durante el periodo consultado.';


$ariaEvolucion =
    $esFelicitacion
        ? 'Gráfica de evolución temporal de felicitaciones'
        : 'Gráfica de evolución temporal de quejas';


/* =========================================================
   NORMALIZAR DATOS PARA FRONTEND
========================================================= */

$datosFrontend =
    [];


foreach (
    $datosEvolucion
    as $registro
) {

    if (
        !is_array(
            $registro
        )
    ) {

        continue;
    }


    $fecha =
        trim(
            (string) (
                $registro['fecha']
                ?? ''
            )
        );


    if (
        $fecha === ''
    ) {

        continue;
    }


    $datosFrontend[] = [

        'fecha' =>
            $fecha,

        'total' =>
            (int) (
                $registro['total']
                ?? 0
            ),

    ];
}


$sinDatosEvolucion =
    empty($datosFrontend)
    || $totalEvolucion <= 0;


/* =========================================================
   TEXTO DE AGRUPACIÓN
========================================================= */

$textoAgrupacion =
    match ($agrupacion) {

        'mes' =>
            'Agrupación mensual',

        'semana' =>
            'Agrupación semanal',

        default =>
            'Agrupación diaria',
    };

?>


<section class="dashboard-evolucion" id="dashboard-evolucion">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-evolucion__encabezado">

        <div>

            <span class="dashboard-evolucion__eyebrow">
                Tendencia de registros
            </span>


            <h2 class="dashboard-evolucion__titulo">
                Evolución temporal
            </h2>


            <p class="dashboard-evolucion__descripcion">
                <?= esc($descripcionEvolucion) ?>
            </p>

        </div>


        <div class="dashboard-evolucion__resumen">

            <span class="dashboard-evolucion__resumen-etiqueta">
                Total del periodo
            </span>


            <strong class="dashboard-evolucion__resumen-valor" id="dashboard-evolucion-total">
                <?= esc($totalEvolucion) ?>
            </strong>

        </div>

    </div>


    <!-- =====================================================
         META
    ====================================================== -->

    <div class="dashboard-evolucion__meta">

        <span class="dashboard-evolucion__agrupacion" id="dashboard-evolucion-agrupacion">
            <?= esc($textoAgrupacion) ?>
        </span>

    </div>


    <!-- =====================================================
         GRÁFICA
    ====================================================== -->

    <div class="dashboard-evolucion__grafica">

        <?php if (!$sinDatosEvolucion): ?>

        <canvas id="dashboard-evolucion-chart" class="dashboard-evolucion__canvas"
            aria-label="<?= esc($ariaEvolucion) ?>" role="img"></canvas>

        <?php else: ?>

        <div class="dashboard-grafica__placeholder">

            <strong>
                Sin datos
            </strong>


            <span>
                Sin datos para los filtros seleccionados.
            </span>

        </div>

        <?php endif; ?>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script type="application/json" id="dashboard-evolucion-datos">
    <?= json_encode(
        [
            'tipo' =>
                $esFelicitacion
                    ? 'felicitacion'
                    : 'reporte',

            'agrupacion' =>
                $agrupacion,

            'datos' =>
                $datosFrontend,

            'total' =>
                $totalEvolucion,
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
