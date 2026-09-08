<!-- =========================================================
     PASO 1 - DATOS DE LA FELICITACIÓN
========================================================= -->


<!-- =========================================================
     DATOS GENERALES
========================================================= -->

<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Nuevo registro
            </span>

            <h2 class="report-section__title">
                Datos de la felicitación
            </h2>

            <p class="report-section__description">
                Información general para registrar la felicitación.
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

                <label for="felicitacion-nomenclatura">
                    Nomenclatura
                </label>

                <input type="text" id="felicitacion-nomenclatura" class="report-input report-input--readonly"
                    value="CGSC/CAI/FEL/ — Automático" readonly>

                <small class="report-field__help">
                    Se genera automáticamente con el número de folio asignado.
                </small>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     INFORMACIÓN DE LA FELICITACIÓN
========================================================= -->

<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Felicitante
            </span>

            <h2 class="report-section__title">
                Información de la felicitación
            </h2>

            <p class="report-section__description">
                Captura quién realiza la felicitación y la razón de la misma.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">


            <!-- =================================================
                 NOMBRE DEL FELICITANTE
            ================================================== -->

            <div class="report-field report-field--full">

                <label for="nombre_felicitante">
                    Nombre de la persona que da la felicitación
                    <span class="required">*</span>
                </label>

                <input type="text" id="nombre_felicitante" name="nombre_felicitante" class="report-input"
                    placeholder="Ingresa el nombre" autocomplete="off" required>

            </div>


            <!-- =================================================
                 RAZÓN
            ================================================== -->

            <div class="report-field report-field--full">

                <label for="razon_felicitacion">
                    Razón de la felicitación
                    <span class="required">*</span>
                </label>

                <textarea id="razon_felicitacion" name="razon_felicitacion" class="report-textarea"
                    placeholder="Describe la razón de la felicitación" rows="6" required></textarea>

            </div>

        </div>

    </div>

</section>