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


/* =========================================================
   TIPO ACTIVO
========================================================= */

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


/* =========================================================
   ESTADOS
========================================================= */

$sinPersonaSeleccionada =
    $personalIndividualSeleccionado === '';


$sinDatosPersonal =
    !$sinPersonaSeleccionada
    && $totalRegistros <= 0;


/* =========================================================
   DATOS VISUALES DE LA PERSONA
========================================================= */

$nombrePersona =
    trim(
        (string) (
            $persona['nombre']
            ?? ''
        )
    );


$areaPersona =
    trim(
        (string) (
            $persona['area']
            ?? ''
        )
    );


$turnoPersona =
    trim(
        (string) (
            $persona['turno']
            ?? ''
        )
    );


$perscodPersona =
    trim(
        (string) (
            $persona['perscod']
            ?? ''
        )
    );


$fotoPersona =
    trim(
        (string) (
            $persona['foto']
            ?? ''
        )
    );


$inicialPersona =
    $nombrePersona !== ''
        ? mb_strtoupper(
            mb_substr(
                $nombrePersona,
                0,
                1,
                'UTF-8'
            ),
            'UTF-8'
        )
        : '?';


$textoTipoRegistro =
    $tipoAnalisisPersonal === 'felicitacion'
        ? 'felicitaciones'
        : 'quejas';


$tituloUltimos =
    $tipoAnalisisPersonal === 'felicitacion'
        ? 'Últimas 10 felicitaciones'
        : 'Últimas 10 quejas';

?>


