document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaTurnos();

    }
);


/* =========================================================
   GRÁFICA
   QUEJAS POR TURNO
========================================================= */

function inicializarGraficaTurnos() {

    const canvas =
        document.querySelector(
            '#grafica-turnos'
        );


    const fuenteDatos =
        document.querySelector(
            '#datos-grafica-turnos'
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
            'No fue posible interpretar los datos de quejas por turno:',
            error
        );

        return;
    }


    const turnos =
        Array.isArray(
            datosBackend.turnos
        )
            ? datosBackend.turnos
            : [];


    const totales =
        Array.isArray(
            datosBackend.totales
        )
            ? datosBackend.totales.map(
                (total) =>
                    Number(total) || 0
            )
            : [];


    /*
     * Convertimos la respuesta del backend
     * al formato que ya utilizaba esta gráfica.
     */

    const datosTurnos =
        turnos.map(
            (
                nombre,
                indice
            ) => {

                return {
                    nombre:
                        nombre,

                    valor:
                        totales[indice]
                        ?? 0,
                };

            }
        );


    if (
        datosTurnos.length === 0
    ) {
        return;
    }


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
       TOTAL REAL
    ===================================================== */

    const totalCalculado =
        datosTurnos.reduce(
            (
                acumulado,
                turno
            ) => {

                return (
                    acumulado
                    + Number(
                        turno.valor
                        || 0
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
            '#turnos-total'
        );


    if (totalElemento) {

        totalElemento.textContent =
            String(
                total
            );

    }


    /* =====================================================
       PORCENTAJES
    ===================================================== */

    const porcentajes =
        datosTurnos.map(
            (turno) => {

                if (
                    total <= 0
                ) {
                    return 0;
                }


                return (
                    Number(
                        turno.valor
                    )
                    / total
                ) * 100;

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
                    datosTurnos.map(
                        (turno) => {

                            return (
                                turno.nombre
                            );

                        }
                    ),

                datasets: [
                    {
                        label:
                            'Quejas',

                        data:
                            datosTurnos.map(
                                (turno) => {

                                    return (
                                        turno.valor
                                    );

                                }
                            ),

                        backgroundColor: [
                            'rgba(8, 139, 96, 0.96)',
                            'rgba(33, 157, 116, 0.84)',
                            'rgba(58, 174, 136, 0.74)',
                            'rgba(91, 186, 153, 0.66)',
                            'rgba(112, 195, 165, 0.62)',
                            'rgba(138, 205, 180, 0.58)',
                            'rgba(165, 216, 197, 0.56)',
                        ],

                        hoverBackgroundColor: [
                            '#067a55',
                            '#168e68',
                            '#319f7b',
                            '#4eae8d',
                            '#69b99b',
                            '#84c4aa',
                            '#9dcdb9',
                        ],

                        borderWidth:
                            0,

                        borderSkipped:
                            false,

                        borderRadius:
                            12,

                        barPercentage:
                            0.62,

                        categoryPercentage:
                            0.72,

                        maxBarThickness:
                            28,
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
                   ESPACIADO
                ================================================= */

                layout: {

                    padding: {

                        top:
                            8,

                        right:
                            14,

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
                         * Ya no utilizamos suggestedMax: 90,
                         * porque ese límite pertenecía a los
                         * datos temporales del Excel.
                         *
                         * Chart.js calculará la escala con
                         * base en los datos reales.
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
                                '#40534c',

                            padding:
                                10,

                            font: {

                                size:
                                    9,

                                weight:
                                    '700',

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
                            '#31453d',

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
                                '700',

                        },


                        callbacks: {

                            title(
                                elementos
                            ) {

                                return (
                                    elementos[0]
                                        ?.label
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
                                            ? 'queja'
                                            : 'quejas'
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