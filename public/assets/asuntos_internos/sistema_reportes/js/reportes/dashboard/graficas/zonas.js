/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Dashboard - Gráfica de zonas
========================================================= */


document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaZonas();

    }
);


/* =========================================================
   GRÁFICA
   QUEJAS POR ZONA
========================================================= */

function inicializarGraficaZonas() {

    const canvas =
        document.querySelector(
            '#grafica-zonas'
        );


    if (
        !canvas
        || typeof Chart === 'undefined'
    ) {
        return;
    }


    /* =====================================================
       DATOS REALES
    ===================================================== */

    const datosElemento =
        document.querySelector(
            '#dashboard-datos-zonas'
        );


    if (!datosElemento) {

        console.warn(
            'No se encontraron los datos de la gráfica de zonas.'
        );

        return;
    }


    let datosServidor;


    try {

        datosServidor =
            JSON.parse(
                datosElemento.textContent
                || '{}'
            );

    } catch (error) {

        console.error(
            'No fue posible interpretar los datos de zonas:',
            error
        );

        return;
    }


    /* =====================================================
       NORMALIZAR DATOS
    ===================================================== */

    const labels =
        Array.isArray(
            datosServidor.zonas
        )
            ? datosServidor.zonas.map(
                (zona) =>
                    String(
                        zona
                        || ''
                    ).trim()
            )
            : [];


    const valores =
        Array.isArray(
            datosServidor.totales
        )
            ? datosServidor.totales.map(
                (valor) => {

                    const numero =
                        Number(
                            valor
                            || 0
                        );


                    return Number.isFinite(
                        numero
                    )
                        ? numero
                        : 0;

                }
            )
            : [];


    const datosZonas = {

        labels,

        valores,

    };


    /* =====================================================
       TOTAL
    ===================================================== */

    const total =
        datosZonas.valores.reduce(
            (
                acumulado,
                valor
            ) => {

                return (
                    acumulado
                    + Number(
                        valor
                        || 0
                    )
                );

            },
            0
        );


    const totalElemento =
        document.querySelector(
            '#grafica-zonas-total'
        );


    if (totalElemento) {

        totalElemento.textContent =
            String(
                total
            );

    }


    /* =====================================================
       CREAR GRÁFICA
    ===================================================== */

    new Chart(
        canvas,
        {

            type:
                'bar',


            /* =================================================
               DATOS
            ================================================= */

            data: {

                labels:
                    datosZonas.labels,

                datasets: [
                    {

                        label:
                            'Quejas',

                        data:
                            datosZonas.valores,


                        /* =========================================
                           COLORES
                        ========================================= */

                        backgroundColor: [
                            'rgba(61, 157, 128, 0.58)',
                            'rgba(15, 151, 105, 0.95)',
                            'rgba(70, 171, 140, 0.66)',
                            'rgba(102, 187, 160, 0.56)',
                        ],

                        hoverBackgroundColor: [
                            'rgba(61, 157, 128, 0.78)',
                            '#07885e',
                            'rgba(70, 171, 140, 0.84)',
                            'rgba(102, 187, 160, 0.78)',
                        ],


                        /* =========================================
                           BARRAS
                        ========================================= */

                        borderWidth:
                            0,

                        borderSkipped:
                            false,

                        borderRadius: {

                            topLeft:
                                18,

                            topRight:
                                18,

                            bottomLeft:
                                18,

                            bottomRight:
                                18,

                        },

                        categoryPercentage:
                            0.70,

                        barPercentage:
                            0.72,

                        maxBarThickness:
                            58,

                    },
                ],

            },


            /* =================================================
               OPCIONES
            ================================================= */

            options: {

                responsive:
                    true,

                maintainAspectRatio:
                    false,


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
                            22,

                        right:
                            6,

                        bottom:
                            0,

                        left:
                            2,

                    },

                },


                /* =================================================
                   EJES
                ================================================= */

                scales: {

                    /* =============================================
                       EJE X
                    ============================================= */

                    x: {

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
                                '#70827c',

                            padding:
                                12,

                            font: {

                                size:
                                    9,

                                weight:
                                    '700',

                            },

                        },

                    },


                    /* =============================================
                       EJE Y
                    ============================================= */

                    y: {

                        beginAtZero:
                            true,


                        border: {

                            display:
                                false,

                        },


                        ticks: {

                            /*
                             * Las quejas son cantidades enteras.
                             *
                             * Evitamos mostrar:
                             *
                             * 0.1
                             * 0.2
                             * 0.3
                             *
                             * cuando solamente existen
                             * una o dos quejas.
                             */

                            precision:
                                0,


                            callback(
                                value
                            ) {

                                const numero =
                                    Number(
                                        value
                                    );


                                if (
                                    Number.isInteger(
                                        numero
                                    )
                                ) {

                                    return numero;
                                }


                                return null;

                            },


                            padding:
                                8,

                            color:
                                '#94a09c',

                            font: {

                                size:
                                    9,

                            },

                        },


                        grid: {

                            color:
                                'rgba(105, 132, 121, 0.12)',

                            borderDash: [
                                4,
                                5,
                            ],

                            drawTicks:
                                false,

                        },

                    },

                },


                /* =================================================
                   PLUGINS
                ================================================= */

                plugins: {

                    /* =============================================
                       LEYENDA
                    ============================================= */

                    legend: {

                        display:
                            false,

                    },


                    /* =============================================
                       TOOLTIP
                    ============================================= */

                    tooltip: {

                        enabled:
                            true,

                        displayColors:
                            false,

                        backgroundColor:
                            'rgba(255, 255, 255, 0.98)',

                        titleColor:
                            '#344740',

                        bodyColor:
                            '#087d59',

                        borderColor:
                            'rgba(16, 137, 96, 0.16)',

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
                                '600',

                        },


                        bodyFont: {

                            size:
                                13,

                            weight:
                                '800',

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

                                const valor =
                                    Number(
                                        contexto.raw
                                        ?? 0
                                    );


                                return (
                                    valor === 1
                                        ? '1 queja'
                                        : `${valor} quejas`
                                );

                            },

                        },

                    },

                },

            },

        }
    );

}