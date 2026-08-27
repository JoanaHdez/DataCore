<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Nuevo reporte | Asuntos Internos
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="nuevo-reporte">

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>


    <form id="form-nuevo-reporte" class="form-nuevo-reporte" method="post" action="#">

        <?= csrf_field() ?>


        <!-- =====================================================
            INDICADOR DE PASOS
        ====================================================== -->
        <div class="report-steps">

            <div class="report-steps__item report-steps__item--active" data-step-indicator="1">
                <span>1</span>
                <strong>Datos del reporte</strong>
            </div>

            <div class="report-steps__item" data-step-indicator="2">
                <span>2</span>
                <strong>Datos de los hechos</strong>
            </div>

            <div class="report-steps__item" data-step-indicator="3">
                <span>3</span>
                <strong>Personal y unidades</strong>
            </div>

            <div class="report-steps__item" data-step-indicator="4">
                <span>4</span>
                <strong>Datos del quejoso</strong>
            </div>

            <div class="report-steps__item" data-step-indicator="5">
                <span>5</span>
                <strong>Clasificación y seguimiento</strong>
            </div>

        </div>


        <!-- =====================================================
            PASO 1
            DATOS DEL REPORTE + IDENTIFICACIÓN
        ====================================================== -->
        <div class="report-step report-step--active" data-step="1">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_registro'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\identificacion'
            ) ?>

        </div>


        <!-- =====================================================
            PASO 2
            DATOS DE LOS HECHOS + UBICACIÓN
        ====================================================== -->
        <div class="report-step" data-step="2">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_hechos'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\ubicacion'
            ) ?>

        </div>


        <!-- =====================================================
            PASO 3
            PERSONAL Y UNIDADES
        ====================================================== -->

        <div
            class="report-step"
            data-step="3">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\personal_involucrado'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\unidad'
            ) ?>

        </div>


        <!-- =====================================================
            PASO 4
            DATOS DEL QUEJOSO
        ====================================================== -->
        <div class="report-step" data-step="4">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_quejoso'
            ) ?>

        </div>


        <!-- =====================================================
            PASO 5
            CLASIFICACIÓN + OBSERVACIONES
        ====================================================== -->
        <div class="report-step" data-step="5">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\clasificacion_seguimiento'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\observaciones'
            ) ?>

        </div>


        <!-- =====================================================
            NAVEGACIÓN DEL FORMULARIO
        ====================================================== -->
        <div class="report-step-actions">

            <button type="button" class="button button--secondary report-step-control--hidden" id="btn-step-anterior">
                Anterior
            </button>


            <div class="report-step-actions__right">

                <button type="reset" class="button button--secondary" id="btn-limpiar-reporte">
                    Limpiar formulario
                </button>

                <button type="button" class="button button--primary" id="btn-step-siguiente">
                    Siguiente
                </button>

                <button type="submit" class="button button--primary report-step-control--hidden"
                    id="btn-guardar-reporte">
                    Guardar reporte
                </button>

            </div>

        </div>

    </form>

</div>

<?= $this->endSection() ?>