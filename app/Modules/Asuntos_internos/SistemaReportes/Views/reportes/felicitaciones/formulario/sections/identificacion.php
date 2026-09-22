<!-- =========================================================
     IDENTIFICACIÓN DE LA FELICITACIÓN
========================================================= -->

<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Nuevo registro
            </span>

            <h2 class="report-section__title">
                Identificación de la felicitación
            </h2>

            <p class="report-section__description">
                Datos generales para identificar el registro.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">


            <!-- =================================================
                 FOLIO
            ================================================== -->

            <div class="report-field">

                <label for="felicitacion-folio-visual">
                    Número de folio
                </label>

                <input type="text" id="felicitacion-folio-visual" class="report-input report-input--readonly"
                    value="FEL- — Automático" readonly>

                <small class="report-field__help">
                    El consecutivo se asigna automáticamente.
                </small>

            </div>


            <!-- =================================================
                 FECHA
            ================================================== -->

            <div class="report-field">

                <label for="felicitacion-fecha-registro">
                    Fecha de registro
                </label>

                <input type="text" id="felicitacion-fecha-registro" name="fecha_registro"
                    class="report-input report-input--readonly" value="<?= date('d/m/Y') ?>" readonly>

                <small class="report-field__help">
                    Se asigna automáticamente con la fecha actual.
                </small>

            </div>


            <!-- =================================================
                NOMENCLATURA
            ================================================== -->

            <div class="report-field report-field--full">

                <label for="felicitacion-nomenclatura-visual">
                    Nomenclatura
                </label>

                <input type="text" id="felicitacion-nomenclatura-visual" class="report-input report-input--readonly"
                    value="CGSC/CAI/FEL/ — Automática" readonly>

                <small class="report-field__help">
                    Se genera automáticamente con el número de folio y el año de registro.
                </small>

                <input type="hidden" id="felicitacion-nomenclatura" name="nomenclatura" value="">

            </div>
        </div>

    </div>

</section>