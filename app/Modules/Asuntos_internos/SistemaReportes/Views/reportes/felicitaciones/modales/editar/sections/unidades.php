<section class="modal-felicitacion__seccion">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="modal-felicitacion__seccion-header">

        <span class="modal-felicitacion__eyebrow">
            Unidades relacionadas
        </span>

        <h3 class="modal-felicitacion__seccion-title">
            Unidades involucradas
        </h3>

    </div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="modal-felicitacion-unidades-editar">


        <!-- =================================================
             TIPO DE ASIGNACIÓN
        ================================================== -->

        <div class="modal-felicitacion-unidades-editar__grupo">

            <label class="modal-felicitacion-unidades-editar__label">
                Tipo de asignación
            </label>


            <div class="modal-felicitacion-unidades-editar__opciones">

                <label class="modal-felicitacion-unidades-editar__opcion">

                    <input type="radio" name="modalidad_unidad_editar" value="CON_UNIDAD"
                        id="editar-felicitacion-con-unidad">

                    <span>
                        Con unidad
                    </span>

                </label>


                <label class="modal-felicitacion-unidades-editar__opcion">

                    <input type="radio" name="modalidad_unidad_editar" value="SIN_UNIDAD_OFICINA"
                        id="editar-felicitacion-sin-unidad">

                    <span>
                        Sin unidad / Oficina
                    </span>

                </label>

            </div>


            <small class="modal-felicitacion-unidades-editar__ayuda">
                Selecciona “Sin unidad / Oficina” cuando el personal felicitado no tenga una unidad vehicular
                relacionada.
            </small>

        </div>


        <!-- =================================================
             BLOQUE CON UNIDAD
        ================================================== -->

        <div id="editar-felicitacion-con-unidad-contenido">


            <!-- =============================================
                 BUSCADOR
            ============================================== -->

            <div class="modal-felicitacion-unidades-editar__grupo">

                <label for="editar-felicitacion-buscar-unidad" class="modal-felicitacion-unidades-editar__label">
                    Buscar unidad
                </label>


                <input type="text" id="editar-felicitacion-buscar-unidad" class="report-input"
                    placeholder="Busca por número económico o placas" autocomplete="off">


                <small class="modal-felicitacion-unidades-editar__ayuda">
                    Puedes agregar una o más unidades a la felicitación.
                </small>


                <!-- =========================================
                     RESULTADOS
                ========================================== -->

                <div class="modal-felicitacion-unidades-editar__resultados" id="editar-felicitacion-unidades-resultados"
                    hidden></div>

            </div>


            <!-- =============================================
     UNIDAD SELECCIONADA
============================================== -->

            <div class="editar-unidad-seleccionada" id="editar-felicitacion-unidad-seleccionada" hidden>

                <input type="hidden" id="editar-felicitacion-unidad-parque-id">


                <div class="editar-reporte-grid">


                    <!-- UNIDAD -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-no-economico">
                            Unidad
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-no-economico" readonly>

                    </div>


                    <!-- PLACAS -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-placas">
                            Placas
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-placas" readonly>

                    </div>


                    <!-- MARCA -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-marca">
                            Marca
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-marca" readonly>

                    </div>


                    <!-- SUBMARCA -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-submarca">
                            Submarca
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-submarca" readonly>

                    </div>


                    <!-- COLOR -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-color">
                            Color
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-color" readonly>

                    </div>


                    <!-- ESTATUS -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-estatus">
                            Estatus de la unidad
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-estatus" readonly>

                    </div>


                    <!-- SERVICIO -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-servicio">
                            Servicio y adscripción
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-servicio" readonly>

                    </div>


                    <!-- TIPO -->

                    <div class="editar-reporte-campo">

                        <label for="editar-felicitacion-unidad-tipo">
                            Tipo de vehículo
                        </label>

                        <input type="text" id="editar-felicitacion-unidad-tipo" readonly>

                    </div>

                </div>


                <div class="editar-unidad-seleccionada__acciones">

                    <button type="button" class="button button--primary" id="btn-editar-agregar-unidad-felicitacion">
                        Agregar unidad
                    </button>

                </div>

            </div>


            <!-- =============================================
                 UNIDADES AGREGADAS
            ============================================== -->

            <div class="modal-felicitacion-unidades-editar__agregadas" id="editar-felicitacion-unidades-agregadas">

                <div class="modal-felicitacion-unidades-editar__subheader">

                    <span class="modal-felicitacion__eyebrow">
                        Unidades agregadas
                    </span>

                    <strong>
                        Unidades relacionadas con la felicitación
                    </strong>

                </div>


                <div class="modal-felicitacion__tabla-wrapper">

                    <table class="modal-felicitacion__tabla">

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


                        <tbody id="editar-felicitacion-unidades">

                            <tr>

                                <td colspan="7">
                                    Sin unidades relacionadas
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- =================================================
             BLOQUE SIN UNIDAD / OFICINA
        ================================================== -->

        <div class="modal-felicitacion-unidades-editar__sin-unidad" id="editar-felicitacion-sin-unidad-contenido"
            hidden>

            <strong>
                Sin unidad / Oficina
            </strong>

            <p>
                El personal relacionado con la felicitación no cuenta con una unidad vehicular asignada.
            </p>

        </div>


        <!-- =================================================
             INPUTS DINÁMICOS
        ================================================== -->

        <div id="editar-felicitacion-unidades-inputs"></div>

    </div>

</section>