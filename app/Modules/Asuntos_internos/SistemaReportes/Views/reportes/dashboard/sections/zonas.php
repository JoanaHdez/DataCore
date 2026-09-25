<?php

$quejasPorZona =
    $quejasPorZona
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


$tituloZona =
    $esFelicitacion
        ? 'Felicitaciones por zona'
        : 'Quejas por zona';


$descripcionZona =
    $esFelicitacion
        ? 'Distribución de las felicitaciones registradas de acuerdo con la zona territorial.'
        : 'Distribución de las quejas registradas de acuerdo con la zona territorial.';


$textoTotal =
    $esFelicitacion
        ? 'Felicitaciones'
        : 'Quejas';


$ariaZona =
    $esFelicitacion
        ? 'Gráfica de felicitaciones por zona'
        : 'Gráfica de quejas por zona';


$zonas =
    $quejasPorZona['zonas']
    ?? [
        'Zona Norte',
        'Zona Poniente',
        'Zona Centro',
        'Zona Oriente',
    ];


$totales =
    $quejasPorZona['totales']
    ?? [
        0,
        0,
        0,
        0,
    ];


$total =
    (int) (
        $quejasPorZona['total']
        ?? 0
    );

?>


<section class="dashboard-grafica dashboard-grafica--zona">

    <div class="dashboard-grafica-zona__encabezado">

        <div class="dashboard-grafica-zona__icono" aria-hidden="true">

            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">

                <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" />

                <circle cx="12" cy="10" r="2.3" />

            </svg>

        </div>


        <div>

            <span class="dashboard-grafica__eyebrow">
                Distribución territorial
            </span>


            <h2 class="dashboard-grafica__titulo">
                <?= esc($tituloZona) ?>
            </h2>


            <p class="dashboard-grafica__descripcion">
                <?= esc($descripcionZona) ?>
            </p>

        </div>

    </div>


    <div class="dashboard-grafica-zona__contenido">

        <div class="dashboard-grafica-zona__resumen">

            <span>
                Total registrado
            </span>


            <strong id="grafica-zonas-total">
                <?= esc($total) ?>
            </strong>


            <small>
                <?= esc($textoTotal) ?>
            </small>

        </div>


        <div class="
                dashboard-grafica__canvas
                dashboard-grafica__canvas--zonas
            ">

            <canvas id="grafica-zonas" aria-label="<?= esc($ariaZona) ?>"></canvas>

        </div>

    </div>


    <!-- =====================================================
         DATOS PARA JAVASCRIPT
    ====================================================== -->

    <script type="application/json" id="dashboard-datos-zonas">
    <?= json_encode(
        [
            'tipo' =>
                $esFelicitacion
                    ? 'felicitacion'
                    : 'queja',

            'titulo' =>
                $tituloZona,

            'zonas' =>
                $zonas,

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

</section>