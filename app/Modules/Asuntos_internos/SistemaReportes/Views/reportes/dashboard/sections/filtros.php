<?php

/* =========================================================
   OPCIONES DINÁMICAS

   Estas opciones son enviadas desde
   DashboardService / Reportes_Controller.
========================================================= */

$opcionesFiltros =
    $opcionesFiltros
    ?? [];


/* =========================================================
   ÁREAS
========================================================= */

$areasFiltro =
    $opcionesFiltros['areas']
    ?? [];


/* =========================================================
   UNIDADES
========================================================= */

$unidadesFiltro =
    $opcionesFiltros['unidades']
    ?? [];


/* =========================================================
   CLASIFICACIONES

   Por ahora se prepara la vista para recibirlas.
   Después DashboardFiltrosService las obtendrá
   dinámicamente desde los datos existentes.
========================================================= */

$clasificacionesFiltro =
    $opcionesFiltros['clasificaciones']
    ?? [];


/* =========================================================
   TURNOS ANALÍTICOS

   TEMPORAL:
   actualmente se conservan los nombres existentes.

   En V1 deberán obtenerse dinámicamente de los datos
   y este arreglo desaparecerá.
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


/* =========================================================
   ZONAS

   La zona no se almacena directamente.
   Se obtiene a partir del sector:

   Norte     → 1 - 3
   Poniente  → 4 - 7
   Centro    → 8 - 10
   Oriente   → 11 - 15

   Los valores coinciden con
   DashboardFiltrosService::obtenerCondicionSqlZona().
========================================================= */

$zonasFiltro = [

    [
        'valor' => 'Zona Norte',
        'texto' => 'Norte',
    ],

    [
        'valor' => 'Zona Poniente',
        'texto' => 'Poniente',
    ],

    [
        'valor' => 'Zona Centro',
        'texto' => 'Centro',
    ],

    [
        'valor' => 'Zona Oriente',
        'texto' => 'Oriente',
    ],

];

?>


