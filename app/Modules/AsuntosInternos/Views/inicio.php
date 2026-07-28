<?= $this->extend('App\Modules\AsuntosInternos\Views\layouts\main') ?>

<?= $this->section('title') ?>
Inicio | Asuntos Internos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="page-header">
    <div>
        <span class="page-header__label">Gestión de archivos</span>

        <h1>Procesar archivo Excel</h1>

        <p>
            Sube un archivo Excel para estandarizar automáticamente
            el formato de sus fechas.
        </p>
    </div>
</section>

<section class="upload-card">

    <form action="<?= base_url('asuntos-internos/archivos/procesar') ?>" method="post" enctype="multipart/form-data"
        class="upload-form" data-upload-form>
        <?= csrf_field() ?>

        <label
    class="upload-area"
    for="archivo_excel"
    data-drop-area
>
    <div class="upload-area__icon">
        ↑
    </div>

    <strong>
        Selecciona un archivo Excel
    </strong>

    <span>
        Archivos permitidos: .xlsx y .xlsm, máximo 50 MB
    </span>

    <span
        class="button button--secondary upload-area__button"
        role="button"
        tabindex="0"
        data-file-trigger
    >
        Seleccionar archivo
    </span>

    <span id="file-name">
        Ningún archivo seleccionado
    </span>

    <input
        type="file"
        name="archivo_excel"
        id="archivo_excel"
        accept=".xlsx,.xlsm"
        hidden
    >
</label>

        <button type="submit" class="button button--primary" data-submit-button>
            <span class="button__spinner" aria-hidden="true"></span>

            <span data-button-text>
                Procesar archivo
            </span>
        </button>
    </form>

</section>

<section class="recent-section">

    <div class="section-heading">
        <div>
            <span class="page-header__label">Actividad reciente</span>
            <h2>Últimos archivos</h2>
        </div>

        <a href="<?= base_url('asuntos-internos/archivos') ?>" class="text-link">
            Ver historial
        </a>
    </div>

    <?php if (empty($archivosRecientes)): ?>

    <div class="empty-state">
        <strong>Aún no hay archivos procesados</strong>

        <p>
            Los archivos almacenados aparecerán en esta sección.
        </p>
    </div>

    <?php else: ?>

    <div class="recent-files">

        <?php foreach ($archivosRecientes as $archivo): ?>

        <article class="recent-file">

            <div class="recent-file__info">
                <div class="recent-file__icon">
                    XLS
                </div>

                <div>
                    <strong>
                        <?= esc($archivo['nombre_original']) ?>
                    </strong>

                    <span>
                        <?= esc(
                                date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $archivo['fecha_procesamiento']
                                    )
                                )
                            ) ?>
                        ·
                        <?= esc(
                                number_format(
                                    ($archivo['tamano'] ?? 0) / 1024,
                                    2
                                )
                            ) ?> KB
                    </span>
                </div>
            </div>

            <div class="recent-file__actions">

                <a class="table-action" href="<?= base_url(
                            'asuntos-internos/archivos/descargar/'
                            . rawurlencode(
                                $archivo['archivo_fisico']
                            )
                        ) ?>">
                    Descargar
                </a>

            </div>

        </article>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</section>

<script>
const inputArchivo = document.getElementById('archivo_excel');
const nombreArchivo = document.getElementById('file-name');

inputArchivo.addEventListener('change', function() {
    nombreArchivo.textContent = this.files.length ?
        this.files[0].name :
        'Ningún archivo seleccionado';
});
</script>

<?= $this->endSection() ?>