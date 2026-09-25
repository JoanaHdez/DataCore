<?php

$hallazgosDashboard =
    $hallazgosDashboard
    ?? [];


if (
    !is_array(
        $hallazgosDashboard
    )
) {

    $hallazgosDashboard = [];
}

?>


<?php if (!empty($hallazgosDashboard)): ?>

<section class="dashboard-hallazgos" id="dashboard-hallazgos">

    <div class="dashboard-hallazgos__encabezado">

        <div>

            <span class="dashboard-hallazgos__eyebrow">
                Lectura rápida
            </span>

            <h2 class="dashboard-hallazgos__titulo">
                Hallazgos del periodo
            </h2>

            <p class="dashboard-hallazgos__descripcion">
                Principales resultados obtenidos a partir de los filtros seleccionados.
            </p>

        </div>

    </div>


    <div class="dashboard-hallazgos__grid">

        <?php foreach ($hallazgosDashboard as $hallazgo): ?>

        <?php

            $titulo =
                trim(
                    (string) (
                        $hallazgo['titulo']
                        ?? ''
                    )
                );


            $valor =
                trim(
                    (string) (
                        $hallazgo['valor']
                        ?? ''
                    )
                );


            $descripcion =
                trim(
                    (string) (
                        $hallazgo['descripcion']
                        ?? ''
                    )
                );

            ?>


        <article class="dashboard-hallazgos__item">

            <span class="dashboard-hallazgos__item-etiqueta">
                <?= esc($titulo) ?>
            </span>


            <strong class="dashboard-hallazgos__item-valor">
                <?= esc($valor) ?>
            </strong>


            <p class="dashboard-hallazgos__item-descripcion">
                <?= esc($descripcion) ?>
            </p>

        </article>

        <?php endforeach; ?>

    </div>

</section>

<?php endif; ?>