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


<section
    class="dashboard-dimension"
    id="dashboard-dimension"
>

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-dimension__encabezado">

        <div>

            <span class="dashboard-dimension__eyebrow">
                Distribución institucional
            </span>

            <h2 class="dashboard-dimension__titulo">
                Análisis por <?= esc($tituloDimension) ?>
            </h2>

            <p class="dashboard-dimension__descripcion">
                Distribución de las quejas según la dimensión seleccionada.
            </p>

        </div>


        <div class="dashboard-dimension__resumen">

            <span class="dashboard-dimension__resumen-etiqueta">
                Total asociado
            </span>

            <strong
                class="dashboard-dimension__resumen-valor"
                id="dashboard-dimension-total"
            >
                <?= esc($totalDimension) ?>
            </strong>

        </div>

    </div>


    <!-- =====================================================
         SELECTOR
    ====================================================== -->

    <div class="dashboard-dimension__selector">

        <label
            for="dashboard-dimension-select"
            class="dashboard-dimension__selector-label"
        >
            Analizar por
        </label>


        <select
            id="dashboard-dimension-select"
            class="dashboard-dimension__selector-control"
        >

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

                <option
                    value="<?= esc($valor) ?>"
                    <?= $valor === $dimensionSeleccionada
                        ? 'selected'
                        : '' ?>
                >
                    <?= esc($texto) ?>
                </option>

            <?php endforeach; ?>

        </select>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="dashboard-dimension__contenido">

        <!-- =============================================
             ESPACIO PARA LA GRÁFICA
        ============================================== -->

        <div class="dashboard-dimension__grafica">

            <canvas
                id="dashboard-dimension-chart"
                aria-label="Distribución de quejas por dimensión"
                role="img"
            ></canvas>

        </div>


        <!-- =============================================
             LISTA DE RESULTADOS
        ============================================== -->

        <div class="dashboard-dimension__lista">

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

                    <div class="dashboard-dimension__item">

                        <div class="dashboard-dimension__item-info">

                            <span class="dashboard-dimension__item-etiqueta">
                                <?= esc($etiqueta) ?>
                            </span>

                            <span class="dashboard-dimension__item-porcentaje">
                                <?= esc($porcentaje) ?>%
                            </span>

                        </div>


                        <strong class="dashboard-dimension__item-total">
                            <?= esc($cantidad) ?>
                        </strong>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="dashboard-dimension__vacio">

                    <strong>
                        Sin información para mostrar
                    </strong>

                    <span>
                        No existen registros para la dimensión seleccionada.
                    </span>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script
        type="application/json"
        id="dashboard-dimension-datos"
    ><?= json_encode(
        [
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
    ) ?></script>

</section>


<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const selector =
            document.getElementById(
                'dashboard-dimension-select'
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
                    'dimension',
                    selector.value
                );


                window.location.href =
                    url.toString();

            }
        );

    }
);
</script>