<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Nuevo registro | Asuntos Internos
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="nuevo-reporte">

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>


    <!-- =====================================================
        SELECTOR INICIAL DE TIPO DE REGISTRO
    ====================================================== -->

    <div class="tipo-registro-modal" id="modal-tipo-registro" aria-hidden="false">

        <div class="tipo-registro-modal__overlay"></div>


        <div class="tipo-registro-modal__contenido" role="dialog" aria-modal="true"
            aria-labelledby="titulo-tipo-registro">

            <div class="tipo-registro-modal__header">

                <span class="tipo-registro-modal__eyebrow">
                    Nuevo registro
                </span>

                <h2 class="tipo-registro-modal__titulo" id="titulo-tipo-registro">
                    Selecciona el tipo de registro
                </h2>

                <p class="tipo-registro-modal__descripcion">
                    Elige el tipo de registro que deseas capturar.
                </p>

            </div>


            <div class="tipo-registro-modal__opciones">

                <!-- QUEJA -->
                <button type="button" class="tipo-registro-card" data-tipo-registro="QUEJA">

                    <div class="tipo-registro-card__icono">
                        QJ
                    </div>

                    <div class="tipo-registro-card__contenido">

                        <strong>
                            Queja
                        </strong>

                        <span>
                            Registrar una nueva queja y su seguimiento.
                        </span>

                    </div>

                </button>


                <!-- FELICITACIÓN -->
                <button type="button" class="tipo-registro-card" data-tipo-registro="FELICITACION">

                    <div class="tipo-registro-card__icono">
                        FEL
                    </div>

                    <div class="tipo-registro-card__contenido">

                        <strong>
                            Felicitación
                        </strong>

                        <span>
                            Registrar una felicitación al personal.
                        </span>

                    </div>

                </button>

            </div>

        </div>

    </div>


    <!-- =====================================================
        CONTENEDOR QUEJA
    ====================================================== -->

    <div id="contenedor-registro-queja" class="registro-tipo-contenedor" hidden>

        <form id="form-nuevo-reporte" class="form-nuevo-reporte" method="post" action="#" enctype="multipart/form-data">

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
            ====================================================== -->

            <div class="report-step" data-step="3">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\personal_involucrado'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\unidad'
                ) ?>

            </div>


            <!-- =====================================================
                PASO 4
            ====================================================== -->

            <div class="report-step" data-step="4">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_quejoso'
                ) ?>

            </div>


            <!-- =====================================================
                PASO 5
            ====================================================== -->

            <div class="report-step" data-step="5">

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\clasificacion_seguimiento'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\evidencia'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\observaciones'
                ) ?>

            </div>


            <!-- =====================================================
                NAVEGACIÓN
            ====================================================== -->

            <div class="report-step-actions">

                <button type="button" class="button button--secondary report-step-control--hidden"
                    id="btn-step-anterior">
                    Anterior
                </button>


                <div class="report-step-actions__right">

                    <button type="button" class="button button--secondary" id="btn-limpiar-reporte">
                        Limpiar sección
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


    <!-- =====================================================
        CONTENEDOR FELICITACIÓN
        LO CONSTRUIREMOS DESPUÉS
    ====================================================== -->

    <div id="contenedor-registro-felicitacion" class="registro-tipo-contenedor" hidden>

        <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\formulario'
    ) ?>

    </div>

</div>

<?= $this->endSection() ?>