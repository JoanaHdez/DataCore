<?php

$quejosoIndividualSeleccionado =
    trim(
        (string) (
            $quejosoIndividualSeleccionado
            ?? ''
        )
    );


$quejosoIndividual =
    is_array(
        $quejosoIndividual
        ?? null
    )
        ? $quejosoIndividual
        : [];


$quejoso =
    is_array(
        $quejosoIndividual['quejoso']
        ?? null
    )
        ? $quejosoIndividual['quejoso']
        : [];


$nombreQuejoso =
    trim(
        (string) (
            $quejoso['nombre']
            ?? ''
        )
    );


$indicadoresQuejoso =
    is_array(
        $quejosoIndividual['indicadores']
        ?? null
    )
        ? $quejosoIndividual['indicadores']
        : [];


$historialQuejoso =
    is_array(
        $quejosoIndividual['historial']
        ?? null
    )
        ? $quejosoIndividual['historial']
        : [];


$recurrenciasQuejoso =
    is_array(
        $quejosoIndividual['recurrencias']
        ?? null
    )
        ? $quejosoIndividual['recurrencias']
        : [];


$categoriasRecurrenciasQuejoso = [
    'personal' =>
        'Personal involucrado',

    'areas' =>
        'Areas del personal',

    'sectores' =>
        'Sectores',

    'turnos' =>
        'Turnos',

    'unidades' =>
        'Unidades',

    'motivos' =>
        'Motivos',
];


$totalQuejasResumen =
    (int) (
        $indicadoresQuejoso['total']
        ?? 0
    );


$pendientesResumen =
    (int) (
        $indicadoresQuejoso['pendientes']
        ?? 0
    );


$enProcesoResumen =
    (int) (
        $indicadoresQuejoso['en_proceso']
        ?? 0
    );


$finalizadasResumen =
    (int) (
        $indicadoresQuejoso['finalizadas']
        ?? 0
    );


$resumenQuejoso =
    'Este quejoso no registra quejas con los filtros actuales.';


if ($totalQuejasResumen > 0) {

    $resumenQuejoso =
        'Este quejoso registra '
        . $totalQuejasResumen
        . ' '
        . (
            $totalQuejasResumen === 1
                ? 'queja'
                : 'quejas'
        )
        . ' en el periodo consultado.';


    if (
        $totalQuejasResumen === 1
    ) {
        if (
            $pendientesResumen === 1
        ) {
            $resumenQuejoso .=
                ' Se encuentra pendiente.';
        } elseif (
            $enProcesoResumen === 1
        ) {
            $resumenQuejoso .=
                ' Se encuentra en proceso.';
        } elseif (
            $finalizadasResumen === 1
        ) {
            $resumenQuejoso .=
                ' Se encuentra finalizada.';
        }
    } elseif (
        $finalizadasResumen === $totalQuejasResumen
    ) {
        $resumenQuejoso .=
            ' Todas se encuentran finalizadas.';
    } elseif (
        $pendientesResumen === $totalQuejasResumen
    ) {
        $resumenQuejoso .=
            ' Todas se encuentran pendientes.';
    } elseif (
        $enProcesoResumen === $totalQuejasResumen
    ) {
        $resumenQuejoso .=
            ' Todas se encuentran en proceso.';
    } else {
        $partesResumen = [];

        if (
            $pendientesResumen > 0
        ) {
            $partesResumen[] =
                $pendientesResumen
                . (
                    $pendientesResumen === 1
                        ? ' pendiente'
                        : ' pendientes'
                );
        }

        if (
            $enProcesoResumen > 0
        ) {
            $partesResumen[] =
                $enProcesoResumen
                . ' en proceso';
        }

        if (
            $finalizadasResumen > 0
        ) {
            $partesResumen[] =
                $finalizadasResumen
                . (
                    $finalizadasResumen === 1
                        ? ' finalizada'
                        : ' finalizadas'
                );
        }

        if (
            !empty($partesResumen)
        ) {
            $ultimaParteResumen =
                array_pop(
                    $partesResumen
                );

            $resumenQuejoso .=
                ' Distribucion actual: '
                . (
                    !empty($partesResumen)
                        ? implode(
                            ', ',
                            $partesResumen
                        ) . ' y '
                        : ''
                )
                . $ultimaParteResumen
                . '.';
        }
    }
}


$tipoDashboardQuejoso =
    strtoupper(
        trim(
            (string) (
                $_GET['tipo']
                ?? ''
            )
        )
    );


