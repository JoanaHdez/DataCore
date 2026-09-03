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


    const estadoSinDatos =
        document.querySelector(
            '#sanciones-sin-datos'
        );


    const totalElemento =
        document.querySelector(
            '#sanciones-total'
        );


    const datosElemento =
        document.querySelector(
            '#dashboard-datos-sanciones'
        );


    if (
        !canvas
        || typeof Chart === 'undefined'
    ) {
        return;
    }


    /* =====================================================
       DATOS DEL BACKEND
    ===================================================== */

    let datosBackend = {
        tipos: [],
        totales: [],
        total: 0,
    };


    if (datosElemento) {

        try {

            datosBackend =
                JSON.parse(
                    datosElemento.textContent
                    || '{}'
                );

        } catch (error) {

            console.error(
                'No fue posible leer los datos de sanciones:',
                error
            );

        }

    }


    /* =====================================================
       NORMALIZAR DATOS
    ===================================================== */

    const tipos =
        Array.isArray(
            datosBackend.tipos
        )
            ? datosBackend.tipos
            : [];


    const totales =
        Array.isArray(
            datosBackend.totales
        )
            ? datosBackend.totales.map(
                (valor) =>
                    Number(valor || 0)
            )
            : [];


    /*
     * Garantizamos que cada tipo tenga
     * un valor numérico correspondiente.
     */

    const valores =
        tipos.map(
            (tipo, indice) =>
                Number(
                    totales[indice]
                    ?? 0
                )
        );


    const datosSanciones = {

        labels:
            tipos,

        valores:
            valores,

    };


    /* =====================================================
       TOTAL

       Lo calculamos nuevamente en JavaScript para que
       la cifra mostrada siempre coincida exactamente
       con las barras renderizadas.
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


    if (totalElemento) {

        totalElemento.textContent =
            String(
                total
            );

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
       SIN INFORMACIÓN
    ===================================================== */

    if (
        total <= 0
        || datosSanciones.labels.length === 0
    ) {

        canvas.hidden =
            true;


        if (estadoSinDatos) {

            estadoSinDatos.hidden =
                false;

        }


        return;
    }


    canvas.hidden =
        false;


    if (estadoSinDatos) {

        estadoSinDatos.hidden =
            true;

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
                            'rgba(211, 158, 72, 0.86)',
                            'rgba(202, 105, 96, 0.82)',
                            'rgba(120, 91, 153, 0.82)',
                        ],

                        hoverBackgroundColor: [
                            '#b98535',
                            '#b75b54',
                            '#674c86',
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
                                    `${valor} ${
                                        valor === 1
                                            ? 'sanción'
                                            : 'sanciones'
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