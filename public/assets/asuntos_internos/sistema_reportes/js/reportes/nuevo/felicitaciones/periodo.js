/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Periodo
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarPeriodoFelicitaciones();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarPeriodoFelicitaciones() {

    const fechaInicio =
        document.querySelector(
            '#felicitaciones-fecha-inicio'
        );


    const fechaFin =
        document.querySelector(
            '#felicitaciones-fecha-fin'
        );


    const botonAplicar =
        document.querySelector(
            '#btn-aplicar-periodo-felicitaciones'
        );


    const botonLimpiar =
        document.querySelector(
            '#btn-limpiar-filtros-felicitaciones'
        );


    if (
        !fechaInicio
        || !fechaFin
        || !botonAplicar
        || !botonLimpiar
    ) {
        return;
    }


    /* =====================================================
       APLICAR PERIODO
    ===================================================== */

    botonAplicar.addEventListener(
        'click',
        () => {

            const inicio =
                fechaInicio.value;


            const fin =
                fechaFin.value;


            limpiarErroresPeriodoFelicitaciones();


            if (
                !inicio
                && !fin
            ) {

                mostrarErrorPeriodoFelicitaciones(
                    'Selecciona al menos una fecha para realizar la consulta.'
                );

                return;
            }


            if (
                inicio
                && fin
                && inicio > fin
            ) {

                mostrarErrorPeriodoFelicitaciones(
                    'La fecha inicial no puede ser posterior a la fecha final.'
                );

                return;
            }


            const detalle = {

                fechaInicio:
                    inicio,

                fechaFin:
                    fin,

            };


            document.dispatchEvent(
                new CustomEvent(
                    'periodoFelicitacionesAplicado',
                    {
                        detail:
                            detalle,
                    }
                )
            );


            mostrarPeriodoSeleccionadoFelicitaciones(
                inicio,
                fin
            );
        }
    );


    /* =====================================================
       LIMPIAR TODOS LOS FILTROS
    ===================================================== */

    botonLimpiar.addEventListener(
        'click',
        () => {

            limpiarTodosLosFiltrosFelicitaciones(
                fechaInicio,
                fechaFin
            );
        }
    );
}


/* =========================================================
   LIMPIAR TODOS LOS FILTROS
========================================================= */

function limpiarTodosLosFiltrosFelicitaciones(
    fechaInicio,
    fechaFin
) {

    /* =====================================================
       PERIODO
    ===================================================== */

    fechaInicio.value =
        '';


    fechaFin.value =
        '';


    limpiarErroresPeriodoFelicitaciones();


    const contenedor =
        document.querySelector(
            '.felicitaciones-page .reportes-periodo'
        );


    const resultadoPeriodo =
        contenedor?.querySelector(
            '[data-periodo-resultado]'
        );


    resultadoPeriodo?.remove();


    /* =====================================================
       BUSCADOR GENERAL
    ===================================================== */

    const buscador =
        document.querySelector(
            '#filtro_felicitaciones_busqueda'
        );


    if (buscador) {

        buscador.value =
            '';
    }


    /* =====================================================
       SELECTS
    ===================================================== */

    const selects = [

        document.querySelector(
            '#filtro_felicitaciones_sector'
        ),

        document.querySelector(
            '#filtro_felicitaciones_area'
        ),

        document.querySelector(
            '#filtro_felicitaciones_turno'
        ),

    ];


    selects.forEach(
        (select) => {

            if (!select) {
                return;
            }


            select.selectedIndex =
                0;
        }
    );


    /* =====================================================
       INFORMAR QUE YA NO HAY PERIODO
    ===================================================== */

    document.dispatchEvent(
        new CustomEvent(
            'periodoFelicitacionesAplicado',
            {
                detail: {

                    fechaInicio:
                        '',

                    fechaFin:
                        '',

                },
            }
        )
    );


    /* =====================================================
       EVENTO GLOBAL DE FELICITACIONES
    ===================================================== */

    document.dispatchEvent(
        new CustomEvent(
            'filtrosFelicitacionesLimpiados'
        )
    );
}


/* =========================================================
   MOSTRAR PERIODO SELECCIONADO
========================================================= */

function mostrarPeriodoSeleccionadoFelicitaciones(
    fechaInicio,
    fechaFin
) {

    const contenedor =
        document.querySelector(
            '.felicitaciones-page .reportes-periodo'
        );


    if (!contenedor) {
        return;
    }


    let mensaje =
        contenedor.querySelector(
            '[data-periodo-resultado]'
        );


    if (!mensaje) {

        mensaje =
            document.createElement(
                'div'
            );


        mensaje.className =
            'reportes-periodo__resultado';


        mensaje.dataset.periodoResultado =
            '';


        const body =
            contenedor.querySelector(
                '.reportes-periodo__body'
            );


        body?.after(
            mensaje
        );
    }


    if (
        fechaInicio
        && fechaFin
    ) {

        mensaje.textContent =
            `Periodo seleccionado: `
            + `${formatearFechaFelicitaciones(fechaInicio)} al `
            + `${formatearFechaFelicitaciones(fechaFin)}`;

        return;
    }


    if (fechaInicio) {

        mensaje.textContent =
            `Desde: ${formatearFechaFelicitaciones(fechaInicio)}`;

        return;
    }


    mensaje.textContent =
        `Hasta: ${formatearFechaFelicitaciones(fechaFin)}`;
}


/* =========================================================
   ERROR
========================================================= */

function mostrarErrorPeriodoFelicitaciones(
    mensaje
) {

    const contenedor =
        document.querySelector(
            '.felicitaciones-page .reportes-periodo'
        );


    if (!contenedor) {
        return;
    }


    let error =
        contenedor.querySelector(
            '[data-periodo-error]'
        );


    if (!error) {

        error =
            document.createElement(
                'div'
            );


        error.className =
            'reportes-periodo__error';


        error.dataset.periodoError =
            '';


        const body =
            contenedor.querySelector(
                '.reportes-periodo__body'
            );


        body?.after(
            error
        );
    }


    error.textContent =
        mensaje;
}


/* =========================================================
   LIMPIAR ERROR
========================================================= */

function limpiarErroresPeriodoFelicitaciones() {

    const contenedor =
        document.querySelector(
            '.felicitaciones-page .reportes-periodo'
        );


    const error =
        contenedor?.querySelector(
            '[data-periodo-error]'
        );


    error?.remove();
}


/* =========================================================
   FORMATEAR FECHA
========================================================= */

function formatearFechaFelicitaciones(
    fecha
) {

    if (!fecha) {
        return '';
    }


    const partes =
        fecha.split(
            '-'
        );


    if (
        partes.length !== 3
    ) {
        return fecha;
    }


    const [
        anio,
        mes,
        dia,
    ] =
        partes;


    return `${dia}/${mes}/${anio}`;
}