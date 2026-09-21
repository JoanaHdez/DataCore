/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Resumen
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarResumenReportes();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

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


    const conArresto =
        document.querySelector(
            '#resumen-con-arresto'
        );


    if (
        !total
        || !enProceso
        || !finalizados
        || !conArresto
    ) {
        return;
    }


    /* =====================================================
       CÁLCULO INICIAL
    ===================================================== */

    actualizarResumen(
        obtenerFilasVisibles()
    );


    /* =====================================================
       ACTUALIZAR AL FILTRAR
    ===================================================== */

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

        let cantidadProceso =
            0;


        let cantidadFinalizados =
            0;


        let cantidadConArresto =
            0;


        filas.forEach(
            (fila) => {

                const celdas =
                    fila.querySelectorAll(
                        'td'
                    );


                if (
                    celdas.length < 7
                ) {
                    return;
                }


                /* =============================================
                   ESTADO
                ============================================== */

                const resolucion =
                    normalizarEstado(
                        celdas[5].textContent
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


                /* =============================================
                   ARRESTO

                   Cada reporte cuenta una sola vez.

                   data-tiene-arresto:
                   1 = tiene al menos un arresto
                   0 = no tiene arrestos
                ============================================== */

                const tieneArresto =
                    String(
                        fila.dataset
                            .tieneArresto
                        || '0'
                    ) === '1';


                if (
                    tieneArresto
                ) {

                    cantidadConArresto++;

                }

            }
        );


        total.textContent =
            String(
                filas.length
            );


        enProceso.textContent =
            String(
                cantidadProceso
            );


        finalizados.textContent =
            String(
                cantidadFinalizados
            );


        conArresto.textContent =
            String(
                cantidadConArresto
            );

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
        tbody.querySelectorAll(
            'tr'
        )
    ).filter(
        (fila) => {

            return (
                !fila.classList.contains(
                    'reportes-tabla__empty'
                )
                && !fila.hidden
            );

        }
    );

}


/* =========================================================
   NORMALIZAR ESTADO
========================================================= */

function normalizarEstado(
    estado
) {

    return String(
        estado
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