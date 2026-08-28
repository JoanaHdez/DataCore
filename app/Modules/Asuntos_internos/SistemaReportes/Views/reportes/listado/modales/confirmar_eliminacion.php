<!-- Modal: Confirmar eliminación de reporte -->
<div
    class="modal-reporte"
    id="modal-confirmar-eliminacion-reporte"
    aria-hidden="true"
>
    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-confirmar-eliminacion
    ></div>

    <div
        class="modal-reporte__dialog modal-reporte__dialog--confirmacion"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-confirmar-eliminacion"
    >
        <div class="modal-reporte__header">
            <div>
                <span class="modal-reporte__eyebrow">
                    Confirmación
                </span>

                <h2
                    class="modal-reporte__title"
                    id="titulo-confirmar-eliminacion"
                >
                    Eliminar reporte
                </h2>
            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-confirmar-eliminacion
                aria-label="Cerrar"
            >
                &times;
            </button>
        </div>

        <form id="form-confirmar-eliminacion-reporte" novalidate>

            <div class="modal-reporte__body">

                <div class="modal-reporte__alert modal-reporte__alert--danger">

                    <div class="modal-reporte__alert-content">

                        <strong>
                            ¿Deseas eliminar este reporte?
                        </strong>

                        <p>
                            El reporte
                            <strong id="confirmar-eliminacion-folio">
                                —
                            </strong>
                            será eliminado del listado.
                        </p>

                        <p>
                            Esta acción quedará registrada en el historial
                            del sistema.
                        </p>

                    </div>

                </div>

            </div>

            <div class="modal-reporte__footer">

                <button
                    type="button"
                    class="btn btn--secondary"
                    data-cerrar-modal-confirmar-eliminacion
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="btn btn--danger"
                    id="btn-confirmar-eliminacion-admin"
                >
                    Eliminar reporte
                </button>

            </div>

        </form>
    </div>
</div>