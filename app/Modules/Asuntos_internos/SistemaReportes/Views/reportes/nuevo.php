<!DOCTYPE html>
<html lang="es">

<head>
    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\layouts\head'
    ) ?>
</head>

<body>

    <main class="nuevo-reporte">

        <?= $this->include(
            'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\encabezado'
        ) ?>

        <form id="form-nuevo-reporte" class="form-nuevo-reporte" method="post" action="#">

            <?= csrf_field() ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_registro'
            ) ?>

        
            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\identificacion'
            ) ?>


            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_hechos'
            ) ?>


            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\personal_involucrado'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\datos_quejoso'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\clasificacion_seguimiento'
            ) ?>

            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\observaciones'
            ) ?>
            
            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\ubicacion'
            ) ?>


            <?= $this->include(
                'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\sections\acciones'
            ) ?>

        </form>

    </main>

</body>

</html>