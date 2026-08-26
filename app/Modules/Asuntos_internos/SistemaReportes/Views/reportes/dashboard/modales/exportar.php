<div
    class="modal-reporte"
    id="modal-exportar-dashboard"
    aria-hidden="true"
>

    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-exportar
    ></div>

    <div
        class="modal-reporte__dialog modal-reporte__dialog--exportar"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-exportar-dashboard-titulo"
    >

        <!-- HEADER -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Exportación de información
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-exportar-dashboard-titulo"
                >
                    Exportar a Excel
                </h2>

            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-exportar
                aria-label="Cerrar"
            >
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
                            Elige los bloques del Dashboard que deseas incluir
                            en el archivo de Excel.
                        </p>

                    </div>


                    <!-- SELECCIONAR TODO -->
                    <label
                        class="
                            dashboard-exportar__opcion
                            dashboard-exportar__opcion--principal
                        "
                    >

                        <input
                            type="checkbox"
                            id="exportar-seleccionar-todo"
                        >

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

                        <label class="dashboard-exportar__opcion">

                            <input
                                type="checkbox"
                                name="secciones[]"
                                value="indicadores"
                                checked
                            >

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


                        <label class="dashboard-exportar__opcion">

                            <input
                                type="checkbox"
                                name="secciones[]"
                                value="clasificaciones"
                                checked
                            >

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Reportes por clasificación
                                </strong>

                                <small>
                                    Datos utilizados en la gráfica de clasificación.
                                </small>

                            </span>

                        </label>


                        <label class="dashboard-exportar__opcion">

                            <input
                                type="checkbox"
                                name="secciones[]"
                                value="areas"
                                checked
                            >

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Reportes por área
                                </strong>

                                <small>
                                    Distribución de reportes entre las áreas.
                                </small>

                            </span>

                        </label>


                        <label class="dashboard-exportar__opcion">

                            <input
                                type="checkbox"
                                name="secciones[]"
                                value="turnos"
                                checked
                            >

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Reportes por turno
                                </strong>

                                <small>
                                    Cantidad de reportes correspondiente a cada turno.
                                </small>

                            </span>

                        </label>


                        <label class="dashboard-exportar__opcion">

                            <input
                                type="checkbox"
                                name="secciones[]"
                                value="tendencia"
                                checked
                            >

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Tendencia de reportes
                                </strong>

                                <small>
                                    Evolución de reportes durante el periodo consultado.
                                </small>

                            </span>

                        </label>


                        <label class="dashboard-exportar__opcion">

                            <input
                                type="checkbox"
                                name="secciones[]"
                                value="recientes"
                                checked
                            >

                            <span class="dashboard-exportar__check"></span>

                            <span class="dashboard-exportar__contenido">

                                <strong>
                                    Reportes recientes
                                </strong>

                                <small>
                                    Información de los últimos reportes registrados.
                                </small>

                            </span>

                        </label>

                    </div>


                    <!-- MENSAJE -->
                    <div
                        class="dashboard-exportar__mensaje"
                        id="exportar-dashboard-mensaje"
                        hidden
                    >
                        Selecciona al menos una sección para continuar.
                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-reporte__footer">

                <button
                    type="button"
                    class="
                        modal-reporte__button
                        modal-reporte__button--secondary
                    "
                    data-cerrar-modal-exportar
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="
                        modal-reporte__button
                        modal-reporte__button--primary
                    "
                    id="btn-generar-excel"
                >
                    Generar Excel
                </button>

            </div>

        </form>

    </div>

</div>