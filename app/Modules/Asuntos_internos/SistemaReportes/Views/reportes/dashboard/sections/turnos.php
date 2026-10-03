<?php

$quejasPorTurno =
    $quejasPorTurno
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


$tituloTurno =
    $esFelicitacion
        ? 'Felicitaciones por turno'
        : 'Quejas por turno';


$descripcionTurno =
    $esFelicitacion
        ? 'Distribución general de las felicitaciones registradas de acuerdo con el turno relacionado.'
        : 'Distribución general de las quejas registradas de acuerdo con el turno relacionado.';


$textoTotal =
    $esFelicitacion
        ? 'felicitaciones'
        : 'quejas';


$textoRegistroSingular =
    $esFelicitacion
        ? 'felicitación'
        : 'queja';


$textoRegistroPlural =
    $esFelicitacion
        ? 'felicitaciones'
        : 'quejas';


$ariaTurno =
    $esFelicitacion
        ? 'Gráfica de felicitaciones por turno'
        : 'Gráfica de quejas por turno';


$turnos =
    $quejasPorTurno['turnos']
    ?? [];


$totales =
    $quejasPorTurno['totales']
    ?? [];


$total =
    (int) (
        $quejasPorTurno['total']
        ?? 0
    );


/* =========================================================
   NORMALIZAR
========================================================= */

if (
    !is_array(
        $turnos
    )
) {

    $turnos = [];
}


if (
    !is_array(
        $totales
    )
) {

    $totales = [];
}


/* =========================================================
   PREPARAR DETALLE
========================================================= */

$filasTurno =
    [];


foreach (
    $turnos
    as $indice => $turno
) {

    $nombre =
        trim(
            (string) $turno
        );


    if (
        $nombre === ''
    ) {

        continue;
    }


    $cantidad =
        (int) (
            $totales[$indice]
            ?? 0
        );


    $porcentaje =
        $total > 0
            ? round(
                (
                    $cantidad
                    / $total
                )
                * 100,
                1
            )
            : 0;


    $filasTurno[] = [

        'turno' =>
            $nombre,

        'total' =>
            $cantidad,

        'porcentaje' =>
            $porcentaje,

    ];
}


$sinDatosTurno =
    empty($filasTurno)
    || $total <= 0;

?>


<section class="dashboard-grafica dashboard-grafica--turnos">


    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-turnos__encabezado">

        <div class="dashboard-turnos__encabezado-info">

            <span class="dashboard-grafica__eyebrow">
                Distribución operativa
            </span>


            <h2 class="dashboard-grafica__titulo">
                <?= esc($tituloTurno) ?>
            </h2>


            <p class="dashboard-grafica__descripcion">
                <?= esc($descripcionTurno) ?>
            </p>

        </div>


        <div class="dashboard-turnos__total">

            <span>
                Total
            </span>


            <strong id="turnos-total">
                <?= esc($total) ?>
            </strong>


            <small>
                <?= esc($textoTotal) ?>
            </small>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="dashboard-turnos__contenido">


        <?php if ($sinDatosTurno): ?>


        <div class="dashboard-grafica__placeholder">

            <strong>
                Sin datos
            </strong>


            <span>
                Sin datos para los filtros seleccionados.
            </span>

        </div>


        <?php else: ?>


        <!-- =================================================
                 GRÁFICA
            ================================================== -->

        <div class="dashboard-turnos__panel">

            <div class="dashboard-turnos__panel-header">

                <div>

                    <span class="dashboard-turnos__panel-eyebrow">
                        Distribución
                    </span>


                    <h3 class="dashboard-turnos__panel-titulo">
                        Registros por turno
                    </h3>

                </div>

            </div>


            <div class="
                        dashboard-grafica__canvas
                        dashboard-grafica__canvas--turnos
                    ">

                <canvas id="grafica-turnos" aria-label="<?= esc($ariaTurno) ?>" role="img"></canvas>

            </div>

        </div>


        <!-- =================================================
                 DETALLE
            ================================================== -->

        <div class="
                    dashboard-turnos__panel
                    dashboard-turnos__panel--detalle
                ">

            <div class="dashboard-turnos__panel-header">

                <div>

                    <span class="dashboard-turnos__panel-eyebrow">
                        Detalle
                    </span>


                    <h3 class="dashboard-turnos__panel-titulo">
                        Todos los turnos
                    </h3>

                </div>


                <span class="dashboard-turnos__panel-registros">
                    <?= esc(
                            count(
                                $filasTurno
                            )
                        ) ?>
                    categorías
                </span>

            </div>


            <div class="dashboard-turnos__lista">

                <?php foreach ($filasTurno as $fila): ?>

                <?php

                        $cantidad =
                            (int) (
                                $fila['total']
                                ?? 0
                            );


                        $porcentaje =
                            (float) (
                                $fila['porcentaje']
                                ?? 0
                            );

                        ?>


                <article class="dashboard-turnos__item">

                    <div class="dashboard-turnos__item-cabecera">

                        <span class="dashboard-turnos__item-nombre">
                            <?= esc(
                                        $fila['turno']
                                        ?? ''
                                    ) ?>
                        </span>


                        <strong class="dashboard-turnos__item-porcentaje">
                            <?= esc($porcentaje) ?>%
                        </strong>

                    </div>


                    <div class="dashboard-turnos__item-datos">

                        <strong class="dashboard-turnos__item-total">
                            <?= esc($cantidad) ?>
                        </strong>


                        <span>
                            <?= esc(
                                        $cantidad === 1
                                            ? $textoRegistroSingular
                                            : $textoRegistroPlural
                                    ) ?>
                        </span>

                    </div>


                    <div class="dashboard-turnos__progreso" aria-hidden="true">

                        <span style="width: <?= esc(
                                        min(
                                            100,
                                            max(
                                                0,
                                                $porcentaje
                                            )
                                        )
                                    ) ?>%;"></span>

                    </div>

                </article>

                <?php endforeach; ?>

            </div>

        </div>


        <?php endif; ?>


    </div>


</section>


<!-- =========================================================
     DATOS PARA JAVASCRIPT
========================================================= -->

<script type="application/json" id="datos-grafica-turnos">
<?= json_encode(
    [
        'tipo' =>
            $esFelicitacion
                ? 'felicitacion'
                : 'queja',

        'titulo' =>
            $tituloTurno,

        'turnos' =>
            $turnos,

        'totales' =>
            $totales,

        'total' =>
            $total,
    ],
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) ?>
</script>