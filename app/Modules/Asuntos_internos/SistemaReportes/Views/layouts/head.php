<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $this->renderSection('title') ?: 'Sistema de Reportes | Asuntos Internos' ?>
    </title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/asuntos_internos/sistema_reportes/img/logo.png'
                                ) ?>" alt="Coordinación de Asuntos Internos">

    <link rel="stylesheet" href="<?= base_url(
                                        'assets/asuntos_internos/sistema_reportes/css/app.css'
                                    ) ?>">

</head>

<body>

    <?= $this->renderSection('content') ?>

    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\components\resultado'
    ) ?>

    
    <?= $this->include(
        'App\Modules\Asuntos_internos\SistemaReportes\Views\reportes\components\modal-confirmar'
    ) ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script type="module" src="<?= base_url(
                                    'assets/asuntos_internos/sistema_reportes/js/main.js'
                                ) ?>"></script>

</body>

</html>