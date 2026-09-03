<div class="editar-reporte-seccion__bloque">

    <div class="detalle-reporte-seccion__header">

        <span>
            Unidades relacionadas
        </span>

        <h3>
            Unidades involucradas
        </h3>

    </div>


    <!-- =====================================================
         MODALIDAD DE UNIDAD
    ====================================================== -->

    <div class="editar-reporte-grid">

        <div class="editar-reporte-campo editar-reporte-campo--full">

            <label>
                Tipo de asignación
            </label>

            <div class="unidad-modalidad">

                <label class="unidad-modalidad__opcion">

                    <input type="radio" name="modalidad_unidad" id="editar-modalidad-con-unidad" value="CON_UNIDAD">

                    <span>
                        Con unidad
                    </span>

                </label>


                <label class="unidad-modalidad__opcion">

                    <input type="radio" name="modalidad_unidad" id="editar-modalidad-sin-unidad"
                        value="SIN_UNIDAD_OFICINA">

                    <span>
                        Sin unidad / Oficina
                    </span>

                </label>

            </div>

            <small class="editar-reporte-campo__help">
                Selecciona “Sin unidad / Oficina” cuando el personal involucrado no tenga una unidad vehicular asignada.
            </small>

        </div>

    </div>


    <!-- =====================================================
         CONTENIDO PARA REPORTES CON UNIDAD
    ====================================================== -->

    <div id="editar-contenedor-unidades-con-unidad">


        <!-- =================================================
             BUSCADOR
        ================================================== -->

        <div class="editar-reporte-grid">

            <div class="editar-reporte-campo editar-reporte-campo--full">

                <label for="editar-unidad-busqueda">
                    Buscar unidad
                </label>

                <input type="text" id="editar-unidad-busqueda" autocomplete="off"
                    placeholder="Busca por número económico o placas">

                <small class="editar-reporte-campo__help">
                    Puedes agregar una o más unidades al reporte.
                </small>


                <div class="editar-unidad-resultados" id="editar-unidad-resultados" hidden></div>

            </div>

        </div>


        <!-- =================================================
             UNIDAD SELECCIONADA
        ================================================== -->

        <div class="editar-unidad-seleccionada" id="editar-unidad-seleccionada" hidden>

            <input type="hidden" id="editar-unidad-parque-id">


            <div class="editar-reporte-grid">

                <!-- UNIDAD -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-no-economico">
                        Unidad
                    </label>

                    <input type="text" id="editar-unidad-no-economico" readonly>

                </div>


                <!-- PLACAS -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-placas">
                        Placas
                    </label>

                    <input type="text" id="editar-unidad-placas" readonly>

                </div>


                <!-- MARCA -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-marca">
                        Marca
                    </label>

                    <input type="text" id="editar-unidad-marca" readonly>

                </div>


                <!-- SUBMARCA -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-submarca">
                        Submarca
                    </label>

                    <input type="text" id="editar-unidad-submarca" readonly>

                </div>


                <!-- COLOR -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-color">
                        Color
                    </label>

                    <input type="text" id="editar-unidad-color" readonly>

                </div>


                <!-- ESTATUS -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-estatus">
                        Estatus
                    </label>

                    <input type="text" id="editar-unidad-estatus" readonly>

                </div>


                <!-- SERVICIO -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-servicio">
                        Servicio y adscripción
                    </label>

                    <input type="text" id="editar-unidad-servicio" readonly>

                </div>


                <!-- TIPO -->
                <div class="editar-reporte-campo">

                    <label for="editar-unidad-tipo">
                        Tipo de vehículo
                    </label>

                    <input type="text" id="editar-unidad-tipo" readonly>

                </div>

            </div>


            <div class="editar-unidad-seleccionada__acciones">

                <button type="button" class="modal-reporte__button modal-reporte__button--primary"
                    id="btn-editar-agregar-unidad">
                    Agregar unidad
                </button>

            </div>

        </div>


        <!-- =================================================
             UNIDADES ACTUALES DEL REPORTE
        ================================================== -->

        <div class="editar-unidades-agregadas" id="editar-unidades-agregadas" hidden>

            <div class="editar-unidades-agregadas__header">

                <div>

                    <span>
                        Unidades agregadas
                    </span>

                    <strong>
                        Unidades relacionadas con el reporte
                    </strong>

                </div>

            </div>


            <div class="editar-unidades-agregadas__tabla-wrapper">

                <table class="editar-unidades-agregadas__tabla">

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

                    <tbody id="editar-unidades-agregadas-body"></tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- =====================================================
         SIN UNIDAD / OFICINA
    ====================================================== -->

    <div class="unidad-sin-unidad" id="editar-unidad-sin-unidad" hidden>

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

    <div id="editar-unidades-hidden-inputs"></div>

</div>