<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Reportes | Asuntos Internos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="reportes-page">

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>

    <main class="reportes-page__main">

        <div class="reportes-page__container">

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\encabezado'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\periodo'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\resumen'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\filtros'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\tabla'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\paginacion'
            ) ?>

        </div>

    </main>

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\listado\modales'
    ) ?>

</div>

<?= $this->endSection() ?>