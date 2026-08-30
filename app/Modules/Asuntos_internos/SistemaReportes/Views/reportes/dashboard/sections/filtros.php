<section class="dashboard-filtros">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-filtros__encabezado">

        <div class="dashboard-filtros__encabezado-principal">

            <div
                class="dashboard-filtros__encabezado-icono"
                aria-hidden="true"
            >
                <svg viewBox="0 0 24 24">
                    <path d="M4 6h16"/>
                    <path d="M7 12h10"/>
                    <path d="M10 18h4"/>
                </svg>
            </div>

            <div>

                <span class="dashboard-filtros__eyebrow">
                    Consulta de información
                </span>

                <h2 class="dashboard-filtros__titulo">
                    Filtrar Dashboard
                </h2>

                <p class="dashboard-filtros__descripcion">
                    Personaliza la información mostrada en los
                    indicadores y gráficas.
                </p>

            </div>

        </div>


        <button
            type="button"
            class="dashboard-filtros__mas"
            id="dashboard-mas-filtros"
            aria-expanded="false"
            aria-controls="dashboard-filtros-avanzados"
        >

            <svg
                class="dashboard-filtros__mas-icono"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path d="M4 6h16"/>
                <path d="M7 12h10"/>
                <path d="M10 18h4"/>
            </svg>

            <span>
                Más filtros
            </span>

            <svg
                class="dashboard-filtros__mas-flecha"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path d="m8 10 4 4 4-4"/>
            </svg>

        </button>

    </div>


    <!-- =====================================================
         FILTROS PRINCIPALES
    ====================================================== -->

    <div class="dashboard-filtros__principales">

        <!-- DESDE -->
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


        <!-- HASTA -->
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


        <!-- PERIODO -->
        <div class="dashboard-filtros__campo">

            <label for="dashboard-periodo">
                Periodo rápido
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

                <option value="semestre">
                    Últimos 6 meses
                </option>

                <option value="anio">
                    Año actual
                </option>

                <option value="todo">
                    Todo
                </option>

                <option value="personalizado">
                    Personalizado
                </option>
            </select>

        </div>


        <!-- FECHA A ANALIZAR -->
        <div class="dashboard-filtros__campo">

            <label for="dashboard-tipo-fecha">
                Fecha a analizar
            </label>

            <select
                id="dashboard-tipo-fecha"
                name="tipo_fecha"
            >
                <option value="registro">
                    Fecha de registro
                </option>

                <option value="queja">
                    Fecha de queja
                </option>

                <option value="hechos">
                    Fecha de los hechos
                </option>

                <option value="acuerdo">
                    Fecha de acuerdo
                </option>
            </select>

        </div>

    </div>


    <!-- =====================================================
         FILTROS AVANZADOS
    ====================================================== -->

    <div
        class="dashboard-filtros__avanzados"
        id="dashboard-filtros-avanzados"
        hidden
    >

        <!-- =================================================
             TERRITORIO
        ================================================== -->

        <div class="dashboard-filtros__grupo">

            <div class="dashboard-filtros__grupo-encabezado">

                <span class="dashboard-filtros__grupo-icono">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"/>
                        <circle cx="12" cy="10" r="2"/>
                    </svg>
                </span>

                <span>
                    Territorio
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-sector">
                        Sector / Área
                    </label>

                    <select
                        id="dashboard-sector"
                        name="sector"
                    >
                        <option value="">
                            Todos
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-zona">
                        Zona
                    </label>

                    <select
                        id="dashboard-zona"
                        name="zona"
                        disabled
                        title="Pendiente de definir la relación entre sectores y zonas"
                    >
                        <option value="">
                            Pendiente de configuración
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-cuadrante">
                        Cuadrante
                    </label>

                    <select
                        id="dashboard-cuadrante"
                        name="cuadrante"
                    >
                        <option value="">
                            Todos
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-colonia">
                        Colonia
                    </label>

                    <select
                        id="dashboard-colonia"
                        name="colonia"
                    >
                        <option value="">
                            Todas
                        </option>
                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             REPORTE
        ================================================== -->

        <div class="dashboard-filtros__grupo">

            <div class="dashboard-filtros__grupo-encabezado">

                <span class="dashboard-filtros__grupo-icono">
                    <svg viewBox="0 0 24 24">
                        <path d="M6 3h9l3 3v15H6Z"/>
                        <path d="M15 3v4h4"/>
                        <path d="M9 11h6"/>
                        <path d="M9 15h6"/>
                    </svg>
                </span>

                <span>
                    Reporte
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-estado">
                        Estado del reporte
                    </label>

                    <select
                        id="dashboard-estado"
                        name="estado_actual"
                    >
                        <option value="">
                            Todos
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


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-clasificacion">
                        Clasificación / Incidencia
                    </label>

                    <select
                        id="dashboard-clasificacion"
                        name="clasificacion"
                    >
                        <option value="">
                            Todas
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-resolucion">
                        Resolución
                    </label>

                    <select
                        id="dashboard-resolucion"
                        name="resolucion"
                    >
                        <option value="">
                            Todas
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-seguimiento">
                        Seguimiento
                    </label>

                    <select
                        id="dashboard-seguimiento"
                        name="seguimiento"
                    >
                        <option value="">
                            Todos
                        </option>

                        <option value="con">
                            Con seguimiento
                        </option>

                        <option value="sin">
                            Sin seguimiento
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-evidencia">
                        Evidencia
                    </label>

                    <select
                        id="dashboard-evidencia"
                        name="evidencia"
                    >
                        <option value="">
                            Todos
                        </option>

                        <option value="con">
                            Con evidencia
                        </option>

                        <option value="sin">
                            Sin evidencia
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-antiguedad">
                        Antigüedad del caso
                    </label>

                    <select
                        id="dashboard-antiguedad"
                        name="antiguedad"
                    >
                        <option value="">
                            Todas
                        </option>

                        <option value="0-30">
                            0 a 30 días
                        </option>

                        <option value="31-60">
                            31 a 60 días
                        </option>

                        <option value="61-90">
                            61 a 90 días
                        </option>

                        <option value="90+">
                            Más de 90 días
                        </option>
                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             OPERACIÓN
        ================================================== -->

        <div class="dashboard-filtros__grupo">

            <div class="dashboard-filtros__grupo-encabezado">

                <span class="dashboard-filtros__grupo-icono">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </span>

                <span>
                    Operación
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-turno">
                        Turno
                    </label>

                    <select
                        id="dashboard-turno"
                        name="turno"
                    >
                        <option value="">
                            Todos
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-area-personal">
                        Área del personal
                    </label>

                    <select
                        id="dashboard-area-personal"
                        name="area_personal"
                    >
                        <option value="">
                            Todas
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-unidad">
                        Unidad
                    </label>

                    <select
                        id="dashboard-unidad"
                        name="unidad"
                    >
                        <option value="">
                            Todas
                        </option>
                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             RESPONSABLES
        ================================================== -->

        <div class="dashboard-filtros__grupo">

            <div class="dashboard-filtros__grupo-encabezado">

                <span class="dashboard-filtros__grupo-icono">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M5 21a7 7 0 0 1 14 0"/>
                    </svg>
                </span>

                <span>
                    Responsables
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-inspector">
                        Inspector
                    </label>

                    <select
                        id="dashboard-inspector"
                        name="inspector"
                    >
                        <option value="">
                            Todos
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-investigador">
                        Investigador
                    </label>

                    <select
                        id="dashboard-investigador"
                        name="investigador"
                    >
                        <option value="">
                            Todos
                        </option>
                    </select>

                </div>


                <div class="dashboard-filtros__campo">

                    <label for="dashboard-emite-resolucion">
                        Emite resolución
                    </label>

                    <select
                        id="dashboard-emite-resolucion"
                        name="emite_resolucion"
                    >
                        <option value="">
                            Todos
                        </option>
                    </select>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         PIE / ACCIONES
    ====================================================== -->

    <div class="dashboard-filtros__pie">

        <div class="dashboard-filtros__estado">

            <span class="dashboard-filtros__estado-punto"></span>

            <span id="dashboard-filtros-estado">
                Consulta general
            </span>

        </div>


        <div class="dashboard-filtros__acciones">

            <button
                type="button"
                class="
                    dashboard-filtros__boton
                    dashboard-filtros__boton--secondary
                "
                id="dashboard-limpiar-filtros"
            >
                Limpiar
            </button>


            <button
                type="button"
                class="
                    dashboard-filtros__boton
                    dashboard-filtros__boton--primary
                "
                id="dashboard-aplicar-filtros"
            >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16"/>
                    <path d="M7 12h10"/>
                    <path d="M10 18h4"/>
                </svg>

                <span>
                    Aplicar filtros
                </span>

            </button>

        </div>

    </div>

</section>