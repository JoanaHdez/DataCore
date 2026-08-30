<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Dashboard | Asuntos Internos
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="dashboard-page">

    <!-- HEADER GENERAL DEL SISTEMA -->
    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>

    <main class="dashboard-page__main"
        data-dashboard-requiere-autorizacion="<?= !empty($requiereAutorizacionAdmin) ? '1' : '0' ?>">

        <div class="dashboard-page__container">

            <!-- ENCABEZADO DEL DASHBOARD -->
            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\encabezado'
            ) ?>


            <!-- FILTROS -->
            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\filtros'
            ) ?>


            <!-- INDICADORES -->
            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\indicadores'
            ) ?>


            <!-- =====================================================
     DASHBOARD - COMPOSICIÓN DE GRÁFICAS
===================================================== -->

<div class="dashboard-layout">


    <!-- =================================================
         PRINCIPAL
    ================================================== -->

    <div class="dashboard-layout__principal">

        <?= $this->include(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\areas'
        ) ?>

    </div>

<div class="dashboard-layout__areas">

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\areas_involucradas'
    ) ?>

</div>

    <!-- =================================================
         FILA 1
         ZONAS + SANCIONES
    ================================================== -->

    <div class="dashboard-layout__fila dashboard-layout__fila--zonas">

        <div class="dashboard-layout__zona">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\zonas'
            ) ?>

        </div>


        <div class="dashboard-layout__sanciones">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\sanciones'
            ) ?>

        </div>

    </div>


    <!-- =================================================
         FILA 2
         RESOLUCIONES + CATÁLOGO
    ================================================== -->

    <div class="dashboard-layout__fila dashboard-layout__fila--analisis">

        <div class="dashboard-layout__resoluciones">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\resoluciones'
            ) ?>

        </div>


        <div class="dashboard-layout__catalogo">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\catalogo'
            ) ?>

        </div>

    </div>


    <!-- =================================================
         FILA 3
         TURNOS + RECIENTES
    ================================================== -->

    <div class="dashboard-layout__fila dashboard-layout__fila--operativa">

        <div class="dashboard-layout__turnos">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\turnos'
            ) ?>

        </div>


        <div class="dashboard-layout__recientes">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\recientes'
            ) ?>

        </div>

    </div>


</div>

        </div>

    </main>

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\modales\exportar'
    ) ?>

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\modales\autorizacion'
    ) ?>

</div>

<?= $this->endSection() ?>