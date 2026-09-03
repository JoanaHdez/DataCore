<div class="modal-reporte" id="modal-exportar-dashboard" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-exportar></div>

    <div class="modal-reporte__dialog modal-reporte__dialog--exportar" role="dialog" aria-modal="true"
        aria-labelledby="modal-exportar-dashboard-titulo">

        <!-- HEADER -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Exportación de información
                </span>

                <h2 class="modal-reporte__title" id="modal-exportar-dashboard-titulo">
                    Exportar a Excel
                </h2>

            </div>

            <button type="button" class="modal-reporte__close" data-cerrar-modal-exportar aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- FORMULARIO -->
        <form id="form-exportar-dashboard">

            <div class="modal-reporte__body">

                <div class="dashboard-exportar">

                    <!-- INTRODUCCIÓN -->
                    <div class="dashboard-exportar__introduccion">

                        <h3>
                            Selecciona la información
                        </h3>

                        <p>
                            Elige las secciones del Dashboard que deseas incluir
                            en el archivo de Excel.
                        </p>

                    </div>


                    <!-- SELECCIONAR TODO -->
                    <label class="
                            dashboard-exportar__opcion
                            dashboard-exportar__opcion--principal
                        ">

                        <input type="checkbox" id="exportar-seleccionar-todo">

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


                    <!-- OPCIONES -->
                    <div class="dashboard-exportar__opciones">

                        <!-- INDICADORES -->
                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="indicadores" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Indicadores generales
                                </strong>

                                <small>
                                    Total, pendientes, en proceso y finalizados.
                                </small>

                            </span>

                        </label>


                        <!-- SECTORES Y TURNOS -->
                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="sectores_turnos" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Quejas por sectores y turnos
                                </strong>

                                <small>
                                    Distribución de quejas entre los 15 sectores
                                    y las categorías de turno.
                                </small>

                            </span>

                        </label>


                        <!-- ÁREAS -->
                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="areas" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Quejas por área
                                </strong>

                                <small>
                                    Distribución de reportes entre las áreas
                                    involucradas.
                                </small>

                            </span>

                        </label>


                        <!-- TURNOS -->
                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="turnos" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Quejas por turno
                                </strong>

                                <small>
                                    Cantidad de quejas correspondiente a cada
                                    categoría de turno.
                                </small>

                            </span>

                        </label>

                        <!-- SECTORES -->
                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="sectores" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Quejas por sector
                                </strong>

                                <small>
                                    Distribución de quejas entre los 15 sectores
                                    institucionales.
                                </small>

                            </span>

                        </label>


                        <!-- SANCIONES -->
                        <label class="dashboard-exportar__opcion">

                            <input type="checkbox" name="secciones[]" value="sanciones" checked>

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Sanciones disciplinarias
                                </strong>

                                <small>
                                    Distribución de las sanciones disciplinarias
                                    vigentes registradas en los reportes.
                                </small>

                            </span>

                        </label>

                    </div>


                    <!-- MENSAJE -->
                    <div class="dashboard-exportar__mensaje" id="exportar-dashboard-mensaje" hidden>
                        Selecciona al menos una sección para continuar.
                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-reporte__footer">

                <button type="button" class="
                        modal-reporte__button
                        modal-reporte__button--secondary
                    " data-cerrar-modal-exportar>
                    Cancelar
                </button>

                <button type="submit" class="
                        modal-reporte__button
                        modal-reporte__button--primary
                    " id="btn-generar-excel">
                    Generar Excel
                </button>

            </div>

        </form>

    </div>

</div>