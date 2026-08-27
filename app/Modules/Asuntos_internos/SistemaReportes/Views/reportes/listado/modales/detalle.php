<div class="modal-reporte" id="modal-detalle-reporte" aria-hidden="true">

    <!-- =====================================================
         OVERLAY
    ====================================================== -->
    <div class="modal-reporte__overlay" data-cerrar-modal></div>


    <!-- =====================================================
         MODAL
    ====================================================== -->
    <div class="modal-reporte__dialog modal-reporte__dialog--detalle" role="dialog" aria-modal="true"
        aria-labelledby="modal-detalle-titulo">

        <!-- =================================================
             HEADER
        ================================================== -->
        <div class="modal-reporte__header">

            <div>

                <span class="modal-reporte__eyebrow">
                    Detalle del registro
                </span>

                <h2 class="modal-reporte__title" id="modal-detalle-titulo">
                    Reporte
                </h2>


                <div class="detalle-reporte__meta">

                    <span>
                        Expediente:
                        <strong id="detalle-meta-expediente">
                            —
                        </strong>
                    </span>

                    <span>
                        Estado:
                        <strong id="detalle-meta-estado">
                            —
                        </strong>
                    </span>

                </div>

            </div>


            <button type="button" class="modal-reporte__close" data-cerrar-modal aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =================================================
             MENÚ
        ================================================== -->
        <nav class="detalle-reporte-nav">

            <button type="button" class="detalle-reporte-nav__item detalle-reporte-nav__item--active"
                data-detalle-seccion="datos">
                Datos del reporte
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-detalle-seccion="hechos">
                Datos de los hechos
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-detalle-seccion="personal">
                Personal y unidades
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-detalle-seccion="quejoso">
                Datos del quejoso
            </button>

            <button type="button" class="detalle-reporte-nav__item" data-detalle-seccion="clasificacion">
                Clasificación y seguimiento
            </button>

        </nav>


        <!-- =================================================
             BODY
        ================================================== -->
        <div class="modal-reporte__body modal-reporte__body--detalle">


            <!-- =============================================
                 PASO 1
                 DATOS DEL REPORTE
            ============================================== -->
            <section class="detalle-reporte-seccion detalle-reporte-seccion--active" data-detalle-panel="datos">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\datos_registro'
                ) ?>


                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\identificacion'
                ) ?>

            </section>


            <!-- =============================================
                 PASO 2
                 DATOS DE LOS HECHOS
                 Lo construiremos después
            ============================================== -->
            <section class="detalle-reporte-seccion" data-detalle-panel="hechos">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\datos_hechos'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\ubicacion'
                ) ?>
            </section>


            <!-- =============================================
                 PASO 3
                 PERSONAL Y UNIDADES
            ============================================== -->
            <section class="detalle-reporte-seccion" data-detalle-panel="personal">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\personal'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\unidad'
                ) ?>
            </section>

            <!-- =============================================
                 PASO 4
                 QUEJOSO
            ============================================== -->
            <section class="detalle-reporte-seccion" data-detalle-panel="quejoso">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\datos_quejoso'
                ) ?>

            </section>


            <!-- =============================================
                 PASO 5
                 CLASIFICACIÓN Y SEGUIMIENTO
            ============================================== -->
            <section class="detalle-reporte-seccion" data-detalle-panel="clasificacion">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\clasificacion'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\evidencia'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales\detalle\sections\observaciones'
                ) ?>
            </section>

        </div>

    </div>

</div>