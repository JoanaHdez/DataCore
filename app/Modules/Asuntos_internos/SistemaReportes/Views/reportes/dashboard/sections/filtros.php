<section class="dashboard-filtros">

    <div class="dashboard-filtros__encabezado">

        <div>
            <span class="dashboard-filtros__eyebrow">
                Periodo de consulta
            </span>

            <h2 class="dashboard-filtros__titulo">
                Filtrar información
            </h2>
        </div>

    </div>


    <div class="dashboard-filtros__controles">

        <!-- FECHA INICIAL -->
        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-inicio">
                Desde
            </label>

            <input
                type="date"
                id="dashboard-fecha-inicio"
                name="fecha_inicio"
            >

        </div>


        <!-- FECHA FINAL -->
        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-fin">
                Hasta
            </label>

            <input
                type="date"
                id="dashboard-fecha-fin"
                name="fecha_fin"
            >

        </div>


        <!-- PERIODO RÁPIDO -->
        <div class="dashboard-filtros__campo">

            <label for="dashboard-periodo">
                Periodo
            </label>

            <select
                id="dashboard-periodo"
                name="periodo"
            >
                <option value="actual">
                    Mes actual
                </option>

                <option value="anterior">
                    Mes anterior
                </option>

                <option value="trimestre">
                    Últimos 3 meses
                </option>

                <option value="anio">
                    Año actual
                </option>

                <option value="personalizado">
                    Personalizado
                </option>
            </select>

        </div>


        <!-- ACCIONES -->
        <div class="dashboard-filtros__acciones">

            <button
                type="button"
                class="dashboard-filtros__boton dashboard-filtros__boton--secondary"
                id="dashboard-limpiar-filtros"
            >
                Limpiar
            </button>

            <button
                type="button"
                class="dashboard-filtros__boton dashboard-filtros__boton--primary"
                id="dashboard-aplicar-filtros"
            >
                Aplicar filtros
            </button>

        </div>

    </div>

</section>