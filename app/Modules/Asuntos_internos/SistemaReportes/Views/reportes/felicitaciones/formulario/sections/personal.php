<!-- =========================================================
     PASO 2 - PERSONAL FELICITADO
========================================================= -->

<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Personal relacionado
            </span>

            <h2 class="report-section__title">
                Personal felicitado
            </h2>

            <p class="report-section__description">
                Busca y agrega una o más personas relacionadas con la felicitación.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <!-- =====================================================
             BÚSQUEDA
        ====================================================== -->

        <div class="report-form-grid">

            <div class="report-field report-field--full">

                <label for="felicitacion-oficial">
                    Buscar personal
                    <span class="required">*</span>
                </label>

                <input type="text" id="felicitacion-oficial" class="report-input"
                    placeholder="Busca por nombre o nómina" autocomplete="off">

                <small class="report-field__help">
                    Selecciona una persona para cargar automáticamente sus datos.
                </small>

                <div class="personal-resultados" id="felicitacion-personal-resultados" hidden></div>

            </div>

        </div>


        <!-- =====================================================
             PERSONA SELECCIONADA
        ====================================================== -->

        <div class="personal-seleccionado" id="felicitacion-personal-seleccionado" hidden>

            <!-- FOTO -->

            <div class="personal-seleccionado__foto">

                <img id="felicitacion-personal-foto" src="" alt="" hidden>

                <span id="felicitacion-personal-foto-fallback">
                    —
                </span>

            </div>


            <!-- DATOS -->

            <div class="personal-seleccionado__datos">

                <input type="hidden" id="felicitacion-personal-plantilla-id">

                <input type="hidden" id="felicitacion-personal-perscod">


                <div class="report-form-grid">

                    <!-- NOMBRE -->

                    <div class="report-field report-field--full">

                        <label>
                            Nombre
                        </label>

                        <input type="text" id="felicitacion-personal-nombre" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- ÁREA -->

                    <div class="report-field">

                        <label>
                            Área
                        </label>

                        <input type="text" id="felicitacion-personal-area" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- TURNO -->

                    <div class="report-field">

                        <label for="felicitacion-personal-turno">
                            Turno
                        </label>

                        <input type="text" id="felicitacion-personal-turno" class="report-input" placeholder="Turno">

                    </div>


                    <!-- ALIAS -->

                    <div class="report-field report-field--full">

                        <label for="felicitacion-personal-alias">
                            Alias
                        </label>

                        <input type="text" id="felicitacion-personal-alias" class="report-input"
                            placeholder="Alias del elemento, si aplica" autocomplete="off">

                        <small class="report-field__help">
                            Este campo es opcional.
                        </small>

                    </div>

                </div>

            </div>


            <!-- ACCIONES -->

            <div class="personal-seleccionado__acciones">

                <button type="button" class="button button--primary" id="btn-agregar-personal-felicitacion">
                    Agregar personal
                </button>

            </div>

        </div>


        <!-- =====================================================
             PERSONAL AGREGADO
        ====================================================== -->

        <div class="personal-agregado" id="felicitacion-personal-agregado" hidden>

            <div class="personal-agregado__header">

                <div>

                    <span>
                        Personal agregado
                    </span>

                    <strong>
                        Elementos relacionados con la felicitación
                    </strong>

                </div>

            </div>


            <div class="personal-agregado__tabla-wrapper">

                <table class="personal-agregado__tabla">

                    <thead>

                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Área</th>
                            <th>Turno</th>
                            <th>Alias</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody id="felicitacion-personal-agregado-body"></tbody>

                </table>

            </div>

        </div>


        <!-- =====================================================
             DATOS PARA BACKEND
        ====================================================== -->

        <div id="felicitacion-personal-hidden-inputs"></div>

    </div>

</section>