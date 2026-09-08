<form id="form-nueva-felicitacion" class="form-nuevo-reporte" method="post" action="#">

    <?= csrf_field() ?>


    <!-- =========================================================
     INDICADOR DE PASOS
    ========================================================== -->

    <div class="report-steps">

        <div class="report-steps__item report-steps__item--active" data-felicitacion-step-indicator="1">
            <span>1</span>

            <strong>
                Datos de la felicitación
            </strong>
        </div>


        <div class="report-steps__item" data-felicitacion-step-indicator="2">
            <span>2</span>

            <strong>
                Personal y unidad
            </strong>
        </div>

    </div>


    <!-- =========================================================
         PASO 1
         DATOS DE LA FELICITACIÓN
    ========================================================== -->

    <div class="felicitacion-paso" data-felicitacion-paso="1">

        <?= $this->include(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\formulario\sections\datos'
        ) ?>


        <!-- =====================================================
            ACCIONES PASO 1
        ====================================================== -->

        <div class="report-step-actions">

            <button type="button" class="button button--secondary" id="btn-limpiar-felicitacion"
                data-limpiar-felicitacion-paso="1">
                Limpiar sección
            </button>


            <div class="report-step-actions__right">

                <button type="button" class="button button--primary" id="btn-siguiente-felicitacion">
                    Siguiente
                </button>

            </div>

        </div>

    </div>


    <!-- =========================================================
         PASO 2
         PERSONAL Y UNIDAD
    ========================================================== -->

    <div class="felicitacion-paso" data-felicitacion-paso="2" hidden>

        <!-- =====================================================
             PERSONAL FELICITADO
        ====================================================== -->

        <?= $this->include(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\formulario\sections\personal'
        ) ?>


        <!-- =====================================================
             UNIDADES RELACIONADAS
        ====================================================== -->

        <?= $this->include(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\formulario\sections\unidades'
        ) ?>


        <!-- =====================================================
            ACCIONES PASO 2
        ====================================================== -->

        <div class="report-step-actions">

            <button type="button" class="button button--secondary" id="btn-anterior-felicitacion">
                Anterior
            </button>


            <div class="report-step-actions__right">

                <button type="button" class="button button--secondary" id="btn-limpiar-felicitacion-paso-2"
                    data-limpiar-felicitacion-paso="2">
                    Limpiar sección
                </button>


                <button type="submit" class="button button--primary" id="btn-guardar-felicitacion">
                    Guardar felicitación
                </button>

            </div>

        </div>
    </div>

</form>