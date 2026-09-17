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