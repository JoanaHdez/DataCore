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
                Selecciona la ubicación en el mapa para completar automáticamente la dirección.
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
                    También puedes seleccionar directamente un punto en el mapa.
                </small>

            </div>

            <!-- =====================================================
                 MAPA
            ====================================================== -->
            <div id="mapa-ubicacion" class="report-location__map"></div>


            <!-- =====================================================
                 DIRECCIÓN DESGLOSADA
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
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="entre_calle" name="entre_calle" class="report-input" autocomplete="off"
                        required>

                </div>


                <!-- Y CALLE -->
                <div class="report-field">

                    <label for="y_calle">
                        Y calle
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="y_calle" name="y_calle" class="report-input" autocomplete="off" required>

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

            </div>


            <!-- =====================================================
                 COORDENADAS
            ====================================================== -->

            <input type="hidden" id="latitud" name="latitud" required>

            <input type="hidden" id="longitud" name="longitud" required>

        </div>

    </div>

</section>