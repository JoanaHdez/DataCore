<div class="modal-felicitacion modal-felicitacion-editar" id="modal-editar-felicitacion" aria-hidden="true" hidden>

    <!-- =====================================================
         OVERLAY
    ====================================================== -->

    <div class="modal-felicitacion__overlay" data-cerrar-editar-felicitacion></div>


    <!-- =====================================================
         CONTENIDO
    ====================================================== -->

    <div class="modal-felicitacion__contenido modal-felicitacion-editar__contenido" role="dialog" aria-modal="true"
        aria-labelledby="titulo-editar-felicitacion">

        <!-- =================================================
             HEADER
        ================================================== -->

        <header class="modal-felicitacion-editar__header">

            <div class="modal-felicitacion-editar__header-info">

                <span class="modal-felicitacion-editar__eyebrow">
                    Modificación del registro
                </span>


                <h2 class="modal-felicitacion-editar__titulo" id="titulo-editar-felicitacion">
                    Editar felicitación
                    <span id="editar-felicitacion-titulo-folio"></span>
                </h2>

            </div>


            <button type="button" class="modal-felicitacion-editar__cerrar" data-cerrar-editar-felicitacion
                aria-label="Cerrar">
                ×
            </button>

        </header>


        <!-- =================================================
             FORMULARIO
        ================================================== -->

        <form id="form-editar-felicitacion" class="modal-felicitacion-editar__form" novalidate>

            <?= csrf_field() ?>


            <input type="hidden" id="editar-felicitacion-id" name="id_felicitacion">


            <!-- =================================================
                 NAVEGACIÓN
            ================================================== -->

            <nav class="modal-felicitacion-editar__nav" aria-label="Secciones de edición">

                <button type="button"
                    class="modal-felicitacion-editar__nav-btn modal-felicitacion-editar__nav-btn--activo"
                    data-seccion-editar-felicitacion="datos">
                    Datos de la felicitación
                </button>


                <button type="button" class="modal-felicitacion-editar__nav-btn"
                    data-seccion-editar-felicitacion="personal">
                    Personal y unidad
                </button>

            </nav>


            <!-- =================================================
                 CONTENIDO CON SCROLL
            ================================================== -->

            <div class="modal-felicitacion-editar__body">


                <!-- =============================================
                     SECCIÓN 1
                     DATOS DE LA FELICITACIÓN
                ============================================== -->

                <div class="modal-felicitacion-editar__panel modal-felicitacion-editar__panel--activo"
                    data-panel-editar-felicitacion="datos">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\editar\sections\datos_generales'
                    ) ?>


                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\editar\sections\informacion'
                    ) ?>

                </div>


                <!-- =============================================
                     SECCIÓN 2
                     PERSONAL Y UNIDAD
                ============================================== -->

                <div class="modal-felicitacion-editar__panel" data-panel-editar-felicitacion="personal" hidden>

                    <!-- =========================================
                        PERSONAL
                    ========================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\editar\sections\personal'
                    ) ?>


                    <!-- =========================================
                        UNIDADES
                    ========================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\editar\sections\unidades'
                    ) ?>

                </div>

            </div>


            <!-- =================================================
                 FOOTER FIJO
            ================================================== -->

            <footer class="modal-felicitacion-editar__footer">

                <button type="button" class="button button--secondary" data-cerrar-editar-felicitacion>
                    Cancelar
                </button>


                <button type="submit" class="button button--primary" id="btn-actualizar-felicitacion">
                    Guardar cambios
                </button>

            </footer>

        </form>

    </div>

</div>