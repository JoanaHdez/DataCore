/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Filtros
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarFiltrosFelicitaciones();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarFiltrosFelicitaciones() {

    const tbody =
        document.querySelector(
            '#tabla-felicitaciones-body'
        );


    const inputBusqueda =
        document.querySelector(
            '#filtro_felicitaciones_busqueda'
        );


    const filtroSector =
        document.querySelector(
            '#filtro_felicitaciones_sector'
        );


    const filtroArea =
        document.querySelector(
            '#filtro_felicitaciones_area'
        );


    const filtroTurno =
        document.querySelector(
            '#filtro_felicitaciones_turno'
        );


    if (
        !tbody
        || !inputBusqueda
        || !filtroSector
        || !filtroArea
        || !filtroTurno
    ) {
        return;
    }


    const periodo = {

        inicio:
            '',

        fin:
            '',

    };


    /* =====================================================
       OBTENER FILAS REALES
    ===================================================== */

    const obtenerFilas = () => {

        return Array.from(
            tbody.querySelectorAll(
                'tr'
            )
        ).filter(
            (fila) => {

                return (
                    !fila.classList.contains(
                        'felicitaciones-tabla__empty'
                    )
                    && !fila.classList.contains(
                        'felicitaciones-tabla__empty--filtros'
                    )
                );
            }
        );
    };


    /* =====================================================
       APLICAR FILTROS
    ===================================================== */

    const aplicarFiltros = () => {

        const filas =
            obtenerFilas();


        const busqueda =
            normalizarTexto(
                inputBusqueda.value
            );


        const sector =
            normalizarSector(
                filtroSector.value
            );


        const area =
            normalizarTexto(
                filtroArea.value
            );


        const turno =
            normalizarTexto(
                filtroTurno.value
            );


        const filasVisibles =
            [];


        filas.forEach(
            (fila) => {

                /* =============================================
                   DATOS DE LA FELICITACIÓN
                ============================================= */

                const folio =
                    normalizarTexto(
                        fila.dataset.folio
                    );


                const fecha =
                    String(
                        fila.dataset.fecha
                        || ''
                    ).trim();


                const felicitante =
                    normalizarTexto(
                        fila.dataset.felicitante
                    );


                const personal =
                    normalizarTexto(
                        fila.dataset.personal
                    );


                const aliases =
                    normalizarTexto(
                        fila.dataset.aliases
                    );


                const areas =
                    separarValores(
                        fila.dataset.areas
                    );


                const turnos =
                    separarValores(
                        fila.dataset.turnos
                    );


                const sectores =
                    separarSectores(
                        fila.dataset.sectores
                    );


                /* =============================================
                   BÚSQUEDA GENERAL
                ============================================= */

                const textoBusqueda = [

                    folio,
                    felicitante,
                    personal,
                    aliases,

                ].join(
                    ' '
                );


                const coincideBusqueda =
                    !busqueda
                    || textoBusqueda.includes(
                        busqueda
                    );


                /* =============================================
                   SECTOR
                ============================================= */

                const coincideSector =
                    !sector
                    || sectores.includes(
                        sector
                    );


                /* =============================================
                   ÁREA
                ============================================= */

                const coincideArea =
                    !area
                    || areas.includes(
                        area
                    );


                /* =============================================
                   TURNO
                ============================================= */

                const coincideTurno =
                    !turno
                    || turnos.includes(
                        turno
                    );


                /* =============================================
                   PERIODO
                ============================================= */

                const coincidePeriodo =
                    validarPeriodo(
                        fecha,
                        periodo.inicio,
                        periodo.fin
                    );


                /* =============================================
                   RESULTADO FINAL
                ============================================= */

                const mostrar =
                    coincideBusqueda
                    && coincideSector
                    && coincideArea
                    && coincideTurno
                    && coincidePeriodo;


                fila.hidden =
                    !mostrar;


                if (
                    mostrar
                ) {

                    filasVisibles.push(
                        fila
                    );
                }
            }
        );


        /* =================================================
           ESTADO VACÍO
        ================================================= */

        actualizarEstadoVacio(
            tbody,
            filasVisibles.length
        );


        /* =================================================
           INFORMAR PAGINACIÓN / RESUMEN
        ================================================= */

        document.dispatchEvent(
            new CustomEvent(
                'felicitacionesFiltradasActualizadas',
                {
                    detail: {

                        total:
                            filasVisibles.length,

                        filas:
                            filasVisibles,

                    },
                }
            )
        );
    };


    /* =====================================================
       BUSCADOR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        aplicarFiltros
    );


    /* =====================================================
       SELECTS
    ===================================================== */

    filtroSector.addEventListener(
        'change',
        aplicarFiltros
    );


    filtroArea.addEventListener(
        'change',
        aplicarFiltros
    );


    filtroTurno.addEventListener(
        'change',
        aplicarFiltros
    );


    /* =====================================================
       PERIODO
    ===================================================== */

    document.addEventListener(
        'periodoFelicitacionesAplicado',
        (evento) => {

            periodo.inicio =
                evento.detail
                    ?.fechaInicio
                || '';


            periodo.fin =
                evento.detail
                    ?.fechaFin
                || '';


            aplicarFiltros();
        }
    );


    /* =====================================================
       LIMPIAR
    ===================================================== */

    document.addEventListener(
        'filtrosFelicitacionesLimpiados',
        () => {

            periodo.inicio =
                '';


            periodo.fin =
                '';


            aplicarFiltros();
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    aplicarFiltros();
}


/* =========================================================
   SEPARAR VALORES
========================================================= */

function separarValores(
    valor
) {

    return String(
        valor
        || ''
    )
        .split(
            '|'
        )
        .map(
            (item) =>
                normalizarTexto(
                    item
                )
        )
        .filter(
            Boolean
        );
}


/* =========================================================
   SEPARAR SECTORES
========================================================= */

function separarSectores(
    valor
) {

    return String(
        valor
        || ''
    )
        .split(
            '|'
        )
        .map(
            (item) =>
                normalizarSector(
                    item
                )
        )
        .filter(
            Boolean
        );
}


/* =========================================================
   NORMALIZAR SECTOR

   SECTOR 1
   SECTOR 01
        ↓
   sector 01
========================================================= */

function normalizarSector(
    valor
) {

    const texto =
        normalizarTexto(
            valor
        );


    if (
        !texto
    ) {
        return '';
    }


    const coincidencia =
        texto.match(
            /sector\s+0*([0-9]+)/
        );


    if (
        !coincidencia
    ) {
        return texto;
    }


    const numero =
        Number(
            coincidencia[1]
        );


    if (
        !Number.isInteger(
            numero
        )
        || numero <= 0
    ) {
        return texto;
    }


    return (
        'sector '
        + String(
            numero
        ).padStart(
            2,
            '0'
        )
    );
}


/* =========================================================
   VALIDAR PERIODO
========================================================= */

function validarPeriodo(
    fecha,
    fechaInicio,
    fechaFin
) {

    if (
        !fechaInicio
        && !fechaFin
    ) {
        return true;
    }


    if (
        !fecha
    ) {
        return false;
    }


    if (
        fechaInicio
        && fecha < fechaInicio
    ) {
        return false;
    }


    if (
        fechaFin
        && fecha > fechaFin
    ) {
        return false;
    }


    return true;
}


/* =========================================================
   ESTADO VACÍO
========================================================= */

function actualizarEstadoVacio(
    tbody,
    totalVisible
) {

    const existente =
        tbody.querySelector(
            '.felicitaciones-tabla__empty--filtros'
        );


    if (
        totalVisible > 0
    ) {

        existente?.remove();

        return;
    }


    const vacioOriginal =
        tbody.querySelector(
            '.felicitaciones-tabla__empty'
        );


    if (
        vacioOriginal
    ) {
        return;
    }


    if (
        existente
    ) {
        return;
    }


    const fila =
        document.createElement(
            'tr'
        );


    fila.className =
        'felicitaciones-tabla__empty felicitaciones-tabla__empty--filtros';


    fila.innerHTML = `
        <td colspan="5">

            <div class="felicitaciones-tabla__empty-content">

                <strong>
                    No se encontraron felicitaciones
                </strong>

                <span>
                    Intenta modificar los filtros de búsqueda.
                </span>

            </div>

        </td>
    `;


    tbody.appendChild(
        fila
    );
}


/* =========================================================
   NORMALIZAR TEXTO
========================================================= */

function normalizarTexto(
    texto
) {

    return String(
        texto
        || ''
    )
        .trim()
        .toLowerCase()
        .normalize(
            'NFD'
        )
        .replace(
            /[\u0300-\u036f]/g,
            ''
        );
}