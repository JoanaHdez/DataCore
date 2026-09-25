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
   TEXTO PARA TÍTULO
========================================================= */

$textoPrincipal =
    ucfirst(
        $principalSeleccionado
    );


$textoSecundaria =
    ucfirst(
        $secundariaSeleccionada
    );

?>


<section
    class="dashboard-cruce"
    id="dashboard-cruce"
>

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-cruce__encabezado">

        <div>

            <span class="dashboard-cruce__eyebrow">
                Análisis cruzado
            </span>

            <h2 class="dashboard-cruce__titulo">
                <?= esc($textoPrincipal) ?>
                ×
                <?= esc($textoSecundaria) ?>
            </h2>

            <p class="dashboard-cruce__descripcion">
                Relación entre dos dimensiones de las quejas registradas.
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


        <!-- =================================================
             PRINCIPAL
        ================================================== -->

        <div class="dashboard-cruce__selector">

            <label for="dashboard-cruce-principal">
                Analizar por
            </label>


            <select
                id="dashboard-cruce-principal"
                class="dashboard-cruce__selector-control"
            >

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

                    <option
                        value="<?= esc($valor) ?>"
                        <?= $valor === $principalSeleccionado
                            ? 'selected'
                            : '' ?>
                    >
                        <?= esc($texto) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- =================================================
             SECUNDARIA
        ================================================== -->

        <div class="dashboard-cruce__selector">

            <label for="dashboard-cruce-secundaria">
                Comparar con
            </label>


            <select
                id="dashboard-cruce-secundaria"
                class="dashboard-cruce__selector-control"
            >

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

                    <option
                        value="<?= esc($valor) ?>"
                        <?= $valor === $secundariaSeleccionada
                            ? 'selected'
                            : '' ?>
                    >
                        <?= esc($texto) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>


    <!-- =====================================================
         ESPACIO PARA GRÁFICA FINAL
    ====================================================== -->

    <div class="dashboard-cruce__grafica">

        <canvas
            id="dashboard-cruce-chart"
            aria-label="Análisis cruzado de quejas"
            role="img"
        ></canvas>

    </div>


    <!-- =====================================================
         MATRIZ DE DATOS
         TEMPORAL PARA VALIDACIÓN
    ====================================================== -->

    <div class="dashboard-cruce__tabla-contenedor">

        <?php if (
            !empty($categorias)
            && !empty($series)
        ): ?>

            <table class="dashboard-cruce__tabla">

                <thead>

                    <tr>

                        <th>
                            <?= esc($textoPrincipal) ?>
                        </th>

                        <?php foreach ($series as $serie): ?>

                            <th>
                                <?= esc(
                                    $serie['nombre']
                                    ?? ''
                                ) ?>
                            </th>

                        <?php endforeach; ?>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($categorias as $indice => $categoria): ?>

                        <tr>

                            <th>
                                <?= esc($categoria) ?>
                            </th>


                            <?php foreach ($series as $serie): ?>

                                <?php

                                $datosSerie =
                                    $serie['datos']
                                    ?? [];


                                $cantidad =
                                    (int) (
                                        $datosSerie[$indice]
                                        ?? 0
                                    );

                                ?>

                                <td>
                                    <?= esc($cantidad) ?>
                                </td>

                            <?php endforeach; ?>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="dashboard-cruce__vacio">

                <strong>
                    Sin información para mostrar
                </strong>

                <span>
                    No existen registros para la combinación seleccionada.
                </span>

            </div>

        <?php endif; ?>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script
        type="application/json"
        id="dashboard-cruce-datos"
    ><?= json_encode(
        [
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
    ) ?></script>

</section>


<script>
document.addEventListener(
    'DOMContentLoaded',
    () => {

        const principal =
            document.getElementById(
                'dashboard-cruce-principal'
            );


        const secundaria =
            document.getElementById(
                'dashboard-cruce-secundaria'
            );


        if (
            !principal
            || !secundaria
        ) {

            return;
        }


        const actualizarCruce =
            () => {

                const url =
                    new URL(
                        window.location.href
                    );


                url.searchParams.set(
                    'cruce_principal',
                    principal.value
                );


                url.searchParams.set(
                    'cruce_secundaria',
                    secundaria.value
                );


                window.location.href =
                    url.toString();
            };


        principal.addEventListener(
            'change',
            () => {

                /*
                 * Cuando cambia la dimensión principal,
                 * dejamos que el backend valide si la
                 * combinación secundaria sigue siendo válida.
                 */

                const url =
                    new URL(
                        window.location.href
                    );


                url.searchParams.set(
                    'cruce_principal',
                    principal.value
                );


                url.searchParams.delete(
                    'cruce_secundaria'
                );


                window.location.href =
                    url.toString();
            }
        );


        secundaria.addEventListener(
            'change',
            actualizarCruce
        );

    }
);
</script>