<div
    class="modal-reporte"
    id="modal-seguimiento-reporte"
    aria-hidden="true"
>

    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-seguimiento
    ></div>

    <div
        class="modal-reporte__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-seguimiento-titulo"
    >

        <!-- =====================================================
             HEADER
        ====================================================== -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Seguimiento del caso
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-seguimiento-titulo"
                >
                    Seguimiento
                </h2>

            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-seguimiento
                aria-label="Cerrar"
            >
                ×
            </button>

        </div>


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->
        <form
            class="modal-reporte__form"
            id="form-seguimiento-reporte"
        >

            <div class="modal-reporte__body">

                <!-- =================================================
                     INFORMACIÓN DEL REPORTE
                ================================================== -->
                <div class="seguimiento-reporte__info">

                    <div class="seguimiento-reporte__dato">

                        <span>
                            Folio
                        </span>

                        <strong id="seguimiento-folio">
                            —
                        </strong>

                    </div>


                    <div class="seguimiento-reporte__dato">

                        <span>
                            Expediente
                        </span>

                        <strong id="seguimiento-expediente">
                            —
                        </strong>

                    </div>


                    <div class="seguimiento-reporte__dato">

                        <span>
                            Estado actual
                        </span>

                        <strong id="seguimiento-estado-actual">
                            —
                        </strong>

                    </div>

                </div>


                <!-- =================================================
                     NUEVO SEGUIMIENTO
                ================================================== -->
                <div class="seguimiento-reporte__section">

                    <div class="seguimiento-reporte__section-header">

                        <span>
                            Nuevo movimiento
                        </span>

                        <h3>
                            Registrar seguimiento
                        </h3>

                    </div>


                    <div class="seguimiento-reporte-grid">

                        <!-- FECHA -->
                        <div class="editar-reporte-campo">

                            <label for="seguimiento-fecha">
                                Fecha
                            </label>

                            <input
                                type="date"
                                id="seguimiento-fecha"
                                name="fecha"
                                required
                            >

                        </div>


                        <!-- TIPO DE SEGUIMIENTO -->
                        <div class="editar-reporte-campo">

                            <label for="seguimiento-tipo">
                                Tipo de seguimiento
                            </label>

                            <select
                                id="seguimiento-tipo"
                                name="tipo"
                                required
                            >

                                <option value="">
                                    Selecciona
                                </option>

                                <option value="Actualización">
                                    Actualización
                                </option>

                                <option value="Investigación">
                                    Investigación
                                </option>

                                <option value="Turnado">
                                    Turnado
                                </option>

                                <option value="Resolución">
                                    Resolución
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                        </div>


                        <!-- ESTADO RESULTANTE -->
                        <div
                            class="editar-reporte-campo editar-reporte-campo--full"
                        >

                            <label for="seguimiento-estado">
                                Estado resultante
                            </label>

                            <select
                                id="seguimiento-estado"
                                name="estado"
                                required
                            >

                                <option value="">
                                    Selecciona
                                </option>

                                <option value="Pendiente">
                                    Pendiente
                                </option>

                                <option value="En proceso">
                                    En proceso
                                </option>

                                <option value="Finalizado">
                                    Finalizado
                                </option>

                            </select>

                        </div>


                        <!-- OBSERVACIONES -->
                        <div
                            class="editar-reporte-campo editar-reporte-campo--full"
                        >

                            <label for="seguimiento-observaciones">
                                Observaciones
                            </label>

                            <textarea
                                id="seguimiento-observaciones"
                                name="observaciones"
                                class="seguimiento-reporte__textarea"
                                rows="5"
                                placeholder="Describe el seguimiento realizado..."
                                required
                            ></textarea>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     HISTORIAL DE SEGUIMIENTOS
                ================================================== -->
                <div class="seguimiento-historial">

                    <div class="seguimiento-historial__header">

                        <span>
                            Historial
                        </span>

                        <h3>
                            Seguimientos registrados
                        </h3>

                        <p>
                            Consulta los movimientos realizados sobre este reporte.
                        </p>

                    </div>


                    <div
                        class="seguimiento-historial__lista"
                        id="seguimiento-historial-lista"
                    >

                        <div class="seguimiento-historial__vacio">

                            <strong>
                                Sin seguimientos registrados
                            </strong>

                            <span>
                                Los movimientos del reporte aparecerán aquí.
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 FOOTER
            ====================================================== -->
            <div class="modal-reporte__footer">

                <button
                    type="button"
                    class="
                        modal-reporte__button
                        modal-reporte__button--secondary
                    "
                    data-cerrar-modal-seguimiento
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="
                        modal-reporte__button
                        modal-reporte__button--primary
                    "
                >
                    Registrar seguimiento
                </button>

            </div>

        </form>

    </div>

</div>