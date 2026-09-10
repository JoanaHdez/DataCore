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
                 SANCIÓN DISCIPLINARIA
            ====================================================== -->

            <div class="report-field">

                <label for="sancion_disciplinaria">
                    Sanción disciplinaria
                </label>


                <select id="sancion_disciplinaria" name="sancion_disciplinaria" class="report-select">

                    <option value="">
                        Sin sanción
                    </option>

                    <option value="Arresto">
                        Arresto
                    </option>

                    <option value="Amonestación">
                        Amonestación
                    </option>

                    <option value="Otro">
                        Otro
                    </option>

                </select>

            </div>


            <!-- =====================================================
                 OTRA SANCIÓN
            ====================================================== -->

            <div class="report-field" id="campo-sancion-otro" hidden>

                <label for="sancion_otro">

                    Especifique la sanción

                    <span class="required">
                        *
                    </span>

                </label>


                <input type="text" id="sancion_otro" name="sancion_otro" class="report-input"
                    placeholder="Ingresa la sanción correspondiente" autocomplete="off" maxlength="255" disabled>

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


            <!-- =====================================================
                 MOTIVOS
            ====================================================== -->

            <div class="report-field report-field--full">

                <label for="motivos">
                    Motivos
                </label>


                <textarea id="motivos" name="motivos" class="report-textarea"
                    placeholder="Ingresa los motivos"></textarea>

            </div>


        </div>

    </div>

</section>