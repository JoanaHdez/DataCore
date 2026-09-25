<?php

$estadosQuejas =
    $estadosQuejas
    ?? [];


$estados =
    $estadosQuejas['estados']
    ?? [];


$totales =
    $estadosQuejas['totales']
    ?? [];


$porcentajes =
    $estadosQuejas['porcentajes']
    ?? [];


$totalQuejas =
    (int) (
        $estadosQuejas['total']
        ?? 0
    );

?>


<section class="dashboard-estado" id="dashboard-estado">

    <div class="dashboard-estado__encabezado">

        <div>

            <span class="dashboard-estado__eyebrow">
                Situación actual
            </span>

            <h2 class="dashboard-estado__titulo">
                Estado de las Quejas
            </h2>

            <p class="dashboard-estado__descripcion">
                Distribución de las quejas según su estado actual.
            </p>

        </div>


        <div class="dashboard-estado__resumen">

            <span class="dashboard-estado__resumen-etiqueta">
                Total de quejas
            </span>

            <strong class="dashboard-estado__resumen-valor">
                <?= esc($totalQuejas) ?>
            </strong>

        </div>

    </div>


    <div class="dashboard-estado__contenido">

        <!-- =============================================
             ESPACIO PARA LA GRÁFICA
        ============================================== -->

        <div class="dashboard-estado__grafica">

            <canvas id="dashboard-estado-chart" aria-label="Distribución de las quejas por estado" role="img"></canvas>

        </div>


        <!-- =============================================
             RESUMEN DE DATOS
        ============================================== -->

        <div class="dashboard-estado__lista">

            <?php foreach ($estados as $indice => $estado): ?>

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

            <div class="dashboard-estado__item">

                <div class="dashboard-estado__item-info">

                    <span class="dashboard-estado__item-estado">
                        <?= esc($estado) ?>
                    </span>

                    <span class="dashboard-estado__item-porcentaje">
                        <?= esc($porcentaje) ?>%
                    </span>

                </div>


                <strong class="dashboard-estado__item-total">
                    <?= esc($cantidad) ?>
                </strong>

            </div>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- =============================================
         DATOS PARA LA GRÁFICA POSTERIOR
    ============================================== -->

    <script type="application/json" id="dashboard-estado-datos">
    <?= json_encode(
        [
            'estados' =>
                $estados,

            'totales' =>
                $totales,

            'porcentajes' =>
                $porcentajes,

            'total' =>
                $totalQuejas,
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