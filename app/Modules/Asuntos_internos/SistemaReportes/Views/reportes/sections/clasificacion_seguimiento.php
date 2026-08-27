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


        </div>

    </div>

</section>