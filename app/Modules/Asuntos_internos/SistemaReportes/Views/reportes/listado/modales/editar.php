<div
    class="modal-reporte"
    id="modal-editar-reporte"
    aria-hidden="true"
>

    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-editar
    ></div>

    <div
        class="modal-reporte__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-editar-titulo"
    >

        <!-- HEADER -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Modificación del registro
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-editar-titulo"
                >
                    Editar reporte
                </h2>

            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-editar
                aria-label="Cerrar"
            >
                ×
            </button>

        </div>


        <!-- FORMULARIO -->
        <form
            class="modal-reporte__form"
            id="form-editar-reporte"
        >

            <div class="modal-reporte__body">

                <div class="editar-reporte-grid">

                    <!-- FOLIO -->
                    <div class="editar-reporte-campo">

                        <label for="editar-folio">
                            Folio
                        </label>

                        <input
                            type="text"
                            id="editar-folio"
                            name="folio"
                            readonly
                        >

                    </div>


                    <!-- FECHA DE QUEJA -->
                    <div class="editar-reporte-campo">

                        <label for="editar-fecha-queja">
                            Fecha de queja
                        </label>

                        <input
                            type="date"
                            id="editar-fecha-queja"
                            name="fecha_queja"
                        >

                    </div>


                    <!-- EXPEDIENTE -->
                    <div class="editar-reporte-campo">

                        <label for="editar-expediente">
                            Expediente
                        </label>

                        <input
                            type="text"
                            id="editar-expediente"
                            name="expediente"
                        >

                    </div>


                    <!-- CLASIFICACIÓN -->
                    <div class="editar-reporte-campo">

                        <label for="editar-clasificacion">
                            Clasificación
                        </label>

                        <input
                            type="text"
                            id="editar-clasificacion"
                            name="clasificacion"
                        >

                    </div>


                    <!-- QUEJOSO -->
                    <div class="editar-reporte-campo editar-reporte-campo--full">

                        <label for="editar-quejoso">
                            Quejoso
                        </label>

                        <input
                            type="text"
                            id="editar-quejoso"
                            name="quejoso"
                        >

                    </div>


                    <!-- ÁREA -->
                    <div class="editar-reporte-campo">

                        <label for="editar-area">
                            Área
                        </label>

                        <input
                            type="text"
                            id="editar-area"
                            name="area"
                        >

                    </div>


                    <!-- TURNO -->
                    <div class="editar-reporte-campo">

                        <label for="editar-turno">
                            Turno
                        </label>

                        <select
                            id="editar-turno"
                            name="turno"
                        >
                            <option value="">
                                Selecciona
                            </option>

                            <option value="Primer turno">
                                Primer turno
                            </option>

                            <option value="Segundo turno">
                                Segundo turno
                            </option>

                            <option value="Tercer turno">
                                Tercer turno
                            </option>
                        </select>

                    </div>


                    <!-- RESOLUCIÓN -->
                    <div class="editar-reporte-campo editar-reporte-campo--full">

                        <label for="editar-resolucion">
                            Resolución
                        </label>

                        <select
                            id="editar-resolucion"
                            name="resolucion"
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

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-reporte__footer">

                <button
                    type="button"
                    class="modal-reporte__button modal-reporte__button--secondary"
                    data-cerrar-modal-editar
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="modal-reporte__button modal-reporte__button--primary"
                >
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>