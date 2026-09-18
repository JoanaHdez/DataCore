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

            <!-- =====================================================
                TIPO DE FOLIO
            ====================================================== -->

            <div class="report-field">

                <label for="tipo_folio">
                    Tipo de folio
                    <span class="required">*</span>
                </label>

                <select id="tipo_folio" name="tipo_folio" class="report-select" required>

                    <option value="QJ" selected>
                        QJ - Queja
                    </option>

                    <option value="QJV">
                        QJV - Queja verbal
                    </option>

                    <option value="QJF">
                        QJF - Queja foránea
                    </option>

                </select>

                <small class="report-field__help">
                    El consecutivo se asignará de acuerdo con el tipo de folio seleccionado.
                </small>

            </div>


            <!-- =====================================================
                NÚMERO DE FOLIO
            ====================================================== -->

            <div class="report-field">

                <label for="folio_visual">
                    Número de folio
                </label>

                <input type="text" id="folio_visual" class="report-input report-input--readonly"
                    value="<?= esc($folioVisual ?? 'QJ- — Automático') ?>" readonly>

                <small class="report-field__help">
                    El consecutivo se asignará automáticamente al guardar.
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