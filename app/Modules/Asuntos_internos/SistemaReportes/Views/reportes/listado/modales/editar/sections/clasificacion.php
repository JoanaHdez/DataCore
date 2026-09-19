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

            <label for="editar-inspector-busqueda">
                Inspector
                <span class="required">*</span>
            </label>


            <!-- =================================================
                BUSCADOR
            ================================================== -->

            <input type="text" id="editar-inspector-busqueda" class="report-input" autocomplete="off"
                placeholder="Busca al inspector por nombre o nómina">


            <small class="editar-reporte-campo__help">
                Busca y selecciona personal activo de la Coordinación de Asuntos Internos.
            </small>


            <!-- =================================================
                RESULTADOS
            ================================================== -->

            <div class="personal-resultados" id="editar-inspector-resultados" hidden>
            </div>


            <!-- =================================================
                PERSONA SELECCIONADA
            ================================================== -->

            <div class="personal-seleccionado" id="editar-inspector-seleccionado" hidden>


                <!-- FOTO -->

                <div class="personal-seleccionado__foto">

                    <img id="editar-inspector-foto" src="" alt="" hidden>


                    <span id="editar-inspector-foto-fallback">
                        —
                    </span>

                </div>


                <!-- DATOS -->

                <div class="personal-seleccionado__datos">

                    <strong id="editar-inspector-nombre">
                        —
                    </strong>


                    <span id="editar-inspector-nomina">
                        —
                    </span>


                    <small id="editar-inspector-area">
                        —
                    </small>

                </div>


                <!-- QUITAR -->

                <button type="button" class="personal-seleccionado__quitar" id="btn-editar-quitar-inspector"
                    aria-label="Quitar inspector" title="Quitar inspector">
                    ×
                </button>

            </div>


            <!-- =================================================
                VALORES PARA BACKEND
            ================================================== -->

            <input type="hidden" id="editar-inspector" name="inspector" value="" required>


            <input type="hidden" id="editar-inspector-plantilla-id" value="">


            <input type="hidden" id="editar-inspector-perscod" value="">

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

            <label>
                Estado
            </label>


            <!-- =================================================
                VALOR REAL PARA BACKEND
            ================================================== -->

            <input type="hidden" id="editar-estado-actual" name="estado_actual" value="Pendiente">


            <!-- =================================================
                SELECTOR VISUAL
            ================================================== -->

            <button type="button" class="estado-select" id="editar-estado-select" aria-expanded="false"
                aria-controls="editar-estado-resultados">

                <span class="estado-select__texto" id="editar-estado-select-texto">
                    Pendiente
                </span>


                <span class="estado-select__flecha" aria-hidden="true">
                    ▾
                </span>

            </button>


            <small class="editar-reporte-campo__help">
                Indica el estado actual de atención de la queja.
            </small>


            <!-- =================================================
                CATÁLOGO
            ================================================== -->

            <div class="estado-resultados" id="editar-estado-resultados" hidden>


                <!-- PENDIENTE -->

                <button type="button" class="estado-resultados__item" data-editar-estado-opcion data-estado="Pendiente">

                    <span class="estado-resultados__avatar">
                        P
                    </span>


                    <span class="estado-resultados__datos">

                        <strong>
                            Pendiente
                        </strong>

                        <small>
                            Atención pendiente de seguimiento
                        </small>

                    </span>

                </button>


                <!-- EN PROCESO -->

                <button type="button" class="estado-resultados__item" data-editar-estado-opcion
                    data-estado="En proceso">

                    <span class="estado-resultados__avatar">
                        EP
                    </span>


                    <span class="estado-resultados__datos">

                        <strong>
                            En proceso
                        </strong>

                        <small>
                            Reporte actualmente en atención
                        </small>

                    </span>

                </button>


                <!-- FINALIZADO -->

                <button type="button" class="estado-resultados__item" data-editar-estado-opcion
                    data-estado="Finalizado">

                    <span class="estado-resultados__avatar">
                        F
                    </span>


                    <span class="estado-resultados__datos">

                        <strong>
                            Finalizado
                        </strong>

                        <small>
                            Atención del reporte concluida
                        </small>

                    </span>

                </button>

            </div>

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
            TOTAL DE HORAS DE ARRESTO
        ====================================================== -->

        <div class="editar-reporte-campo editar-reporte-campo--horas-arresto">

            <label for="editar-total-horas-arresto">
                Total de horas de arresto
            </label>

            <input type="text" id="editar-total-horas-arresto" name="total_horas_arresto"
                class="editar-reporte-input--readonly editar-reporte-input--contador" value="0" readonly>

            <small class="editar-reporte-campo__help">
                Se calcula automáticamente con las sanciones
                de arresto seleccionadas.
            </small>

        </div>

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