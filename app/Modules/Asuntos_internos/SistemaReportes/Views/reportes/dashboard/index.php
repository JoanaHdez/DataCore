<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>


<?= $this->section('title') ?>

Dashboard | Asuntos Internos

<?= $this->endSection() ?>


<?= $this->section('content') ?>


<?php

$tipoDashboard =
    strtoupper(
        trim(
            (string) (
                $_GET['tipo']
                ?? ''
            )
        )
    );


$esFelicitacion =
    $tipoDashboard === 'FELICITACION';

?>


<div class="dashboard-page">


    <!-- =====================================================
         HEADER GENERAL DEL SISTEMA
    ====================================================== -->

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>


    <main class="dashboard-page__main"
        data-dashboard-requiere-autorizacion="<?= !empty($requiereAutorizacionAdmin) ? '1' : '0' ?>">

        <div class="dashboard-page__container">


            <!-- =================================================
                 ENCABEZADO DEL DASHBOARD
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\encabezado'
            ) ?>


            <!-- =================================================
                 LAYOUT GENERAL DEL DASHBOARD
            ================================================== -->

            <div class="dashboard-shell">


                <!-- =================================================
                     SIDEBAR DE FILTROS
                ================================================== -->

                <aside class="dashboard-shell__sidebar">

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\filtros'
                    ) ?>

                </aside>


                <!-- =================================================
                     CONTENIDO PRINCIPAL
                ================================================== -->

                <div class="dashboard-shell__contenido">


                    <!-- =================================================
                         1. RESUMEN GENERAL
                    ================================================== -->


                    <!-- =============================================
                         INDICADORES
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\indicadores'
                    ) ?>



                    <!-- =================================================
                         2. ANÁLISIS TEMPORAL
                    ================================================== -->


                    <!-- =============================================
                         EVOLUCIÓN TEMPORAL
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\evolucion'
                    ) ?>


                    <?php if (!$esFelicitacion): ?>


                    <!-- =============================================
                             COMPARATIVA TEMPORAL
                        ============================================== -->

                    <?= $this->include(
                            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\comparativa'
                        ) ?>


                    <?php endif; ?>



                    <!-- =================================================
                         3. SITUACIÓN ACTUAL
                    ================================================== -->


                    <?php if (!$esFelicitacion): ?>


                    <!-- =============================================
                             ESTADO DE LAS QUEJAS
                        ============================================== -->

                    <?= $this->include(
                            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\estado'
                        ) ?>


                    <?php endif; ?>



                    <!-- =================================================
                         4. DISTRIBUCIÓN TERRITORIAL Y OPERATIVA
                    ================================================== -->


                    <div class="dashboard-layout">


                        <!-- =============================================
                             ZONA + SANCIONES
                        ============================================== -->

                        <div class="
                                dashboard-layout__fila
                                dashboard-layout__fila--zonas
                            ">


                            <!-- =========================================
                                 ZONA
                            ========================================== -->

                            <div class="dashboard-layout__zona">

                                <?= $this->include(
                                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\zonas'
                                ) ?>

                            </div>


                            <?php if (!$esFelicitacion): ?>


                            <!-- =====================================
                                     SANCIONES DISCIPLINARIAS
                                ====================================== -->

                            <div class="dashboard-layout__sanciones">

                                <?= $this->include(
                                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\sanciones'
                                    ) ?>

                            </div>


                            <?php endif; ?>


                        </div>


                        <!-- =============================================
                             TURNO
                        ============================================== -->

                        <div class="
                                dashboard-layout__fila
                                dashboard-layout__fila--operativa
                            ">

                            <div class="dashboard-layout__turnos">

                                <?= $this->include(
                                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\turnos'
                                ) ?>

                            </div>

                        </div>


                    </div>



                    <!-- =============================================
                         SECTOR
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\sectores'
                    ) ?>



                    <!-- =================================================
                         5. DISTRIBUCIÓN INSTITUCIONAL
                    ================================================== -->


                    <!-- =============================================
                         ÁREA / UNIDAD
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\dimension'
                    ) ?>



                    <!-- =================================================
                         6. RELACIONES ENTRE DIMENSIONES
                    ================================================== -->


                    <!-- =============================================
                         ANÁLISIS CRUZADO
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\cruce'
                    ) ?>



                    <!-- =================================================
                         7. PRINCIPALES CONCENTRACIONES
                    ================================================== -->


                    <!-- =============================================
                         RANKING TOP 5
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\ranking'
                    ) ?>



                    <!-- =================================================
                         8. SÍNTESIS AUTOMÁTICA
                    ================================================== -->


                    <!-- =============================================
                         HALLAZGOS DEL PERIODO
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\hallazgos'
                    ) ?>



                    <!-- =================================================
                         9. ANÁLISIS INDIVIDUAL
                    ================================================== -->


                    <!-- =============================================
                         PERSONAL INDIVIDUAL
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\personal_individual'
                    ) ?>


                </div>

            </div>

        </div>

    </main>


    <!-- =====================================================
         MODALES
    ====================================================== -->

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\modales\exportar'
    ) ?>

    <!--
        GENERADOR DE ANALISIS DASHBOARD
        Modal tecnico preparado para IA + Word futuro. El acceso
        visual se controla desde sections/encabezado.php.
    -->
    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\modales\informe'
    ) ?>


    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\modales\historial'
    ) ?>


    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\modales\autorizacion'
    ) ?>


</div>


<?= $this->endSection() ?>
