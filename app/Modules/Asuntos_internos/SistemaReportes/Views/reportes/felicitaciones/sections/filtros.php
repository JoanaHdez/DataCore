<section class="reportes-filtros">

    <div class="reportes-filtros__header">

        <div>

            <span class="reportes-filtros__eyebrow">
                Filtros
            </span>

            <h2 class="reportes-filtros__title">
                Buscar felicitaciones
            </h2>

            <p class="reportes-filtros__description">
                Utiliza los filtros para localizar registros específicos.
            </p>

        </div>

    </div>


    <div class="reportes-filtros__body">

        <!-- =====================================================
             BÚSQUEDA GENERAL
        ====================================================== -->

        <div class="reportes-filtros__field reportes-filtros__field--search">

            <label for="filtro_felicitaciones_busqueda">
                Buscar
            </label>

            <input type="search" id="filtro_felicitaciones_busqueda" class="reportes-filtros__input"
                placeholder="Folio, felicitante, personal, alias..." autocomplete="off">

        </div>


        <!-- =====================================================
             SECTOR
        ====================================================== -->

        <div class="reportes-filtros__field">

            <label for="filtro_felicitaciones_sector">
                Sector
            </label>

            <select id="filtro_felicitaciones_sector" class="reportes-filtros__select">

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


        <!-- =====================================================
            ÁREA
        ====================================================== -->

        <div class="reportes-filtros__field">

            <label for="filtro_felicitaciones_area">
                Área
            </label>

            <select id="filtro_felicitaciones_area" class="reportes-filtros__select">

                <option value="">
                    Todas
                </option>

                <?php foreach (($areas ?? []) as $area): ?>

                <option value="<?= esc($area) ?>">
                    <?= esc($area) ?>
                </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- =====================================================
            TURNO
        ====================================================== -->

        <div class="reportes-filtros__field">

            <label for="filtro_felicitaciones_turno">
                Turno
            </label>

            <select id="filtro_felicitaciones_turno" class="reportes-filtros__select">

                <option value="">
                    Todos
                </option>

                <?php foreach (($turnos ?? []) as $turno): ?>

                <option value="<?= esc($turno) ?>">
                    <?= esc($turno) ?>
                </option>

                <?php endforeach; ?>

            </select>

        </div>

    </div>

</section>