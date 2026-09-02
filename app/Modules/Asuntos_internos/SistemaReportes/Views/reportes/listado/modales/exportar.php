<div class="modal-reporte" id="modal-exportar-listado" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-exportar-listado></div>


    <div class="modal-reporte__dialog modal-reporte__dialog--exportar" role="dialog" aria-modal="true"
        aria-labelledby="modal-exportar-listado-titulo">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Exportación de información
                </span>

                <h2 class="modal-reporte__title" id="modal-exportar-listado-titulo">
                    Exportar listado a Excel
                </h2>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal-exportar-listado aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->

        <form id="form-exportar-listado">
            
            <?= csrf_field() ?>

            <div class="modal-reporte__body">

                <div class="dashboard-exportar">


                    <!-- =================================================
                         INTRODUCCIÓN
                    ================================================== -->

                    <div class="dashboard-exportar__introduccion">

                        <h3>
                            Selecciona la información
                        </h3>

                        <p>
                            Elige las secciones que deseas incluir
                            en el archivo de Excel.
                        </p>

                    </div>


                    <!-- =================================================
                         SELECCIONAR TODO
                    ================================================== -->

                    <label class="
                            dashboard-exportar__opcion
                            dashboard-exportar__opcion--principal
                        ">

                        <input type="checkbox" id="exportar-listado-seleccionar-todo" checked>

                        <span class="dashboard-exportar__check"></span>

                        <span class="dashboard-exportar__contenido">

                            <strong>
                                Seleccionar todo
                            </strong>

                            <small>
                                Incluir toda la información disponible.
                            </small>

                        </span>

                    </label>


                    <!-- =================================================
                         OPCIONES
                    ================================================== -->

                    <div class="dashboard-exportar__opciones">


                        <!-- DATOS DEL REPORTE -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="datos_reporte" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Datos del reporte
                                </strong>

                                <small>
                                    Folio y fecha de registro.
                                </small>

                            </span>

                        </label>


                        <!-- IDENTIFICACIÓN -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="identificacion" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Datos de identificación
                                </strong>

                                <small>
                                    Folio IP, fechas, expediente, nomenclatura
                                    y número de oficio.
                                </small>

                            </span>

                        </label>


                        <!-- HECHOS -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="hechos" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Datos de los hechos
                                </strong>

                                <small>
                                    Fecha, hora y descripción de los hechos.
                                </small>

                            </span>

                        </label>


                        <!-- UBICACIÓN -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="ubicacion" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Ubicación
                                </strong>

                                <small>
                                    Domicilio, sector, cuadrante,
                                    ID de cuadra y coordenadas.
                                </small>

                            </span>

                        </label>


                        <!-- PERSONAL -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="personal" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Personal involucrado
                                </strong>

                                <small>
                                    Oficiales, áreas y turnos relacionados.
                                </small>

                            </span>

                        </label>


                        <!-- UNIDADES -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="unidades" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Unidades
                                </strong>

                                <small>
                                    Número económico, placas y características
                                    de las unidades relacionadas.
                                </small>

                            </span>

                        </label>


                        <!-- QUEJOSO -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="quejoso" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Datos del quejoso
                                </strong>

                                <small>
                                    Nombre, edad, género y datos de contacto.
                                </small>

                            </span>

                        </label>


                        <!-- CLASIFICACIÓN -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="clasificacion" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Clasificación y resolución
                                </strong>

                                <small>
                                    Clasificación, responsables, resolución
                                    y motivos.
                                </small>

                            </span>

                        </label>


                        <!-- OBSERVACIONES -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="observaciones" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Observaciones
                                </strong>

                                <small>
                                    Observaciones generales del reporte.
                                </small>

                            </span>

                        </label>


                        <!-- SEGUIMIENTOS -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="seguimientos" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Seguimientos
                                </strong>

                                <small>
                                    Generar una hoja adicional con todos
                                    los seguimientos registrados.
                                </small>

                            </span>

                        </label>


                        <!-- EVIDENCIAS -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="evidencias" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Evidencias
                                </strong>

                                <small>
                                    Generar una hoja adicional con los archivos
                                    de evidencia relacionados.
                                </small>

                            </span>

                        </label>

                    </div>


                    <!-- =================================================
                         MENSAJE
                    ================================================== -->

                    <div class="dashboard-exportar__mensaje" id="exportar-listado-mensaje" hidden>
                        Selecciona al menos una sección para continuar.
                    </div>

                </div>

            </div>


            <!-- =====================================================
                 FOOTER
            ====================================================== -->

            <div class="modal-reporte__footer">

                <button type="button" class="
                        modal-reporte__button
                        modal-reporte__button--secondary
                    " data-cerrar-modal-exportar-listado>
                    Cancelar
                </button>


                <button type="submit" class="
                        modal-reporte__button
                        modal-reporte__button--primary
                    " id="btn-generar-excel-listado">
                    Generar Excel
                </button>

            </div>

        </form>

    </div>

</div>