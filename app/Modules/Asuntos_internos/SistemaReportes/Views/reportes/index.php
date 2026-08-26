<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>


<?= $this->section('title') ?>

Reportes | Asuntos Internos

<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div class="reportes-page">

    <!-- ENCABEZADO DEL SISTEMA -->
    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>


    <!-- CONTENIDO -->
    <main class="reportes-page__main">

        <div class="reportes-page__container">

            <div class="reportes-page__heading">

                <div>

                    <span class="reportes-page__eyebrow">
                        Asuntos Internos
                    </span>

                    <h1 class="reportes-page__title">
                        Reportes
                    </h1>

                    <p class="reportes-page__description">
                        Consulta, administra y da seguimiento a los reportes registrados.
                    </p>

                </div>

            </div>

        </div>

    </main>

</div>

<?= $this->endSection() ?>