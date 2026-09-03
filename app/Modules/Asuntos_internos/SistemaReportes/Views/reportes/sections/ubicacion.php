<section class="report-section">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Geolocalización
            </span>

            <h2 class="report-section__title">
                Ubicación de los hechos
            </h2>

            <p class="report-section__description">
                Busca una dirección, selecciona un punto en el mapa
                o captura el domicilio manualmente.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-location">

            <!-- =====================================================
                 BUSCADOR
            ====================================================== -->

            <div class="report-field report-field--full">

                <label for="ubicacion_busqueda">
                    Buscar ubicación
                </label>

                <input type="text" id="ubicacion_busqueda" name="ubicacion_busqueda" class="report-input"
                    placeholder="Busca una calle, colonia o dirección" autocomplete="off">

                <small class="report-field__help">
                    También puedes seleccionar directamente un punto
                    en el mapa.
                </small>

            </div>


            <!-- =====================================================
                 MAPA
            ====================================================== -->

            <div id="mapa-ubicacion" class="report-location__map"
                aria-label="Mapa para seleccionar la ubicación de los hechos"></div>


            <!-- =====================================================
                 DIRECCIÓN
            ====================================================== -->

            <div class="report-form-grid report-location__fields">

                <!-- CALLE -->

                <div class="report-field">

                    <label for="calle">
                        Calle
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="calle" name="calle" class="report-input" autocomplete="off" required>

                </div>


                <!-- NÚMERO EXTERIOR -->

                <div class="report-field">

                    <label for="numero">
                        No. Ext.
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="numero" name="numero" class="report-input" autocomplete="off" required>

                </div>


                <!-- COLONIA -->

                <div class="report-field">

                    <label for="colonia">
                        Colonia
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="colonia" name="colonia" class="report-input" autocomplete="off" required>

                </div>


                <!-- ENTRE CALLE -->

                <div class="report-field">

                    <label for="entre_calle">
                        Entre calle
                    </label>

                    <input type="text" id="entre_calle" name="entre_calle" class="report-input" autocomplete="off">

                </div>


                <!-- Y CALLE -->

                <div class="report-field">

                    <label for="y_calle">
                        Y calle
                    </label>

                    <input type="text" id="y_calle" name="y_calle" class="report-input" autocomplete="off">

                </div>


                <!-- MUNICIPIO -->

                <div class="report-field">

                    <label for="municipio">
                        Ciudad / Municipio
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="municipio" name="municipio" class="report-input" autocomplete="off" required>

                </div>


                <!-- ESTADO -->

                <div class="report-field">

                    <label for="estado">
                        Estado
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="estado" name="estado" class="report-input" autocomplete="off" required>

                </div>


                <!-- SECTOR -->

                <div class="report-field">

                    <label for="sector">
                        Sector
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="sector" name="sector" class="report-input" autocomplete="off" required>

                </div>


                <!-- CUADRANTE -->

                <div class="report-field">

                    <label for="cuadrante">
                        Cuadrante
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="cuadrante" name="cuadrante" class="report-input" autocomplete="off" required>

                </div>


                <!-- ID CUADRA / CALLE -->

                <div class="report-field">

                    <label for="id_cuadra">
                        ID de cuadra / calle
                    </label>

                    <input type="text" id="id_cuadra" name="id_cuadra" class="report-input" autocomplete="off" readonly>

                </div>


                <!-- LONGITUD / X -->

                <div class="report-field">

                    <label for="longitud_visible">
                        Longitud (X)
                    </label>

                    <input type="text" id="longitud_visible" class="report-input" readonly>

                </div>


                <!-- LATITUD / Y -->

                <div class="report-field">

                    <label for="latitud_visible">
                        Latitud (Y)
                    </label>

                    <input type="text" id="latitud_visible" class="report-input" readonly>

                </div>


                <!-- COORDENADAS -->

                <div class="report-field report-field--full">

                    <label for="coordenadas">
                        Coordenadas
                    </label>

                    <input type="text" id="coordenadas" name="coordenadas" class="report-input" readonly>

                    <small class="report-field__help">
                        Formato: latitud, longitud.
                    </small>

                </div>

            </div>


            <!-- =====================================================
                 DATOS OCULTOS REALES

                 Estos son los valores que se envían al backend
                 y posteriormente se guardan en ai_reportes.
            ====================================================== -->

            <input type="hidden" id="latitud" name="latitud" required>

            <input type="hidden" id="longitud" name="longitud" required>

            <input type="hidden" id="origen_ubicacion" name="origen_ubicacion" value="manual">

        </div>

    </div>

</section>


<!-- =============================================================
     GOOGLE MAPS
============================================================= -->

<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY"></script>