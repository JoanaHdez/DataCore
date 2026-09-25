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

?>


<section class="dashboard-grafica dashboard-grafica--turnos">

    <div class="dashboard-turnos__encabezado">

        <div>

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


    <div class="dashboard-turnos__contenido">

        <div class="
                dashboard-grafica__canvas
                dashboard-grafica__canvas--turnos
            ">

            <canvas id="grafica-turnos" aria-label="<?= esc($ariaTurno) ?>"></canvas>

        </div>

    </div>

</section>


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