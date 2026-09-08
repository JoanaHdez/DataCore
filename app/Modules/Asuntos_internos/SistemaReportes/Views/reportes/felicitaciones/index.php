<?= $this->extend(
    'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
) ?>

<?= $this->section('title') ?>
Felicitaciones | Asuntos Internos
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<?php

$usuarioReportes =
    session()->get(
        'usuario_reportes'
    )
    ?? [];


$rolUsuario =
    $usuarioReportes['rol']
    ?? 'usuario';

?>


<div class="reportes-page felicitaciones-page" data-usuario-rol="<?= esc($rolUsuario) ?>">

    <!-- =====================================================
         ENCABEZADO GENERAL DEL SISTEMA
    ====================================================== -->

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
    ) ?>


    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <main class="reportes-page__main">

        <div class="reportes-page__container">


            <!-- =================================================
                 ENCABEZADO DE FELICITACIONES
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\sections\encabezado'
            ) ?>


            <!-- =================================================
                 PERIODO DE CONSULTA
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\sections\periodo'
            ) ?>


            <!-- =================================================
                 RESUMEN
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\sections\resumen'
            ) ?>


            <!-- =================================================
                 FILTROS
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\sections\filtros'
            ) ?>


            <!-- =================================================
                 TABLA
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\sections\tabla'
            ) ?>


            <!-- =================================================
                 PAGINACIÓN
            ================================================== -->

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\sections\paginacion'
            ) ?>


        </div>

    </main>


    <!-- =====================================================
         MODALES
    ====================================================== -->

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\detalle'
    ) ?>

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\felicitaciones\modales\editar'
    ) ?>

</div>


<?= $this->endSection() ?>