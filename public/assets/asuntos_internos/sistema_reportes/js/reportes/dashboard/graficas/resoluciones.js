document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaResoluciones();

    }
);


/* =========================================================
   GRÁFICA
   RESOLUCIÓN GENERAL
========================================================= */

function inicializarGraficaResoluciones() {

    const canvas =
        document.querySelector(
            '#grafica-resoluciones'
        );


    const fuenteDatos =
        document.querySelector(
            '#datos-grafica-resoluciones'
        );


    if (
        !canvas
        || !fuenteDatos
        || typeof Chart === 'undefined'
    ) {
        return;
    }


    /* =====================================================
       DATOS REALES DEL BACKEND
    ===================================================== */

    let datosBackend;


    try {

        datosBackend =
            JSON.parse(
                fuenteDatos.textContent
                || '{}'
            );

    } catch (error) {

        console.error(
            'No fue posible interpretar los datos de resoluciones:',
            error
        );

        return;
    }


    const labels =
        Array.isArray(
            datosBackend.resoluciones
        )
            ? datosBackend.resoluciones
            : [];


    const valores =
        Array.isArray(
            datosBackend.totales
        )
            ? datosBackend.totales.map(
                (total) =>
                    Number(total) || 0
            )
            : [];


    const datosResoluciones = {
        labels:
            labels,

        valores:
            valores,
    };


    /* =====================================================
       DESTRUIR GRÁFICA PREVIA
    ===================================================== */

    const graficaExistente =
        Chart.getChart(
            canvas
        );


    if (graficaExistente) {

        graficaExistente.destroy();

    }


    /* =====================================================
       TOTAL
    ===================================================== */

    const totalCalculado =
        datosResoluciones.valores.reduce(
            (
                acumulado,
                valor
            ) => {

                return (
                    acumulado
                    + Number(
                        valor || 0
                    )
                );

            },
            0
        );


    const totalBackend =
        Number(
            datosBackend.total
        );


    const total =
        Number.isFinite(
            totalBackend
        )
            ? totalBackend
            : totalCalculado;


    const totalElemento =
        document.querySelector(
            '#resoluciones-total'
        );


    if (totalElemento) {

        totalElemento.textContent =
            String(
                total
            );

    }


    /* =====================================================
       SIN DATOS
    ===================================================== */

    if (
        datosResoluciones.labels.length === 0
        || datosResoluciones.valores.length === 0
    ) {
        return;
    }


    /* =====================================================
       PORCENTAJES
    ===================================================== */

    const porcentajes =
        datosResoluciones.valores.map(
            (valor) => {

                if (
                    total <= 0
                ) {
                    return 0;
                }


                return (
                    Number(valor)
                    / total
                ) * 100;

            }
        );


    /* =====================================================
       COLORES DINÁMICOS
    ===================================================== */

    const coloresBase = [
        'rgba(15, 148, 103, 0.95)',
        'rgba(58, 165, 130, 0.76)',
        'rgba(83, 179, 148, 0.70)',
        'rgba(107, 190, 160, 0.66)',
        'rgba(130, 200, 175, 0.62)',
        'rgba(153, 211, 190, 0.58)',
        'rgba(180, 219, 205, 0.56)',
    ];


    const coloresHoverBase = [
        '#087e59',
        '#238f6d',
        '#3d9f7d',
        '#55ad8c',
        '#70b99d',
        '#8bc5ae',
        '#a6d0bf',
    ];


    /*
     * Ahora las resoluciones son dinámicas.
     * Si existen más de siete categorías,
     * reutilizamos suavemente la paleta.
     */

    const colores =
        datosResoluciones.labels.map(
            (
                resolucion,
                indice
            ) => {

                return (
                    coloresBase[
                        indice
                        % coloresBase.length
                    ]
                );

            }
        );


    const coloresHover =
        datosResoluciones.labels.map(
            (
                resolucion,
                indice
            ) => {

                return (
                    coloresHoverBase[
                        indice
                        % coloresHoverBase.length
                    ]
                );

            }
        );


    /* =====================================================
       CREAR GRÁFICA
    ===================================================== */

    new Chart(
        canvas,
        {
            type:
                'bar',

            data: {

                labels:
                    datosResoluciones.labels,

                datasets: [
                    {
                        label:
                            'Reportes',

                        data:
                            datosResoluciones.valores,

                        backgroundColor:
                            colores,

                        hoverBackgroundColor:
                            coloresHover,

                        borderWidth:
                            0,

                        borderSkipped:
                            false,

                        borderRadius:
                            12,

                        barPercentage:
                            0.68,

                        categoryPercentage:
                            0.76,

                        maxBarThickness:
                            24,

                    },
                ],

            },


            options: {

                responsive:
                    true,

                maintainAspectRatio:
                    false,

                indexAxis:
                    'y',


                /* =================================================
                   ANIMACIÓN
                ================================================= */

                animation: {

                    duration:
                        750,

                    easing:
                        'easeOutQuart',

                },


                /* =================================================
                   INTERACCIÓN
                ================================================= */

                interaction: {

                    mode:
                        'nearest',

                    intersect:
                        false,

                },


                /* =================================================
                   LAYOUT
                ================================================= */

                layout: {

                    padding: {

                        top:
                            8,

                        right:
                            16,

                        bottom:
                            4,

                        left:
                            2,

                    },

                },


                /* =================================================
                   EJES
                ================================================= */

                scales: {

                    x: {

                        beginAtZero:
                            true,

                        /*
                         * Eliminamos suggestedMax: 220
                         * y stepSize: 50 porque pertenecían
                         * a los datos del Excel.
                         *
                         * La escala ahora se adapta
                         * automáticamente a los datos reales.
                         */

                        border: {

                            display:
                                false,

                        },

                        ticks: {

                            precision:
                                0,

                            padding:
                                8,

                            color:
                                '#8b9994',

                            font: {

                                size:
                                    9,

                                weight:
                                    '500',

                            },

                        },

                        grid: {

                            color:
                                'rgba(103, 130, 119, 0.12)',

                            borderDash: [
                                4,
                                5,
                            ],

                            drawTicks:
                                false,

                        },

                    },


                    y: {

                        border: {

                            display:
                                false,

                        },

                        grid: {

                            display:
                                false,

                        },

                        ticks: {

                            color:
                                '#42554e',

                            padding:
                                10,

                            font: {

                                size:
                                    9,

                                weight:
                                    '700',

                            },


                            /*
                             * Las resoluciones de la BD pueden
                             * tener nombres considerablemente
                             * más largos que los del Excel.
                             */

                            callback(
                                value
                            ) {

                                const texto =
                                    this.getLabelForValue(
                                        value
                                    );


                                if (
                                    texto.length
                                    <= 30
                                ) {

                                    return texto;

                                }


                                return (
                                    texto.substring(
                                        0,
                                        27
                                    )
                                    + '...'
                                );

                            },

                        },

                    },

                },


                /* =================================================
                   PLUGINS
                ================================================= */

                plugins: {

                    legend: {

                        display:
                            false,

                    },


                    tooltip: {

                        enabled:
                            true,

                        displayColors:
                            false,

                        backgroundColor:
                            'rgba(255, 255, 255, 0.98)',

                        titleColor:
                            '#32463e',

                        bodyColor:
                            '#087d59',

                        borderColor:
                            'rgba(12, 140, 96, 0.16)',

                        borderWidth:
                            1,

                        cornerRadius:
                            12,

                        padding:
                            12,

                        caretPadding:
                            8,

                        titleFont: {

                            size:
                                10,

                            weight:
                                '700',

                        },

                        bodyFont: {

                            size:
                                10,

                            weight:
                                '600',

                        },


                        callbacks: {

                            title(
                                elementos
                            ) {

                                const indice =
                                    elementos[0]
                                        ?.dataIndex
                                    ?? 0;


                                /*
                                 * Aunque el eje muestre el texto
                                 * abreviado, el tooltip conserva
                                 * la resolución completa.
                                 */

                                return (
                                    datosResoluciones
                                        .labels[indice]
                                    || ''
                                );

                            },


                            label(
                                contexto
                            ) {

                                const indice =
                                    contexto.dataIndex;


                                const valor =
                                    Number(
                                        contexto.raw
                                        ?? 0
                                    );


                                const porcentaje =
                                    porcentajes[
                                        indice
                                    ]
                                    ?? 0;


                                return (
                                    `${valor} ${
                                        valor === 1
                                            ? 'reporte'
                                            : 'reportes'
                                    } `
                                    + `(${porcentaje.toFixed(1)}%)`
                                );

                            },

                        },

                    },

                },

            },

        }
    );

}