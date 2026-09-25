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
                Comportamiento de los reportes registrados
                durante el periodo consultado.
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

        <?php if (
            !empty(
                $datosFrontend
            )
        ): ?>

        <canvas id="dashboard-evolucion-chart" class="dashboard-evolucion__canvas"
            aria-label="Gráfica de evolución temporal de reportes" role="img"></canvas>

        <?php else: ?>

        <div class="dashboard-evolucion__vacio">

            <div class="dashboard-evolucion__vacio-icono" aria-hidden="true">

                <svg viewBox="0 0 24 24">

                    <path d="M4 19V5" />
                    <path d="M4 19h16" />
                    <path d="m7 15 4-4 3 2 5-6" />

                </svg>

            </div>


            <strong>
                Sin información para mostrar
            </strong>

            <span>
                No existen registros para los filtros seleccionados.
            </span>

        </div>

        <?php endif; ?>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT

         No se muestran visualmente.
         El JS de evolución los utilizará para generar Chart.js.
    ====================================================== -->

    <script type="application/json" id="dashboard-evolucion-datos">
    <?= json_encode(
        $datosFrontend,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) ?>
    </script>

</section>