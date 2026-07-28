<?= $this->extend('App\Modules\AsuntosInternos\Views\layouts\main') ?>

<?= $this->section('title') ?>
Historial | Asuntos Internos
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="page-header page-header--row">

    <div>
        <span class="page-header__label">Archivos almacenados</span>

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
            placeholder="Buscar por nombre de archivo"
        >

        <input
            type="date"
            class="date-input"
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
                <tr>
                    <td colspan="6" class="table-empty">
                        No hay archivos almacenados.
                    </td>
                </tr>
            </tbody>

        </table>

    </div>

</section>

<?= $this->endSection() ?>