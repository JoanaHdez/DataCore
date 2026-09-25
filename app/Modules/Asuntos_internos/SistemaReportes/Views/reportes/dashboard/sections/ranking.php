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
   TEXTOS DINÁMICOS
========================================================= */

if (
    $tipoSeleccionado === 'personal'
) {

    $descripcionRanking =
        $esFelicitacion
            ? 'Personal con mayor número de felicitaciones asociadas.'
            : 'Personal con mayor número de registros asociados.';

} else {

    $descripcionRanking =
        $esFelicitacion
            ? 'Principales resultados de felicitaciones según la categoría seleccionada.'
            : 'Principales resultados según la categoría seleccionada.';
}


$ariaRanking =
    $esFelicitacion
        ? 'Ranking Top 5 de felicitaciones'
        : 'Ranking Top 5 de registros';


$textoSinDatos =
    $esFelicitacion
        ? 'No existen felicitaciones para el ranking seleccionado.'
        : 'No existen registros para el ranking seleccionado.';


/* =========================================================
   NORMALIZAR ARREGLOS
========================================================= */

if (
    !is_array(
        $etiquetas
    )
) {

    $etiquetas = [];
}


if (
    !is_array(
        $totales
    )
) {

    $totales = [];
}


if (
    !is_array(
        $porcentajes
    )
) {

    $porcentajes = [];
}


if (
    !is_array(
        $opciones
    )
) {

    $opciones = [];
}

?>


<section class="dashboard-ranking" id="dashboard-ranking">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-ranking__encabezado">

        <div>

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

        </div>

    </div>


    <!-- =====================================================
         SELECTOR
    ====================================================== -->

    <div class="dashboard-ranking__selector">

        <label for="dashboard-ranking-select" class="dashboard-ranking__selector-label">
            Mostrar ranking de
        </label>


        <select id="dashboard-ranking-select" class="dashboard-ranking__selector-control">

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

                ?>

            <option value="<?= esc($valor) ?>" <?= $valor === $tipoSeleccionado
                        ? 'selected'
                        : '' ?>>
                <?= esc($texto) ?>
            </option>

            <?php endforeach; ?>

        </select>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="dashboard-ranking__contenido">


        <!-- =============================================
             ESPACIO PARA GRÁFICA FINAL
        ============================================== -->

        <div class="dashboard-ranking__grafica">

            <canvas id="dashboard-ranking-chart" aria-label="<?= esc($ariaRanking) ?>" role="img"></canvas>

        </div>


        <!-- =============================================
             LISTA TOP 5
        ============================================== -->

        <div class="dashboard-ranking__lista">

            <?php if (!empty($etiquetas)): ?>

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


            <div class="dashboard-ranking__item">

                <div class="dashboard-ranking__posicion">
                    <?= esc($indice + 1) ?>
                </div>


                <div class="dashboard-ranking__item-info">

                    <span class="dashboard-ranking__item-etiqueta">
                        <?= esc($etiqueta) ?>
                    </span>


                    <span class="dashboard-ranking__item-porcentaje">
                        <?= esc($porcentaje) ?>%
                    </span>

                </div>


                <strong class="dashboard-ranking__item-total">
                    <?= esc($cantidad) ?>
                </strong>

            </div>

            <?php endforeach; ?>


            <?php else: ?>

            <div class="dashboard-ranking__vacio">

                <strong>
                    Sin información para mostrar
                </strong>


                <span>
                    <?= esc($textoSinDatos) ?>
                </span>

            </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
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

</section>


<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const selector =
            document.getElementById(
                'dashboard-ranking-select'
            );


        if (!selector) {
            return;
        }


        selector.addEventListener(
            'change',
            () => {

                const url =
                    new URL(
                        window.location.href
                    );


                url.searchParams.set(
                    'ranking',
                    selector.value
                );


                window.location.href =
                    url.toString();

            }
        );

    }
);
</script>