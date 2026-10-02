<?php

$personalIndividualSeleccionado =
    trim(
        (string) (
            $personalIndividualSeleccionado
            ?? ''
        )
    );


$personalIndividual =
    is_array(
        $personalIndividual
        ?? null
    )
        ? $personalIndividual
        : [];


$persona =
    is_array(
        $personalIndividual['personal']
        ?? null
    )
        ? $personalIndividual['personal']
        : [];


$totalQuejas =
    (int) (
        $personalIndividual['total_quejas']
        ?? 0
    );


$totalFelicitaciones =
    (int) (
        $personalIndividual['total_felicitaciones']
        ?? 0
    );


$ultimasQuejas =
    is_array(
        $personalIndividual['ultimas_quejas']
        ?? null
    )
        ? $personalIndividual['ultimas_quejas']
        : [];


$ultimasFelicitaciones =
    is_array(
        $personalIndividual['ultimas_felicitaciones']
        ?? null
    )
        ? $personalIndividual['ultimas_felicitaciones']
        : [];


$tipoDashboardPersonal =
    strtoupper(
        trim(
            (string) (
                $_GET['tipo']
                ?? ''
            )
        )
    );


$esFelicitacionPersonal =
    $tipoDashboardPersonal === 'FELICITACION';


$tipoAnalisisPersonal =
    (string) (
        $personalIndividual['tipo_analisis']
        ?? (
            $esFelicitacionPersonal
                ? 'felicitacion'
                : 'queja'
        )
    );


$totalRegistros =
    $tipoAnalisisPersonal === 'felicitacion'
        ? $totalFelicitaciones
        : $totalQuejas;


$ultimosRegistros =
    $tipoAnalisisPersonal === 'felicitacion'
        ? $ultimasFelicitaciones
        : $ultimasQuejas;


$motivosAgrupados =
    is_array(
        $personalIndividual['motivos_agrupados']
        ?? null
    )
        ? $personalIndividual['motivos_agrupados']
        : [];


$sinPersonaSeleccionada =
    $personalIndividualSeleccionado === '';


$sinDatosPersonal =
    !$sinPersonaSeleccionada
    && $totalRegistros <= 0;

?>


<section
    class="dashboard-grafica dashboard-grafica--personal-individual"
    id="dashboard-personal-individual"
