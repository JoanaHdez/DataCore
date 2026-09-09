<!-- =========================================================
     MODAL: ELIMINAR FELICITACIÓN
     USUARIO NORMAL
========================================================= -->

<div class="modal-reporte" id="modal-eliminar-felicitacion" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-eliminar-felicitacion></div>


    <div class="modal-reporte__dialog" role="dialog" aria-modal="true"
        aria-labelledby="modal-eliminar-felicitacion-titulo">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Eliminación de registro
                </span>

                <h2 class="modal-reporte__title" id="modal-eliminar-felicitacion-titulo">
                    Eliminar felicitación
                </h2>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal-eliminar-felicitacion
                aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->

        <form class="modal-reporte__form" id="form-eliminar-felicitacion" novalidate>

            <?= csrf_field() ?>


            <div class="modal-reporte__body">


                <!-- =================================================
                     INFORMACIÓN
                ================================================== -->

                <div class="eliminar-reporte__info">

                    <span>
                        Felicitación seleccionada
                    </span>

                    <strong id="eliminar-felicitacion-folio">
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
                        La felicitación será eliminada únicamente si el usuario
                        cuenta con los permisos correspondientes y la contraseña
                        ingresada es correcta.
                    </p>

                </div>


                <!-- =================================================
                     CONTRASEÑA
                ================================================== -->

                <div class="editar-reporte-campo">

                    <label for="eliminar-felicitacion-password">

                        Contraseña del administrador

                        <span class="required">
                            *
                        </span>

                    </label>


                    <input type="password" id="eliminar-felicitacion-password" name="password"
                        autocomplete="current-password" placeholder="Ingresa la contraseña del administrador">

                </div>


                <!-- =================================================
                     MENSAJE
                ================================================== -->

                <div class="eliminar-reporte__mensaje" id="eliminar-felicitacion-mensaje" hidden></div>

            </div>


            <!-- =====================================================
                 FOOTER
            ====================================================== -->

            <div class="modal-reporte__footer">

                <button type="button" class="modal-reporte__button modal-reporte__button--secondary" data-cerrar-modal-confirmar-eliminacion-felicitacion>
                    Cancelar
                </button>


                <button type="submit" class="modal-reporte__button modal-reporte__button--danger" id="btn-confirmar-eliminacion-felicitacion-admin">
                    Eliminar felicitación
                </button>

            </div>

        </form>

    </div>

</div>