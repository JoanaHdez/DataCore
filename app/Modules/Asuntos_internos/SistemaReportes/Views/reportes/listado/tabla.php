<section class="reportes-tabla">

    <div class="reportes-tabla__header">

        <div>

            <span class="reportes-tabla__eyebrow">
                Registros
            </span>

            <h2 class="reportes-tabla__title">
                Reportes registrados
            </h2>

            <p class="reportes-tabla__description">
                Consulta los reportes y accede a sus diferentes acciones.
            </p>

        </div>


        <div class="reportes-tabla__actions">

            <button
                type="button"
                class="reportes-tabla__export"
                id="btn-exportar-reportes">
                Exportar Excel
            </button>

        </div>

    </div>


    <div class="reportes-tabla__container">

        <table class="reportes-tabla__table">

            <thead>

                <tr>

                    <th>
                        Folio
                    </th>

                    <th>
                        Fecha queja
                    </th>

                    <th>
                        Expediente
                    </th>

                    <th>
                        Clasificación
                    </th>

                    <th>
                        Quejoso
                    </th>

                    <th>
                        Área
                    </th>

                    <th>
                        Turno
                    </th>

                    <th>
                        Resolución
                    </th>

                    <th class="reportes-tabla__acciones-columna">
                        Acciones
                    </th>

                </tr>

            </thead>


            <tbody id="tabla-reportes-body">

                <?php if (!empty($reportes)): ?>


                    <?php foreach ($reportes as $reporte): ?>


                        <?php

                        $idReporte =
                            (int) (
                                $reporte['id_reporte']
                                ?? 0
                            );


                        $folio =
                            trim(
                                (string) (
                                    $reporte['folio']
                                    ?? ''
                                )
                            );


                        $fechaQueja =
                            trim(
                                (string) (
                                    $reporte['fecha_queja']
                                    ?? ''
                                )
                            );


                        $expediente =
                            trim(
                                (string) (
                                    $reporte['expediente']
                                    ?? ''
                                )
                            );


                        $clasificacion =
                            trim(
                                (string) (
                                    $reporte['clasificacion']
                                    ?? ''
                                )
                            );


                        $quejoso =
                            trim(
                                (string) (
                                    $reporte['quejoso']
                                    ?? ''
                                )
                            );


                        $area =
                            trim(
                                (string) (
                                    $reporte['area']
                                    ?? ''
                                )
                            );


                        $turno =
                            trim(
                                (string) (
                                    $reporte['turno']
                                    ?? ''
                                )
                            );


                        $resolucion =
                            trim(
                                (string) (
                                    $reporte['resolucion']
                                    ?? ''
                                )
                            );


                        $claseResolucion =
                            match (
                                mb_strtolower(
                                    $resolucion
                                )
                            ) {

                                'finalizado' =>
                                    'estado--finalizado',

                                'en proceso' =>
                                    'estado--proceso',

                                default =>
                                    'estado--pendiente',

                            };

                        ?>


                        <tr
                            data-id-reporte="<?= $idReporte ?>"
                            data-folio="<?= esc($folio) ?>">


                            <!-- =================================================
                                 FOLIO
                            ================================================== -->

                            <td>

                                <strong>
                                    <?= esc($folio ?: '—') ?>
                                </strong>

                            </td>


                            <!-- =================================================
                                 FECHA QUEJA
                            ================================================== -->

                            <td>

                                <?= esc(
                                    $fechaQueja
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- =================================================
                                 EXPEDIENTE
                            ================================================== -->

                            <td>

                                <?= esc(
                                    $expediente
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- =================================================
                                 CLASIFICACIÓN
                            ================================================== -->

                            <td>

                                <?= esc(
                                    $clasificacion
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- =================================================
                                 QUEJOSO
                            ================================================== -->

                            <td>

                                <?= esc(
                                    $quejoso
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- =================================================
                                 ÁREA
                            ================================================== -->

                            <td>

                                <?= esc(
                                    $area
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- =================================================
                                 TURNO
                            ================================================== -->

                            <td>

                                <?= esc(
                                    $turno
                                    ?: '—'
                                ) ?>

                            </td>


                            <!-- =================================================
                                 RESOLUCIÓN
                            ================================================== -->

                            <td>

                                <span
                                    class="reportes-tabla__estado <?= esc($claseResolucion) ?>">

                                    <?= esc(
                                        $resolucion
                                        ?: 'Pendiente'
                                    ) ?>

                                </span>

                            </td>


                            <!-- =================================================
                                 ACCIONES
                            ================================================== -->

                            <td class="reportes-tabla__acciones">


                                <!-- VER -->

                                <button
                                    type="button"
                                    class="reportes-tabla__accion"
                                    data-accion="ver"
                                    data-id-reporte="<?= $idReporte ?>"
                                    data-folio="<?= esc($folio) ?>">
                                    Ver
                                </button>


                                <!-- EDITAR -->

                                <button
                                    type="button"
                                    class="reportes-tabla__accion"
                                    data-accion="editar"
                                    data-id-reporte="<?= $idReporte ?>"
                                    data-folio="<?= esc($folio) ?>">
                                    Editar
                                </button>


                                <!-- SEGUIMIENTO -->

                                <button
                                    type="button"
                                    class="reportes-tabla__accion"
                                    data-accion="seguimiento"
                                    data-id-reporte="<?= $idReporte ?>"
                                    data-folio="<?= esc($folio) ?>">
                                    Seguimiento
                                </button>


                                <!-- TARJETA -->

                                <button
                                    type="button"
                                    class="reportes-tabla__accion"
                                    data-accion="tarjeta"
                                    data-id-reporte="<?= $idReporte ?>"
                                    data-folio="<?= esc($folio) ?>">
                                    Tarjeta
                                </button>


                                <!-- ELIMINAR -->

                                <button
                                    type="button"
                                    class="
                                        reportes-tabla__accion
                                        reportes-tabla__accion--eliminar
                                    "
                                    data-accion="eliminar"
                                    data-id-reporte="<?= $idReporte ?>"
                                    data-folio="<?= esc($folio) ?>">
                                    Eliminar
                                </button>

                            </td>

                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>


                    <tr class="reportes-tabla__empty">

                        <td colspan="9">

                            <div class="reportes-tabla__empty-content">

                                <strong>
                                    No hay reportes para mostrar
                                </strong>

                                <span>
                                    Los registros aparecerán aquí cuando existan reportes disponibles.
                                </span>

                            </div>

                        </td>

                    </tr>


                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>