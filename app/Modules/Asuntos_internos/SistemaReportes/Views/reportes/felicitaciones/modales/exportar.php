<div class="modal-reporte" id="modal-exportar-felicitaciones" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-exportar-felicitaciones></div>


    <div class="
            modal-reporte__dialog
            modal-reporte__dialog--exportar
        " role="dialog" aria-modal="true" aria-labelledby="modal-exportar-felicitaciones-titulo">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Exportación de información
                </span>

                <h2 class="modal-reporte__title" id="modal-exportar-felicitaciones-titulo">
                    Exportar felicitaciones a Excel
                </h2>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal-exportar-felicitaciones
                aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->

        <form id="form-exportar-felicitaciones">

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

                        <input type="checkbox" id="exportar-felicitaciones-seleccionar-todo" checked>

                        <span class="dashboard-exportar__check"></span>


                        <span class="dashboard-exportar__contenido">

                            <strong>
                                Seleccionar todo
                            </strong>

                            <small>
                                Incluir todas las secciones de la felicitación.
                            </small>

                        </span>

                    </label>


                    <!-- =================================================
                         OPCIONES
                    ================================================== -->

                    <div class="dashboard-exportar__opciones">


                        <!-- =============================================
                             IDENTIFICACIÓN
                        ============================================== -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="identificacion" checked>

                            <span class="dashboard-exportar__check"></span>


                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Identificación de la felicitación
                                </strong>

                                <small>
                                    Folio, fecha de registro y nomenclatura.
                                </small>

                            </span>

                        </label>


                        <!-- =============================================
                             DATOS DE LA FELICITACIÓN
                        ============================================== -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="datos_felicitacion" checked>

                            <span class="dashboard-exportar__check"></span>


                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Datos de la felicitación
                                </strong>

                                <small>
                                    Persona que felicita y razón de la felicitación.
                                </small>

                            </span>

                        </label>


                        <!-- =============================================
                             PERSONAL
                        ============================================== -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="personal" checked>

                            <span class="dashboard-exportar__check"></span>


                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Personal felicitado
                                </strong>

                                <small>
                                    Personal relacionado con la felicitación.
                                </small>

                            </span>

                        </label>


                        <!-- =============================================
                             UNIDADES
                        ============================================== -->

                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="unidades" checked>

                            <span class="dashboard-exportar__check"></span>


                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Unidades involucradas
                                </strong>

                                <small>
                                    Unidades relacionadas con la felicitación.
                                </small>

                            </span>

                        </label>

                    </div>


                    <!-- =================================================
                        CANTIDAD DE REGISTROS
                    ================================================== -->

                    <div class="dashboard-exportar__introduccion felicitaciones-exportar__cantidad-titulo">

                        <h3>
                            Cantidad de registros
                        </h3>

                        <p>
                            Indica cuántos registros deseas exportar.
                            Si dejas el campo vacío, se exportarán todos.
                        </p>

                    </div>


                    <div class="report-field">

                        <label for="exportar-felicitaciones-cantidad">
                            ¿Cuántos registros quieres exportar?
                        </label>

                        <input type="number" id="exportar-felicitaciones-cantidad" name="cantidad" class="report-input"
                            min="1" step="1" placeholder="Ej. 50" autocomplete="off">

                        <small class="report-field__help">
                            Déjalo vacío para exportar todos los registros disponibles.
                        </small>

                    </div>


                    <!-- =================================================
                         MENSAJE
                    ================================================== -->

                    <div class="dashboard-exportar__mensaje" id="exportar-felicitaciones-mensaje" hidden>
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
                    " data-cerrar-modal-exportar-felicitaciones>
                    Cancelar
                </button>


                <button type="submit" class="
                        modal-reporte__button
                        modal-reporte__button--primary
                    " id="btn-generar-excel-felicitaciones">
                    Generar Excel
                </button>

            </div>

        </form>

    </div>

</div>