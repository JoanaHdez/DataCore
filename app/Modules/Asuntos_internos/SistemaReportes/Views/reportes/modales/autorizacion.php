<!-- =========================================================
     MODAL - AUTORIZACIÓN ADMINISTRATIVA
========================================================= -->

<div
    class="modal-reporte"
    id="modal-autorizacion-admin"
    aria-hidden="true">
    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-autorizacion></div>

    <div
        class="modal-reporte__dialog modal-reporte__dialog--autorizacion"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-autorizacion-titulo">

        <!-- HEADER -->
        <div class="modal-reporte__header">

            <div>
                <span class="modal-reporte__eyebrow">
                    Autorización administrativa
                </span>

                <h2
                    class="modal-reporte__title"
                    id="modal-autorizacion-titulo">
                    Acceso restringido
                </h2>
            </div>

            <button
                type="button"
                class="modal-reporte__close"
                data-cerrar-modal-autorizacion
                aria-label="Cerrar">
                &times;
            </button>

        </div>


        <!-- FORMULARIO -->
        <form
            id="form-autorizacion-admin"
            class="modal-reporte__form"
            novalidate>

            <?= csrf_field() ?>

            <div class="modal-reporte__body">

                <!-- INFORMACIÓN -->
                <div class="autorizacion-admin__aviso">

                    <strong>
                        Esta sección requiere autorización
                    </strong>

                    <p id="autorizacion-admin-descripcion">
                        Para continuar, ingresa la contraseña
                        de un administrador.
                    </p>

                </div>


                <!-- CONTRASEÑA -->
                <div class="autorizacion-admin__campo">

                    <label
                        class="autorizacion-admin__label"
                        for="autorizacion-admin-password">
                        Contraseña del administrador
                    </label>

                    <input
                        type="password"
                        id="autorizacion-admin-password"
                        name="password_admin"
                        class="autorizacion-admin__input"
                        autocomplete="current-password"
                        placeholder="Ingresa la contraseña">

                </div>


                <!-- MENSAJE -->
                <div
                    class="autorizacion-admin__mensaje"
                    id="autorizacion-admin-mensaje"
                    role="alert"
                    hidden></div>

            </div>


            <!-- FOOTER -->
            <div class="modal-reporte__footer">

                <button
                    type="button"
                    class="btn btn--secondary"
                    data-cerrar-modal-autorizacion>
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="btn btn--primary"
                    id="btn-autorizar-acceso">
                    Autorizar acceso
                </button>

            </div>

        </form>

    </div>
</div>