<?php

/* =========================================================
   OPCIONES DINÁMICAS

   Estas opciones son enviadas desde
   DashboardService / Reportes_Controller.

   Se inicializan vacías para evitar errores si alguna
   opción todavía no está disponible.
========================================================= */

$opcionesFiltros =
    $opcionesFiltros
    ?? [];


$areasFiltro =
    $opcionesFiltros['areas']
    ?? [];


$generosFiltro =
    $opcionesFiltros['generos']
    ?? [];


$unidadesFiltro =
    $opcionesFiltros['unidades']
    ?? [];


/*
 * Los turnos del Dashboard utilizan las mismas
 * categorías analíticas que las gráficas.
 *
 * No usamos directamente todas las variantes
 * existentes en plantilla.
 */

$turnosFiltro = [
    'Primer turno',
    'Segundo turno',
    'Tercer turno',
    'Alfa',
    'Beta',
    'Diario',
    'No refiere ni fecha ni horario',
];

?>


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


                <!-- ESTADO -->
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


                <!-- SEGUIMIENTO -->
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


                <!-- EVIDENCIA -->
                <div class="dashboard-filtros__campo">

                    <label for="dashboard-evidencia">
                        Evidencia
                    </label>

                    <select
                        id="dashboard-evidencia"
                        name="evidencia"
                    >

                        <option value="">
                            Todas
                        </option>

                        <option value="con">
                            Con evidencia
                        </option>

                        <option value="sin">
                            Sin evidencia
                        </option>

                    </select>

                </div>


                <!-- CLASIFICACIÓN - PENDIENTE -->
                <div class="dashboard-filtros__campo">

                    <label for="dashboard-clasificacion">
                        Clasificación
                    </label>

                    <select
                        id="dashboard-clasificacion"
                        name="clasificacion"
                        disabled
                        title="Pendiente de definir el catálogo institucional de clasificación"
                    >

                        <option value="">
                            Pendiente de catálogo
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             PERSONAL INVOLUCRADO
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
                    Personal involucrado
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">


                <!-- ÁREA INVOLUCRADA -->
                <div class="dashboard-filtros__campo">

                    <label for="dashboard-area-personal">
                        Área involucrada
                    </label>

                    <select
                        id="dashboard-area-personal"
                        name="area_personal"
                    >

                        <option value="">
                            Todas
                        </option>

                        <?php foreach (
                            $areasFiltro
                            as $area
                        ): ?>

                            <option
                                value="<?= esc($area) ?>"
                            >
                                <?= esc($area) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- TURNO -->
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

                        <?php foreach (
                            $turnosFiltro
                            as $turno
                        ): ?>

                            <option
                                value="<?= esc($turno) ?>"
                            >
                                <?= esc($turno) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             QUEJOSO
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
                    Quejoso
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">


                <!-- GÉNERO -->
                <div class="dashboard-filtros__campo">

                    <label for="dashboard-genero">
                        Género
                    </label>

                    <select
                        id="dashboard-genero"
                        name="genero"
                    >

                        <option value="">
                            Todos
                        </option>

                        <?php foreach (
                            $generosFiltro
                            as $genero
                        ): ?>

                            <option
                                value="<?= esc($genero) ?>"
                            >
                                <?= esc($genero) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             UNIDADES
        ================================================== -->

        <div class="dashboard-filtros__grupo">

            <div class="dashboard-filtros__grupo-encabezado">

                <span class="dashboard-filtros__grupo-icono">

                    <svg viewBox="0 0 24 24">
                        <path d="M3 13l2-5h14l2 5"/>
                        <path d="M5 13h14v5H5Z"/>
                        <circle cx="8" cy="17" r="1"/>
                        <circle cx="16" cy="17" r="1"/>
                    </svg>

                </span>

                <span>
                    Unidades
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">


                <!-- UNIDAD -->
                <div class="dashboard-filtros__campo">

                    <label for="dashboard-unidad">
                        Unidad involucrada
                    </label>

                    <select
                        id="dashboard-unidad"
                        name="unidad"
                    >

                        <option value="">
                            Todas
                        </option>

                        <?php foreach (
                            $unidadesFiltro
                            as $unidad
                        ): ?>

                            <?php

                            if (is_array($unidad)) {

                                $valorUnidad =
                                    trim(
                                        (string) (
                                            $unidad['valor']
                                            ?? $unidad['no_economico']
                                            ?? $unidad['placas']
                                            ?? ''
                                        )
                                    );


                                $textoUnidad =
                                    trim(
                                        (string) (
                                            $unidad['texto']
                                            ?? $unidad['no_economico']
                                            ?? $unidad['placas']
                                            ?? $valorUnidad
                                        )
                                    );

                            } else {

                                $valorUnidad =
                                    trim(
                                        (string) $unidad
                                    );


                                $textoUnidad =
                                    $valorUnidad;
                            }

                            ?>


                            <?php if ($valorUnidad !== ''): ?>

                                <option
                                    value="<?= esc($valorUnidad) ?>"
                                >
                                    <?= esc($textoUnidad) ?>
                                </option>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             PENDIENTES DE CONFIGURACIÓN
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
                    Configuración pendiente
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">


                <!-- ZONA - PENDIENTE -->
                <div class="dashboard-filtros__campo">

                    <label for="dashboard-zona">
                        Zona
                    </label>

                    <select
                        id="dashboard-zona"
                        name="zona"
                        disabled
                        title="Pendiente de definir la relación oficial entre sectores y zonas"
                    >

                        <option value="">
                            Pendiente de relación Sector → Zona
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

            <span
                class="dashboard-filtros__estado-punto"
            ></span>

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

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
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