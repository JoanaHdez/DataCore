<div class="modal-reporte" id="modal-editar-reporte" aria-hidden="true">

    <!-- =====================================================
         OVERLAY
    ====================================================== -->
    <div class="modal-reporte__overlay" data-cerrar-modal-editar></div>


    <!-- =====================================================
         MODAL
    ====================================================== -->
    <div class="modal-reporte__dialog modal-reporte__dialog--editar" role="dialog" aria-modal="true"
        aria-labelledby="modal-editar-titulo">

        <!-- =================================================
             HEADER
        ================================================== -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Modificación del registro
                </span>

                <h2 class="modal-reporte__title" id="modal-editar-titulo">
                    Editar reporte
                </h2>


                <div class="detalle-reporte__meta">

                    <span>
                        Expediente:
                        <strong id="editar-meta-expediente">
                            —
                        </strong>
                    </span>

                    <span>
                        Estado:
                        <strong id="editar-meta-estado">
                            —
                        </strong>
                    </span>

                </div>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal-editar aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =================================================
             NAVEGACIÓN
        ================================================== -->
        <nav class="detalle-reporte-nav">

            <button type="button" class="detalle-reporte-nav__item detalle-reporte-nav__item--active"
                data-editar-seccion="datos">
                Datos del reporte
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-editar-seccion="hechos">
                Datos de los hechos
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-editar-seccion="personal">
                Personal y unidades
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-editar-seccion="quejoso">
                Datos del quejoso
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-editar-seccion="clasificacion">
                Clasificación y seguimiento
            </button>

        </nav>


        <!-- =================================================
             FORMULARIO
        ================================================== -->
        <form class="modal-reporte__form modal-reporte__form--editar" id="form-editar-reporte"
            enctype="multipart/form-data" novalidate>

            <?= csrf_field() ?>

            <div class="modal-reporte__body modal-reporte__body--editar">


                <!-- =========================================
                     PASO 1
                     DATOS DEL REPORTE
                ========================================== -->
                <section class="detalle-reporte-seccion detalle-reporte-seccion--active" data-editar-panel="datos">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\datos_registro'
                    ) ?>

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\identificacion'
                    ) ?>

                </section>


                <!-- =========================================
     PASO 2
     DATOS DE LOS HECHOS
========================================== -->
                <section class="detalle-reporte-seccion" data-editar-panel="hechos">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\datos_hechos'
                    ) ?>

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\ubicacion'
                    ) ?>

                </section>


                <!-- =========================================
                     PASO 3
                     PERSONAL Y UNIDADES
                ========================================== -->
                <section class="detalle-reporte-seccion" data-editar-panel="personal">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\personal'
                    ) ?>

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\unidad'
                    ) ?>

                </section>


                <!-- =========================================
                     PASO 4
                     DATOS DEL QUEJOSO
                ========================================== -->

                <section class="detalle-reporte-seccion" data-editar-panel="quejoso">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\datos_quejoso'
                    ) ?>

                </section>


                <!-- =========================================
                     PASO 5
                     CLASIFICACIÓN Y SEGUIMIENTO
                ========================================== -->
                <section class="detalle-reporte-seccion" data-editar-panel="clasificacion">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\clasificacion'
                    ) ?>

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\evidencia'
                    ) ?>

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\editar\sections\observaciones'
                    ) ?>

                </section>
            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->
            <div class="modal-reporte__footer">

                <button type="button" class="modal-reporte__button modal-reporte__button--secondary"
                    data-cerrar-modal-editar>
                    Cancelar
                </button>

                <button type="submit" class="modal-reporte__button modal-reporte__button--primary">
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</div>