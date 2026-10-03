<?php

$rankingDashboard =
    $rankingDashboard
    ?? [];


$tipoSeleccionado =
    trim(
        (string) (
            $rankingDashboard['tipo']
            ?? 'sector'
        )
    );


$titulo =
    trim(
        (string) (
            $rankingDashboard['titulo']
            ?? 'Sectores'
        )
    );


$etiquetas =
    $rankingDashboard['etiquetas']
    ?? [];


$totales =
    $rankingDashboard['totales']
    ?? [];


$porcentajes =
    $rankingDashboard['porcentajes']
    ?? [];


$totalTop =
    (int) (
        $rankingDashboard['total_top']
        ?? 0
    );


$opciones =
    $rankingDashboard['opciones']
    ?? [];


$detallesRanking =
    $rankingDashboard['detalles_ranking']
    ?? (
        $rankingDashboard['detalles_personal']
        ?? []
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


/* =========================================================
   TEXTOS
========================================================= */

if (
    $tipoSeleccionado === 'personal'
) {

    $descripcionRanking =
        $esFelicitacion
            ? 'Personal con mayor número de felicitaciones asociadas.'
            : 'Personal con mayor número de quejas asociadas.';

} else {

    $descripcionRanking =
        $esFelicitacion
            ? 'Principales resultados de felicitaciones según la categoría seleccionada.'
            : 'Principales concentraciones de quejas según la categoría seleccionada.';
}


$ariaRanking =
    $esFelicitacion
        ? 'Ranking Top 5 de felicitaciones'
        : 'Ranking Top 5 de quejas';


/* =========================================================
   NORMALIZAR
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


if (!is_array($detallesRanking)) {
    $detallesRanking = [];
}


/* =========================================================
   DETALLE
========================================================= */

$tiposConDetalleRanking = [
    'sector',
    'area',
    'unidad',
    'personal',
];


$mostrarDetalleRanking =
    in_array(
        $tipoSeleccionado,
        $tiposConDetalleRanking,
        true
    )
    && !$esFelicitacion
    && !empty($detallesRanking);


$sinDatosRanking =
    empty($etiquetas)
    || $totalTop <= 0;

?>


<section class="
        dashboard-ranking
        <?= $esFelicitacion
            ? 'dashboard-ranking--felicitaciones'
            : 'dashboard-ranking--quejas' ?>
    " id="dashboard-ranking">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-ranking__encabezado">

        <div class="dashboard-ranking__encabezado-info">

            <span class="dashboard-ranking__eyebrow">
                Ranking
            </span>


            <h2 class="dashboard-ranking__titulo">
                Top 5 — <?= esc($titulo) ?>
            </h2>


            <p class="dashboard-ranking__descripcion">
                <?= esc($descripcionRanking) ?>
            </p>

        </div>


        <div class="dashboard-ranking__resumen">

            <span class="dashboard-ranking__resumen-etiqueta">
                Total Top 5
            </span>


            <strong class="dashboard-ranking__resumen-valor" id="dashboard-ranking-total">
                <?= esc($totalTop) ?>
            </strong>


            <small>
                <?= $esFelicitacion
                    ? 'felicitaciones'
                    : 'quejas' ?>
            </small>

        </div>

    </div>


    <!-- =====================================================
         SELECTOR VISUAL
    ====================================================== -->

    <div class="dashboard-ranking__selector">

        <span class="dashboard-ranking__selector-label">
            Mostrar ranking de
        </span>


        <div class="dashboard-ranking__tabs" role="group" aria-label="Seleccionar ranking">

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
                    $valor === $tipoSeleccionado;

                ?>


            <button type="button" class="
                        dashboard-ranking__tab
                        <?= $activo
                            ? 'dashboard-ranking__tab--activo'
                            : '' ?>
                    " data-ranking="<?= esc($valor) ?>" aria-pressed="<?= $activo ? 'true' : 'false' ?>">
                <?= esc($texto) ?>
            </button>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <?php if ($sinDatosRanking): ?>

    <div class="dashboard-ranking__vacio">

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

    <div class="dashboard-ranking__panel">

        <!-- =============================================
                 GRÁFICA
            ============================================== -->

        <div class="dashboard-ranking__bloque">

            <div class="dashboard-ranking__bloque-header">

                <div>

                    <span class="dashboard-ranking__bloque-eyebrow">
                        Distribución
                    </span>

                    <h3 class="dashboard-ranking__bloque-titulo">
                        Top 5 — <?= esc($titulo) ?>
                    </h3>

                </div>

            </div>


            <div class="dashboard-ranking__grafica">

                <canvas id="dashboard-ranking-chart" aria-label="<?= esc($ariaRanking) ?>" role="img"></canvas>

            </div>

        </div>


        <!-- =============================================
                 CLASIFICACIÓN
            ============================================== -->

        <div class="
                    dashboard-ranking__bloque
                    dashboard-ranking__bloque--clasificacion
                ">

            <div class="dashboard-ranking__bloque-header">

                <div>

                    <span class="dashboard-ranking__bloque-eyebrow">
                        Clasificación
                    </span>

                    <h3 class="dashboard-ranking__bloque-titulo">
                        Posiciones
                    </h3>

                </div>

            </div>


            <div class="dashboard-ranking__lista">

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


                        $posicion =
                            $indice + 1;

                        ?>


                <article class="
                                dashboard-ranking__item
                                dashboard-ranking__item--<?= esc(
                                    (string) $posicion
                                ) ?>
                            ">

                    <!-- =================================
                                 POSICIÓN
                            ================================== -->

                    <div class="dashboard-ranking__posicion">

                        <?php if (
                                    $esFelicitacion
                                    && $posicion <= 3
                                ): ?>

                        <span class="
                                            dashboard-ranking__medalla
                                            dashboard-ranking__medalla--<?= esc(
                                                (string) $posicion
                                            ) ?>
                                        " aria-hidden="true"></span>

                        <?php endif; ?>


                        <strong>
                            <?= esc($posicion) ?>
                        </strong>

                    </div>


                    <!-- =================================
                                 INFORMACIÓN
                            ================================== -->

                    <div class="dashboard-ranking__item-info">

                        <span class="dashboard-ranking__item-etiqueta">
                            <?= esc($etiqueta) ?>
                        </span>


                        <div class="dashboard-ranking__item-meta">

                            <span class="dashboard-ranking__item-porcentaje">
                                <?= esc($porcentaje) ?>%
                            </span>


                            <span aria-hidden="true">
                                ·
                            </span>


                            <span>
                                <?= esc($cantidad) ?>
                                <?= $cantidad === 1
                                            ? (
                                                $esFelicitacion
                                                    ? 'felicitación'
                                                    : 'queja'
                                            )
                                            : (
                                                $esFelicitacion
                                                    ? 'felicitaciones'
                                                    : 'quejas'
                                            ) ?>
                            </span>

                        </div>

                    </div>


                    <!-- =================================
                                 TOTAL
                            ================================== -->

                    <strong class="dashboard-ranking__item-total">
                        <?= esc($cantidad) ?>
                    </strong>


                    <!-- =================================
                                 DETALLE
                            ================================== -->

                    <?php if ($mostrarDetalleRanking): ?>

                    <button type="button" class="dashboard-ranking__detalle" data-ranking-personal-detalle
                        data-ranking-personal-detalle-indice="<?= esc(
                                        (string) $indice
                                    ) ?>">
                        Ver detalle
                    </button>

                    <?php endif; ?>

                </article>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

    <?php endif; ?>


    <!-- =====================================================
         DATOS GRÁFICA
    ====================================================== -->

    <script type="application/json" id="dashboard-ranking-datos">
    <?= json_encode(
            [
                'tipo_registro' =>
                    $esFelicitacion
                        ? 'felicitacion'
                        : 'reporte',

                'tipo' =>
                    $tipoSeleccionado,

                'titulo' =>
                    $titulo,

                'etiquetas' =>
                    $etiquetas,

                'totales' =>
                    $totales,

                'porcentajes' =>
                    $porcentajes,

                'total_top' =>
                    $totalTop,
            ],
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        ) ?>
    </script>


    <!-- =====================================================
         DATOS DETALLE
    ====================================================== -->

    <script type="application/json" id="dashboard-ranking-personal-detalles">
    <?= json_encode(
            $detallesRanking,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        ) ?>
    </script>


    <!-- =====================================================
         MODAL
         Se conserva funcionalmente.
         El visual lo trabajaremos después.
    ====================================================== -->

    <?php if ($mostrarDetalleRanking): ?>

    <div class="modal-reporte" id="modal-ranking-personal-detalle" aria-hidden="true">

        <div class="modal-reporte__overlay" data-ranking-personal-detalle-cerrar></div>


        <div class="modal-reporte__dialog" role="dialog" aria-modal="true"
            aria-labelledby="ranking-personal-detalle-titulo">

            <header class="modal-reporte__header">

                <div>

                    <span class="modal-reporte__eyebrow">
                        Ranking
                    </span>


                    <h3 class="modal-reporte__title" id="ranking-personal-detalle-titulo">
                        Detalle de quejas y motivos
                    </h3>


                    <p class="dashboard-ranking__descripcion" id="ranking-personal-detalle-nombre"></p>

                </div>


                <button type="button" class="modal-reporte__close" aria-label="Cerrar"
                    data-ranking-personal-detalle-cerrar>
                    &times;
                </button>

            </header>


            <div class="
                        modal-reporte__body
                        dashboard-personal-individual__detalle
                    ">

                <p>
                    Total de quejas:
                    <strong id="ranking-personal-detalle-total">
                        0
                    </strong>
                </p>


                <h4>
                    Folios considerados
                </h4>


                <ul id="ranking-personal-detalle-folios"></ul>


                <h4>
                    Motivos agrupados
                </h4>


                <div id="ranking-personal-detalle-motivos"></div>


                <div class="dashboard-grafica__placeholder" id="ranking-personal-detalle-motivos-vacio" hidden>

                    <strong>
                        Sin motivos
                    </strong>


                    <span>
                        Sin motivos registrados para los filtros seleccionados.
                    </span>

                </div>

            </div>

        </div>

    </div>

    <?php endif; ?>

</section>