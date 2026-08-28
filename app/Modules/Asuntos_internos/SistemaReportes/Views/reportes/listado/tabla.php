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

            <button type="button" class="reportes-tabla__export" id="btn-exportar-reportes">
                Exportar Excel
            </button>

        </div>

    </div>


    <div class="reportes-tabla__container">

        <table class="reportes-tabla__table">

            <thead>

                <tr>
                    <th>Folio</th>
                    <th>Fecha queja</th>
                    <th>Expediente</th>
                    <th>Clasificación</th>
                    <th>Quejoso</th>
                    <th>Área</th>
                    <th>Turno</th>
                    <th>Resolución</th>
                    <th class="reportes-tabla__acciones-columna">
                        Acciones
                    </th>
                </tr>

            </thead>

            <tbody id="tabla-reportes-body">

                <?php if (! empty($reportes)): ?>

                <?php foreach ($reportes as $reporte): ?>

                <tr>

                    <td>
                        <strong>
                            <?= esc($reporte['folio']) ?>
                        </strong>
                    </td>

                    <td>
                        <?= esc($reporte['fecha_queja']) ?>
                    </td>

                    <td>
                        <?= esc($reporte['expediente']) ?>
                    </td>

                    <td>
                        <?= esc($reporte['clasificacion']) ?>
                    </td>

                    <td>
                        <?= esc($reporte['quejoso']) ?>
                    </td>

                    <td>
                        <?= esc($reporte['area']) ?>
                    </td>

                    <td>
                        <?= esc($reporte['turno']) ?>
                    </td>

                    <td>

                        <?php
                    $resolucion = $reporte['resolucion'];

                    $claseResolucion = match ($resolucion) {
                        'Finalizado' => 'estado--finalizado',
                        'En proceso' => 'estado--proceso',
                        default => 'estado--pendiente',
                    };
                    ?>

                        <span class="reportes-tabla__estado <?= $claseResolucion ?>">
                            <?= esc($resolucion) ?>
                        </span>

                    </td>

                    <td class="reportes-tabla__acciones">

                        <button type="button" class="reportes-tabla__accion" data-accion="ver"
                            data-folio="<?= esc($reporte['folio']) ?>">
                            Ver
                        </button>

                        <button type="button" class="reportes-tabla__accion" data-accion="editar"
                            data-folio="<?= esc($reporte['folio']) ?>">
                            Editar
                        </button>

                        <button type="button" class="reportes-tabla__accion" data-accion="seguimiento"
                            data-folio="<?= esc($reporte['folio']) ?>">
                            Seguimiento
                        </button>

                        <button type="button" class="reportes-tabla__accion" data-accion="tarjeta"
                            data-folio="<?= esc($reporte['folio']) ?>">
                            Tarjeta
                        </button>

                        <button type="button" class="reportes-tabla__accion reportes-tabla__accion--eliminar"
                            data-accion="eliminar" data-folio="<?= esc($reporte['folio']) ?>">
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