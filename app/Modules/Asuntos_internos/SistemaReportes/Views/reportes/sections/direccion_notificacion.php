<section class="report-section" id="seccion-direccion-notificacion">

    <div class="report-section__header">

        <div>

            <span class="report-section__eyebrow">
                Información para notificación
            </span>

            <h2 class="report-section__title">
                Dirección para notificación
            </h2>

            <p class="report-section__description">
                Indica si la dirección pertenece al municipio de Nezahualcóyotl
                y captura el domicilio para notificación.
            </p>

        </div>

    </div>


    <div class="report-section__body">

        <div class="report-location">


            <!-- =====================================================
                 PERTENECE A NEZAHUALCÓYOTL
            ====================================================== -->

            <div class="report-field report-field--full">

                <label>
                    ¿Pertenece al municipio de Nezahualcóyotl?
                    <span class="required">*</span>
                </label>


                <div class="report-options">

                    <!-- SÍ -->

                    <label class="report-option">

                        <input type="radio" name="notificacion_pertenece_neza" id="notificacion-pertenece-neza-si"
                            value="1" required>

                        <span>
                            Sí
                        </span>

                    </label>


                    <!-- NO -->

                    <label class="report-option">

                        <input type="radio" name="notificacion_pertenece_neza" id="notificacion-pertenece-neza-no"
                            value="0" required>

                        <span>
                            No
                        </span>

                    </label>

                </div>


                <small class="report-field__help report-field__help--notificacion">
                    Selecciona “No” cuando la dirección para notificación
                    se encuentre fuera del municipio de Nezahualcóyotl.
                </small>


                <!-- =================================================
                     ADVERTENCIA PARA DOMICILIO FORÁNEO
                ================================================== -->

                <div class="report-notification-warning" id="notificacion-advertencia-foraneo" hidden>
                    Se debe de ingresar un domicilio dentro del municipio
                    de Nezahualcóyotl para continuar con su proceso de
                    seguimiento y notificación.
                </div>

            </div>


            <!-- =====================================================
                 BUSCADOR
            ====================================================== -->

            <div class="report-field report-field--full">

                <label for="notificacion-ubicacion-busqueda">
                    Buscar ubicación
                    <span class="required">*</span>
                </label>

                <input type="text" id="notificacion-ubicacion-busqueda" name="notificacion_ubicacion_busqueda"
                    class="report-input" placeholder="Busca una calle, colonia o dirección" autocomplete="off" required>

                <small class="report-field__help">
                    También puedes seleccionar directamente un punto
                    en el mapa.
                </small>

            </div>


            <!-- =====================================================
                 MAPA
            ====================================================== -->

            <div id="notificacion-mapa-ubicacion" class="report-location__map"
                aria-label="Mapa para seleccionar la dirección de notificación"></div>


            <!-- =====================================================
                 DIRECCIÓN
            ====================================================== -->

            <div class="report-form-grid report-location__fields">


                <!-- CALLE -->

                <div class="report-field">

                    <label for="notificacion-calle">
                        Calle
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-calle" name="notificacion_calle" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- NÚMERO EXTERIOR -->

                <div class="report-field">

                    <label for="notificacion-numero">
                        No. Ext.
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-numero" name="notificacion_numero_exterior" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- COLONIA -->

                <div class="report-field">

                    <label for="notificacion-colonia">
                        Colonia
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-colonia" name="notificacion_colonia" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- ENTRE CALLE -->

                <div class="report-field">

                    <label for="notificacion-entre-calle">
                        Entre calle
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-entre-calle" name="notificacion_entre_calle"
                        class="report-input" autocomplete="off" required>

                </div>


                <!-- Y CALLE -->

                <div class="report-field">

                    <label for="notificacion-y-calle">
                        Y calle
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-y-calle" name="notificacion_y_calle" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- MUNICIPIO -->

                <div class="report-field">

                    <label for="notificacion-municipio">
                        Ciudad / Municipio
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-municipio" name="notificacion_municipio" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- ESTADO -->

                <div class="report-field">

                    <label for="notificacion-estado">
                        Estado
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-estado" name="notificacion_estado" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- SECTOR -->

                <div class="report-field">

                    <label for="notificacion-sector">
                        Sector
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-sector" name="notificacion_sector" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- CUADRANTE -->

                <div class="report-field">

                    <label for="notificacion-cuadrante">
                        Cuadrante
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-cuadrante" name="notificacion_cuadrante" class="report-input"
                        autocomplete="off" required>

                </div>


                <!-- ID CUADRA / CALLE -->

                <div class="report-field">

                    <label for="notificacion-id-cuadra">
                        ID de cuadra / calle
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-id-cuadra" name="notificacion_id_cuadra" class="report-input"
                        autocomplete="off" readonly required>

                </div>


                <!-- LONGITUD / X -->

                <div class="report-field">

                    <label for="notificacion-longitud-visible">
                        Longitud (X)
                    </label>

                    <input type="text" id="notificacion-longitud-visible" class="report-input" readonly>

                </div>


                <!-- LATITUD / Y -->

                <div class="report-field">

                    <label for="notificacion-latitud-visible">
                        Latitud (Y)
                    </label>

                    <input type="text" id="notificacion-latitud-visible" class="report-input" readonly>

                </div>


                <!-- COORDENADAS -->

                <div class="report-field report-field--full">

                    <label for="notificacion-coordenadas">
                        Coordenadas
                        <span class="required">*</span>
                    </label>

                    <input type="text" id="notificacion-coordenadas" name="notificacion_coordenadas"
                        class="report-input" readonly required>

                    <small class="report-field__help">
                        Formato: latitud, longitud.
                    </small>

                </div>

            </div>


            <!-- =====================================================
                 DATOS OCULTOS REALES
            ====================================================== -->

            <input type="hidden" id="notificacion-latitud" name="notificacion_latitud" required>

            <input type="hidden" id="notificacion-longitud" name="notificacion_longitud" required>

            <input type="hidden" id="notificacion-origen-ubicacion" name="notificacion_origen_ubicacion" value="manual"
                required>

        </div>

    </div>

</section>


<!-- =============================================================
     GOOGLE MAPS
============================================================= -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxfm6rP0X1P2_6p2YdMIfkuTGFpQwBu6A"></script>