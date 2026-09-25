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
========================================================= */

$clasificacionesFiltro =
    $opcionesFiltros['clasificaciones']
    ?? [];


/* =========================================================
   TURNOS ANALÍTICOS

   TEMPORAL:
   después deberán obtenerse dinámicamente desde los datos.
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
   SECTORES
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
========================================================= */

$zonasFiltro = [

    [
        'valor' => 'Zona Norte',
        'texto' => 'Norte',
        'avatar' => 'N',
        'descripcion' => 'Sectores 1 a 3',
    ],

    [
        'valor' => 'Zona Poniente',
        'texto' => 'Poniente',
        'avatar' => 'P',
        'descripcion' => 'Sectores 4 a 7',
    ],

    [
        'valor' => 'Zona Centro',
        'texto' => 'Centro',
        'avatar' => 'C',
        'descripcion' => 'Sectores 8 a 10',
    ],

    [
        'valor' => 'Zona Oriente',
        'texto' => 'Oriente',
        'avatar' => 'O',
        'descripcion' => 'Sectores 11 a 15',
    ],

];


/* =========================================================
   FUNCIÓN AUXILIAR PARA AVATAR

   Obtiene hasta dos iniciales de un texto.
========================================================= */

if (!function_exists('dashboardFiltroIniciales')) {

    function dashboardFiltroIniciales(
        string $texto
    ): string {

        $texto =
            trim(
                preg_replace(
                    '/\s+/u',
                    ' ',
                    $texto
                )
                ?? ''
            );


        if ($texto === '') {

            return '—';
        }


        $partes =
            preg_split(
                '/\s+/u',
                $texto
            )
            ?: [];


        $iniciales = '';


        foreach (
            $partes
            as $parte
        ) {

            if ($parte === '') {

                continue;
            }


            $iniciales .=
                mb_substr(
                    $parte,
                    0,
                    1,
                    'UTF-8'
                );


            if (
                mb_strlen(
                    $iniciales,
                    'UTF-8'
                ) >= 2
            ) {

                break;
            }

        }


        return mb_strtoupper(
            $iniciales,
            'UTF-8'
        );
    }

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
             FECHA INICIAL
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-registro-inicio">
                Fecha inicial
            </label>

            <input
                type="date"
                id="dashboard-fecha-registro-inicio"
                name="fecha_registro_inicio"
            >

        </div>


        <!-- =================================================
             FECHA FINAL
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label for="dashboard-fecha-registro-fin">
                Fecha final
            </label>

            <input
                type="date"
                id="dashboard-fecha-registro-fin"
                name="fecha_registro_fin"
            >

        </div>


        <!-- =================================================
             TIPO
        ================================================== -->

        <div class="dashboard-filtros__campo">

            <label id="dashboard-tipo-label">
                Tipo
            </label>

            <div
                class="dashboard-catalogo"
                data-dashboard-catalogo
            >

                <input
                    type="hidden"
                    id="dashboard-tipo"
                    name="tipo"
                    value=""
                    data-dashboard-catalogo-valor
                >


                <button
                    type="button"
                    class="dashboard-catalogo__selector"
                    data-dashboard-catalogo-selector
                    aria-expanded="false"
                    aria-haspopup="listbox"
                    aria-labelledby="
                        dashboard-tipo-label
                        dashboard-tipo-texto
                    "
                >

                    <span class="dashboard-catalogo__selector-contenido">

                        <span
                            class="
                                dashboard-catalogo__avatar
                                dashboard-catalogo__avatar--selector
                            "
                            data-dashboard-catalogo-avatar
                        >
                            T
                        </span>

                        <span class="dashboard-catalogo__selector-datos">

                            <strong
                                id="dashboard-tipo-texto"
                                data-dashboard-catalogo-texto
                            >
                                Todos
                            </strong>

                            <small data-dashboard-catalogo-descripcion>
                                Quejas y felicitaciones
                            </small>

                        </span>

                    </span>

                    <svg
                        class="dashboard-catalogo__flecha"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="m8 10 4 4 4-4" />
                    </svg>

                </button>


                <div
                    class="dashboard-catalogo__resultados"
                    data-dashboard-catalogo-resultados
                    role="listbox"
                    hidden
                >

                    <button
                        type="button"
                        class="
                            dashboard-catalogo__item
                            dashboard-catalogo__item--activo
                        "
                        data-dashboard-catalogo-opcion
                        data-value=""
                        data-texto="Todos"
                        data-descripcion="Quejas y felicitaciones"
                        data-avatar="T"
                        role="option"
                        aria-selected="true"
                    >

                        <span class="dashboard-catalogo__avatar">
                            T
                        </span>

                        <span class="dashboard-catalogo__datos">

                            <strong>
                                Todos
                            </strong>

                            <small>
                                Quejas y felicitaciones
                            </small>

                        </span>

                    </button>


                    <button
                        type="button"
                        class="dashboard-catalogo__item"
                        data-dashboard-catalogo-opcion
                        data-value="QUEJA"
                        data-texto="Quejas"
                        data-descripcion="Mostrar únicamente quejas"
                        data-avatar="Q"
                        role="option"
                        aria-selected="false"
                    >

                        <span class="dashboard-catalogo__avatar">
                            Q
                        </span>

                        <span class="dashboard-catalogo__datos">

                            <strong>
                                Quejas
                            </strong>

                            <small>
                                Mostrar únicamente quejas
                            </small>

                        </span>

                    </button>


                    <button
                        type="button"
                        class="dashboard-catalogo__item"
                        data-dashboard-catalogo-opcion
                        data-value="FELICITACION"
                        data-texto="Felicitaciones"
                        data-descripcion="Mostrar únicamente felicitaciones"
                        data-avatar="F"
                        role="option"
                        aria-selected="false"
                    >

                        <span class="dashboard-catalogo__avatar">
                            F
                        </span>

                        <span class="dashboard-catalogo__datos">

                            <strong>
                                Felicitaciones
                            </strong>

                            <small>
                                Mostrar únicamente felicitaciones
                            </small>

                        </span>

                    </button>

                </div>

            </div>

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

                    <label id="dashboard-estado-label">
                        Estado
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-estado"
                            name="estado"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                            aria-haspopup="listbox"
                            aria-labelledby="
                                dashboard-estado-label
                                dashboard-estado-texto
                            "
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong
                                        id="dashboard-estado-texto"
                                        data-dashboard-catalogo-texto
                                    >
                                        Todos
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Mostrar todos los estados
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            role="listbox"
                            hidden
                        >

                            <?php
                            $estadosFiltro = [
                                [
                                    'valor' => '',
                                    'texto' => 'Todos',
                                    'descripcion' => 'Mostrar todos los estados',
                                    'avatar' => 'T',
                                ],
                                [
                                    'valor' => 'Pendiente',
                                    'texto' => 'Pendiente',
                                    'descripcion' => 'Quejas pendientes de atención',
                                    'avatar' => 'P',
                                ],
                                [
                                    'valor' => 'En proceso',
                                    'texto' => 'En proceso',
                                    'descripcion' => 'Quejas actualmente en seguimiento',
                                    'avatar' => 'E',
                                ],
                                [
                                    'valor' => 'Finalizado',
                                    'texto' => 'Finalizado',
                                    'descripcion' => 'Quejas con proceso concluido',
                                    'avatar' => 'F',
                                ],
                            ];
                            ?>


                            <?php foreach (
                                $estadosFiltro
                                as $indice => $estadoFiltro
                            ): ?>

                                <button
                                    type="button"
                                    class="
                                        dashboard-catalogo__item
                                        <?= $indice === 0
                                            ? 'dashboard-catalogo__item--activo'
                                            : '' ?>
                                    "
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($estadoFiltro['valor']) ?>"
                                    data-texto="<?= esc($estadoFiltro['texto']) ?>"
                                    data-descripcion="<?= esc($estadoFiltro['descripcion']) ?>"
                                    data-avatar="<?= esc($estadoFiltro['avatar']) ?>"
                                    role="option"
                                    aria-selected="<?= $indice === 0 ? 'true' : 'false' ?>"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($estadoFiltro['avatar']) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($estadoFiltro['texto']) ?>
                                        </strong>

                                        <small>
                                            <?= esc($estadoFiltro['descripcion']) ?>
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     CLASIFICACIÓN
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label id="dashboard-clasificacion-label">
                        Clasificación
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-clasificacion"
                            name="clasificacion"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                            aria-haspopup="listbox"
                            aria-labelledby="
                                dashboard-clasificacion-label
                                dashboard-clasificacion-texto
                            "
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong
                                        id="dashboard-clasificacion-texto"
                                        data-dashboard-catalogo-texto
                                    >
                                        Todas
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Todas las clasificaciones
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            role="listbox"
                            hidden
                        >

                            <button
                                type="button"
                                class="
                                    dashboard-catalogo__item
                                    dashboard-catalogo__item--activo
                                "
                                data-dashboard-catalogo-opcion
                                data-value=""
                                data-texto="Todas"
                                data-descripcion="Todas las clasificaciones"
                                data-avatar="T"
                                role="option"
                                aria-selected="true"
                            >

                                <span class="dashboard-catalogo__avatar">
                                    T
                                </span>

                                <span class="dashboard-catalogo__datos">

                                    <strong>
                                        Todas
                                    </strong>

                                    <small>
                                        Todas las clasificaciones
                                    </small>

                                </span>

                            </button>


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


                                $avatarClasificacion =
                                    dashboardFiltroIniciales(
                                        $textoClasificacion
                                    );

                                ?>


                                <?php if (
                                    $valorClasificacion !== ''
                                ): ?>

                                    <button
                                        type="button"
                                        class="dashboard-catalogo__item"
                                        data-dashboard-catalogo-opcion
                                        data-value="<?= esc($valorClasificacion) ?>"
                                        data-texto="<?= esc($textoClasificacion) ?>"
                                        data-descripcion="Clasificación del reporte"
                                        data-avatar="<?= esc($avatarClasificacion) ?>"
                                        role="option"
                                        aria-selected="false"
                                    >

                                        <span class="dashboard-catalogo__avatar">
                                            <?= esc($avatarClasificacion) ?>
                                        </span>

                                        <span class="dashboard-catalogo__datos">

                                            <strong>
                                                <?= esc($textoClasificacion) ?>
                                            </strong>

                                            <small>
                                                Clasificación del reporte
                                            </small>

                                        </span>

                                    </button>

                                <?php endif; ?>


                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     SEGUIMIENTO
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label id="dashboard-seguimiento-label">
                        Seguimiento
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-seguimiento"
                            name="seguimiento"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todos
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Con y sin seguimiento
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <?php
                            $seguimientosFiltro = [
                                [
                                    'valor' => '',
                                    'texto' => 'Todos',
                                    'descripcion' => 'Con y sin seguimiento',
                                    'avatar' => 'T',
                                ],
                                [
                                    'valor' => 'con',
                                    'texto' => 'Con seguimiento',
                                    'descripcion' => 'Reportes con seguimiento registrado',
                                    'avatar' => 'CS',
                                ],
                                [
                                    'valor' => 'sin',
                                    'texto' => 'Sin seguimiento',
                                    'descripcion' => 'Reportes sin seguimiento registrado',
                                    'avatar' => 'SS',
                                ],
                            ];
                            ?>


                            <?php foreach (
                                $seguimientosFiltro
                                as $indice => $opcion
                            ): ?>

                                <button
                                    type="button"
                                    class="
                                        dashboard-catalogo__item
                                        <?= $indice === 0
                                            ? 'dashboard-catalogo__item--activo'
                                            : '' ?>
                                    "
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($opcion['valor']) ?>"
                                    data-texto="<?= esc($opcion['texto']) ?>"
                                    data-descripcion="<?= esc($opcion['descripcion']) ?>"
                                    data-avatar="<?= esc($opcion['avatar']) ?>"
                                    aria-selected="<?= $indice === 0 ? 'true' : 'false' ?>"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($opcion['avatar']) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($opcion['texto']) ?>
                                        </strong>

                                        <small>
                                            <?= esc($opcion['descripcion']) ?>
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     QUEJA ANÓNIMA
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label>
                        Queja anónima
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-anonima"
                            name="es_anonimo"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todas
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Anónimas y no anónimas
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <?php
                            $anonimasFiltro = [
                                [
                                    'valor' => '',
                                    'texto' => 'Todas',
                                    'descripcion' => 'Anónimas y no anónimas',
                                    'avatar' => 'T',
                                ],
                                [
                                    'valor' => '1',
                                    'texto' => 'Sí',
                                    'descripcion' => 'Únicamente quejas anónimas',
                                    'avatar' => 'S',
                                ],
                                [
                                    'valor' => '0',
                                    'texto' => 'No',
                                    'descripcion' => 'Únicamente quejas identificadas',
                                    'avatar' => 'N',
                                ],
                            ];
                            ?>


                            <?php foreach (
                                $anonimasFiltro
                                as $indice => $opcion
                            ): ?>

                                <button
                                    type="button"
                                    class="
                                        dashboard-catalogo__item
                                        <?= $indice === 0
                                            ? 'dashboard-catalogo__item--activo'
                                            : '' ?>
                                    "
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($opcion['valor']) ?>"
                                    data-texto="<?= esc($opcion['texto']) ?>"
                                    data-descripcion="<?= esc($opcion['descripcion']) ?>"
                                    data-avatar="<?= esc($opcion['avatar']) ?>"
                                    aria-selected="<?= $indice === 0 ? 'true' : 'false' ?>"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($opcion['avatar']) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($opcion['texto']) ?>
                                        </strong>

                                        <small>
                                            <?= esc($opcion['descripcion']) ?>
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

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

                    <label>
                        Zona
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-zona"
                            name="zona"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todas
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Todas las zonas
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <button
                                type="button"
                                class="
                                    dashboard-catalogo__item
                                    dashboard-catalogo__item--activo
                                "
                                data-dashboard-catalogo-opcion
                                data-value=""
                                data-texto="Todas"
                                data-descripcion="Todas las zonas"
                                data-avatar="T"
                                aria-selected="true"
                            >

                                <span class="dashboard-catalogo__avatar">
                                    T
                                </span>

                                <span class="dashboard-catalogo__datos">

                                    <strong>
                                        Todas
                                    </strong>

                                    <small>
                                        Todas las zonas
                                    </small>

                                </span>

                            </button>


                            <?php foreach (
                                $zonasFiltro
                                as $zona
                            ): ?>

                                <button
                                    type="button"
                                    class="dashboard-catalogo__item"
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($zona['valor']) ?>"
                                    data-texto="<?= esc($zona['texto']) ?>"
                                    data-descripcion="<?= esc($zona['descripcion']) ?>"
                                    data-avatar="<?= esc($zona['avatar']) ?>"
                                    aria-selected="false"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($zona['avatar']) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($zona['texto']) ?>
                                        </strong>

                                        <small>
                                            <?= esc($zona['descripcion']) ?>
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     SECTOR
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label>
                        Sector
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-sector"
                            name="sector"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todos
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Todos los sectores
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <button
                                type="button"
                                class="
                                    dashboard-catalogo__item
                                    dashboard-catalogo__item--activo
                                "
                                data-dashboard-catalogo-opcion
                                data-value=""
                                data-texto="Todos"
                                data-descripcion="Todos los sectores"
                                data-avatar="T"
                                aria-selected="true"
                            >

                                <span class="dashboard-catalogo__avatar">
                                    T
                                </span>

                                <span class="dashboard-catalogo__datos">

                                    <strong>
                                        Todos
                                    </strong>

                                    <small>
                                        Todos los sectores
                                    </small>

                                </span>

                            </button>


                            <?php foreach (
                                $sectoresFiltro
                                as $sector
                            ): ?>

                                <?php

                                preg_match(
                                    '/([0-9]+)/',
                                    $sector,
                                    $coincidenciaSector
                                );


                                $numeroSector =
                                    $coincidenciaSector[1]
                                    ?? '';


                                $avatarSector =
                                    $numeroSector !== ''
                                    ? $numeroSector
                                    : 'S';

                                ?>


                                <button
                                    type="button"
                                    class="dashboard-catalogo__item"
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($sector) ?>"
                                    data-texto="<?= esc($sector) ?>"
                                    data-descripcion="Sector institucional"
                                    data-avatar="<?= esc($avatarSector) ?>"
                                    aria-selected="false"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($avatarSector) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($sector) ?>
                                        </strong>

                                        <small>
                                            Sector institucional
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     TURNO
                ========================================== -->

                <div class="dashboard-filtros__campo">

                    <label>
                        Turno
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-turno"
                            name="turno"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todos
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Todos los turnos
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <button
                                type="button"
                                class="
                                    dashboard-catalogo__item
                                    dashboard-catalogo__item--activo
                                "
                                data-dashboard-catalogo-opcion
                                data-value=""
                                data-texto="Todos"
                                data-descripcion="Todos los turnos"
                                data-avatar="T"
                                aria-selected="true"
                            >

                                <span class="dashboard-catalogo__avatar">
                                    T
                                </span>

                                <span class="dashboard-catalogo__datos">

                                    <strong>
                                        Todos
                                    </strong>

                                    <small>
                                        Todos los turnos
                                    </small>

                                </span>

                            </button>


                            <?php foreach (
                                $turnosFiltro
                                as $turno
                            ): ?>

                                <?php

                                $avatarTurno =
                                    dashboardFiltroIniciales(
                                        $turno
                                    );

                                ?>


                                <button
                                    type="button"
                                    class="dashboard-catalogo__item"
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($turno) ?>"
                                    data-texto="<?= esc($turno) ?>"
                                    data-descripcion="Turno del personal involucrado"
                                    data-avatar="<?= esc($avatarTurno) ?>"
                                    aria-selected="false"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($avatarTurno) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($turno) ?>
                                        </strong>

                                        <small>
                                            Turno del personal involucrado
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

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

                    <label>
                        Área
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-area-personal"
                            name="area_personal"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todas
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Todas las áreas
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <button
                                type="button"
                                class="
                                    dashboard-catalogo__item
                                    dashboard-catalogo__item--activo
                                "
                                data-dashboard-catalogo-opcion
                                data-value=""
                                data-texto="Todas"
                                data-descripcion="Todas las áreas"
                                data-avatar="T"
                                aria-selected="true"
                            >

                                <span class="dashboard-catalogo__avatar">
                                    T
                                </span>

                                <span class="dashboard-catalogo__datos">

                                    <strong>
                                        Todas
                                    </strong>

                                    <small>
                                        Todas las áreas
                                    </small>

                                </span>

                            </button>


                            <?php foreach (
                                $areasFiltro
                                as $area
                            ): ?>

                                <?php

                                $area =
                                    trim(
                                        (string) $area
                                    );


                                if ($area === '') {

                                    continue;
                                }


                                $avatarArea =
                                    dashboardFiltroIniciales(
                                        $area
                                    );

                                ?>


                                <button
                                    type="button"
                                    class="dashboard-catalogo__item"
                                    data-dashboard-catalogo-opcion
                                    data-value="<?= esc($area) ?>"
                                    data-texto="<?= esc($area) ?>"
                                    data-descripcion="Área del personal involucrado"
                                    data-avatar="<?= esc($avatarArea) ?>"
                                    aria-selected="false"
                                >

                                    <span class="dashboard-catalogo__avatar">
                                        <?= esc($avatarArea) ?>
                                    </span>

                                    <span class="dashboard-catalogo__datos">

                                        <strong>
                                            <?= esc($area) ?>
                                        </strong>

                                        <small>
                                            Área del personal involucrado
                                        </small>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     PERSONAL

                     Sigue siendo buscador.
                ========================================== -->

                <div
                    class="
                        dashboard-filtros__campo
                        dashboard-filtros__campo--personal
                    "
                >

                    <label for="dashboard-personal-busqueda">
                        Personal
                    </label>

                    <div class="dashboard-filtros__personal">

                        <input
                            type="search"
                            id="dashboard-personal-busqueda"
                            placeholder="Buscar por nombre o nómina..."
                            autocomplete="off"
                        >

                        <input
                            type="hidden"
                            id="dashboard-personal"
                            name="personal"
                            value=""
                        >

                        <div
                            class="dashboard-filtros__personal-resultados"
                            id="dashboard-personal-resultados"
                            hidden
                        ></div>

                        <div
                            class="dashboard-filtros__personal-seleccion"
                            id="dashboard-personal-seleccion"
                            hidden
                        >

                            <span
                                id="dashboard-personal-seleccion-texto"
                            ></span>

                            <button
                                type="button"
                                id="dashboard-personal-quitar"
                                aria-label="Quitar personal seleccionado"
                            >
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

                    <label>
                        Unidad involucrada
                    </label>

                    <div
                        class="dashboard-catalogo"
                        data-dashboard-catalogo
                    >

                        <input
                            type="hidden"
                            id="dashboard-unidad"
                            name="unidad"
                            value=""
                            data-dashboard-catalogo-valor
                        >


                        <button
                            type="button"
                            class="dashboard-catalogo__selector"
                            data-dashboard-catalogo-selector
                            aria-expanded="false"
                        >

                            <span class="dashboard-catalogo__selector-contenido">

                                <span
                                    class="
                                        dashboard-catalogo__avatar
                                        dashboard-catalogo__avatar--selector
                                    "
                                    data-dashboard-catalogo-avatar
                                >
                                    T
                                </span>

                                <span class="dashboard-catalogo__selector-datos">

                                    <strong data-dashboard-catalogo-texto>
                                        Todas
                                    </strong>

                                    <small data-dashboard-catalogo-descripcion>
                                        Todas las unidades
                                    </small>

                                </span>

                            </span>

                            <svg
                                class="dashboard-catalogo__flecha"
                                viewBox="0 0 24 24"
                            >
                                <path d="m8 10 4 4 4-4" />
                            </svg>

                        </button>


                        <div
                            class="dashboard-catalogo__resultados"
                            data-dashboard-catalogo-resultados
                            hidden
                        >

                            <button
                                type="button"
                                class="
                                    dashboard-catalogo__item
                                    dashboard-catalogo__item--activo
                                "
                                data-dashboard-catalogo-opcion
                                data-value=""
                                data-texto="Todas"
                                data-descripcion="Todas las unidades"
                                data-avatar="T"
                                aria-selected="true"
                            >

                                <span class="dashboard-catalogo__avatar">
                                    T
                                </span>

                                <span class="dashboard-catalogo__datos">

                                    <strong>
                                        Todas
                                    </strong>

                                    <small>
                                        Todas las unidades
                                    </small>

                                </span>

                            </button>


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


                                $avatarUnidad =
                                    dashboardFiltroIniciales(
                                        $valorUnidad
                                    );

                                ?>


                                <?php if (
                                    $valorUnidad !== ''
                                ): ?>

                                    <button
                                        type="button"
                                        class="dashboard-catalogo__item"
                                        data-dashboard-catalogo-opcion
                                        data-value="<?= esc($valorUnidad) ?>"
                                        data-texto="<?= esc($textoUnidad) ?>"
                                        data-descripcion="Unidad relacionada con el reporte"
                                        data-avatar="<?= esc($avatarUnidad) ?>"
                                        aria-selected="false"
                                    >

                                        <span class="dashboard-catalogo__avatar">
                                            <?= esc($avatarUnidad) ?>
                                        </span>

                                        <span class="dashboard-catalogo__datos">

                                            <strong>
                                                <?= esc($textoUnidad) ?>
                                            </strong>

                                            <small>
                                                Unidad relacionada con el reporte
                                            </small>

                                        </span>

                                    </button>

                                <?php endif; ?>


                            <?php endforeach; ?>

                        </div>

                    </div>

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