>

    <div class="dashboard-grafica__encabezado">
        <div class="dashboard-grafica__encabezado-info">
            <span class="dashboard-grafica__eyebrow">
                Analisis individual
            </span>

            <h2 class="dashboard-grafica__titulo">
                Personal individual
            </h2>

            <p class="dashboard-grafica__descripcion">
                Consulta los registros asociados a una persona especifica.
            </p>
        </div>
    </div>


    <div class="dashboard-filtros__personal">

        <input
            type="search"
            id="dashboard-personal-individual-busqueda"
            class="dashboard-filtros__input"
            placeholder="Buscar personal..."
            autocomplete="off"
        >

        <input
            type="hidden"
            id="dashboard-personal-individual-valor"
            value="<?= esc($personalIndividualSeleccionado) ?>"
        >

        <div
            class="dashboard-filtros__personal-resultados"
            id="dashboard-personal-individual-resultados"
            hidden
        ></div>

        <div
            class="dashboard-filtros__personal-seleccion"
            id="dashboard-personal-individual-seleccion"
            <?= $sinPersonaSeleccionada ? 'hidden' : '' ?>
        >
            <span id="dashboard-personal-individual-seleccion-texto">
                <?= esc($persona['nombre'] ?? $personalIndividualSeleccionado) ?>
            </span>

            <button
                type="button"
                id="dashboard-personal-individual-quitar"
                aria-label="Quitar personal individual"
            >
                &times;
            </button>
        </div>
    </div>


    <?php if ($sinPersonaSeleccionada): ?>

    <div class="dashboard-grafica__placeholder">
        <strong>
            Selecciona una persona
        </strong>

        <span>
            Selecciona una persona para consultar su informacion.
        </span>
    </div>

    <?php elseif ($sinDatosPersonal): ?>

    <div class="dashboard-grafica__placeholder">
        <strong>
            Sin datos
        </strong>

        <span>
            Sin datos para los filtros seleccionados.
        </span>
    </div>

    <?php else: ?>

    <div class="dashboard-personal-individual__contenido">

        <div class="dashboard-personal-individual__persona">
            <h3>
                Persona seleccionada
            </h3>

            <dl>
                <div>
                    <dt>Nombre</dt>
                    <dd><?= esc($persona['nombre'] ?? 'Sin informacion') ?></dd>
                </div>

                <div>
                    <dt>Area</dt>
                    <dd><?= esc($persona['area'] ?? 'Sin informacion') ?></dd>
                </div>

                <div>
                    <dt>Turno</dt>
                    <dd><?= esc($persona['turno'] ?? 'Sin informacion') ?></dd>
                </div>
            </dl>
        </div>


        <p class="dashboard-personal-individual__total">
            <strong>
                <?= $tipoAnalisisPersonal === 'felicitacion'
                    ? 'Total de felicitaciones:'
                    : 'Total de quejas:' ?>
            </strong>

            <?= esc($totalRegistros) ?>
        </p>


        <h3>
            <?= $tipoAnalisisPersonal === 'felicitacion'
                ? 'Ultimas 10 felicitaciones'
                : 'Ultimas 10 quejas' ?>
        </h3>

        <div class="dashboard-personal-individual__tabla-contenedor">
            <table class="dashboard-personal-individual__tabla">
                <thead>
                    <?php if ($tipoAnalisisPersonal === 'felicitacion'): ?>

                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Felicitante</th>
                        <th>Razon</th>
                    </tr>

                    <?php else: ?>

                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Clasificacion</th>
                        <th>Motivos</th>
                    </tr>

                    <?php endif; ?>
                </thead>

                <tbody>
                    <?php foreach ($ultimosRegistros as $registro): ?>

                    <?php if ($tipoAnalisisPersonal === 'felicitacion'): ?>

                    <tr>
                        <td><?= esc($registro['folio'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($registro['fecha'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($registro['felicitante'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($registro['razon'] ?? 'Sin informacion') ?></td>
                    </tr>

                    <?php else: ?>

                    <tr>
                        <td><?= esc($registro['folio'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($registro['fecha'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($registro['estado'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($registro['clasificacion'] ?? 'Sin informacion') ?></td>
                        <td>
                            <button
                                type="button"
                                class="dashboard-personal-individual__motivos-boton"
                                data-personal-individual-motivos
                            >
                                Ver motivos
                            </button>
                        </td>
                    </tr>

                    <?php endif; ?>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>

</section>


<?php if ($tipoAnalisisPersonal === 'queja' && !$sinPersonaSeleccionada): ?>

<div
    class="modal-reporte"
    id="modal-personal-individual-motivos"
    aria-hidden="true"
>
    <div
        class="modal-reporte__overlay"
        data-personal-individual-motivos-cerrar
    ></div>

    <div
        class="modal-reporte__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-personal-individual-motivos-titulo"
    >
        <div class="modal-reporte__header">
            <div>
                <span class="modal-reporte__eyebrow">
                    Personal individual
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-personal-individual-motivos-titulo"
                >
                    Motivos agrupados
                </h2>
            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-personal-individual-motivos-cerrar
                aria-label="Cerrar"
            >
                &times;
            </button>
        </div>

        <div class="modal-reporte__body">
            <?php if (empty($motivosAgrupados)): ?>

            <div class="dashboard-grafica__placeholder">
                <strong>
                    Sin motivos
                </strong>

                <span>
                    Sin motivos registrados para los filtros seleccionados.
                </span>
            </div>

            <?php else: ?>

            <div class="dashboard-personal-individual__motivos-lista">
                <?php foreach ($motivosAgrupados as $grupoMotivo): ?>

                <?php

                $variantesMotivo =
                    is_array(
                        $grupoMotivo['variantes']
                        ?? null
                    )
                        ? $grupoMotivo['variantes']
                        : [];

                ?>

                <article class="dashboard-personal-individual__motivo">
                    <h3>
                        <?= esc($grupoMotivo['motivo'] ?? 'Sin informacion') ?>
                    </h3>

                    <?php if (!empty($variantesMotivo)): ?>

                    <div class="dashboard-personal-individual__motivo-variantes">
                        <?php foreach ($variantesMotivo as $varianteMotivo): ?>

                        <?php

                        $foliosVariante =
                            is_array(
                                $varianteMotivo['folios']
                                ?? null
                            )
                                ? $varianteMotivo['folios']
                                : [];

                        $horasArresto =
                            $varianteMotivo['horas_arresto']
                            ?? null;

                        ?>

                        <div class="dashboard-personal-individual__motivo-variante">
                            <strong>
                                Sanción:
                                <?= esc($varianteMotivo['sancion'] ?? 'Sin sanción') ?>
                            </strong>

                            <?php if ($horasArresto !== null): ?>

                            <span>
                                Horas de arresto:
                                <?= esc((string) $horasArresto) ?>
                            </span>

                            <?php endif; ?>

                            <span>
                                <?= esc((int) ($varianteMotivo['cantidad'] ?? 0)) ?>
                                quejas
                            </span>

                            <?php if (!empty($foliosVariante)): ?>

                            <small>
                                Folios:
                                <?= esc(implode(', ', $foliosVariante)) ?>
                            </small>

                            <?php endif; ?>
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

<?php endif; ?>
