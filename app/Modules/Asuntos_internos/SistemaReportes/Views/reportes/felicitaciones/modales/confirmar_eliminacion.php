<!-- =========================================================
     MODAL: CONFIRMAR ELIMINACIÓN DE FELICITACIÓN
     ADMINISTRADOR
========================================================= -->

<div class="modal-reporte" id="modal-confirmar-eliminacion-felicitacion" aria-hidden="true">

    <div class="modal-reporte__overlay" data-cerrar-modal-confirmar-eliminacion-felicitacion></div>


    <div class="modal-reporte__dialog modal-reporte__dialog--confirmacion" role="dialog" aria-modal="true"
        aria-labelledby="titulo-confirmar-eliminacion-felicitacion">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Confirmación
                </span>


                <h2 class="modal-reporte__title" id="titulo-confirmar-eliminacion-felicitacion">
                    Eliminar felicitación
                </h2>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal-confirmar-eliminacion-felicitacion
                aria-label="Cerrar">
                &times;
            </button>

        </div>


        <!-- =====================================================
             FORMULARIO
        ====================================================== -->

        <form id="form-confirmar-eliminacion-felicitacion" novalidate>

            <?= csrf_field() ?>


            <div class="modal-reporte__body">

                <div class="
                        modal-reporte__alert
                        modal-reporte__alert--danger
                    ">

                    <div class="modal-reporte__alert-content">

                        <strong>
                            ¿Deseas eliminar esta felicitación?
                        </strong>


                        <p>

                            La felicitación

                            <strong id="confirmar-eliminacion-felicitacion-folio">
                                —
                            </strong>

                            será eliminada del listado.

                        </p>


                        <p>
                            Esta acción quedará registrada en el historial
                            del sistema.
                        </p>

                    </div>

                </div>

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
    </div>

    </form>

</div>

</div>