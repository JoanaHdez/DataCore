<?php

$quejasPorSector =
    $quejasPorSector
    ?? [];


$sectores =
    $quejasPorSector['sectores']
    ?? [];


$totales =
    $quejasPorSector['totales']
    ?? [];


$totalSector =
    (int) (
        $quejasPorSector['total']
        ?? 0
    );


/* =========================================================
   PREPARAR FILAS
========================================================= */

$filas = [];


foreach (
    $sectores
    as $indice => $sector
) {

    $cantidad =
        (int) (
            $totales[$indice]
            ?? 0
        );


    $filas[] = [

        'sector' =>
            $sector,

        'total' =>
            $cantidad,

    ];
}


/* =========================================================
   ORDENAR MAYOR → MENOR
========================================================= */

usort(
    $filas,
    static function (
        array $a,
        array $b
    ): int {

        return
            $b['total']
            <=>
            $a['total'];
    }
);

?>


<section class="dashboard-sectores" id="dashboard-sectores">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-sectores__encabezado">

        <div>

            <span class="dashboard-sectores__eyebrow">
                Distribución operativa
            </span>

            <h2 class="dashboard-sectores__titulo">
                Quejas por sector
            </h2>

            <p class="dashboard-sectores__descripcion">
                Distribución de las quejas según el sector
                relacionado con el personal involucrado.
            </p>

        </div>


        <div class="dashboard-sectores__resumen">

            <span class="dashboard-sectores__resumen-etiqueta">
                Registros sectorizados
            </span>

            <strong class="dashboard-sectores__resumen-valor" id="dashboard-sectores-total">
                <?= esc($totalSector) ?>
            </strong>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="dashboard-sectores__contenido">


        <!-- =================================================
             ESPACIO PARA GRÁFICA
        ================================================== -->

        <div class="dashboard-sectores__grafica">

            <canvas id="dashboard-sectores-chart" aria-label="Quejas por sector" role="img"></canvas>

        </div>


        <!-- =================================================
             DATOS
        ================================================== -->

        <div class="dashboard-sectores__lista">

            <?php foreach ($filas as $fila): ?>

            <?php

                $cantidad =
                    (int) (
                        $fila['total']
                        ?? 0
                    );


                $porcentaje =
                    $totalSector > 0
                    ? round(
                        ($cantidad / $totalSector) * 100,
                        1
                    )
                    : 0;

                ?>


            <div class="dashboard-sectores__item">

                <div class="dashboard-sectores__item-info">

                    <span class="dashboard-sectores__item-sector">
                        <?= esc(
                                $fila['sector']
                                ?? ''
                            ) ?>
                    </span>


                    <span class="dashboard-sectores__item-porcentaje">
                        <?= esc($porcentaje) ?>%
                    </span>

                </div>


                <strong class="dashboard-sectores__item-total">
                    <?= esc($cantidad) ?>
                </strong>

            </div>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script type="application/json" id="dashboard-sectores-datos">
    <?= json_encode(
        [
            'sectores' =>
                $sectores,

            'totales' =>
                $totales,

            'total' =>
                $totalSector,
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