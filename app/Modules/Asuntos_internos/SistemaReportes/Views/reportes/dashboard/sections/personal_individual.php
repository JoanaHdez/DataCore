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


$ultimasQuejas =
    is_array(
        $personalIndividual['ultimas_quejas']
        ?? null
    )
        ? $personalIndividual['ultimas_quejas']
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


$sinPersonaSeleccionada =
    $personalIndividualSeleccionado === '';


$sinDatosPersonal =
    !$sinPersonaSeleccionada
    && $totalQuejas <= 0;

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
                Consulta las quejas asociadas a una persona especifica.
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


    <?php if ($esFelicitacionPersonal): ?>

    <div class="dashboard-grafica__placeholder">
        <strong>
            No aplicable
        </strong>

        <span>
            Personal individual corresponde a Quejas.
        </span>
    </div>

    <?php elseif ($sinPersonaSeleccionada): ?>

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
                Total de quejas:
            </strong>

            <?= esc($totalQuejas) ?>
        </p>


        <h3>
            Ultimas 10 quejas
        </h3>

        <div class="dashboard-personal-individual__tabla-contenedor">
            <table class="dashboard-personal-individual__tabla">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Clasificacion</th>
                        <th>Motivos</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($ultimasQuejas as $queja): ?>

                    <?php

                    $motivos =
                        is_array(
                            $queja['motivos']
                            ?? null
                        )
                            ? $queja['motivos']
                            : [];

                    ?>

                    <tr>
                        <td><?= esc($queja['folio'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($queja['fecha'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($queja['estado'] ?? 'Sin informacion') ?></td>
                        <td><?= esc($queja['clasificacion'] ?? 'Sin informacion') ?></td>
                        <td>
                            <?php if (empty($motivos)): ?>

                            Sin informacion

                            <?php else: ?>

                            <ul>
                                <?php foreach ($motivos as $motivo): ?>

                                <li>
                                    <?= esc($motivo['motivo'] ?? 'Sin informacion') ?>
                                </li>

                                <?php endforeach; ?>
                            </ul>

                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>

</section>
