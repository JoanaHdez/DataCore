<?php

/* =========================================================
   OPCIONES DINÁMICAS

   Estas opciones son enviadas desde
   DashboardService / Reportes_Controller.
========================================================= */

$opcionesFiltros =
    $opcionesFiltros
    ?? [];


$areasFiltro =
    $opcionesFiltros['areas']
    ?? [];


$unidadesFiltro =
    $opcionesFiltros['unidades']
    ?? [];


/* =========================================================
   TURNOS ANALÍTICOS
========================================================= */

$turnosFiltro = [
    'Primer turno',
    'Segundo turno',
    'Tercer turno',
    'Alfa',
    'Beta',
    'Diario',
    'No refiere ni fecha ni horario',
];


/* =========================================================
   SECTORES INSTITUCIONALES
========================================================= */

$sectoresFiltro = [];

for (
    $numero = 1;
    $numero <= 15;
    $numero++
) {

    $sectoresFiltro[] =
        'SECTOR '
        . str_pad(
            (string) $numero,
            2,
            '0',
            STR_PAD_LEFT
        );
}

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

                    <path d="M4 6h16" />
                    <path d="M7 12h10" />
                    <path d="M10 18h4" />

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

                <path d="M4 6h16" />
                <path d="M7 12h10" />
                <path d="M10 18h4" />

            </svg>

            <span>
                Más filtros
            </span>

            <svg
                class="dashboard-filtros__mas-flecha"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >

                <path d="m8 10 4 4 4-4" />

            </svg>

        </button>

    </div>


    <!-- =====================================================
         FILTROS PRINCIPALES
    ====================================================== -->

    <div class="dashboard-filtros__principales">

        <!-- =================================================
             FECHA DE REGISTRO - DESDE
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-registro-inicio">
                Registro desde
            </label>

            <input
                type="date"
                id="dashboard-fecha-registro-inicio"
                name="fecha_registro_inicio"
            >

        </div>


        <!-- =================================================
             FECHA DE REGISTRO - HASTA
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-registro-fin">
                Registro hasta
            </label>

            <input
                type="date"
                id="dashboard-fecha-registro-fin"
                name="fecha_registro_fin"
            >

        </div>


        <!-- =================================================
             FECHA DE QUEJA - DESDE
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-queja-inicio">
                Queja desde
            </label>

            <input
                type="date"
                id="dashboard-fecha-queja-inicio"
                name="fecha_queja_inicio"
            >

        </div>


        <!-- =================================================
             FECHA DE QUEJA - HASTA
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-queja-fin">
                Queja hasta
            </label>

            <input
                type="date"
                id="dashboard-fecha-queja-fin"
                name="fecha_queja_fin"
            >

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

                        <path d="M6 3h9l3 3v15H6Z" />
                        <path d="M15 3v4h4" />
                        <path d="M9 11h6" />
                        <path d="M9 15h6" />

                    </svg>

                </span>

                <span>
                    Reporte
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <!-- =========================================
                     ESTADO
                ========================================== -->

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


                <!-- =========================================
                     SEGUIMIENTO
                ========================================== -->

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


                <!-- =========================================
                     CLASIFICACIÓN - PENDIENTE
                ========================================== -->

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

                        <circle cx="12" cy="8" r="4" />

                        <path d="M5 21a7 7 0 0 1 14 0" />

                    </svg>

                </span>

                <span>
                    Personal involucrado
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <!-- =========================================
                     ÁREA INVOLUCRADA
                ========================================== -->

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

                            <option value="<?= esc($area) ?>">
                                <?= esc($area) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- =========================================
                     TURNO
                ========================================== -->

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

                            <option value="<?= esc($turno) ?>">
                                <?= esc($turno) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- =========================================
                     SECTOR
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-sector">
                        Sector
                    </label>

                    <select
                        id="dashboard-sector"
                        name="sector"
                    >

                        <option value="">
                            Todos
                        </option>


                        <?php foreach (
                            $sectoresFiltro
                            as $sector
                        ): ?>

                            <option value="<?= esc($sector) ?>">
                                <?= esc($sector) ?>
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

                        <path d="M3 13l2-5h14l2 5" />

                        <path d="M5 13h14v5H5Z" />

                        <circle cx="8" cy="17" r="1" />

                        <circle cx="16" cy="17" r="1" />

                    </svg>

                </span>

                <span>
                    Unidades
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <!-- =========================================
                     UNIDAD
                ========================================== -->

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


                            <?php if (
                                $valorUnidad !== ''
                            ): ?>

                                <option value="<?= esc($valorUnidad) ?>">
                                    <?= esc($textoUnidad) ?>
                                </option>

                            <?php endif; ?>


                        <?php endforeach; ?>

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

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >

                    <path d="M4 6h16" />

                    <path d="M7 12h10" />

                    <path d="M10 18h4" />

                </svg>

                <span>
                    Aplicar filtros
                </span>

            </button>

        </div>

    </div>

</section>