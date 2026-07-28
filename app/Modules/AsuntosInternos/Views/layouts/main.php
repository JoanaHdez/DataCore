<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $this->renderSection('title') ?: 'Asuntos Internos' ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/asuntos_internos/css/main.css') ?>">
</head>

<body>

    <header class="topbar">
        <div class="topbar__brand">
            <span class="topbar__logo">DC</span>

            <div>
                <strong>Asuntos Internos</strong>
                <small>DataCore</small>
            </div>
        </div>

        <nav class="topbar__nav">
            <a
                href="<?= base_url('asuntos-internos') ?>"
                class="<?= url_is('asuntos-internos') ? 'active' : '' ?>"
            >
                Inicio
            </a>

            <a
                href="<?= base_url('asuntos-internos/archivos') ?>"
                class="<?= url_is('asuntos-internos/archivos*') ? 'active' : '' ?>"
            >
                Historial
            </a>
        </nav>
    </header>

    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

</body>

</html>