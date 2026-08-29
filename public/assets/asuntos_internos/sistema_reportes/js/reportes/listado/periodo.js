document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarPeriodo();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarPeriodo() {

    const fechaInicio =
        document.querySelector(
            '#fecha_inicio'
        );


    const fechaFin =
        document.querySelector(
            '#fecha_fin'
        );


    const botonAplicar =
        document.querySelector(
            '#btn-aplicar-periodo'
        );


    const botonLimpiar =
        document.querySelector(
            '#btn-limpiar-filtros'
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


            limpiarErroresPeriodo();


            if (
                !inicio
                && !fin
            ) {

                mostrarErrorPeriodo(
                    'Selecciona al menos una fecha para realizar la consulta.'
                );


                return;
            }


            if (
                inicio
                && fin
                && inicio > fin
            ) {

                mostrarErrorPeriodo(
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
                    'periodoReportesAplicado',
                    {
                        detail:
                            detalle,
                    }
                )
            );


            mostrarPeriodoSeleccionado(
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

            limpiarTodosLosFiltros(
                fechaInicio,
                fechaFin
            );

        }
    );

}


/* =========================================================
   LIMPIAR TODOS LOS FILTROS
========================================================= */

function limpiarTodosLosFiltros(
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


    limpiarErroresPeriodo();


    const resultadoPeriodo =
        document.querySelector(
            '[data-periodo-resultado]'
        );


    resultadoPeriodo?.remove();


    /* =====================================================
       BUSCADOR GENERAL
    ===================================================== */

    const buscador =
        document.querySelector(
            '#filtro_busqueda'
        );


    if (buscador) {

        buscador.value =
            '';


        buscador.dispatchEvent(
            new Event(
                'input',
                {
                    bubbles:
                        true,
                }
            )
        );

    }


    /* =====================================================
       SELECTS DE FILTROS
    ===================================================== */

    const selects =
        document.querySelectorAll(
            `
            .reportes-filtros select,
            [data-filtro-reporte] select
            `
        );


    selects.forEach(
        (select) => {

            select.selectedIndex =
                0;


            select.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles:
                            true,
                    }
                )
            );

        }
    );


    /* =====================================================
       INPUTS DE FILTROS
    ===================================================== */

    const inputs =
        document.querySelectorAll(
            `
            .reportes-filtros
            input:not([type="button"]):not([type="submit"]),

            [data-filtro-reporte]
            input:not([type="button"]):not([type="submit"])
            `
        );


    inputs.forEach(
        (input) => {

            /*
             * El buscador ya fue limpiado arriba.
             */
            if (
                input.id ===
                'filtro_busqueda'
            ) {
                return;
            }


            switch (
                input.type
            ) {

                case 'checkbox':

                case 'radio':

                    input.checked =
                        false;

                    break;


                default:

                    input.value =
                        '';

                    break;

            }


            input.dispatchEvent(
                new Event(
                    'input',
                    {
                        bubbles:
                            true,
                    }
                )
            );


            input.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles:
                            true,
                    }
                )
            );

        }
    );


    /* =====================================================
       INFORMAR QUE YA NO HAY PERIODO
    ===================================================== */

    document.dispatchEvent(
        new CustomEvent(
            'periodoReportesAplicado',
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
       EVENTO GLOBAL
    ===================================================== */

    /*
     * Este evento nos servirá también para que otros
     * módulos como filtros.js, paginacion.js o resumen.js
     * puedan reaccionar al limpiar completamente el listado.
     */

    document.dispatchEvent(
        new CustomEvent(
            'filtrosReportesLimpiados'
        )
    );

}


/* =========================================================
   MOSTRAR PERIODO SELECCIONADO
========================================================= */

function mostrarPeriodoSeleccionado(
    fechaInicio,
    fechaFin
) {

    const contenedor =
        document.querySelector(
            '.reportes-periodo'
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
            + `${formatearFecha(fechaInicio)} al `
            + `${formatearFecha(fechaFin)}`;


        return;
    }


    if (fechaInicio) {

        mensaje.textContent =
            `Desde: ${formatearFecha(fechaInicio)}`;


        return;
    }


    mensaje.textContent =
        `Hasta: ${formatearFecha(fechaFin)}`;

}


/* =========================================================
   ERROR
========================================================= */

function mostrarErrorPeriodo(
    mensaje
) {

    const contenedor =
        document.querySelector(
            '.reportes-periodo'
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

function limpiarErroresPeriodo() {

    const error =
        document.querySelector(
            '[data-periodo-error]'
        );


    error?.remove();

}


/* =========================================================
   FORMATEAR FECHA
========================================================= */

function formatearFecha(
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