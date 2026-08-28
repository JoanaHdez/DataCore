<div
    class="modal-reporte"
    id="modal-eliminar-reporte"
    aria-hidden="true"
>

    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-eliminar
    ></div>


    <div
        class="modal-reporte__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-eliminar-titulo"
    >

        <!-- =====================================================
             HEADER
        ====================================================== -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Eliminación de registro
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-eliminar-titulo"
                >
                    Eliminar reporte
                </h2>

            </div>


            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-eliminar
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
            id="form-eliminar-reporte"
            novalidate
        >

            <div class="modal-reporte__body">

                <!-- =================================================
                     INFORMACIÓN DEL REPORTE
                ================================================== -->
                <div class="eliminar-reporte__info">

                    <span>
                        Reporte seleccionado
                    </span>

                    <strong id="eliminar-reporte-folio">
                        —
                    </strong>

                </div>


                <!-- =================================================
                     ADVERTENCIA
                ================================================== -->
                <div class="eliminar-reporte__advertencia">

                    <strong>
                        Esta acción requiere autorización.
                    </strong>

                    <p>
                        El registro será eliminado únicamente si el usuario
                        cuenta con los permisos correspondientes y la contraseña
                        ingresada es correcta.
                    </p>

                </div>


                <!-- =================================================
                     CONTRASEÑA
                ================================================== -->
                <div class="editar-reporte-campo">

                    <label for="eliminar-reporte-password">
                        Contraseña
                        <span class="required">*</span>
                    </label>

                    <input
                        type="password"
                        id="eliminar-reporte-password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    >

                </div>


                <!-- =================================================
                     MENSAJE
                ================================================== -->
                <div
                    class="eliminar-reporte__mensaje"
                    id="eliminar-reporte-mensaje"
                    hidden
                ></div>

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
                    data-cerrar-modal-eliminar
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="
                        modal-reporte__button
                        modal-reporte__button--danger
                    "
                    id="btn-confirmar-eliminar-reporte"
                >
                    Eliminar reporte
                </button>

            </div>

        </form>

    </div>

</div>