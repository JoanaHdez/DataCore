document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaAreasInvolucradas();

    }
);


/* =========================================================
   GRÁFICA
   QUEJAS POR ÁREA
========================================================= */

function inicializarGraficaAreasInvolucradas() {

    const canvas =
        document.querySelector(
            '#grafica-areas-involucradas'
        );


    const fuenteDatos =
        document.querySelector(
            '#datos-grafica-areas-involucradas'
        );


    if (
        !canvas
        || !fuenteDatos
    ) {
        return;
    }


    if (
        typeof Chart === 'undefined'
    ) {

        console.error(
            'Chart.js no está disponible para la gráfica de quejas por área.'
        );

        return;
    }


    /* =====================================================
       DATOS DEL BACKEND
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
            'No fue posible interpretar los datos de quejas por área:',
            error
        );

        return;
    }


    const areas =
        Array.isArray(
            datosBackend.areas
        )
            ? datosBackend.areas
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


    if (
        areas.length === 0
        || totales.length === 0
    ) {
        return;
    }


    /* =====================================================
       TOP DE ÁREAS

       El backend envía todas las áreas ordenadas
       de mayor a menor.

       Visualmente mostramos únicamente las 10
       con mayor número de quejas.
    ===================================================== */

    const limiteAreas =
        10;


    const areasMostradas =
        areas.slice(
            0,
            limiteAreas
        );


    const totalesMostrados =
        totales.slice(
            0,
            limiteAreas
        );


    /* =====================================================
       EVITAR GRÁFICAS DUPLICADAS
    ===================================================== */

    const graficaExistente =
        Chart.getChart(
            canvas
        );


    if (graficaExistente) {

        graficaExistente.destroy();

    }


    /* =====================================================
       CONFIGURACIÓN DE COLORES
    ===================================================== */

    const colores =
    totalesMostrados.map(
        (
            total,
            indice
        ) => {

            if (indice === 0) {

                return (
                    'rgba(23, 73, 122, 0.96)'
                );
            }


            if (indice === 1) {

                return (
                    'rgba(37, 99, 160, 0.84)'
                );
            }


            if (indice === 2) {

                return (
                    'rgba(64, 132, 190, 0.74)'
                );
            }


            return (
                'rgba(116, 169, 211, 0.58)'
            );
        }
    );


    const coloresHover =
        totalesMostrados.map(
            (
                total,
                indice
            ) => {

                if (indice === 0) {
                    return '#123f6b';
                }


                if (indice === 1) {
                    return '#1d5d99';
                }


                if (indice === 2) {
                    return '#347db8';
                }


                return '#659ecb';
            }
        );

    /* =====================================================
       GRÁFICA
    ===================================================== */

    new Chart(
        canvas,
        {
            type:
                'bar',


            data: {

                labels:
                    areasMostradas,


                datasets: [
                    {
                        label:
                            'Quejas',

                        data:
                            totalesMostrados,

                        backgroundColor:
                            colores,

                        hoverBackgroundColor:
                            coloresHover,

                        borderWidth:
                            0,

                        borderRadius:
                            10,

                        borderSkipped:
                            false,

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

                indexAxis:
                    'y',

                responsive:
                    true,

                maintainAspectRatio:
                    false,


                /* =================================================
                   ANIMACIÓN
                ================================================= */

                animation: {

                    duration:
                        700,

                    easing:
                        'easeOutQuart',

                },


                /* =================================================
                   INTERACCIÓN
                ================================================= */

                interaction: {

                    mode:
                        'nearest',

                    axis:
                        'y',

                    intersect:
                        false,

                },


                /* =================================================
                   ESPACIADO
                ================================================= */

                layout: {

                    padding: {

                        top:
                            4,

                        right:
                            14,

                        bottom:
                            2,

                        left:
                            2,

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
                            '#2f423b',

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
                                context
                            ) {

                                return (
                                    context[0]
                                        ?.label
                                    || ''
                                );

                            },


                            label(
                                context
                            ) {

                                const total =
                                    Number(
                                        context.raw
                                    )
                                    || 0;


                                return (
                                    `${total} `
                                    + (
                                        total === 1
                                            ? 'queja'
                                            : 'quejas'
                                    )
                                );

                            },

                        },

                    },

                },


                /* =================================================
                   EJES
                ================================================= */

                scales: {

                    x: {

                        beginAtZero:
                            true,

                        border: {
                            display:
                                false,
                        },


                        ticks: {

                            precision:
                                0,

                            stepSize:
                                1,

                            padding:
                                8,

                            color:
                                '#8a9994',

                            font: {

                                size:
                                    9,

                                weight:
                                    '500',

                            },

                        },


                        grid: {

                            color:
                                'rgba(103, 130, 119, 0.10)',

                            borderDash: [
                                4,
                                4,
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

                            autoSkip:
                                false,

                            padding:
                                10,

                            color:
                                '#40534c',

                            font: {

                                size:
                                    9,

                                weight:
                                    '700',

                            },


                            callback(
                                value
                            ) {

                                const texto =
                                    this.getLabelForValue(
                                        value
                                    );


                                /*
                                 * Los nombres institucionales
                                 * pueden ser muy extensos.
                                 *
                                 * Mostramos una versión abreviada
                                 * en el eje y conservamos el nombre
                                 * completo dentro del tooltip.
                                 */

                                if (
                                    texto.length
                                    <= 36
                                ) {

                                    return texto;

                                }


                                return (
                                    texto.substring(
                                        0,
                                        33
                                    )
                                    + '...'
                                );

                            },

                        },

                    },

                },

            },

        }
    );

}