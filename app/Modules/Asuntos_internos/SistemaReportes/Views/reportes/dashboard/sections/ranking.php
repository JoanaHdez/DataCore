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
   TEXTOS DINÁMICOS
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
            : 'Principales resultados de quejas según la categoría seleccionada.';
}


$ariaRanking =
    $esFelicitacion
        ? 'Ranking Top 5 de felicitaciones'
        : 'Ranking Top 5 de quejas';


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


if (
    !is_array(
        $detallesRanking
    )
) {

    $detallesRanking = [];
}


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

        <?php if ($sinDatosRanking): ?>

        <div class="dashboard-grafica__placeholder">
            <strong>
                Sin datos
            </strong>

            <span>
                Sin datos para los filtros seleccionados.
            </span>
        </div>

        <?php else: ?>


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

                <?php if ($mostrarDetalleRanking): ?>

                <button
                    type="button"
                    class="dashboard-personal-individual__boton"
                    data-ranking-personal-detalle
                    data-ranking-personal-detalle-indice="<?= esc((string) $indice) ?>"
                >
                    Ver detalle
                </button>

                <?php endif; ?>

            </div>

            <?php endforeach; ?>

        </div>

        <?php endif; ?>

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


    <?php if ($mostrarDetalleRanking): ?>

    <div
        class="modal-reporte"
        id="modal-ranking-personal-detalle"
        aria-hidden="true"
    >

        <div
            class="modal-reporte__overlay"
            data-ranking-personal-detalle-cerrar
        ></div>


        <div class="modal-reporte__dialog" role="dialog" aria-modal="true" aria-labelledby="ranking-personal-detalle-titulo">

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

                <button
                    type="button"
                    class="modal-reporte__close"
                    aria-label="Cerrar"
                    data-ranking-personal-detalle-cerrar
                >
                    &times;
                </button>

            </header>


            <div class="modal-reporte__body dashboard-personal-individual__detalle">

                <p>
                    Total de quejas:
                    <strong id="ranking-personal-detalle-total">0</strong>
                </p>


                <h4>Folios considerados</h4>

                <ul id="ranking-personal-detalle-folios"></ul>


                <h4>Motivos agrupados</h4>

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


        const detallesJson =
            document.getElementById(
                'dashboard-ranking-personal-detalles'
            );

        const modal =
            document.getElementById(
                'modal-ranking-personal-detalle'
            );


        if (!detallesJson || !modal) {
            return;
        }


        if (modal.parentElement !== document.body) {
            document.body.appendChild(
                modal
            );
        }


        let detalles = [];


        try {

            detalles =
                JSON.parse(
                    detallesJson.textContent
                    || '[]'
                );

        } catch (error) {

            detalles = [];
        }


        const nombre =
            document.getElementById(
                'ranking-personal-detalle-nombre'
            );

        const total =
            document.getElementById(
                'ranking-personal-detalle-total'
            );

        const folios =
            document.getElementById(
                'ranking-personal-detalle-folios'
            );

        const motivos =
            document.getElementById(
                'ranking-personal-detalle-motivos'
            );

        const motivosVacio =
            document.getElementById(
                'ranking-personal-detalle-motivos-vacio'
            );


        const limpiarNodo =
            (nodo) => {

                if (!nodo) {
                    return;
                }


                while (nodo.firstChild) {
                    nodo.removeChild(nodo.firstChild);
                }
            };


        const agregarLista =
            (contenedor, valores) => {

                limpiarNodo(
                    contenedor
                );


                valores.forEach(
                    (valor) => {

                        const item =
                            document.createElement(
                                'li'
                            );

                        item.textContent =
                            String(
                                valor
                            );

                        contenedor.appendChild(
                            item
                        );

                    }
                );
            };


        const renderizarMotivos =
            (items) => {

                limpiarNodo(
                    motivos
                );


                const hayMotivos =
                    Array.isArray(
                        items
                    )
                    && items.length > 0;


                if (motivosVacio) {
                    motivosVacio.hidden =
                        hayMotivos;

                    motivosVacio.style.display =
                        hayMotivos
                            ? 'none'
                            : '';
                }


                if (!hayMotivos || !motivos) {
                    return;
                }


                items.forEach(
                    (item) => {

                        const bloque =
                            document.createElement(
                                'article'
                            );

                        bloque.className =
                            'dashboard-personal-individual__motivo';


                        const titulo =
                            document.createElement(
                                'strong'
                            );

                        titulo.textContent =
                            item.motivo
                            || 'Sin información';


                        const variantes =
                            Array.isArray(item.variantes)
                                ? item.variantes
                                : [];

                        const variantesContenedor =
                            document.createElement(
                                'div'
                            );

                        variantesContenedor.className =
                            'dashboard-personal-individual__motivo-variantes';


                        variantes.forEach(
                            (variante) => {

                                const varianteBloque =
                                    document.createElement(
                                        'div'
                                    );

                                varianteBloque.className =
                                    'dashboard-personal-individual__motivo-variante';


                                const sancion =
                                    document.createElement(
                                        'strong'
                                    );

                                sancion.textContent =
                                    `Sanción: ${variante.sancion || 'Sin sanción'}`;

                                varianteBloque.appendChild(
                                    sancion
                                );


                                if (
                                    variante.horas_arresto !== null
                                    && variante.horas_arresto !== undefined
                                    && variante.horas_arresto !== ''
                                ) {

                                    const horas =
                                        document.createElement(
                                            'span'
                                        );

                                    horas.textContent =
                                        `Horas de arresto: ${variante.horas_arresto}`;

                                    varianteBloque.appendChild(
                                        horas
                                    );
                                }


                                const cantidadVariante =
                                    document.createElement(
                                        'span'
                                    );

                                cantidadVariante.textContent =
                                    `${Number(variante.cantidad_quejas || variante.cantidad || 0)} quejas`;

                                varianteBloque.appendChild(
                                    cantidadVariante
                                );


                                if (
                                    Array.isArray(variante.folios)
                                    && variante.folios.length > 0
                                ) {

                                    const foliosVariante =
                                        document.createElement(
                                            'small'
                                        );

                                    foliosVariante.textContent =
                                        `Folios: ${variante.folios.join(', ')}`;

                                    varianteBloque.appendChild(
                                        foliosVariante
                                    );
                                }


                                variantesContenedor.appendChild(
                                    varianteBloque
                                );

                            }
                        );


                        bloque.appendChild(
                            titulo
                        );

                        if (
                            variantes.length > 0
                        ) {

                            bloque.appendChild(
                                variantesContenedor
                            );
                        }

                        motivos.appendChild(
                            bloque
                        );

                    }
                );
            };


        const abrirModal =
            (detalle) => {

                if (!detalle) {
                    return;
                }


                if (nombre) {
                    nombre.textContent =
                        detalle.nombre
                        || '';
                }


                if (total) {
                    total.textContent =
                        String(
                            detalle.total_quejas
                            || 0
                        );
                }


                if (folios) {
                    agregarLista(
                        folios,
                        Array.isArray(detalle.folios)
                            ? detalle.folios
                            : []
                    );
                }


                renderizarMotivos(
                    detalle.motivos
                    || []
                );


                modal.classList.add(
                    'modal-reporte--visible'
                );

                modal.setAttribute(
                    'aria-hidden',
                    'false'
                );

                document.body.classList.add(
                    'modal-abierto'
                );
            };


        const cerrarModal =
            () => {

                modal.classList.remove(
                    'modal-reporte--visible'
                );

                modal.setAttribute(
                    'aria-hidden',
                    'true'
                );

                document.body.classList.remove(
                    'modal-abierto'
                );
            };


        document
            .querySelectorAll(
                '[data-ranking-personal-detalle]'
            )
            .forEach(
                (boton) => {

                    boton.addEventListener(
                        'click',
                        () => {

                            const indice =
                                Number(
                                    boton.dataset.rankingPersonalDetalleIndice
                                );

                            abrirModal(
                                detalles[indice]
                            );

                        }
                    );

                }
            );


        modal
            .querySelectorAll(
                '[data-ranking-personal-detalle-cerrar]'
            )
            .forEach(
                (boton) => {

                    boton.addEventListener(
                        'click',
                        cerrarModal
                    );

                }
            );


        document.addEventListener(
            'keydown',
            (evento) => {

                if (
                    evento.key === 'Escape'
                    && modal.classList.contains(
                        'modal-reporte--visible'
                    )
                ) {

                    cerrarModal();
                }

            }
        );

    }
);
</script>
