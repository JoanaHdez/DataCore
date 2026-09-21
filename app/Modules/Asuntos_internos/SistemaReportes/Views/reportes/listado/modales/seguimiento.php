<div class="modal-reporte" id="modal-seguimiento-reporte" aria-hidden="true">

    <div
        class="modal-reporte__overlay"
        data-cerrar-modal-seguimiento
    ></div>


    <div
        class="modal-reporte__dialog modal-reporte__dialog--seguimiento"
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
             FORMULARIO PRINCIPAL DEL MODAL
        ====================================================== -->

        <form
            class="modal-reporte__form modal-reporte__form--seguimiento"
            id="form-seguimiento-reporte"
        >

            <?= csrf_field() ?>


            <!-- =================================================
                 CUERPO
            ================================================== -->

            <div class="modal-reporte__body modal-reporte__body--seguimiento">


                <!-- =============================================
                     INFORMACIÓN DEL REPORTE
                ============================================== -->

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\seguimiento\sections\informacion'
                ) ?>


                <!-- =============================================
                     FORMULARIO DE SEGUIMIENTO
                ============================================== -->

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\seguimiento\sections\formulario'
                ) ?>


                <!-- =============================================
                     HISTORIAL
                ============================================== -->

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\seguimiento\sections\historial'
                ) ?>


            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\seguimiento\sections\footer'
            ) ?>


        </form>

    </div>

</div>