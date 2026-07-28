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

    <form
        action="<?= base_url('asuntos-internos/archivos/procesar') ?>"
        method="post"
        enctype="multipart/form-data"
        class="upload-form"
    >
        <?= csrf_field() ?>

        <label class="upload-area" for="archivo_excel">

            <div class="upload-area__icon">↑</div>

            <strong>Selecciona un archivo Excel</strong>

            <span>
                También puedes arrastrarlo y soltarlo aquí
            </span>

            <input
                type="file"
                name="archivo_excel"
                id="archivo_excel"
                accept=".xlsx,.xls,.xlsm,.xlsb"
                required
            >

            <button type="button" class="button button--secondary">
                Seleccionar archivo
            </button>

            <small id="file-name">
                Ningún archivo seleccionado
            </small>

        </label>

        <button type="submit" class="button button--primary">
            Procesar archivo
        </button>
    </form>

</section>

<section class="recent-section">

    <div class="section-heading">
        <div>
            <span class="page-header__label">Actividad reciente</span>
            <h2>Últimos archivos</h2>
        </div>

        <a
            href="<?= base_url('asuntos-internos/archivos') ?>"
            class="text-link"
        >
            Ver historial
        </a>
    </div>

    <div class="empty-state">
        <strong>Aún no hay archivos procesados</strong>

        <p>
            Los archivos almacenados aparecerán en esta sección.
        </p>
    </div>

</section>

<script>
    const inputArchivo = document.getElementById('archivo_excel');
    const nombreArchivo = document.getElementById('file-name');

    inputArchivo.addEventListener('change', function () {
        nombreArchivo.textContent = this.files.length
            ? this.files[0].name
            : 'Ningún archivo seleccionado';
    });
</script>

<?= $this->endSection() ?>