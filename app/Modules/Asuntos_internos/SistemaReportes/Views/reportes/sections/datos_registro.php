<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Nuevo registro
            </span>

            <h2 class="report-section__title">
                Datos del reporte
            </h2>

            <p class="report-section__description">
                Información general para la identificación del reporte.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">

            <!-- QJ -->
            <div class="report-field">

                <label for="prefijo_folio">
                    Prefijo
                </label>

                <input type="text" id="prefijo_folio" name="prefijo_folio" class="report-input report-input--readonly"
                    value="QJ" readonly>

                <small class="report-field__help">
                    Identificador fijo del tipo de registro.
                </small>

            </div>


            <!-- NÚMERO DE FOLIO -->
            <div class="report-field">

                <label for="numero_folio">
                    Número de folio
                    <span class="required">*</span>
                </label>

                <input type="text" id="numero_folio" name="numero_folio" class="report-input"
                    placeholder="Ingresa el número de folio" autocomplete="off" required>

                <small class="report-field__help">
                    Captura únicamente el número correspondiente al folio.
                </small>

            </div>


            <!-- FECHA DE REGISTRO -->
            <div class="report-field">

                <label for="fecha_registro">
                    Fecha de registro
                </label>

                <input type="text" id="fecha_registro" name="fecha_registro" class="report-input report-input--readonly"
                    value="<?= date('d/m/Y') ?>" readonly>

                <small class="report-field__help">
                    Se asigna automáticamente con la fecha actual.
                </small>

            </div>

        </div>

    </div>

</section>