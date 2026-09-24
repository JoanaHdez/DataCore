<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Dashboard | Asuntos Internos
<?= $this->endSection() ?>


<?= $this->section('content') ?>

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

                 IZQUIERDA:
                 filtros

                 DERECHA:
                 indicadores + gráficas
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


                    <!-- =============================================
                         INDICADORES
                    ============================================== -->

                    <?= $this->include(
                        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\indicadores'
                    ) ?>


                    <!-- =============================================
                         COMPOSICIÓN DE GRÁFICAS
                    ============================================== -->

                    <div class="dashboard-layout">


                        <!-- =========================================
                             PRINCIPAL
                        ========================================== -->

                        <div class="dashboard-layout__principal">

                            <?= $this->include(
                                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\areas'
                            ) ?>

                        </div>


                        <!-- =========================================
                             ÁREAS INVOLUCRADAS
                        ========================================== -->

                        <div class="dashboard-layout__areas">

                            <?= $this->include(
                                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\areas_involucradas'
                            ) ?>

                        </div>


                        <!-- =========================================
                             FILA
                             ZONAS + SANCIONES
                        ========================================== -->

                        <div class="
                                dashboard-layout__fila
                                dashboard-layout__fila--zonas
                            ">

                            <?php
                            /*
                            <div class="dashboard-layout__zona">

                                <?= $this->include(
                                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\zonas'
                                ) ?>

                        </div>
                        */
                        ?>


                        <div class="dashboard-layout__sanciones">

                            <?= $this->include(
                                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\sanciones'
                                ) ?>

                        </div>

                    </div>


                    <!-- =========================================
                             FILA
                             TURNOS
                        ========================================== -->

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

<?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\modales\autorizacion'
    ) ?>

</div>

<?= $this->endSection() ?>