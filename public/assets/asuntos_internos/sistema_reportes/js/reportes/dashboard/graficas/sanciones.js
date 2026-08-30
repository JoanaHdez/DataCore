document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaSanciones();

    }
);


/* =========================================================
   GRÁFICA
   SANCIONES DISCIPLINARIAS
========================================================= */

function inicializarGraficaSanciones() {

    const canvas =
        document.querySelector(
            '#grafica-sanciones'
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

    const datosSanciones = {

        labels: [
            'Arrestos',
            'Amonestaciones',
        ],

        valores: [
            447,
            56,
        ],

    };


    /* =====================================================
       TOTAL
    ===================================================== */

    const total =
        datosSanciones.valores.reduce(
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
            '#sanciones-total'
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
        datosSanciones.valores.map(
            (valor) => {

                if (total <= 0) {
                    return 0;
                }


                return (
                    Number(valor)
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
                    datosSanciones.labels,

                datasets: [
                    {
                        label:
                            'Sanciones',

                        data:
                            datosSanciones.valores,

                        backgroundColor: [
                            'rgba(10, 142, 99, 0.94)',
                            'rgba(111, 194, 165, 0.72)',
                        ],

                        hoverBackgroundColor: [
                            '#087b58',
                            '#53ad8d',
                        ],

                        borderWidth:
                            0,

                        borderSkipped:
                            false,

                        borderRadius:
                            16,

                        barPercentage:
                            0.56,

                        categoryPercentage:
                            0.72,

                        maxBarThickness:
                            44,

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
                            10,

                        right:
                            12,

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

                        suggestedMax:
                            500,

                        border: {

                            display:
                                false,

                        },

                        ticks: {

                            precision:
                                0,

                            stepSize:
                                100,

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
                                'rgba(104, 130, 120, 0.12)',

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
                                '#41544d',

                            padding:
                                10,

                            font: {

                                size:
                                    10,

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
                            '#354840',

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
                                '600',

                        },

                        bodyFont: {

                            size:
                                11,

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
                                    `${valor} sanciones `
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