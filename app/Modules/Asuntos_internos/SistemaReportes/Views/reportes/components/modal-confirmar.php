<div class="modal-reporte" id="modal-confirmacion" aria-hidden="true">

    <!-- =====================================================
         OVERLAY
    ====================================================== -->

    <div class="modal-reporte__overlay" data-cerrar-modal-confirmacion></div>


    <!-- =====================================================
         DIALOG
    ====================================================== -->

    <div class="modal-reporte__dialog modal-reporte__dialog--confirmacion" role="dialog" aria-modal="true"
        aria-labelledby="modal-confirmacion-titulo">

        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Confirmación
                </span>

                <h2 class="modal-reporte__title" id="modal-confirmacion-titulo">
                    Confirmar acción
                </h2>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal-confirmacion aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =================================================
             BODY
        ================================================== -->

        <div class="modal-reporte__body">

            <div class="modal-confirmacion">

                <p class="modal-confirmacion__mensaje" id="modal-confirmacion-mensaje">
                    ¿Deseas continuar con esta acción?
                </p>

            </div>

        </div>


        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="modal-reporte__footer">

            <button type="button" class="
                    modal-reporte__button
                    modal-reporte__button--secondary
                " data-cerrar-modal-confirmacion>
                Cancelar
            </button>


            <button type="button" class="
                    modal-reporte__button
                    modal-reporte__button--primary
                " id="btn-confirmar-accion">
                Continuar
            </button>

        </div>

    </div>

</div>