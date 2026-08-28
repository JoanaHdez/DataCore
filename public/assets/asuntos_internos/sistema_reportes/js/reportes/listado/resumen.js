/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Resumen
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarResumenReportes();
});


function inicializarResumenReportes() {

    const total =
        document.querySelector(
            '#resumen-total-reportes'
        );

    const enProceso =
        document.querySelector(
            '#resumen-en-proceso'
        );

    const finalizados =
        document.querySelector(
            '#resumen-finalizados'
        );


    if (
        !total
        || !enProceso
        || !finalizados
    ) {
        return;
    }


    /*
     * Cálculo inicial usando
     * las filas actualmente visibles.
     */
    actualizarResumen(
        obtenerFilasVisibles()
    );


    /*
     * Cada vez que filtros.js actualice
     * la tabla, recalculamos las tarjetas.
     */
    document.addEventListener(
        'reportesFiltradosActualizados',
        (evento) => {

            const filas =
                Array.isArray(
                    evento.detail?.filas
                )
                    ? evento.detail.filas
                    : [];


            actualizarResumen(
                filas
            );

        }
    );


    /* =====================================================
       ACTUALIZAR RESUMEN
    ===================================================== */

    function actualizarResumen(
        filas
    ) {

        let cantidadProceso = 0;
        let cantidadFinalizados = 0;


        filas.forEach((fila) => {

            const celdas =
                fila.querySelectorAll('td');


            if (celdas.length < 8) {
                return;
            }


            const resolucion =
                normalizarEstado(
                    celdas[7].textContent
                );


            if (
                resolucion === 'en proceso'
            ) {

                cantidadProceso++;

            }


            if (
                resolucion === 'finalizado'
                || resolucion === 'finalizados'
            ) {

                cantidadFinalizados++;

            }

        });


        total.textContent =
            filas.length;


        enProceso.textContent =
            cantidadProceso;


        finalizados.textContent =
            cantidadFinalizados;

    }

}


/* =========================================================
   OBTENER FILAS VISIBLES
========================================================= */

function obtenerFilasVisibles() {

    const tbody =
        document.querySelector(
            '#tabla-reportes-body'
        );


    if (!tbody) {
        return [];
    }


    return Array.from(
        tbody.querySelectorAll('tr')
    ).filter((fila) => {

        return !fila.classList.contains(
            'reportes-tabla__empty'
        )
        && !fila.hidden;

    });

}


/* =========================================================
   NORMALIZAR ESTADO
========================================================= */

function normalizarEstado(
    estado
) {

    return String(estado || '')
        .trim()
        .toLowerCase()
        .normalize('NFD')
        .replace(
            /[\u0300-\u036f]/g,
            ''
        );

}