<section class="dashboard-filtros">

    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <div class="dashboard-filtros__encabezado">

        <div class="dashboard-filtros__encabezado-principal">

            <div class="dashboard-filtros__encabezado-icono" aria-hidden="true">

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


        <button type="button" class="dashboard-filtros__mas" id="dashboard-mas-filtros" aria-expanded="false"
            aria-controls="dashboard-filtros-avanzados">

            <svg class="dashboard-filtros__mas-icono" viewBox="0 0 24 24" aria-hidden="true">

                <path d="M4 6h16" />
                <path d="M7 12h10" />
                <path d="M10 18h4" />

            </svg>

            <span>
                Más filtros
            </span>

            <svg class="dashboard-filtros__mas-flecha" viewBox="0 0 24 24" aria-hidden="true">

                <path d="m8 10 4 4 4-4" />

            </svg>

        </button>

    </div>


    <!-- =====================================================
         FILTROS PRINCIPALES
    ====================================================== -->

    <div class="dashboard-filtros__principales">

        <!-- =================================================
             FECHA INICIAL
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-registro-inicio">
                Fecha inicial
            </label>

            <input type="date" id="dashboard-fecha-registro-inicio" name="fecha_registro_inicio">

        </div>


        <!-- =================================================
             FECHA FINAL
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-registro-fin">
                Fecha final
            </label>

            <input type="date" id="dashboard-fecha-registro-fin" name="fecha_registro_fin">

        </div>


        <!-- =================================================
             TIPO DE REGISTRO
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-tipo">
                Tipo
            </label>

            <select id="dashboard-tipo" name="tipo">

                <option value="">
                    Todos
                </option>

                <option value="QUEJA">
                    Quejas
                </option>

                <option value="FELICITACION">
                    Felicitaciones
                </option>

            </select>

        </div>

    </div>


    <!-- =====================================================
         FILTROS AVANZADOS
    ====================================================== -->

    <div class="dashboard-filtros__avanzados" id="dashboard-filtros-avanzados" hidden>


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
                        Estado
                    </label>

                    <select id="dashboard-estado" name="estado">

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
                     CLASIFICACIÓN
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-clasificacion">
                        Clasificación
                    </label>

                    <select id="dashboard-clasificacion" name="clasificacion">

                        <option value="">
                            Todas
                        </option>


                        <?php foreach (
                            $clasificacionesFiltro
                            as $clasificacion
                        ): ?>

                        <?php

                            if (is_array($clasificacion)) {

                                $valorClasificacion =
                                    trim(
                                        (string) (
                                            $clasificacion['valor']
                                            ?? $clasificacion['clasificacion']
                                            ?? ''
                                        )
                                    );


                                $textoClasificacion =
                                    trim(
                                        (string) (
                                            $clasificacion['texto']
                                            ?? $clasificacion['clasificacion']
                                            ?? $valorClasificacion
                                        )
                                    );

                            } else {

                                $valorClasificacion =
                                    trim(
                                        (string) $clasificacion
                                    );


                                $textoClasificacion =
                                    $valorClasificacion;
                            }

                            ?>


                        <?php if (
                                $valorClasificacion !== ''
                            ): ?>

                        <option value="<?= esc($valorClasificacion) ?>">
                            <?= esc($textoClasificacion) ?>
                        </option>

                        <?php endif; ?>


                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- =========================================
                     SEGUIMIENTO
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-seguimiento">
                        Seguimiento
                    </label>

                    <select id="dashboard-seguimiento" name="seguimiento">

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
                     QUEJA ANÓNIMA
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-anonima">
                        Queja anónima
                    </label>

                    <select id="dashboard-anonima" name="es_anonimo">

                        <option value="">
                            Todas
                        </option>

                        <option value="1">
                            Sí
                        </option>

                        <option value="0">
                            No
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <!-- =================================================
             UBICACIÓN OPERATIVA
        ================================================== -->

        <div class="dashboard-filtros__grupo">

            <div class="dashboard-filtros__grupo-encabezado">

                <span class="dashboard-filtros__grupo-icono">

                    <svg viewBox="0 0 24 24">

                        <path d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z" />
                        <circle cx="12" cy="10" r="2" />

                    </svg>

                </span>

                <span>
                    Ubicación operativa
                </span>

            </div>


            <div class="dashboard-filtros__grupo-grid">

                <!-- =========================================
                     ZONA
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-zona">
                        Zona
                    </label>

                    <select id="dashboard-zona" name="zona">

                        <option value="">
                            Todas
                        </option>


                        <?php foreach (
                            $zonasFiltro
                            as $zona
                        ): ?>

                        <option value="<?= esc($zona['valor']) ?>">
                            <?= esc($zona['texto']) ?>
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

                    <select id="dashboard-sector" name="sector">

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


                <!-- =========================================
                     TURNO
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-turno">
                        Turno
                    </label>

                    <select id="dashboard-turno" name="turno">

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
                     ÁREA
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label for="dashboard-area-personal">
                        Área
                    </label>

                    <select id="dashboard-area-personal" name="area_personal">

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
                     PERSONAL

                     La búsqueda institucional se conectará
                     posteriormente desde JavaScript.
                ========================================== -->

                <div class="
                        dashboard-filtros__campo
                        dashboard-filtros__campo--personal
                    ">

                    <label for="dashboard-personal-busqueda">
                        Personal
                    </label>

                    <div class="dashboard-filtros__personal">

                        <input type="search" id="dashboard-personal-busqueda"
                            placeholder="Buscar por nombre o nómina..." autocomplete="off">

                        <input type="hidden" id="dashboard-personal" name="personal" value="">

                        <div class="dashboard-filtros__personal-resultados" id="dashboard-personal-resultados" hidden>
                        </div>

                        <div class="dashboard-filtros__personal-seleccion" id="dashboard-personal-seleccion" hidden>

                            <span id="dashboard-personal-seleccion-texto"></span>

                            <button type="button" id="dashboard-personal-quitar"
                                aria-label="Quitar personal seleccionado">
                                ×
                            </button>

                        </div>

                    </div>

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

                    <select id="dashboard-unidad" name="unidad">

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

            <button type="button" class="
                    dashboard-filtros__boton
                    dashboard-filtros__boton--secondary
                " id="dashboard-limpiar-filtros">

                Limpiar

            </button>


            <button type="button" class="
                    dashboard-filtros__boton
                    dashboard-filtros__boton--primary
                " id="dashboard-aplicar-filtros">

                <svg viewBox="0 0 24 24" aria-hidden="true">

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