$esFelicitacionQuejoso =
    $tipoDashboardQuejoso === 'FELICITACION';


$sinQuejosoSeleccionado =
    $quejosoIndividualSeleccionado === '';


$sinDatosQuejoso =
    !$sinQuejosoSeleccionado
    && $nombreQuejoso === '';

?>


<section class="dashboard-quejoso-individual" id="dashboard-quejoso-individual">

    <div class="dashboard-quejoso-individual__encabezado">

        <div>

            <span class="dashboard-quejoso-individual__eyebrow">
                Analisis individual
            </span>


            <h2 class="dashboard-quejoso-individual__titulo">
                Quejosos
            </h2>


            <p class="dashboard-quejoso-individual__descripcion">
                Busca un quejoso para consultar su analisis en las siguientes etapas.
            </p>

        </div>

    </div>


    <div class="dashboard-quejoso-individual__busqueda">

        <div class="dashboard-quejoso-individual__busqueda-header">

            <div class="dashboard-quejoso-individual__busqueda-icono">

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6" />

                    <path d="m16 16 4 4" />
                </svg>

            </div>


            <div>

                <span>
                    Buscar quejoso
                </span>

                <small>
                    Selecciona un nombre para mantenerlo como analisis independiente.
                </small>

            </div>

        </div>


        <div class="dashboard-filtros__personal">

            <input type="search" id="dashboard-quejoso-individual-busqueda" class="
                    dashboard-filtros__input
                    dashboard-quejoso-individual__busqueda-input
                " placeholder="Buscar por nombre..." autocomplete="off"
                <?= $esFelicitacionQuejoso ? 'disabled' : '' ?>>


            <input type="hidden" id="dashboard-quejoso-individual-valor"
                value="<?= esc($quejosoIndividualSeleccionado) ?>">


            <div class="
                    dashboard-filtros__personal-resultados
                    dashboard-quejoso-individual__resultados
                " id="dashboard-quejoso-individual-resultados" hidden></div>


            <div class="
                    dashboard-filtros__personal-seleccion
                    dashboard-quejoso-individual__seleccion
                " id="dashboard-quejoso-individual-seleccion" <?= (
                    $sinQuejosoSeleccionado
                    || $sinDatosQuejoso
                ) ? 'hidden' : '' ?>>

                <span id="dashboard-quejoso-individual-seleccion-texto">
                    <?= esc($nombreQuejoso) ?>
                </span>


                <button type="button" id="dashboard-quejoso-individual-quitar" aria-label="Quitar quejoso individual">
                    &times;
                </button>

            </div>

        </div>

    </div>


    <?php if ($esFelicitacionQuejoso): ?>

    <div class="dashboard-quejoso-individual__estado">

        <div class="dashboard-quejoso-individual__estado-icono">

            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />

                <path d="M12 8v5" />

                <path d="M12 16.5h.01" />
            </svg>

        </div>


        <div>

            <strong>
                Solo aplica a Quejas
            </strong>

            <span>
                El analisis individual de Quejosos no se muestra para Felicitaciones.
            </span>

        </div>

    </div>


    <?php elseif ($sinQuejosoSeleccionado): ?>

    <div class="dashboard-quejoso-individual__estado">

        <div class="dashboard-quejoso-individual__estado-icono">

            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6" />

                <path d="m16 16 4 4" />

                <path d="M11 8v6" />

                <path d="M8 11h6" />
            </svg>

        </div>


        <div>

            <strong>
                Selecciona un quejoso
            </strong>

            <span>
                Busca un nombre para preparar su analisis individual.
            </span>

        </div>

    </div>


    <?php elseif ($sinDatosQuejoso): ?>

    <div class="dashboard-quejoso-individual__estado">

        <div class="dashboard-quejoso-individual__estado-icono">

            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />

                <path d="M12 8v5" />

                <path d="M12 16.5h.01" />
            </svg>

        </div>


        <div>

            <strong>
                Sin datos
            </strong>

            <span>
                No se encontro informacion para el quejoso seleccionado con los filtros actuales.
            </span>

        </div>

    </div>


    <?php else: ?>

    <div class="dashboard-quejoso-individual__contenido">

        <div class="dashboard-quejoso-individual__resumen">

            <span>
                Resumen del quejoso
            </span>


            <p>
                <?= esc($resumenQuejoso) ?>
            </p>


            <button
                type="button"
                class="dashboard-quejoso-individual__recurrencias-boton"
                id="dashboard-quejoso-individual-recurrencias-abrir"
            >
                Ver analisis de recurrencias
            </button>

        </div>


        <div class="dashboard-quejoso-individual__indicadores">

            <article class="
                    dashboard-quejoso-indicador
                    dashboard-quejoso-indicador--total
                ">

                <span>
                    Total de quejas
                </span>

                <strong>
                    <?= esc((string) ($indicadoresQuejoso['total'] ?? 0)) ?>
                </strong>

            </article>


            <article class="
                    dashboard-quejoso-indicador
                    dashboard-quejoso-indicador--pendiente
                ">

                <span>
                    Pendientes
                </span>

                <strong>
                    <?= esc((string) ($indicadoresQuejoso['pendientes'] ?? 0)) ?>
                </strong>

            </article>


            <article class="
                    dashboard-quejoso-indicador
                    dashboard-quejoso-indicador--proceso
                ">

                <span>
                    En proceso
                </span>

                <strong>
                    <?= esc((string) ($indicadoresQuejoso['en_proceso'] ?? 0)) ?>
                </strong>

            </article>


            <article class="
                    dashboard-quejoso-indicador
                    dashboard-quejoso-indicador--finalizada
                ">

                <span>
                    Finalizadas
                </span>

                <strong>
                    <?= esc((string) ($indicadoresQuejoso['finalizadas'] ?? 0)) ?>
                </strong>

            </article>

        </div>


        <div class="dashboard-quejoso-individual__historial">

            <div class="dashboard-quejoso-individual__historial-header">

                <div>

                    <span>
                        Historial
                    </span>

                    <h3>
                        Quejas del quejoso seleccionado
                    </h3>

                </div>


                <strong>
                    <?= esc((string) count($historialQuejoso)) ?>
                    registros
                </strong>

            </div>


            <?php if (empty($historialQuejoso)): ?>

            <div class="dashboard-quejoso-individual__historial-vacio">

                <strong>
                    Sin quejas con los filtros actuales
                </strong>

                <span>
                    La seleccion se conserva. Al limpiar o cambiar filtros se actualizaran los resultados.
                </span>

            </div>

            <?php else: ?>

            <div class="dashboard-quejoso-individual__tabla-contenedor">

                <table class="dashboard-quejoso-individual__tabla">

                    <thead>

                        <tr>
                            <th>Folio</th>
                            <th>Fecha de registro</th>
                            <th>Estado actual</th>
                            <th>Clasificacion</th>
                            <th>Motivos</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($historialQuejoso as $reporteQuejoso): ?>

                        <?php

                        $folioQuejoso =
                            trim(
                                (string) (
                                    $reporteQuejoso['folio']
                                    ?? ''
                                )
                            );


                        $fechaQuejoso =
                            trim(
                                (string) (
                                    $reporteQuejoso['fecha_registro']
                                    ?? ''
                                )
                            );


                        $estadoQuejoso =
                            trim(
                                (string) (
                                    $reporteQuejoso['estado_actual']
                                    ?? ''
                                )
                            );


                        $clasificacionQuejoso =
                            trim(
                                (string) (
                                    $reporteQuejoso['clasificacion']
                                    ?? ''
                                )
                            );


                        $estadoClaseQuejoso =
                            match ($estadoQuejoso) {
                                'Pendiente' =>
                                    'pendiente',

                                'En proceso' =>
                                    'proceso',

                                'Finalizado' =>
                                    'finalizado',

                                default =>
                                    'neutral',
                            };


                        $idReporteQuejoso =
                            (int) (
                                $reporteQuejoso['id_reporte']
                                ?? 0
                            );


                        $motivosReporteQuejoso =
                            is_array(
                                $reporteQuejoso['motivos']
                                ?? null
                            )
                                ? $reporteQuejoso['motivos']
                                : [];


                        ?>

                        <tr>

                            <td>
                                <strong class="dashboard-quejoso-individual__folio">
                                    <?= esc(
                                        $folioQuejoso !== ''
                                            ? $folioQuejoso
                                            : 'Sin folio asignado'
                                    ) ?>
                                </strong>
                            </td>


                            <td>
                                <?= esc(
                                    $fechaQuejoso !== ''
                                        ? $fechaQuejoso
                                        : 'Sin informacion'
                                ) ?>
                            </td>


                            <td>
                                <span class="
                                        dashboard-quejoso-individual__estado-badge
                                        dashboard-quejoso-individual__estado-badge--<?= esc($estadoClaseQuejoso) ?>
                                    ">
                                    <?= esc(
                                        $estadoQuejoso !== ''
                                            ? $estadoQuejoso
                                            : 'Sin informacion'
                                    ) ?>
                                </span>
                            </td>


                            <td>
                                <?= esc(
                                    $clasificacionQuejoso !== ''
                                        ? $clasificacionQuejoso
                                        : 'Sin informacion'
                                ) ?>
                            </td>


                            <td>
                                <button
                                    type="button"
                                    class="dashboard-personal-individual__motivos-boton"
                                    data-quejoso-individual-motivos="<?= esc((string) $idReporteQuejoso) ?>"
                                >
                                    Motivos
                                </button>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <?php endif; ?>

        </div>


    </div>

    <?php endif; ?>

