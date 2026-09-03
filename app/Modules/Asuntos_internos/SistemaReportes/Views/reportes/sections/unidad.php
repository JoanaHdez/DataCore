<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Unidades relacionadas
            </span>

            <h2 class="report-section__title">
                Unidades involucradas
            </h2>

            <p class="report-section__description">
                Indica si los hechos están relacionados con una unidad vehicular o corresponden a personal sin unidad.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <!-- =====================================================
             MODALIDAD DE UNIDAD
        ====================================================== -->

        <div class="report-form-grid">

            <div class="report-field report-field--full">

                <label>
                    Tipo de asignación
                    <span class="required">*</span>
                </label>

                <div class="unidad-modalidad">

                    <label class="unidad-modalidad__opcion">

                        <input
                            type="radio"
                            name="modalidad_unidad"
                            id="modalidad-con-unidad"
                            value="CON_UNIDAD"
                            checked>

                        <span>
                            Con unidad
                        </span>

                    </label>


                    <label class="unidad-modalidad__opcion">

                        <input
                            type="radio"
                            name="modalidad_unidad"
                            id="modalidad-sin-unidad"
                            value="SIN_UNIDAD_OFICINA">

                        <span>
                            Sin unidad / Oficina
                        </span>

                    </label>

                </div>

                <small class="report-field__help">
                    Selecciona “Sin unidad / Oficina” cuando el personal involucrado no tenga una unidad vehicular asignada.
                </small>

            </div>

        </div>


        <!-- =====================================================
             CONTENIDO PARA REPORTES CON UNIDAD
        ====================================================== -->

        <div id="contenedor-unidades-con-unidad">

            <!-- =================================================
                 BÚSQUEDA
            ================================================== -->

            <div class="report-form-grid">

                <div class="report-field report-field--full">

                    <label for="unidad_busqueda">
                        Buscar unidad
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="unidad_busqueda"
                        class="report-input"
                        placeholder="Busca por número económico o placas"
                        autocomplete="off">

                    <small class="report-field__help">
                        Selecciona una unidad para cargar automáticamente sus datos.
                    </small>


                    <!-- RESULTADOS -->
                    <div
                        class="unidad-resultados"
                        id="unidad-resultados"
                        hidden></div>

                </div>

            </div>


            <!-- =================================================
                 UNIDAD SELECCIONADA
            ================================================== -->

            <div
                class="unidad-seleccionada"
                id="unidad-seleccionada"
                hidden>

                <!-- REFERENCIAS -->
                <input
                    type="hidden"
                    id="unidad-parque-id">


                <div class="report-form-grid report-form-grid--unit">

                    <!-- NÚMERO ECONÓMICO -->
                    <div class="report-field">

                        <label for="unidad_no_economico">
                            Unidad
                        </label>

                        <input
                            type="text"
                            id="unidad_no_economico"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- PLACAS -->
                    <div class="report-field">

                        <label for="unidad_placas">
                            Placas
                        </label>

                        <input
                            type="text"
                            id="unidad_placas"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- MARCA -->
                    <div class="report-field">

                        <label for="unidad_marca">
                            Marca
                        </label>

                        <input
                            type="text"
                            id="unidad_marca"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- SUBMARCA -->
                    <div class="report-field">

                        <label for="unidad_submarca">
                            Submarca
                        </label>

                        <input
                            type="text"
                            id="unidad_submarca"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- COLOR -->
                    <div class="report-field">

                        <label for="unidad_color">
                            Color
                        </label>

                        <input
                            type="text"
                            id="unidad_color"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- ESTATUS -->
                    <div class="report-field">

                        <label for="unidad_estatus">
                            Estatus de la unidad
                        </label>

                        <input
                            type="text"
                            id="unidad_estatus"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- SERVICIO -->
                    <div class="report-field">

                        <label for="unidad_servicio">
                            Servicio y adscripción
                        </label>

                        <input
                            type="text"
                            id="unidad_servicio"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- TIPO -->
                    <div class="report-field">

                        <label for="unidad_tipo">
                            Tipo de vehículo
                        </label>

                        <input
                            type="text"
                            id="unidad_tipo"
                            class="report-input report-input--readonly"
                            readonly>

                    </div>

                </div>


                <!-- ACCIÓN -->
                <div class="unidad-seleccionada__acciones">

                    <button
                        type="button"
                        class="button button--primary"
                        id="btn-agregar-unidad">
                        Agregar unidad
                    </button>

                </div>

            </div>


            <!-- =================================================
                 UNIDADES AGREGADAS
            ================================================== -->

            <div
                class="unidades-agregadas"
                id="unidades-agregadas"
                hidden>

                <div class="unidades-agregadas__header">

                    <div>

                        <span>
                            Unidades agregadas
                        </span>

                        <strong>
                            Unidades relacionadas con el reporte
                        </strong>

                    </div>

                </div>


                <div class="unidades-agregadas__tabla-wrapper">

                    <table class="unidades-agregadas__tabla">

                        <thead>

                            <tr>
                                <th>Unidad</th>
                                <th>Marca / Submarca</th>
                                <th>Color</th>
                                <th>Estatus</th>
                                <th>Servicio</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>

                        </thead>

                        <tbody id="unidades-agregadas-body"></tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- =====================================================
             ESTADO SIN UNIDAD / OFICINA
        ====================================================== -->

        <div
            class="unidad-sin-unidad"
            id="unidad-sin-unidad"
            hidden>

            <div class="unidad-sin-unidad__contenido">

                <strong>
                    Sin unidad / Oficina
                </strong>

                <p>
                    El personal relacionado con los hechos no cuenta con una unidad vehicular asignada.
                </p>

            </div>

        </div>


        <!-- =====================================================
             DATOS PARA BACKEND
        ====================================================== -->

        <div id="unidades-hidden-inputs"></div>

    </div>

</section>