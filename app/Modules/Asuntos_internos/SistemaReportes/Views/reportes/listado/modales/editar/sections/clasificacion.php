<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Investigación
        </span>

        <h3>
            Clasificación y seguimiento
        </h3>

    </div>


    <div class="editar-reporte-grid">


        <!-- =====================================================
             CLASIFICACIÓN
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label>
                Clasificación
                <span class="required">*</span>
            </label>


            <!-- VALOR REAL -->
            <input type="hidden" id="editar-clasificacion" name="clasificacion" value="" required>


            <!-- SELECTOR VISUAL -->
            <button type="button" class="clasificacion-select" id="editar-clasificacion-select" aria-expanded="false">

                <span class="clasificacion-select__texto" id="editar-clasificacion-select-texto">
                    Selecciona una clasificación
                </span>


                <span class="clasificacion-select__flecha" aria-hidden="true">
                    ▾
                </span>

            </button>


            <small class="editar-reporte-campo__help">
                Selecciona el tipo de clasificación correspondiente a la queja.
            </small>


            <!-- OPCIONES -->
            <div class="clasificacion-resultados" id="editar-clasificacion-resultados" hidden>

                <?php if (!empty($clasificaciones)): ?>

                <?php foreach ($clasificaciones as $clasificacion): ?>

                <?php

                        $nombreClasificacion =
                            trim(
                                (string) (
                                    $clasificacion['nombre']
                                    ?? ''
                                )
                            );


                        $letraClasificacion =
                            mb_strtoupper(
                                mb_substr(
                                    $nombreClasificacion,
                                    0,
                                    1,
                                    'UTF-8'
                                ),
                                'UTF-8'
                            );

                        ?>


                <?php if ($nombreClasificacion !== ''): ?>

                <button type="button" class="clasificacion-resultados__item" data-editar-clasificacion-opcion
                    data-clasificacion-nombre="<?= esc($nombreClasificacion) ?>">

                    <span class="clasificacion-resultados__avatar">
                        <?= esc($letraClasificacion) ?>
                    </span>


                    <span class="clasificacion-resultados__datos">

                        <strong>
                            <?= esc($nombreClasificacion) ?>
                        </strong>

                        <small>
                            Tipo de clasificación
                        </small>

                    </span>

                </button>

                <?php endif; ?>

                <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>


        <!-- =====================================================
             INSPECTOR
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-inspector">

                Inspector

                <span class="required">
                    *
                </span>

            </label>


            <input type="text" id="editar-inspector" name="inspector" autocomplete="off"
                placeholder="Ingresa el nombre del inspector" required>

        </div>


        <!-- =====================================================
             INVESTIGADOR
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-investigador">
                Investigador
            </label>


            <input type="text" id="editar-investigador" name="investigador" autocomplete="off"
                placeholder="Ingresa el nombre del investigador">

        </div>


        <!-- =====================================================
            ESTADO ACTUAL
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-estado-actual">
                Estado
            </label>


            <select id="editar-estado-actual" name="estado_actual">

                <option value="Pendiente" selected>
                    Pendiente
                </option>

                <option value="En proceso">
                    En proceso
                </option>

                <option value="Finalizado">
                    Finalizado
                </option>

            </select>


            <small class="editar-reporte-campo__help">
                Indica el estado actual de atención de la queja.
            </small>

        </div>


        <!-- =====================================================
             SITUACIÓN DE LA SANCIÓN
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                Situación de la sanción
            </label>


            <div class="editar-report-options">


                <!-- SIN SANCIONES -->

                <label class="editar-report-option">

                    <input type="checkbox" id="editar-sin-sanciones" name="sin_sanciones" value="1">

                    <span>
                        Sin sanciones
                    </span>

                </label>


                <!-- BAJA VOLUNTARIA -->

                <label class="editar-report-option">

                    <input type="checkbox" id="editar-baja-voluntaria" name="baja_voluntaria" value="1">

                    <span>
                        Baja voluntaria
                    </span>

                </label>

            </div>


            <small class="editar-reporte-campo__help">
                “Sin sanciones” indica que todavía no se ha determinado una sanción.
                “Baja voluntaria” corresponde a una baja definitiva.
            </small>

        </div>


        <!-- =====================================================
             MOTIVOS
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-buscar-motivo">
                Motivos
            </label>


            <input type="text" id="editar-buscar-motivo" placeholder="Escribe para buscar un motivo" autocomplete="off">


            <small class="editar-reporte-campo__help">
                Puedes agregar uno o más motivos. El motivo es opcional.
            </small>


            <!-- =================================================
                 RESULTADOS DEL CATÁLOGO
            ================================================== -->

            <div class="motivos-resultados" id="editar-motivos-resultados" hidden>

                <?php if (!empty($motivos)): ?>

                <?php foreach ($motivos as $motivo): ?>

                <?php

                        $idMotivo =
                            (int) (
                                $motivo['id_motivo']
                                ?? 0
                            );


                        $textoMotivo =
                            trim(
                                (string) (
                                    $motivo['motivo']
                                    ?? ''
                                )
                            );


                        $sancionMotivo =
                            trim(
                                (string) (
                                    $motivo['sancion']
                                    ?? ''
                                )
                            );

                        ?>


                <?php if (
                            $idMotivo > 0
                            && $textoMotivo !== ''
                        ): ?>

                <button type="button" class="motivos-resultados__item" data-editar-motivo-opcion
                    data-motivo-id="<?= $idMotivo ?>" data-motivo-texto="<?= esc($textoMotivo) ?>"
                    data-motivo-sancion="<?= esc($sancionMotivo) ?>">

                    <span class="motivos-resultados__numero">
                        <?= $idMotivo ?>
                    </span>


                    <span class="motivos-resultados__datos">

                        <strong>
                            <?= esc($textoMotivo) ?>
                        </strong>


                        <small>
                            Sanción sugerida:
                            <?= esc(
                                            $sancionMotivo
                                                ?: 'Sin sanción definida'
                                        ) ?>
                        </small>

                    </span>

                </button>

                <?php endif; ?>

                <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>


        <!-- =====================================================
             MOTIVOS AGREGADOS
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <div class="motivos-agregados" id="editar-motivos-agregados" hidden>

                <div class="motivos-agregados__header">

                    <span>
                        Motivos agregados
                    </span>

                    <strong>
                        Motivos relacionados con la queja
                    </strong>

                </div>


                <div class="motivos-agregados__tabla-wrapper">

                    <table class="motivos-agregados__tabla">

                        <thead>

                            <tr>

                                <th>
                                    Num
                                </th>

                                <th>
                                    Motivo
                                </th>

                                <th>
                                    Sanción
                                </th>

                                <th>
                                    Folio sanción
                                </th>

                                <th>
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody id="editar-motivos-agregados-body">

                            <tr>

                                <td colspan="5">
                                    Sin motivos agregados
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- =====================================================
             INPUTS DINÁMICOS DE MOTIVOS
        ====================================================== -->

        <div id="editar-motivos-inputs" hidden></div>


        <!-- =====================================================
             QUIÉN EMITE LA RESOLUCIÓN
        ====================================================== -->

        <div class="editar-reporte-campo">

            <label for="editar-quien-emite-resolucion">
                Quién emite la resolución
            </label>


            <input type="text" id="editar-quien-emite-resolucion" name="quien_emite_resolucion" autocomplete="off"
                placeholder="Ingresa quién emite la resolución">

        </div>


        <!-- =====================================================
             RESOLUCIÓN
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-resolucion">
                Resolución
            </label>


            <textarea id="editar-resolucion" name="resolucion" rows="4" placeholder="Ingresa la resolución"></textarea>

        </div>


    </div>

</div>