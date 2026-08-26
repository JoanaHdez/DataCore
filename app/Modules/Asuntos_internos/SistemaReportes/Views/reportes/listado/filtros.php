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

        <button
            type="button"
            class="reportes-filtros__clear"
            id="btn-limpiar-filtros"
        >
            Limpiar filtros
        </button>

    </div>


    <div class="reportes-filtros__body">

        <!-- BÚSQUEDA GENERAL -->
        <div class="reportes-filtros__field reportes-filtros__field--search">

            <label for="filtro_busqueda">
                Buscar
            </label>

            <input
                type="search"
                id="filtro_busqueda"
                class="reportes-filtros__input"
                placeholder="Folio, expediente, ubicación, quejoso..."
                autocomplete="off"
            >

        </div>


        <!-- CLASIFICACIÓN -->
        <div class="reportes-filtros__field">

            <label for="filtro_clasificacion">
                Clasificación
            </label>

            <select
                id="filtro_clasificacion"
                class="reportes-filtros__select"
            >
                <option value="">
                    Todas
                </option>
            </select>

        </div>


        <!-- ÁREA -->
        <div class="reportes-filtros__field">

            <label for="filtro_area">
                Área
            </label>

            <select
                id="filtro_area"
                class="reportes-filtros__select"
            >
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

            <select
                id="filtro_turno"
                class="reportes-filtros__select"
            >
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

            <select
                id="filtro_resolucion"
                class="reportes-filtros__select"
            >
                <option value="">
                    Todas
                </option>
            </select>

        </div>

    </div>

</section>