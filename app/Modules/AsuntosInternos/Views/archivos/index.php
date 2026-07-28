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
                    <td colspan="6" class="table-empty">
                        No hay archivos almacenados.
                    </td>
                </tr>

                <?php else: ?>

                <?php foreach ($archivos as $archivo): ?>

                <tr data-file-row data-file-name="<?= esc(strtolower($archivo['nombre_original']), 'attr') ?>"
                    data-file-date="<?= esc(date('Y-m-d', strtotime($archivo['fecha_procesamiento'])), 'attr') ?>">

                    <td>
                        <strong>
                            <?= esc($archivo['nombre_original']) ?>
                        </strong>
                    </td>

                    <td>
                        <?= esc(date('d/m/Y H:i', strtotime($archivo['fecha_procesamiento']))) ?>
                    </td>

                    <td>
                        <?= esc(number_format(($archivo['tamano'] ?? 0) / 1024, 2)) ?> KB
                    </td>

                    <td>
                        <span class="status status--success">
                            Completado
                        </span>
                    </td>

                    <td>

                        <a class="table-action" href="<?= base_url(
                                        'asuntos-internos/archivos/descargar/' .
                                        rawurlencode($archivo['archivo_fisico'])
                                    ) ?>">
                            Descargar
                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

            <tbody id="sin-resultados" hidden>

                <tr>
                    <td colspan="6" class="table-empty">
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

<?= $this->endSection() ?>