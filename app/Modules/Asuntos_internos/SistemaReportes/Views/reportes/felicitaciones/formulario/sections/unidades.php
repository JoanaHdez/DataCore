<!-- =========================================================
     PASO 2 - UNIDADES RELACIONADAS
========================================================= -->

<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Unidades relacionadas
            </span>

            <h2 class="report-section__title">
                Unidades relacionadas
            </h2>

            <p class="report-section__description">
                Indica si el personal felicitado está relacionado con una unidad vehicular
                o corresponde a personal sin unidad.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <!-- =====================================================
             MODALIDAD
        ====================================================== -->

        <div class="report-form-grid">

            <div class="report-field report-field--full">

                <label>
                    Tipo de asignación
                    <span class="required">*</span>
                </label>

                <div class="unidad-modalidad">

                    <label class="unidad-modalidad__opcion">

                        <input type="radio" name="modalidad_unidad" id="felicitacion-modalidad-con-unidad"
                            value="CON_UNIDAD" checked>

                        <span>
                            Con unidad
                        </span>

                    </label>


                    <label class="unidad-modalidad__opcion">

                        <input type="radio" name="modalidad_unidad" id="felicitacion-modalidad-sin-unidad"
                            value="SIN_UNIDAD_OFICINA">

                        <span>
                            Sin unidad / Oficina
                        </span>

                    </label>

                </div>

                <small class="report-field__help">
                    Selecciona “Sin unidad / Oficina” cuando el personal felicitado
                    no tenga una unidad vehicular relacionada.
                </small>

            </div>

        </div>


        <!-- =====================================================
             CON UNIDAD
        ====================================================== -->

        <div id="felicitacion-contenedor-unidades-con-unidad">


            <!-- =================================================
                 BÚSQUEDA
            ================================================== -->

            <div class="report-form-grid">

                <div class="report-field report-field--full">

                    <label for="felicitacion-unidad-busqueda">
                        Buscar unidad
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="felicitacion-unidad-busqueda" class="report-input"
                        placeholder="Busca por número económico o placas" autocomplete="off">

                    <small class="report-field__help">
                        Selecciona una unidad para cargar automáticamente sus datos.
                    </small>

                    <div class="unidad-resultados" id="felicitacion-unidad-resultados" hidden></div>

                </div>

            </div>


            <!-- =================================================
                 UNIDAD SELECCIONADA
            ================================================== -->

            <div class="unidad-seleccionada" id="felicitacion-unidad-seleccionada" hidden>

                <input type="hidden" id="felicitacion-unidad-parque-id">


                <div class="report-form-grid report-form-grid--unit">


                    <!-- UNIDAD -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-no-economico">
                            Unidad
                        </label>

                        <input type="text" id="felicitacion-unidad-no-economico"
                            class="report-input report-input--readonly" readonly>

                    </div>


                    <!-- PLACAS -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-placas">
                            Placas
                        </label>

                        <input type="text" id="felicitacion-unidad-placas" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- MARCA -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-marca">
                            Marca
                        </label>

                        <input type="text" id="felicitacion-unidad-marca" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- SUBMARCA -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-submarca">
                            Submarca
                        </label>

                        <input type="text" id="felicitacion-unidad-submarca" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- COLOR -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-color">
                            Color
                        </label>

                        <input type="text" id="felicitacion-unidad-color" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- ESTATUS -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-estatus">
                            Estatus de la unidad
                        </label>

                        <input type="text" id="felicitacion-unidad-estatus" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- SERVICIO -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-servicio">
                            Servicio y adscripción
                        </label>

                        <input type="text" id="felicitacion-unidad-servicio" class="report-input report-input--readonly"
                            readonly>

                    </div>


                    <!-- TIPO -->

                    <div class="report-field">

                        <label for="felicitacion-unidad-tipo">
                            Tipo de vehículo
                        </label>

                        <input type="text" id="felicitacion-unidad-tipo" class="report-input report-input--readonly"
                            readonly>

                    </div>

                </div>


                <!-- =================================================
                     AGREGAR
                ================================================== -->

                <div class="unidad-seleccionada__acciones">

                    <button type="button" class="button button--primary" id="btn-agregar-unidad-felicitacion">
                        Agregar unidad
                    </button>

                </div>

            </div>


            <!-- =================================================
                 UNIDADES AGREGADAS
            ================================================== -->

            <div class="unidades-agregadas" id="felicitacion-unidades-agregadas" hidden>

                <div class="unidades-agregadas__header">

                    <div>

                        <span>
                            Unidades agregadas
                        </span>

                        <strong>
                            Unidades relacionadas con la felicitación
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

                        <tbody id="felicitacion-unidades-agregadas-body"></tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- =====================================================
             SIN UNIDAD / OFICINA
        ====================================================== -->

        <div class="unidad-sin-unidad" id="felicitacion-unidad-sin-unidad" hidden>

            <div class="unidad-sin-unidad__contenido">

                <strong>
                    Sin unidad / Oficina
                </strong>

                <p>
                    El personal felicitado no cuenta con una unidad vehicular relacionada.
                </p>

            </div>

        </div>


        <!-- =====================================================
             DATOS PARA BACKEND
        ====================================================== -->

        <div id="felicitacion-unidades-hidden-inputs"></div>

    </div>

</section>