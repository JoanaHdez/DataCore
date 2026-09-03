/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Filtros
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarFiltrosReportes();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarFiltrosReportes() {

    const tabla =
        document.querySelector(
            '.reportes-tabla__table'
        );


    const tbody =
        document.querySelector(
            '#tabla-reportes-body'
        );


    const inputBusqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


    const filtroSector =
        document.querySelector(
            '#filtro_sector'
        );


    const filtroArea =
        document.querySelector(
            '#filtro_area'
        );


    const filtroTurno =
        document.querySelector(
            '#filtro_turno'
        );


    const filtroResolucion =
        document.querySelector(
            '#filtro_resolucion'
        );


    const botonLimpiar =
        document.querySelector(
            '#btn-limpiar-filtros'
        );


    if (
        !tabla
        || !tbody
        || !inputBusqueda
        || !filtroSector
        || !filtroArea
        || !filtroTurno
        || !filtroResolucion
        || !botonLimpiar
    ) {

        return;

    }


    /* =====================================================
       ESTADO ACTUAL DEL PERIODO
    ===================================================== */

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
                        'reportes-tabla__empty'
                    )
                    && !fila.classList.contains(
                        'reportes-tabla__empty--filtros'
                    )
                );

            }
        );

    };


    /* =====================================================
       CARGAR OPCIONES DINÁMICAS

       Sector:
       viene desde PHP / base de datos.

       Resolución:
       tiene opciones fijas desde PHP.

       Área y Turno:
       se obtienen de los reportes existentes.
    ===================================================== */

    cargarOpcionesFiltros(
        obtenerFilas(),
        {
            area:
                filtroArea,

            turno:
                filtroTurno,
        }
    );


    /* =====================================================
       FUNCIÓN CENTRAL
       APLICAR TODOS LOS FILTROS
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


        const resolucion =
            normalizarTexto(
                filtroResolucion.value
            );


        let visibles =
            0;


        filas.forEach(
            (fila) => {

                const celdas =
                    fila.querySelectorAll(
                        'td'
                    );


                if (
                    celdas.length < 8
                ) {

                    fila.hidden =
                        true;

                    return;

                }


                /* =============================================
                   DATOS DE LA FILA
                ============================================== */

                const fechaQueja =
                    convertirFechaTabla(
                        celdas[1]
                            .textContent
                            .trim()
                    );


                const areaTexto =
                    String(
                        celdas[5]
                            .textContent
                        || ''
                    );


                const areaFila =
                    normalizarTexto(
                        areaTexto
                    );


                /*
                 * Un reporte puede contener más de una persona
                 * involucrada.
                 *
                 * Por ello obtenemos todos los sectores
                 * encontrados dentro del área mostrada.
                 */
                const sectoresFila =
                    obtenerSectoresDesdeArea(
                        areaTexto
                    );


                const turnoFila =
                    normalizarTexto(
                        celdas[6]
                            .textContent
                    );


                const resolucionFila =
                    normalizarTexto(
                        celdas[7]
                            .textContent
                    );


                /*
                 * La búsqueda general revisa
                 * toda la información visible
                 * de la fila.
                 */

                const textoFila =
                    normalizarTexto(
                        fila.textContent
                    );


                /* =============================================
                   VALIDACIONES
                ============================================== */

                const coincideBusqueda =
                    !busqueda
                    || textoFila.includes(
                        busqueda
                    );


                const coincideSector =
                    !sector
                    || sectoresFila.includes(
                        sector
                    );


                const coincideArea =
                    !area
                    || areaFila
                    === area;


                const coincideTurno =
                    !turno
                    || turnoFila
                    === turno;


                const coincideResolucion =
                    !resolucion
                    || resolucionFila
                    === resolucion;


                const coincidePeriodo =
                    validarPeriodoFila(
                        fechaQueja,
                        periodo.inicio,
                        periodo.fin
                    );


                const mostrar =
                    coincideBusqueda
                    && coincideSector
                    && coincideArea
                    && coincideTurno
                    && coincideResolucion
                    && coincidePeriodo;


                fila.hidden =
                    !mostrar;


                if (mostrar) {

                    visibles++;

                }

            }
        );


        /* =================================================
           ESTADO VACÍO
        ================================================= */

        actualizarEstadoVacio(
            tbody,
            visibles
        );


        /* =================================================
           NOTIFICAR AL RESTO DE MÓDULOS
        ================================================= */

        document.dispatchEvent(
            new CustomEvent(
                'reportesFiltradosActualizados',
                {

                    detail: {

                        total:
                            visibles,

                        filas:
                            obtenerFilas()
                                .filter(
                                    (fila) =>
                                        !fila.hidden
                                ),

                    },

                }
            )
        );

    };


    /* =====================================================
       BÚSQUEDA GENERAL
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


    filtroResolucion.addEventListener(
        'change',
        aplicarFiltros
    );


    /* =====================================================
       PERIODO
    ===================================================== */

    document.addEventListener(
        'periodoReportesAplicado',
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
       LIMPIAR FILTROS
    ===================================================== */

    botonLimpiar.addEventListener(
        'click',
        (evento) => {

            evento.preventDefault();


            /* =============================================
               BÚSQUEDA
            ============================================== */

            inputBusqueda.value =
                '';


            /* =============================================
               SELECTS
            ============================================== */

            filtroSector.selectedIndex =
                0;


            filtroArea.selectedIndex =
                0;


            filtroTurno.selectedIndex =
                0;


            filtroResolucion.selectedIndex =
                0;


            /* =============================================
               PERIODO INTERNO
            ============================================== */

            periodo.inicio =
                '';


            periodo.fin =
                '';


            /* =============================================
               CAMPOS DEL PERIODO
            ============================================== */

            const fechaInicio =
                document.querySelector(
                    '#fecha_inicio'
                );


            const fechaFin =
                document.querySelector(
                    '#fecha_fin'
                );


            if (fechaInicio) {

                fechaInicio.value =
                    '';

            }


            if (fechaFin) {

                fechaFin.value =
                    '';

            }


            /* =============================================
               MENSAJES DEL PERIODO
            ============================================== */

            document
                .querySelector(
                    '[data-periodo-resultado]'
                )
                ?.remove();


            document
                .querySelector(
                    '[data-periodo-error]'
                )
                ?.remove();


            /* =============================================
               MOSTRAR NUEVAMENTE LOS REGISTROS
            ============================================== */

            aplicarFiltros();

        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    aplicarFiltros();

}


/* =========================================================
   CARGAR OPCIONES DE FILTROS

   Área y Turno se generan a partir de los reportes
   existentes en la tabla.

   Sector viene desde la BD mediante PHP.

   Resolución tiene opciones fijas en PHP.
========================================================= */

function cargarOpcionesFiltros(
    filas,
    filtros
) {

    const areas =
        new Set();


    const turnos =
        new Set();


    filas.forEach(
        (fila) => {

            const celdas =
                fila.querySelectorAll(
                    'td'
                );


            if (
                celdas.length < 8
            ) {

                return;

            }


            agregarValorSet(
                areas,
                celdas[5].textContent
            );


            agregarValorSet(
                turnos,
                celdas[6].textContent
            );

        }
    );


    agregarOpcionesSelect(
        filtros.area,
        areas
    );


    agregarOpcionesSelect(
        filtros.turno,
        turnos
    );

}


/* =========================================================
   OBTENER SECTORES DESDE EL ÁREA DEL PERSONAL

   Detecta valores como:

   SECTOR 1
   SECTOR 01
   SECTOR 05 CAMPESTRE

   Si un reporte tiene personal de distintos sectores,
   conserva todos los sectores encontrados.
========================================================= */

function obtenerSectoresDesdeArea(
    texto
) {

    const valor =
        String(
            texto || ''
        )
            .toUpperCase()
            .normalize(
                'NFD'
            )
            .replace(
                /[\u0300-\u036f]/g,
                ''
            );


    const sectores =
        [];


    const expresion =
        /SECTOR\s+0*([0-9]+)/g;


    let coincidencia;


    while (
        (
            coincidencia =
                expresion.exec(
                    valor
                )
        ) !== null
    ) {

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

            continue;

        }


        const sector =
            normalizarSector(
                `SECTOR ${numero}`
            );


        if (
            sector
            && !sectores.includes(
                sector
            )
        ) {

            sectores.push(
                sector
            );

        }

    }


    return sectores;

}


/* =========================================================
   NORMALIZAR SECTOR

   SECTOR 1  -> sector 01
   SECTOR 01 -> sector 01
========================================================= */

function normalizarSector(
    valor
) {

    const texto =
        normalizarTexto(
            valor
        );


    if (!texto) {

        return '';

    }


    const coincidencia =
        texto.match(
            /sector\s+0*([0-9]+)/
        );


    if (!coincidencia) {

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
   AGREGAR VALOR A SET
========================================================= */

function agregarValorSet(
    conjunto,
    valor
) {

    const texto =
        String(
            valor || ''
        ).trim();


    if (!texto) {

        return;

    }


    conjunto.add(
        texto
    );

}


/* =========================================================
   AGREGAR OPCIONES A SELECT
========================================================= */

function agregarOpcionesSelect(
    select,
    valores
) {

    if (!select) {

        return;

    }


    const lista =
        Array.from(
            valores
        )
            .sort(
                (a, b) =>
                    a.localeCompare(
                        b,
                        'es',
                        {

                            sensitivity:
                                'base',

                        }
                    )
            );


    lista.forEach(
        (valor) => {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                valor;


            option.textContent =
                valor;


            select.appendChild(
                option
            );

        }
    );

}


/* =========================================================
   VALIDAR PERIODO
========================================================= */

function validarPeriodoFila(
    fechaFila,
    fechaInicio,
    fechaFin
) {

    /*
     * Si no existe periodo,
     * todas las filas cumplen.
     */

    if (
        !fechaInicio
        && !fechaFin
    ) {

        return true;

    }


    /*
     * Si la fila no tiene una fecha válida,
     * no puede pertenecer al periodo.
     */

    if (!fechaFila) {

        return false;

    }


    if (
        fechaInicio
        && fechaFila < fechaInicio
    ) {

        return false;

    }


    if (
        fechaFin
        && fechaFila > fechaFin
    ) {

        return false;

    }


    return true;

}


/* =========================================================
   CONVERTIR FECHA DE TABLA
   dd/mm/aaaa → aaaa-mm-dd
========================================================= */

function convertirFechaTabla(
    fecha
) {

    if (!fecha) {

        return '';

    }


    const partes =
        fecha
            .trim()
            .split(
                '/'
            );


    if (
        partes.length !== 3
    ) {

        return '';

    }


    const [
        dia,
        mes,
        anio,
    ] =
        partes;


    return `${anio}-${mes}-${dia}`;

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
            '.reportes-tabla__empty--filtros'
        );


    if (
        totalVisible > 0
    ) {

        existente?.remove();

        return;

    }


    /*
     * Si la tabla ya viene vacía desde PHP,
     * no agregamos otro mensaje.
     */

    const vacioOriginal =
        tbody.querySelector(
            '.reportes-tabla__empty'
        );


    if (vacioOriginal) {

        return;

    }


    if (existente) {

        return;

    }


    const fila =
        document.createElement(
            'tr'
        );


    fila.className =
        'reportes-tabla__empty reportes-tabla__empty--filtros';


    fila.innerHTML = `

        <td colspan="9">

            <div class="reportes-tabla__empty-content">

                <strong>
                    No se encontraron reportes
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
        texto || ''
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