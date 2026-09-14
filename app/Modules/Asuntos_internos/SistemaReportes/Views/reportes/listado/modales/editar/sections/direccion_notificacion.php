<div class="editar-reporte-seccion__bloque" id="editar-seccion-direccion-notificacion">

    <div class="detalle-reporte-seccion__header">

        <span>
            Información para notificación
        </span>

        <h3>
            Dirección para notificación
        </h3>

    </div>


    <!-- =====================================================
        PERTENECE A NEZAHUALCÓYOTL
    ====================================================== -->

    <div class="editar-reporte-grid">

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                ¿Pertenece al municipio de Nezahualcóyotl?
            </label>


            <div class="editar-report-options">

                <!-- SÍ -->
                <label class="editar-report-option">

                    <input type="radio" name="notificacion_pertenece_neza" id="editar-notificacion-pertenece-neza-si"
                        value="1">

                    <span>
                        Sí
                    </span>

                </label>


                <!-- NO -->
                <label class="editar-report-option">

                    <input type="radio" name="notificacion_pertenece_neza" id="editar-notificacion-pertenece-neza-no"
                        value="0">

                    <span>
                        No
                    </span>

                </label>

            </div>


            <small class="editar-reporte-campo__help">
                Selecciona “No” cuando la dirección para notificación
                se encuentre fuera del municipio de Nezahualcóyotl.
            </small>


            <!-- =================================================
                ADVERTENCIA PARA DOMICILIO FORÁNEO
            ================================================== -->

            <div class="report-notification-warning" id="editar-notificacion-advertencia-foraneo" hidden>
                Se debe de ingresar un domicilio dentro del municipio
                de Nezahualcóyotl para continuar con su proceso de
                seguimiento y notificación.
            </div>

        </div>

    </div>


    <!-- =====================================================
         BUSCADOR DE UBICACIÓN
    ====================================================== -->

    <div class="editar-reporte-campo editar-reporte-campo--completo">

        <label for="editar-notificacion-ubicacion-busqueda">
            Buscar ubicación
        </label>

        <input type="text" id="editar-notificacion-ubicacion-busqueda" autocomplete="off"
            placeholder="Escribe una dirección o pega coordenadas">

    </div>


    <!-- =====================================================
         MAPA
    ====================================================== -->

    <div id="editar-notificacion-mapa-ubicacion" class="mapa-ubicacion"
        aria-label="Mapa para seleccionar la dirección de notificación"></div>


    <!-- =====================================================
         DATOS DE LA DIRECCIÓN
    ====================================================== -->

    <div class="editar-reporte-grid">


        <!-- CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-calle">
                Calle
            </label>

            <input type="text" id="editar-notificacion-calle" name="notificacion_calle" autocomplete="off">

        </div>


        <!-- NÚMERO EXTERIOR -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-numero">
                No. Ext.
            </label>

            <input type="text" id="editar-notificacion-numero" name="notificacion_numero_exterior" autocomplete="off">

        </div>


        <!-- COLONIA -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-colonia">
                Colonia
            </label>

            <input type="text" id="editar-notificacion-colonia" name="notificacion_colonia" autocomplete="off">

        </div>


        <!-- ENTRE CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-entre-calle">
                Entre calle
            </label>

            <input type="text" id="editar-notificacion-entre-calle" name="notificacion_entre_calle" autocomplete="off">

        </div>


        <!-- Y CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-y-calle">
                Y calle
            </label>

            <input type="text" id="editar-notificacion-y-calle" name="notificacion_y_calle" autocomplete="off">

        </div>


        <!-- MUNICIPIO -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-municipio">
                Ciudad / Municipio
            </label>

            <input type="text" id="editar-notificacion-municipio" name="notificacion_municipio" autocomplete="off">

        </div>


        <!-- ESTADO -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-estado">
                Estado
            </label>

            <input type="text" id="editar-notificacion-estado" name="notificacion_estado" autocomplete="off">

        </div>


        <!-- SECTOR -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-sector">
                Sector
            </label>

            <input type="text" id="editar-notificacion-sector" name="notificacion_sector" autocomplete="off" readonly>

        </div>


        <!-- CUADRANTE -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-cuadrante">
                Cuadrante
            </label>

            <input type="text" id="editar-notificacion-cuadrante" name="notificacion_cuadrante" autocomplete="off"
                readonly>

        </div>


        <!-- ID DE CUADRA / CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-id-cuadra">
                ID de cuadra / calle
            </label>

            <input type="text" id="editar-notificacion-id-cuadra" name="notificacion_id_cuadra" autocomplete="off"
                readonly>

        </div>


        <!-- LONGITUD X -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-longitud-visible">
                Longitud (X)
            </label>

            <input type="text" id="editar-notificacion-longitud-visible" autocomplete="off" readonly>

        </div>


        <!-- LATITUD Y -->
        <div class="editar-reporte-campo">

            <label for="editar-notificacion-latitud-visible">
                Latitud (Y)
            </label>

            <input type="text" id="editar-notificacion-latitud-visible" autocomplete="off" readonly>

        </div>


        <!-- COORDENADAS -->
        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label for="editar-notificacion-coordenadas">
                Coordenadas
            </label>

            <input type="text" id="editar-notificacion-coordenadas" autocomplete="off" readonly>

            <small class="editar-reporte-campo__help">
                Formato: latitud, longitud.
            </small>

        </div>

    </div>


    <!-- =====================================================
         DATOS QUE SE GUARDAN EN BD
    ====================================================== -->

    <input type="hidden" id="editar-notificacion-latitud" name="notificacion_latitud">

    <input type="hidden" id="editar-notificacion-longitud" name="notificacion_longitud">

    <input type="hidden" id="editar-notificacion-origen-ubicacion" name="notificacion_origen_ubicacion">

</div>