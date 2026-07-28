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

    <a
        href="<?= base_url('asuntos-internos') ?>"
        class="button button--primary"
    >
        Subir archivo
    </a>

</section>

<section class="history-card">

    <div class="history-toolbar">

        <input
            type="search"
            class="search-input"
            id="buscar-archivo"
            placeholder="Buscar por nombre de archivo"
        >

        <input
            type="date"
            class="date-input"
            id="buscar-fecha"
        >

    </div>

    <div class="table-container">

        <table class="history-table">

            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Fecha de carga</th>
                    <th>Tamaño</th>
                    <th>Fechas modificadas</th>
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

                        <tr>
                            <td>
                                <strong>
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
                                <?= esc(
                                    $archivo['fechas_modificadas'] ?? 0
                                ) ?>
                            </td>

                            <td>
                                <span class="status status--success">
                                    Completado
                                </span>
                            </td>

                            <td>
                                <a
                                    class="table-action"
                                    href="<?= base_url(
                                        'asuntos-internos/archivos/descargar/'
                                        . rawurlencode(
                                            $archivo['archivo_fisico']
                                        )
                                    ) ?>"
                                >
                                    Descargar
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>

<?= $this->endSection() ?>