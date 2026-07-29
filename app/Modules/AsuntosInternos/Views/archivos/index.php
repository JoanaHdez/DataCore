<?= $this->extend('App\Modules\AsuntosInternos\Views\layouts\main') ?>

<?= $this->section('title') ?>
Historial | Asuntos Internos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="page-header page-header--row">

    <div>
        <span class="page-header__label">
            Archivos almacenados
        </span>

        <h1>Historial de archivos</h1>

        <p>
            Consulta y descarga los archivos procesados anteriormente.
        </p>
    </div>

    <a href="<?= base_url('asuntos-internos') ?>" class="button button--primary">
        Subir archivo
    </a>

</section>

<section class="history-card">

    <div class="history-toolbar">

        <input type="search" class="search-input" id="buscar-archivo" placeholder="Buscar por nombre de archivo"
            autocomplete="off">

        <input type="date" class="date-input" id="buscar-fecha">

        <button type="button" class="button button--secondary" id="limpiar-filtros">
            Limpiar filtros
        </button>

    </div>

    <div class="table-container">

        <table class="history-table">

            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Fecha de carga</th>
                    <th>Tamaño</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($archivos)): ?>

                <tr>
                    <td colspan="5" class="table-empty">
                        No hay archivos almacenados.
                    </td>
                </tr>

                <?php else: ?>

                <?php foreach ($archivos as $archivo): ?>

                <tr data-file-row data-file-name="<?= esc(
                                                strtolower($archivo['nombre_original']),
                                                'attr'
                                            ) ?>" data-file-date="<?= esc(
                                                date(
                                                    'Y-m-d',
                                                    strtotime($archivo['fecha_procesamiento'])
                                                ),
                                                'attr'
                                            ) ?>">

                    <td>
                        <strong title="<?= esc(
                                                $archivo['nombre_original'],
                                                'attr'
                                            ) ?>">
                            <?= esc($archivo['nombre_original']) ?>
                        </strong>
                    </td>

                    <td>
                        <?= esc(
                                    date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $archivo['fecha_procesamiento']
                                        )
                                    )
                                ) ?>
                    </td>

                    <td>
                        <?= esc(
                                    number_format(
                                        ($archivo['tamano'] ?? 0) / 1024,
                                        2
                                    )
                                ) ?> KB
                    </td>

                    <td>
                        <span class="status status--success">
                            Completado
                        </span>
                    </td>

                    <td>
                        <div class="table-actions">

                            <a class="table-action" href="<?= base_url(
                                                    'asuntos-internos/archivos/descargar/'
                                                        . rawurlencode(
                                                            $archivo['archivo_fisico']
                                                        )
                                                ) ?>">
                                Descargar
                            </a>

                            <form action="<?= base_url(
                                                    'asuntos-internos/archivos/eliminar/'
                                                        . rawurlencode(
                                                            $archivo['archivo_fisico']
                                                        )
                                                ) ?>" method="post" class="delete-form" data-delete-form
                                data-file-name="<?= esc(
                                                            $archivo['nombre_original'],
                                                            'attr'
                                                        ) ?>">
                                <?= csrf_field() ?>

                                <button type="submit" class="table-action table-action--danger">
                                    Eliminar
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

                <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

            <tbody id="sin-resultados" hidden>
                <tr>
                    <td colspan="5" class="table-empty">
                        No se encontraron archivos con los filtros seleccionados.
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

    <div class="history-pagination" id="history-pagination">

        <span class="history-pagination__summary" id="pagination-summary">
            Mostrando 0 archivos
        </span>

        <div class="history-pagination__controls">

            <button type="button" class="pagination-button" id="pagina-anterior" aria-label="Página anterior">
                ‹
            </button>

            <span class="history-pagination__counter" id="pagination-counter">
                Página 1 de 1
            </span>

            <button type="button" class="pagination-button" id="pagina-siguiente" aria-label="Página siguiente">
                ›
            </button>

        </div>

    </div>

</section>

<?php
$mensajeProceso = session()->getFlashdata('success');
$datosEliminacion = session()->getFlashdata('delete_success');
?>

<?php if ($mensajeProceso): ?>

    <div
        class="confirmation-modal confirmation-modal--success"
        id="process-success-modal"
        data-result-modal
        aria-hidden="false"
    >
        <div class="confirmation-modal__backdrop"></div>

        <div
            class="confirmation-modal__dialog"
            role="status"
            aria-live="polite"
            aria-labelledby="process-success-title"
            aria-describedby="process-success-description"
        >
            <div class="confirmation-modal__icon">
                ✓
            </div>

            <div class="confirmation-modal__content">

                <span class="confirmation-modal__label">
                    Proceso completado
                </span>

                <h2 id="process-success-title">
                    Archivo procesado correctamente
                </h2>

                <p id="process-success-description">
                    <?= esc($mensajeProceso) ?>
                </p>

                <p class="confirmation-modal__message">
                    El archivo ya está disponible para descargar.
                </p>

            </div>
        </div>
    </div>

<?php endif; ?>


<?php if (
    is_array($datosEliminacion)
    && ! empty($datosEliminacion['archivo'])
): ?>

    <div
        class="confirmation-modal confirmation-modal--delete-success"
        id="delete-success-modal"
        data-result-modal
        aria-hidden="false"
    >
        <div class="confirmation-modal__backdrop"></div>

        <div
            class="confirmation-modal__dialog"
            role="status"
            aria-live="polite"
            aria-labelledby="delete-success-title"
            aria-describedby="delete-success-description"
        >
            <div class="confirmation-modal__icon">
                ✓
            </div>

            <div class="confirmation-modal__content">

                <span class="confirmation-modal__label">
                    Eliminación completada
                </span>

                <h2 id="delete-success-title">
                    <?= esc(
                        $datosEliminacion['titulo']
                        ?? 'Archivo eliminado correctamente'
                    ) ?>
                </h2>

                <p id="delete-success-description">
                    Se eliminó el archivo:
                </p>

                <strong class="confirmation-modal__file-name">
                    <?= esc($datosEliminacion['archivo']) ?>
                </strong>

                <p class="confirmation-modal__message">
                    <?= esc(
                        $datosEliminacion['mensaje']
                        ?? 'La eliminación se completó de forma permanente.'
                    ) ?>
                </p>

            </div>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>