<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Evidencia
            </span>

            <h2 class="report-section__title">
                Evidencia fotográfica
            </h2>

            <p class="report-section__description">
                Agrega una o más fotografías relacionadas con el reporte.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-evidence">

            <!-- =================================================
                 INPUT REAL
            ================================================== -->

            <input type="file" id="evidencia_fotografica" name="evidencia_fotografica[]" class="report-evidence__input"
                accept="image/jpeg,image/png,image/webp" multiple>


            <!-- =================================================
                 ZONA DE CARGA
            ================================================== -->

            <label for="evidencia_fotografica" class="report-evidence__dropzone">

                <div class="report-evidence__icon">

                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

                        <path d="M12 16V4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />

                        <path d="M8 8L12 4L16 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />

                        <path d="M5 14V18C5 19.1046 5.89543 20 7 20H17C18.1046 20 19 19.1046 19 18V14"
                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />

                    </svg>

                </div>


                <div class="report-evidence__content">

                    <strong>
                        Seleccionar fotografías
                    </strong>

                    <span>
                        Haz clic para agregar una o varias imágenes
                    </span>

                    <small>
                        JPG, PNG o WEBP
                    </small>

                </div>

            </label>


            <!-- =================================================
                 ARCHIVOS SELECCIONADOS
            ================================================== -->

            <div class="report-evidence__files" id="evidencia-lista-archivos"></div>

        </div>

    </div>

</section>