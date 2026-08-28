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

    <main class="dashboard-page__main" data-dashboard-requiere-autorizacion="<?= !empty($requiereAutorizacionAdmin) ? '1' : '0' ?>">

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


            <!-- GRÁFICAS PRINCIPALES -->
            <div class="dashboard-graficas-grid">

                <!-- CLASIFICACIÓN -->
                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\clasificaciones'
                ) ?>


                <!-- ÁREAS -->
                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\areas'
                ) ?>


                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\turnos'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\tendencia'
                ) ?>

                <?= $this->include(
                    'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\dashboard\sections\recientes'
                ) ?>
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