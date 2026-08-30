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
       DATOS TEMPORALES
       Basados en el dashboard de Excel
    ===================================================== */

    const datosZonas = {

        labels: [
            'Zona Oriente',
            'Zona Centro',
            'Zona Poniente',
            'Zona Norte',
        ],

        valores: [
            45,
            51,
            49,
            45,
        ],

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
                        valor || 0
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

            data: {

                labels:
                    datosZonas.labels,

                datasets: [
                    {
                        label:
                            'Quejas',

                        data:
                            datosZonas.valores,

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


                    y: {

                        beginAtZero:
                            true,

                        suggestedMax:
                            60,

                        border: {

                            display:
                                false,

                        },

                        ticks: {

                            precision:
                                0,

                            stepSize:
                                10,

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
                                    `${valor} quejas`
                                );

                            },

                        },

                    },

                },

            },

        }
    );

}