<section class="
        dashboard-personal-individual
        <?= $tipoAnalisisPersonal === 'felicitacion'
            ? 'dashboard-personal-individual--felicitaciones'
            : 'dashboard-personal-individual--quejas' ?>
    " id="dashboard-personal-individual">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-personal-individual__encabezado">

        <div>

            <span class="dashboard-personal-individual__eyebrow">
                Análisis individual
            </span>


            <h2 class="dashboard-personal-individual__titulo">
                Personal individual
            </h2>


            <p class="dashboard-personal-individual__descripcion">
                Consulta los registros asociados a una persona específica.
            </p>

        </div>

    </div>


    <!-- =====================================================
         BUSCADOR
    ====================================================== -->

    <div class="dashboard-personal-individual__busqueda">

        <div class="dashboard-personal-individual__busqueda-header">

            <div class="dashboard-personal-individual__busqueda-icono">

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6" />

                    <path d="m16 16 4 4" />
                </svg>

            </div>


            <div>

                <span>
                    Buscar personal
                </span>

                <small>
                    Selecciona una persona para consultar su análisis.
                </small>

            </div>

        </div>


        <div class="dashboard-filtros__personal">

            <input type="search" id="dashboard-personal-individual-busqueda" class="
                    dashboard-filtros__input
                    dashboard-personal-individual__busqueda-input
                " placeholder="Buscar por nombre o nómina..." autocomplete="off">


            <input type="hidden" id="dashboard-personal-individual-valor"
                value="<?= esc($personalIndividualSeleccionado) ?>">


            <div class="
                    dashboard-filtros__personal-resultados
                    dashboard-personal-individual__resultados
                " id="dashboard-personal-individual-resultados" hidden></div>


            <div class="
                    dashboard-filtros__personal-seleccion
                    dashboard-personal-individual__seleccion
                " id="dashboard-personal-individual-seleccion" <?= $sinPersonaSeleccionada ? 'hidden' : '' ?>>

                <span id="dashboard-personal-individual-seleccion-texto">
                    <?= esc(
                        $nombrePersona !== ''
                            ? $nombrePersona
                            : $personalIndividualSeleccionado
                    ) ?>
                </span>


                <button type="button" id="dashboard-personal-individual-quitar" aria-label="Quitar personal individual">
                    &times;
                </button>

            </div>

        </div>

    </div>


    <!-- =====================================================
         SIN PERSONA
    ====================================================== -->

    <?php if ($sinPersonaSeleccionada): ?>

    <div class="dashboard-personal-individual__estado">

        <div class="dashboard-personal-individual__estado-icono">

            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="12" cy="8" r="3" />

                <path d="M5 20a7 7 0 0 1 14 0" />

                <path d="M19 5v5" />

                <path d="M16.5 7.5h5" />
            </svg>

        </div>


        <div>

            <strong>
                Selecciona una persona
            </strong>

            <span>
                Busca una persona para consultar su información y registros asociados.
            </span>

        </div>

    </div>


    <?php elseif ($sinDatosPersonal): ?>

    <div class="dashboard-personal-individual__estado">

        <div class="dashboard-personal-individual__estado-icono">

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
                Sin datos para los filtros seleccionados.
            </span>

        </div>

    </div>


    <?php else: ?>

    <div class="dashboard-personal-individual__contenido">

        <!-- =================================================
                 FICHA DESTACADA
            ================================================== -->

        <article class="dashboard-personal-individual__ficha">

            <div class="dashboard-personal-individual__ficha-brillo"></div>


            <!-- FOTO -->

            <div class="dashboard-personal-individual__foto">

                <span>
                    <?= esc($inicialPersona) ?>
                </span>

                <?php if ($fotoPersona !== ''): ?>

                <img
                    src="<?= esc($fotoPersona) ?>"
                    alt=""
                    loading="lazy"
                    style="display: none;"
                    onload="this.previousElementSibling.style.display='none'; this.style.display='block';"
                    onerror="this.style.display='none'; this.removeAttribute('src'); this.previousElementSibling.style.display='flex';"
                >

                <?php endif; ?>

            </div>


            <!-- PERSONA -->

            <div class="dashboard-personal-individual__identidad">

                <span class="dashboard-personal-individual__ficha-eyebrow">
                    Persona seleccionada
                </span>


                <h3>
                    <?= esc(
                            $nombrePersona !== ''
                                ? $nombrePersona
                                : 'Sin información'
                        ) ?>
                </h3>


                <?php if ($perscodPersona !== ''): ?>

                <span class="dashboard-personal-individual__identificador">
                    <?= esc($perscodPersona) ?>
                </span>

                <?php endif; ?>


                <div class="dashboard-personal-individual__datos">

                    <div>

                        <span>
                            Área
                        </span>

                        <strong>
                            <?= esc(
                                    $areaPersona !== ''
                                        ? $areaPersona
                                        : 'Sin información'
                                ) ?>
                        </strong>

                    </div>


                    <div>

                        <span>
                            Turno
                        </span>

                        <strong>
                            <?= esc(
                                    $turnoPersona !== ''
                                        ? $turnoPersona
                                        : 'Sin información'
                                ) ?>
                        </strong>

                    </div>

                </div>

            </div>


            <!-- TOTAL -->

            <div class="dashboard-personal-individual__resumen">

                <span>
                    Total de
                    <?= esc($textoTipoRegistro) ?>
                </span>


                <strong>
                    <?= esc($totalRegistros) ?>
                </strong>


                <small>
                    Registros asociados
                </small>

            </div>

        </article>


        <!-- =================================================
                 REGISTROS RECIENTES
            ================================================== -->

        <div class="dashboard-personal-individual__registros">

            <div class="dashboard-personal-individual__registros-header">

                <div>

                    <span class="dashboard-personal-individual__registros-eyebrow">
                        Actividad reciente
                    </span>


                    <h3>
                        <?= esc($tituloUltimos) ?>
                    </h3>

                </div>


                <span class="dashboard-personal-individual__registros-total">
                    <?= esc(count($ultimosRegistros)) ?>
                    registros
                </span>

            </div>


            <div class="dashboard-personal-individual__tabla-contenedor">

                <table class="dashboard-personal-individual__tabla">

                    <thead>

                        <?php if ($tipoAnalisisPersonal === 'felicitacion'): ?>

                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Felicitante</th>
                            <th>Razón</th>
                        </tr>

                        <?php else: ?>

                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Clasificación</th>
                            <th>Motivos</th>
                        </tr>

                        <?php endif; ?>

                    </thead>


                    <tbody>

                        <?php foreach ($ultimosRegistros as $registro): ?>

                        <?php if ($tipoAnalisisPersonal === 'felicitacion'): ?>

                        <tr>

                            <td>
                                <strong class="dashboard-personal-individual__folio">
                                    <?= esc(
                                                    $registro['folio']
                                                    ?? 'Sin información'
                                                ) ?>
                                </strong>
                            </td>


                            <td>
                                <?= esc(
                                                $registro['fecha']
                                                ?? 'Sin información'
                                            ) ?>
                            </td>


                            <td>
                                <?= esc(
                                                $registro['felicitante']
                                                ?? 'Sin información'
                                            ) ?>
                            </td>


                            <td class="dashboard-personal-individual__texto-largo">
                                <?= esc(
                                                $registro['razon']
                                                ?? 'Sin información'
                                            ) ?>
                            </td>

                        </tr>


                        <?php else: ?>

                        <?php

                                    $estadoRegistro =
                                        trim(
                                            (string) (
                                                $registro['estado']
                                                ?? ''
                                            )
                                        );


                                    $estadoClase =
                                        match (
                                            mb_strtoupper(
                                                $estadoRegistro,
                                                'UTF-8'
                                            )
                                        ) {

                                            'PENDIENTE' =>
                                                'pendiente',

                                            'EN PROCESO' =>
                                                'proceso',

                                            'FINALIZADO' =>
                                                'finalizado',

                                            default =>
                                                'neutral',
                                        };

                                    ?>


                        <tr>

                            <td>

                                <strong class="dashboard-personal-individual__folio">
                                    <?= esc(
                                                    $registro['folio']
                                                    ?? 'Sin información'
                                                ) ?>
                                </strong>

                            </td>


                            <td>
                                <?= esc(
                                                $registro['fecha']
                                                ?? 'Sin información'
                                            ) ?>
                            </td>


                            <td>

                                <span class="
                                                    dashboard-personal-individual__estado-badge
                                                    dashboard-personal-individual__estado-badge--<?= esc(
                                                        $estadoClase
                                                    ) ?>
                                                ">
                                    <?= esc(
                                                    $estadoRegistro !== ''
                                                        ? $estadoRegistro
                                                        : 'Sin información'
                                                ) ?>
                                </span>

                            </td>


                            <td>

                                <span class="dashboard-personal-individual__clasificacion">
                                    <?= esc(
                                                    $registro['clasificacion']
                                                    ?? 'Sin información'
                                                ) ?>
                                </span>

                            </td>


                            <td>

                                <button type="button" class="dashboard-personal-individual__motivos-boton"
                                    data-personal-individual-motivos>
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

    </div>

    <?php endif; ?>

