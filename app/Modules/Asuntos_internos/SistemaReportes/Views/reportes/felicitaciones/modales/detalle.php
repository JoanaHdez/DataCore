<div class="modal-felicitacion" id="modal-detalle-felicitacion" aria-hidden="true" hidden>

    <!-- =====================================================
         OVERLAY
    ====================================================== -->

    <div class="modal-felicitacion__overlay" data-cerrar-detalle-felicitacion></div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="modal-felicitacion__contenido" role="dialog" aria-modal="true"
        aria-labelledby="titulo-detalle-felicitacion">

        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="modal-felicitacion__header">

            <div>

                <span class="modal-felicitacion__eyebrow">
                    Detalle del registro
                </span>

                <h2 class="modal-felicitacion__titulo" id="titulo-detalle-felicitacion">
                    Felicitación

                    <span id="detalle-felicitacion-titulo-folio"></span>
                </h2>

            </div>


            <button type="button" class="modal-felicitacion__cerrar" data-cerrar-detalle-felicitacion
                aria-label="Cerrar">
                ×
            </button>

        </div>


        <!-- =================================================
             NAVEGACIÓN
        ================================================== -->

        <nav class="detalle-felicitacion-nav">

            <button type="button" class="detalle-felicitacion-nav__item detalle-felicitacion-nav__item--active"
                data-detalle-felicitacion-seccion="datos">
                Datos de la felicitación
            </button>


            <button type="button" class="detalle-felicitacion-nav__item" data-detalle-felicitacion-seccion="personal">
                Personal y unidad
            </button>

        </nav>


        <!-- =================================================
             BODY
        ================================================== -->

        <div class="modal-felicitacion__body">


            <!-- =============================================
                 PANEL 1
                 DATOS DE LA FELICITACIÓN
            ============================================== -->

            <section class="detalle-felicitacion-panel detalle-felicitacion-panel--active"
                data-detalle-felicitacion-panel="datos">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\detalle\sections\identificacion'
                ) ?>


                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\detalle\sections\informacion'
                ) ?>

            </section>


            <!-- =============================================
                 PANEL 2
                 PERSONAL Y UNIDAD
            ============================================== -->

            <section class="detalle-felicitacion-panel" data-detalle-felicitacion-panel="personal" hidden>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\detalle\sections\personal'
                ) ?>


                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\detalle\sections\unidades'
                ) ?>

            </section>

        </div>

    </div>

</div>