<section class="reportes-filtros">

    <div class="reportes-filtros__header">

        <div>
            <span class="reportes-filtros__eyebrow">
                Filtros
            </span>

            <h2 class="reportes-filtros__title">
                Buscar reportes
            </h2>

            <p class="reportes-filtros__description">
                Utiliza los filtros para localizar registros específicos.
            </p>
        </div>

    </div>


    <div class="reportes-filtros__body">

        <!-- BÚSQUEDA GENERAL -->
        <div class="reportes-filtros__field reportes-filtros__field--search">

            <label for="filtro_busqueda">
                Buscar
            </label>

            <input type="search" id="filtro_busqueda" class="reportes-filtros__input"
                placeholder="Folio, expediente, ubicación, quejoso..." autocomplete="off">

        </div>


        <!-- SECTOR -->
        <div class="reportes-filtros__field">

            <label for="filtro_sector">
                Sector
            </label>

            <select id="filtro_sector" class="reportes-filtros__select">
                <option value="">
                    Todos
                </option>

                <?php foreach (($sectores ?? []) as $sector): ?>

                <option value="<?= esc($sector) ?>">
                    <?= esc($sector) ?>
                </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- ÁREA -->
        <div class="reportes-filtros__field">

            <label for="filtro_area">
                Área
            </label>

            <select id="filtro_area" class="reportes-filtros__select">
                <option value="">
                    Todas
                </option>
            </select>

        </div>


        <!-- TURNO -->
        <div class="reportes-filtros__field">

            <label for="filtro_turno">
                Turno
            </label>

            <select id="filtro_turno" class="reportes-filtros__select">
                <option value="">
                    Todos
                </option>
            </select>

        </div>


        <!-- RESOLUCIÓN -->
        <div class="reportes-filtros__field">

            <label for="filtro_resolucion">
                Resolución
            </label>

            <select id="filtro_resolucion" class="reportes-filtros__select">
                <option value="">
                    Todas
                </option>

                <option value="Pendiente">
                    Pendiente
                </option>

                <option value="En proceso">
                    En proceso
                </option>

                <option value="Finalizado">
                    Finalizado
                </option>

            </select>

        </div>

    </div>

</section>