</section>


<?php if (
    !$esFelicitacionQuejoso
    && !$sinQuejosoSeleccionado
    && !$sinDatosQuejoso
): ?>

<div class="modal-reporte modal-quejoso-individual-recurrencias"
    id="modal-quejoso-individual-recurrencias" aria-hidden="true">

    <div class="modal-reporte__overlay" data-quejoso-individual-recurrencias-cerrar></div>


    <div class="modal-reporte__dialog" role="dialog" aria-modal="true"
        aria-labelledby="modal-quejoso-individual-recurrencias-titulo" tabindex="-1">

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Quejoso individual
                </span>


                <h2 class="modal-reporte__title" id="modal-quejoso-individual-recurrencias-titulo">
                    Analisis de recurrencias
                </h2>


                <p class="modal-reporte__subtitle">
                    <?= esc($nombreQuejoso) ?>
                </p>

            </div>


            <button type="button" class="modal-reporte__close" data-quejoso-individual-recurrencias-cerrar
                aria-label="Cerrar">
                &times;
            </button>

        </div>


        <div class="modal-reporte__body">

            <?php if ($totalQuejasResumen <= 0): ?>

            <div class="dashboard-quejoso-individual__recurrencias-vacio">

                <strong>
                    Sin recurrencias con los filtros actuales
                </strong>

                <span>
                    La seleccion del quejoso se conserva. Al cambiar los filtros se actualizaran estos resultados.
                </span>

            </div>

            <?php else: ?>

            <div class="dashboard-quejoso-individual__recurrencias-grid">

                <?php foreach ($categoriasRecurrenciasQuejoso as $claveCategoria => $tituloCategoria): ?>

                <?php

                $itemsRecurrencia =
                    is_array(
                        $recurrenciasQuejoso[$claveCategoria]
                        ?? null
                    )
                        ? $recurrenciasQuejoso[$claveCategoria]
                        : [];

                ?>

                <article class="dashboard-quejoso-recurrencia">

                    <div class="dashboard-quejoso-recurrencia__header">

                        <h4>
                            <?= esc($tituloCategoria) ?>
                        </h4>

                        <span>
                            <?= esc((string) count($itemsRecurrencia)) ?>
                        </span>

                    </div>


                    <?php if (empty($itemsRecurrencia)): ?>

                    <div class="dashboard-quejoso-recurrencia__vacio">
                        Sin informacion registrada
                    </div>

                    <?php else: ?>

                    <div class="dashboard-quejoso-recurrencia__lista">

                        <?php foreach ($itemsRecurrencia as $itemRecurrencia): ?>

                        <?php

                        $valorRecurrencia =
                            trim(
                                (string) (
                                    $itemRecurrencia['valor']
                                    ?? ''
                                )
                            );


                        $cantidadRecurrencia =
                            (int) (
                                $itemRecurrencia['cantidad']
                                ?? 0
                            );


                        $foliosRecurrencia =
                            is_array(
                                $itemRecurrencia['folios']
                                ?? null
                            )
                                ? $itemRecurrencia['folios']
                                : [];


                        $recurrenteRecurrencia =
                            !empty(
                                $itemRecurrencia['recurrente']
                            );

                        ?>

                        <div class="
                                dashboard-quejoso-recurrencia__item
                                <?= $recurrenteRecurrencia ? 'dashboard-quejoso-recurrencia__item--recurrente' : '' ?>
                            ">

                            <div class="dashboard-quejoso-recurrencia__item-principal">

                                <strong>
                                    <?= esc(
                                        $valorRecurrencia !== ''
                                            ? $valorRecurrencia
                                            : 'Sin informacion'
                                    ) ?>
                                </strong>

                                <span>
                                    <?= esc((string) $cantidadRecurrencia) ?>
                                    <?= $cantidadRecurrencia === 1 ? 'queja' : 'quejas' ?>
                                </span>

                            </div>


                            <small>
                                Folios:
                                <?= esc(
                                    !empty($foliosRecurrencia)
                                        ? implode(
                                            ', ',
                                            array_map(
                                                static fn ($folio): string =>
                                                    trim(
                                                        (string) $folio
                                                    ) !== ''
                                                        ? trim(
                                                            (string) $folio
                                                        )
                                                        : 'Sin folio asignado',
                                                $foliosRecurrencia
                                            )
                                        )
                                        : 'Sin folio asignado'
                                ) ?>
                            </small>

                        </div>

                        <?php endforeach; ?>

                    </div>

                    <?php endif; ?>

                </article>

                <?php endforeach; ?>

            </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php foreach ($historialQuejoso as $reporteQuejosoModal): ?>

