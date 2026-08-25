<section class="report-section report-section--registro">

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

            <!-- FOLIO -->
            <div class="report-field">

                <label for="folio">
                    Folio
                </label>

                <input
                    type="text"
                    id="folio"
                    name="folio"
                    value="Se generará automáticamente"
                    readonly
                    class="report-input report-input--readonly"
                >

                <small class="report-field__help">
                    El sistema asignará el folio al guardar el registro.
                </small>

            </div>


            <!-- FECHA DE REGISTRO -->
            <div class="report-field">

                <label for="fecha_registro">
                    Fecha de registro
                </label>

                <input
                    type="text"
                    id="fecha_registro"
                    name="fecha_registro"
                    value="<?= date('d/m/Y') ?>"
                    readonly
                    class="report-input report-input--readonly"
                >

            </div>

        </div>

    </div>

</section>