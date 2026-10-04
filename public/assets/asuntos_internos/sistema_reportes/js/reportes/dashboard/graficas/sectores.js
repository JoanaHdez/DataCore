document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaSectoresDashboard();

    }
);


/* =========================================================
   QUEJAS / FELICITACIONES POR SECTOR
========================================================= */

function inicializarGraficaSectoresDashboard() {

    const canvas =
        document.querySelector(
            '#dashboard-sectores-chart'
        );


    const fuenteDatos =
        document.querySelector(
            '#dashboard-sectores-datos'
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
            'Chart.js no está disponible para la gráfica de sectores.'
        );

        return;
    }


    /* =====================================================
       LEER DATOS DEL BACKEND
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
            'No fue posible interpretar los datos de sectores:',
            error
        );

        return;
    }


    const tipo =
        String(
            datosBackend.tipo
            || 'queja'
        )
            .trim()
            .toLowerCase();


    const sectores =
        Array.isArray(
            datosBackend.sectores
        )
            ? datosBackend.sectores
            : [];


    const totales =
        Array.isArray(
            datosBackend.totales
        )
            ? datosBackend.totales.map(
                total =>
                    Number(total)
                    || 0
            )
            : [];


    const totalGeneral =
        Number(
            datosBackend.total
            || 0
        );


    if (
        sectores.length === 0
        || totales.length === 0
        || totalGeneral <= 0
    ) {

        return;
    }


    /* =====================================================
       NORMALIZAR DATOS

       La vista ya recibe sectores y totales desde backend,
       pero ordenamos nuevamente para garantizar que la
       gráfica siempre quede de mayor a menor.
    ===================================================== */

    const registros =
        sectores
            .map(
                (
                    sector,
                    indice
                ) => {

                    return {

                        sector:
                            String(
                                sector
                                || ''
                            ).trim(),

                        total:
                            Number(
                                totales[
                                    indice
                                ]
                            )
                            || 0,

                    };

                }
            )
            .filter(
                registro =>
                    registro.sector !== ''
                    && registro.total > 0
            )
            .sort(
                (
                    a,
                    b
                ) =>
                    b.total
                    - a.total
            );


    if (
        registros.length === 0
    ) {

        return;
    }


    const sectoresOrdenados =
        registros.map(
            registro =>
                registro.sector
        );


    const totalesOrdenados =
        registros.map(
            registro =>
                registro.total
        );


    /* =====================================================
       COLORES

       Top 3 con mayor presencia visual.
       El resto mantiene una misma familia cromática.
    ===================================================== */

    const colores =
        registros.map(
            (
                registro,
                indice
            ) => {

                if (
                    indice === 0
                ) {

                    return (
                        'rgba(23, 73, 122, 0.96)'
                    );
                }


                if (
                    indice === 1
                ) {

                    return (
                        'rgba(37, 99, 160, 0.84)'
                    );
                }


                if (
                    indice === 2
                ) {

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
        registros.map(
            (
                registro,
                indice
            ) => {

                if (
                    indice === 0
                ) {

                    return '#123f6b';
                }


                if (
                    indice === 1
                ) {

                    return '#1d5d99';
                }


                if (
                    indice === 2
                ) {

                    return '#347db8';
                }


                return '#659ecb';

            }
        );


    /* =====================================================
       DESTRUIR GRÁFICA EXISTENTE
    ===================================================== */

    const graficaExistente =
        Chart.getChart(
            canvas
        );


    if (
        graficaExistente
    ) {

        graficaExistente.destroy();
    }


    /* =====================================================
       TEXTO SEGÚN TIPO
    ===================================================== */

    const singular =
        tipo === 'felicitacion'
            ? 'felicitación'
            : 'queja';


    const plural =
        tipo === 'felicitacion'
            ? 'felicitaciones'
            : 'quejas';


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
                    sectoresOrdenados,


                datasets: [
                    {

                        label:
                            tipo === 'felicitacion'
                                ? 'Felicitaciones'
                                : 'Quejas',


                        data:
                            totalesOrdenados,


                        backgroundColor:
                            colores,


                        hoverBackgroundColor:
                            coloresHover,


                        borderWidth:
                            0,


                        borderRadius:
                            9,


                        borderSkipped:
                            false,


                        barPercentage:
                            0.68,


                        categoryPercentage:
                            0.78,


                        maxBarThickness:
                            28,

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
                            6,


                        right:
                            18,


                        bottom:
                            4,


                        left:
                            4,

                    },

                },


                /* =================================================
                   PLUGINS
                ================================================= */

                plugins: {

                    dashboardEtiquetasVisibles:
                        window.DashboardEtiquetasGraficas
                            ?.opciones(
                                {
                                    modo:
                                        'cantidadPorcentaje',
                                }
                            ),

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
                            '#17628f',


                        borderColor:
                            'rgba(23, 73, 122, 0.15)',


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


                                const porcentaje =
                                    totalGeneral > 0
                                        ? (
                                            (
                                                total
                                                / totalGeneral
                                            )
                                            * 100
                                        )
                                        : 0;


                                return (
                                    `${total} `
                                    + (
                                        total === 1
                                            ? singular
                                            : plural
                                    )
                                    + ` · ${porcentaje.toFixed(1)}%`
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


                                if (
                                    texto.length
                                    <= 26
                                ) {

                                    return texto;
                                }


                                return (
                                    texto.substring(
                                        0,
                                        23
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