<?php

$idReporteModal =
    (int) (
        $reporteQuejosoModal['id_reporte']
        ?? 0
    );


if (
    $idReporteModal <= 0
) {
    continue;
}


$folioReporteModal =
    trim(
        (string) (
            $reporteQuejosoModal['folio']
            ?? ''
        )
    );


$motivosReporteModal =
    is_array(
        $reporteQuejosoModal['motivos']
        ?? null
    )
        ? $reporteQuejosoModal['motivos']
        : [];

?>

<div class="modal-reporte modal-quejoso-individual-motivos"
    id="modal-quejoso-individual-motivos-<?= esc((string) $idReporteModal) ?>" aria-hidden="true">

    <div class="modal-reporte__overlay" data-quejoso-individual-motivos-cerrar></div>


    <div class="modal-reporte__dialog" role="dialog" aria-modal="true"
        aria-labelledby="modal-quejoso-individual-motivos-titulo-<?= esc((string) $idReporteModal) ?>">

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Quejoso individual
                </span>


                <h2 class="modal-reporte__title"
                    id="modal-quejoso-individual-motivos-titulo-<?= esc((string) $idReporteModal) ?>">
                    Motivos del reporte
                </h2>


                <p class="modal-reporte__subtitle">
                    <?= esc(
                        $folioReporteModal !== ''
                            ? $folioReporteModal
                            : 'Sin folio asignado'
                    ) ?>
                </p>

            </div>


            <button type="button" class="modal-reporte__close" data-quejoso-individual-motivos-cerrar
                aria-label="Cerrar">
                &times;
            </button>

        </div>


        <div class="modal-reporte__body">

            <?php if (empty($motivosReporteModal)): ?>

            <div class="dashboard-grafica__placeholder">

                <strong>
                    Sin motivos
                </strong>

                <span>
                    Sin motivos registrados para este reporte.
                </span>

            </div>

            <?php else: ?>

            <div class="dashboard-personal-individual__motivos-lista">

                <?php foreach ($motivosReporteModal as $motivoReporteModal): ?>

                <?php

                $horasArrestoModal =
                    $motivoReporteModal['horas_arresto']
                    ?? null;

                ?>

                <article class="dashboard-personal-individual__motivo">

                    <h3>
                        <?= esc(
                            $motivoReporteModal['motivo']
                            ?? 'Sin informacion'
                        ) ?>
                    </h3>


                    <div class="dashboard-personal-individual__motivo-variantes">

                        <div class="dashboard-personal-individual__motivo-variante">

                            <strong>
                                Sancion:
                                <?= esc(
                                    $motivoReporteModal['sancion']
                                    ?? 'Sin sancion'
                                ) ?>
                            </strong>


                            <?php if ($horasArrestoModal !== null): ?>

                            <span>
                                Horas de arresto:
                                <?= esc((string) $horasArrestoModal) ?>
                            </span>

                            <?php endif; ?>


                            <span>
                                1 queja
                            </span>


                            <small>
                                Folios:
                                <?= esc(
                                    $folioReporteModal !== ''
                                        ? $folioReporteModal
                                        : 'Sin folio asignado'
                                ) ?>
                            </small>

                        </div>

                    </div>

                </article>

                <?php endforeach; ?>

            </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php endforeach; ?>

<?php endif; ?>
