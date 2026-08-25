<section class="report-section">

    <div class="report-section__header">

        <div>
            <span class="report-section__eyebrow">
                Información del incidente
            </span>

            <h2 class="report-section__title">
                Datos de los hechos
            </h2>

            <p class="report-section__description">
                Captura la fecha, hora y descripción general de los hechos.
            </p>
        </div>

    </div>

    <div class="report-section__body">

        <div class="report-form-grid">

            <!-- FECHA DE LOS HECHOS -->
            <div class="report-field">

                <label for="fecha_hechos">
                    Fecha de los hechos
                    <span class="required">*</span>
                </label>

                <input
                    type="date"
                    id="fecha_hechos"
                    name="fecha_hechos"
                    class="report-input"
                    required
                >

            </div>


            <!-- HORA DE LOS HECHOS -->
            <div class="report-field">

                <label for="hora_hechos">
                    Hora de los hechos
                    <span class="required">*</span>
                </label>

                <input
                    type="time"
                    id="hora_hechos"
                    name="hora_hechos"
                    class="report-input"
                    required
                >

            </div>


            <!-- UBICACIÓN -->
            <div class="report-field report-field--full">

                <label for="ubicacion">
                    Ubicación
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="ubicacion"
                    name="ubicacion"
                    class="report-input"
                    placeholder="Ingresa la ubicación de los hechos"
                    autocomplete="off"
                    required
                >

                <small class="report-field__help">
                    Más adelante esta información se podrá complementar mediante el mapa.
                </small>

            </div>


            <!-- DESCRIPCIÓN -->
            <div class="report-field report-field--full">

                <label for="descripcion">
                    Descripción de los hechos
                    <span class="required">*</span>
                </label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    class="report-textarea"
                    placeholder="Describe de manera clara los hechos reportados"
                    required
                ></textarea>

            </div>

        </div>

    </div>

</section>