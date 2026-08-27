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

            <!-- CLASIFICACIÓN -->
            <div class="report-field">

                <label for="clasificacion">
                    Clasificación
                    <span class="required">*</span>
                </label>

                <input type="text" id="clasificacion" name="clasificacion" class="report-input"
                    placeholder="Ingresa la clasificación" autocomplete="off" required>

            </div>


            <!-- INSPECTOR -->
            <div class="report-field">

                <label for="inspector">
                    Inspector
                    <span class="required">*</span>
                </label>

                <input type="text" id="inspector" name="inspector" class="report-input"
                    placeholder="Ingresa el nombre del inspector" autocomplete="off" required>

            </div>

            <!-- INVESTIGADOR -->
            <div class="report-field">

                <label for="investigador">
                    Investigador
                </label>

                <input type="text" id="investigador" name="investigador" class="report-input"
                    placeholder="Ingresa el nombre del investigador" autocomplete="off">

            </div>

            <!-- QUIÉN EMITE LA RESOLUCIÓN -->
            <div class="report-field">

                <label for="quien_emite_resolucion">
                    Quién emite la resolución
                </label>

                <input type="text" id="quien_emite_resolucion" name="quien_emite_resolucion" class="report-input"
                    placeholder="Ingresa quién emite la resolución" autocomplete="off">

            </div>

            <!-- RESOLUCIÓN -->
            <div class="report-field report-field--full">

                <label for="resolucion">
                    Resolución
                </label>

                <textarea id="resolucion" name="resolucion" class="report-textarea"
                    placeholder="Ingresa la resolución"></textarea>

            </div>

            <!-- MOTIVOS -->
            <div class="report-field report-field--full">

                <label for="motivos">
                    Motivos
                </label>

                <textarea id="motivos" name="motivos" class="report-textarea"
                    placeholder="Ingresa los motivos"></textarea>

            </div>

            <!-- =====================================================
     EVIDENCIA FOTOGRÁFICA
====================================================== -->
            <div class="report-subsection">

                <div class="report-subsection__header">

                    <span class="report-subsection__eyebrow">
                        Evidencia
                    </span>

                    <h3 class="report-subsection__title">
                        Evidencia fotográfica
                    </h3>

                    <p class="report-subsection__description">
                        Adjunta las fotografías relacionadas con el reporte.
                    </p>

                </div>


                <div class="report-evidence">

                    <input type="file" id="evidencia_fotografica" name="evidencia_fotografica[]"
                        class="report-evidence__input" accept="image/jpeg,image/png,image/webp" multiple>


                    <label for="evidencia_fotografica" class="report-evidence__dropzone">

                        <div class="report-evidence__icon">

                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 16V4M7 9l5-5 5 5" fill="none" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />

                                <path d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" />
                            </svg>

                        </div>


                        <div class="report-evidence__content">

                            <strong>
                                Seleccionar fotografías
                            </strong>

                            <span>
                                Haz clic para elegir una o varias imágenes
                            </span>

                            <small>
                                JPG, PNG o WEBP
                            </small>

                        </div>

                    </label>


                    <div class="report-evidence__files" id="evidencia-lista-archivos">
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>