<?php

$usuario =
    session()->get(
        'usuario_reportes'
    ) ?? [];

$nombreUsuario =
    $usuario['nombre']
    ?? 'Usuario';

$nominaUsuario =
    $usuario['nomina']
    ?? '000000';

$perscodUsuario =
    $usuario['perscod']
    ?? null;

$fotoUsuario = null;

if (!empty($perscodUsuario)) {

    $fotoService =
        new \App\Modules\Asuntos_internos\SistemaReportes\Services\FotoPersonalService();

    $fotoUsuario =
        $fotoService->obtenerBase64(
            $perscodUsuario
        );
}

?>

<header class="report-header">

    <div class="report-header__institution">

        <div class="report-header__logos">

            <img src="<?= base_url(
                            'assets/asuntos_internos/sistema_reportes/img/logo.png'
                        ) ?>" alt="Coordinación de Asuntos Internos">

        </div>

        <div class="report-header__title">

            <span>
                Coordinación de Asuntos Internos
            </span>

            <h1>
                Sistema de Reportes
            </h1>

        </div>

    </div>


    <div class="report-header__user">

        <div class="report-header__user-data">

            <span>
                Sesión activa
            </span>

            <strong>
                <?= esc($nombreUsuario) ?>
            </strong>

            <small>
                Nómina: <?= esc($nominaUsuario) ?>
            </small>

        </div>


        <div class="report-header__user-avatar">

            <?php if (!empty($fotoUsuario)): ?>

                <img
                    src="<?= esc($fotoUsuario) ?>"
                    alt="Foto de <?= esc($nombreUsuario) ?>">

            <?php else: ?>

                <span>
                    <?= esc(
                        strtoupper(
                            mb_substr(
                                $nombreUsuario,
                                0,
                                1
                            )
                        )
                    ) ?>
                </span>

            <?php endif; ?>

        </div>

    </div>

</header>


<nav class="report-nav">

    <a href="<?= base_url(
                    'asuntos-internos/reportes/nuevo'
                ) ?>" class="report-nav__link <?= url_is(
                                                    'asuntos-internos/reportes/nuevo'
                                                ) ? 'report-nav__link--active' : '' ?>">
        Nuevo reporte
    </a>

    <a href="<?= base_url(
                    'asuntos-internos/reportes/listado'
                ) ?>" class="report-nav__link <?= url_is(
                                                    'asuntos-internos/reportes/listado'
                                                ) ? 'report-nav__link--active' : '' ?>">
        Reportes
    </a>

    <a href="<?= base_url(
                    'asuntos-internos/reportes/dashboard'
                ) ?>" class="report-nav__link <?= url_is(
                                                    'asuntos-internos/reportes/dashboard'
                                                ) ? 'report-nav__link--active' : '' ?>">
        Dashboard
    </a>

    <a href="<?= base_url(
                    'asuntos-internos/reportes/logout'
                ) ?>" class="report-nav__logout">
        Cerrar sesión
    </a>

</nav>