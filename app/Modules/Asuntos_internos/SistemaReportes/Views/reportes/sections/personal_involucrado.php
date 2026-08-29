<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Personal relacionado
            </span>

            <h2 class="report-section__title">
                Personal involucrado
            </h2>

            <p class="report-section__description">
                Busca y agrega una o más personas relacionadas con los hechos.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <!-- =====================================================
             BÚSQUEDA
        ====================================================== -->
        <div class="report-form-grid">

            <div class="report-field report-field--full">

                <label for="oficial">
                    Buscar personal
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="oficial"
                    class="report-input"
                    placeholder="Busca por nombre o nómina"
                    autocomplete="off">

                <small class="report-field__help">
                    Selecciona una persona para cargar automáticamente sus datos.
                </small>

                <!-- Resultados de búsqueda -->
                <div
                    class="personal-resultados"
                    id="personal-resultados"
                    hidden></div>

            </div>

        </div>


        <!-- =====================================================
             PERSONA SELECCIONADA
        ====================================================== -->
        <div
            class="personal-seleccionado"
            id="personal-seleccionado"
            hidden>

            <div class="personal-seleccionado__foto">

                <img
                    id="personal-foto"
                    src=""
                    alt=""
                    hidden>

                <span id="personal-foto-fallback">
                    —
                </span>

            </div>


            <div class="personal-seleccionado__datos">

                <input
                    type="hidden"
                    id="personal-plantilla-id">

                <input
                    type="hidden"
                    id="personal-perscod">


                <div class="report-form-grid">

                    <div class="report-field report-field--full">

                        <label>
                            Nombre
                        </label>

                        <input
                            type="text"
                            id="personal-nombre"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <div class="report-field">

                        <label>
                            Área
                        </label>

                        <input
                            type="text"
                            id="personal-area"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <div class="report-field">

                        <label for="personal-turno">
                            Turno
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="personal-turno"
                            class="report-input"
                            placeholder="Turno">

                    </div>

                </div>

            </div>


            <div class="personal-seleccionado__acciones">

                <button
                    type="button"
                    class="button button--primary"
                    id="btn-agregar-personal">
                    Agregar personal
                </button>

            </div>

        </div>


        <!-- =====================================================
             PERSONAL AGREGADO
        ====================================================== -->
        <div
            class="personal-agregado"
            id="personal-agregado"
            hidden>

            <div class="personal-agregado__header">

                <div>

                    <span>
                        Personal agregado
                    </span>

                    <strong>
                        Elementos relacionados con el reporte
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
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody id="personal-agregado-body"></tbody>

                </table>

            </div>

        </div>


        <!-- =====================================================
             DATOS PARA ENVIAR AL BACKEND
        ====================================================== -->
        <div id="personal-hidden-inputs"></div>

    </div>

</section>