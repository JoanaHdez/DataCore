<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Personal relacionado
            </span>

            <h2 class="report-section__title">
                Personal y unidades involucradas
            </h2>

            <p class="report-section__description">
                Información del personal relacionado con los hechos y de la unidad asignada.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-form-grid">

            <!-- OFICIAL -->
            <div class="report-field report-field--full">

                <label for="oficial">
                    Oficial
                    <span class="required">*</span>
                </label>

                <input type="text" id="oficial" name="oficial" class="report-input"
                    placeholder="Ingresa el nombre del oficial" autocomplete="off" required>

                <small class="report-field__help">
                    Al seleccionar al oficial se cargarán automáticamente su área y las unidades relacionadas.
                </small>

            </div>


            <!-- ÁREA -->
            <div class="report-field">

                <label for="area">
                    Área
                </label>

                <input type="text" id="area" name="area" class="report-input report-input--readonly" readonly>

            </div>

            <!-- TURNO -->
            <div class="report-field">

                <label for="turno">
                    Turno
                </label>

                <input type="text" id="turno" name="turno" class="report-input" placeholder="Ingresa el turno"
                    autocomplete="off">

            </div>

        </div>

    </div>

</section>