</section>


<!-- =========================================================
     MODAL
     Se conserva funcionalmente.
     Lo rediseñaremos en la fase de modales.
========================================================= -->

<?php if (
    $tipoAnalisisPersonal === 'queja'
    && !$sinPersonaSeleccionada
): ?>

<div class="modal-reporte" id="modal-personal-individual-motivos" aria-hidden="true">

    <div class="modal-reporte__overlay" data-personal-individual-motivos-cerrar></div>


    <div class="modal-reporte__dialog" role="dialog" aria-modal="true"
        aria-labelledby="modal-personal-individual-motivos-titulo">

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Personal individual
                </span>


                <h2 class="modal-reporte__title" id="modal-personal-individual-motivos-titulo">
                    Motivos agrupados
                </h2>

            </div>


            <button type="button" class="modal-reporte__close" data-personal-individual-motivos-cerrar
                aria-label="Cerrar">
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
                        <?= esc(
                                    $grupoMotivo['motivo']
                                    ?? 'Sin información'
                                ) ?>
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
                                <?= esc(
                                                    $varianteMotivo['sancion']
                                                    ?? 'Sin sanción'
                                                ) ?>
                            </strong>


                            <?php if ($horasArresto !== null): ?>

                            <span>
                                Horas de arresto:
                                <?= esc(
                                                        (string) $horasArresto
                                                    ) ?>
                            </span>

                            <?php endif; ?>


                            <span>
                                <?= esc(
                                                    (int) (
                                                        $varianteMotivo['cantidad']
                                                        ?? 0
                                                    )
                                                ) ?>
                                quejas
                            </span>


                            <?php if (!empty($foliosVariante)): ?>

                            <small>
                                Folios:
                                <?= esc(
                                                        implode(
                                                            ', ',
                                                            $foliosVariante
                                                        )
                                                    ) ?>
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