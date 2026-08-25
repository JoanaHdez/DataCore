<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= $this->renderSection('title') ?: 'Sistema de Reportes | Asuntos Internos' ?>
    </title>

    <link
        rel="icon"
        type="image/png"
        href="<?= base_url(
            'assets/asuntos_internos/sistema_reportes/img/favicon.png'
        ) ?>"
    >

    <link
        rel="stylesheet"
        href="<?= base_url(
            'assets/asuntos_internos/sistema_reportes/css/login.css'
        ) ?>"
    >

    <link
    rel="stylesheet"
    href="<?= base_url(
        'assets/asuntos_internos/sistema_reportes/css/reportes/header.css'
    ) ?>"
>

<link
    rel="stylesheet"
    href="<?= base_url(
        'assets/asuntos_internos/sistema_reportes/css/reportes/formulario.css'
    ) ?>"
>


</head>

<body>

    <?= $this->renderSection('content') ?>

    <script
        src="<?= base_url(
            'assets/asuntos_internos/sistema_reportes/js/login.js'
        ) ?>"
    ></script>

</body>

</html>