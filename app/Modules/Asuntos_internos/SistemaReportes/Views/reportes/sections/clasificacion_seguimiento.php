<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Investigación
            </span>

            <h2 class="report-section__title">
                Clasificación y seguimiento
            </h2>

            <p class="report-section__description">
                Información relacionada con la clasificación y atención del reporte.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">


            <!-- =====================================================
                 CLASIFICACIÓN
            ====================================================== -->

            <div class="report-field">

                <label>
                    Clasificación
                    <span class="required">*</span>
                </label>


                <!-- VALOR REAL QUE SE ENVÍA -->

                <input type="hidden" id="clasificacion" name="clasificacion" value="" required>


                <!-- SELECTOR VISUAL -->

                <button type="button" class="clasificacion-select" id="clasificacion-select" aria-expanded="false">

                    <span class="clasificacion-select__texto" id="clasificacion-select-texto">
                        Selecciona una clasificación
                    </span>


                    <span class="clasificacion-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <small class="report-field__help">
                    Selecciona el tipo de clasificación correspondiente a la queja.
                </small>


                <!-- =================================================
                     OPCIONES
                ================================================== -->

                <div class="clasificacion-resultados" id="clasificacion-resultados" hidden>

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

                    <button type="button" class="clasificacion-resultados__item" data-clasificacion-opcion
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

            <div class="report-field">

                <label for="inspector">

                    Inspector

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="text" id="inspector" name="inspector" class="report-input"
                    placeholder="Ingresa el nombre del inspector" autocomplete="off" required>

            </div>


            <!-- =====================================================
                 INVESTIGADOR
            ====================================================== -->

            <div class="report-field">

                <label for="investigador">
                    Investigador
                </label>


                <input type="text" id="investigador" name="investigador" class="report-input"
                    placeholder="Ingresa el nombre del investigador" autocomplete="off">

            </div>

            <!-- =====================================================
                ESTADO ACTUAL
            ====================================================== -->

            <div class="report-field">

                <label>
                    Estado
                </label>


                <!-- =================================================
                    VALOR REAL PARA BACKEND
                ================================================== -->

                <input type="hidden" id="estado_actual" name="estado_actual" value="Pendiente">


                <!-- =================================================
                    SELECTOR VISUAL
                ================================================== -->

                <button type="button" class="estado-select" id="estado-select" aria-expanded="false"
                    aria-controls="estado-resultados">

                    <span class="estado-select__texto" id="estado-select-texto">
                        Pendiente
                    </span>


                    <span class="estado-select__flecha" aria-hidden="true">
                        ▾
                    </span>

                </button>


                <small class="report-field__help">
                    Indica el estado actual de atención de la queja.
                </small>


                <!-- =================================================
                    CATÁLOGO
                ================================================== -->

                <div class="estado-resultados" id="estado-resultados" hidden>

                    <!-- PENDIENTE -->

                    <button type="button" class="estado-resultados__item" data-estado-opcion data-estado="Pendiente">

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

                    <button type="button" class="estado-resultados__item" data-estado-opcion data-estado="En proceso">

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

                    <button type="button" class="estado-resultados__item" data-estado-opcion data-estado="Finalizado">

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

            <div class="report-field report-field--full">

                <label>
                    Situación de la sanción
                </label>


                <div class="report-options">

                    <!-- =============================================
                         SIN SANCIONES
                    ============================================== -->

                    <label class="report-option">

                        <input type="checkbox" id="sin-sanciones" name="sin_sanciones" value="1">

                        <span>
                            Sin sanciones
                        </span>

                    </label>


                    <!-- =============================================
                         BAJA VOLUNTARIA
                    ============================================== -->

                    <label class="report-option">

                        <input type="checkbox" id="baja-voluntaria" name="baja_voluntaria" value="1">

                        <span>
                            Baja voluntaria
                        </span>

                    </label>

                </div>


                <small class="report-field__help">
                    “Sin sanciones” indica que todavía no se ha determinado una sanción.
                    “Baja voluntaria” corresponde a una baja definitiva.
                </small>

            </div>


            <!-- =====================================================
                 MOTIVOS
            ====================================================== -->

            <div class="report-field report-field--full">

                <label for="buscar-motivo">
                    Motivos
                </label>


                <input type="text" id="buscar-motivo" class="report-input" placeholder="Escribe para buscar un motivo"
                    autocomplete="off">


                <small class="report-field__help">
                    Puedes agregar uno o más motivos. El motivo es opcional.
                </small>


                <!-- =================================================
                     RESULTADOS DEL CATÁLOGO
                ================================================== -->

                <div class="motivos-resultados" id="motivos-resultados" hidden>

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

                    <button type="button" class="motivos-resultados__item" data-motivo-opcion
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

            <div class="report-field report-field--full">

                <div class="motivos-agregados" id="motivos-agregados" hidden>

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


                            <tbody id="motivos-agregados-body">

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

            <div id="motivos-inputs" hidden></div>


            <!-- =====================================================
                TOTAL DE HORAS DE ARRESTO
            ====================================================== -->

            <div class="report-field report-field--horas-arresto">

                <label for="total_horas_arresto">
                    Total de horas de arresto
                </label>

                <input type="text" id="total_horas_arresto" name="total_horas_arresto"
                    class="report-input report-input--readonly report-input--contador" value="0" readonly>

                <small class="report-field__help">
                    Se calcula automáticamente con las sanciones
                    de arresto seleccionadas.
                </small>

            </div>

            <!-- =====================================================
                 QUIÉN EMITE LA RESOLUCIÓN
            ====================================================== -->

            <div class="report-field">

                <label for="quien_emite_resolucion">
                    Quién emite la resolución
                </label>


                <input type="text" id="quien_emite_resolucion" name="quien_emite_resolucion" class="report-input"
                    placeholder="Ingresa quién emite la resolución" autocomplete="off">

            </div>


            <!-- =====================================================
                 RESOLUCIÓN
            ====================================================== -->

            <div class="report-field report-field--full">

                <label for="resolucion">
                    Resolución
                </label>


                <textarea id="resolucion" name="resolucion" class="report-textarea"
                    placeholder="Ingresa la resolución"></textarea>

            </div>


        </div>

    </div>

</section>