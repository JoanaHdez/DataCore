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
                Adjunta las fotografías relacionadas con el reporte.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-evidence">

            <!-- INPUT REAL -->
            <input
                type="file"
                id="evidencia_fotografica"
                name="evidencia_fotografica[]"
                class="report-evidence__input"
                accept="image/jpeg,image/png,image/webp"
                multiple
            >


            <!-- ZONA DE SELECCIÓN -->
            <label
                for="evidencia_fotografica"
                class="report-evidence__dropzone"
            >

                <div class="report-evidence__icon">

                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            d="M12 16V4M7 9l5-5 5 5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        />

                    </svg>

                </div>


                <div class="report-evidence__content">

                    <strong>
                        Seleccionar fotografías
                    </strong>

                    <span>
                        Haz clic para elegir una o varias imágenes
                    </span>

                    <small>
                        JPG, PNG o WEBP
                    </small>

                </div>

            </label>


            <!-- ARCHIVOS SELECCIONADOS -->
            <div
                class="report-evidence__files"
                id="evidencia-lista-archivos"
            ></div>

        </div>

    </div>

</section>