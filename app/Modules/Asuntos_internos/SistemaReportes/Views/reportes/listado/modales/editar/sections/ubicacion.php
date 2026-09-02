<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Geolocalización
        </span>

        <h3>
            Ubicación de los hechos
        </h3>

    </div>


    <!-- =====================================================
         BUSCADOR DE UBICACIÓN
    ====================================================== -->
    <div class="editar-reporte-campo editar-reporte-campo--completo">

        <label for="editar-ubicacion-busqueda">
            Buscar ubicación
        </label>

        <input type="text" id="editar-ubicacion-busqueda" autocomplete="off"
            placeholder="Escribe una dirección o pega coordenadas">

    </div>


    <!-- =====================================================
         MAPA
    ====================================================== -->
    <div id="editar-mapa-ubicacion" class="mapa-ubicacion"
        aria-label="Mapa para seleccionar la ubicación de los hechos"></div>


    <!-- =====================================================
         DATOS DE LA UBICACIÓN
    ====================================================== -->
    <div class="editar-reporte-grid">


        <!-- CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-calle">
                Calle
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-calle" name="calle" autocomplete="off" required>

        </div>


        <!-- NÚMERO EXTERIOR -->
        <div class="editar-reporte-campo">

            <label for="editar-numero">
                No. Ext.
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-numero" name="numero" autocomplete="off" required>

        </div>


        <!-- COLONIA -->
        <div class="editar-reporte-campo">

            <label for="editar-colonia">
                Colonia
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-colonia" name="colonia" autocomplete="off" required>

        </div>


        <!-- ENTRE CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-entre-calle">
                Entre calle
            </label>

            <input type="text" id="editar-entre-calle" name="entre_calle" autocomplete="off">

        </div>


        <!-- Y CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-y-calle">
                Y calle
            </label>

            <input type="text" id="editar-y-calle" name="y_calle" autocomplete="off">

        </div>


        <!-- MUNICIPIO -->
        <div class="editar-reporte-campo">

            <label for="editar-municipio">
                Ciudad / Municipio
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-municipio" name="municipio" autocomplete="off" required>

        </div>


        <!-- ESTADO -->
        <div class="editar-reporte-campo">

            <label for="editar-estado">
                Estado
                <span class="required">*</span>
            </label>

            <input type="text" id="editar-estado" name="estado" autocomplete="off" required>

        </div>


        <!-- SECTOR -->
        <div class="editar-reporte-campo">

            <label for="editar-sector">
                Sector
            </label>

            <input type="text" id="editar-sector" name="sector" autocomplete="off" readonly>

        </div>


        <!-- CUADRANTE -->
        <div class="editar-reporte-campo">

            <label for="editar-cuadrante">
                Cuadrante
            </label>

            <input type="text" id="editar-cuadrante" name="cuadrante" autocomplete="off" readonly>

        </div>


        <!-- ID DE CUADRA / CALLE -->
        <div class="editar-reporte-campo">

            <label for="editar-id-cuadra">
                ID de cuadra / calle
            </label>

            <input type="text" id="editar-id-cuadra" name="id_cuadra" autocomplete="off" readonly>

        </div>


        <!-- LONGITUD X -->
        <div class="editar-reporte-campo">

            <label for="editar-longitud-visible">
                Longitud (X)
            </label>

            <input type="text" id="editar-longitud-visible" autocomplete="off" readonly>

        </div>


        <!-- LATITUD Y -->
        <div class="editar-reporte-campo">

            <label for="editar-latitud-visible">
                Latitud (Y)
            </label>

            <input type="text" id="editar-latitud-visible" autocomplete="off" readonly>

        </div>


        <!-- COORDENADAS -->
        <div class="editar-reporte-campo">

            <label for="editar-coordenadas">
                Coordenadas
            </label>

            <input type="text" id="editar-coordenadas" autocomplete="off" readonly>

        </div>

    </div>


    <!-- =====================================================
         DATOS QUE SE GUARDAN EN BD
    ====================================================== -->

    <input type="hidden" id="editar-latitud" name="latitud">

    <input type="hidden" id="editar-longitud" name="longitud">

    <input type="hidden" id="editar-origen-ubicacion" name="origen_ubicacion">

</div>

<!-- =============================================================
     GOOGLE MAPS
============================================================= -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxfm6rP0X1P2_6p2YdMIfkuTGFpQwBu6A"></script>
