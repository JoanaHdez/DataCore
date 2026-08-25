<section class="report-section">

    <div class="report-section__header">

        <div>
            <span class="report-section__eyebrow">
                Persona que presenta la queja
            </span>

            <h2 class="report-section__title">
                Datos del quejoso
            </h2>

            <p class="report-section__description">
                Información de la persona relacionada con la presentación de la queja.
            </p>
        </div>

    </div>

    <div class="report-section__body">

        <div class="report-form-grid">

            <!-- QUEJOSO -->
            <div class="report-field report-field--full">

                <label for="quejoso">
                    Quejoso
                </label>

                <input type="text" id="quejoso" name="quejoso" class="report-input"
                    placeholder="Ingresa el nombre del quejoso" autocomplete="off">

            </div>

            <!-- EDAD -->
            <div class="report-field">

                <label for="edad">
                    Edad
                </label>

                <input type="number" id="edad" name="edad" class="report-input" placeholder="Ingresa la edad" min="0"
                    max="120">

            </div>

            <!-- GÉNERO -->
            <div class="report-field">

                <label for="genero">
                    Género
                </label>

                <select id="genero" name="genero" class="report-select">
                    <option value="" selected disabled>
                        Selecciona una opción
                    </option>

                    <option value="Mujer">
                        Mujer
                    </option>

                    <option value="Hombre">
                        Hombre
                    </option>

                    <option value="Otro">
                        Otro
                    </option>

                    <option value="No especificado">
                        No especificado
                    </option>

                </select>

            </div>

            <!-- MEDIO DE CONTACTO -->
            <div class="report-field">

                <label for="medio_contacto">
                    Medio de contacto
                </label>

                <input type="text" id="medio_contacto" name="medio_contacto" class="report-input"
                    placeholder="Ingresa el medio de contacto" autocomplete="off">

            </div>

        </div>

    </div>

</section>