/* =========================================================
   DASHBOARD
   ANÁLISIS CRUZADO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarCruceDashboard();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarCruceDashboard() {

    inicializarSelectoresCruce();

    inicializarGraficaCruce();

}


/* =========================================================
   SELECTORES
========================================================= */

function inicializarSelectoresCruce() {

    const principal =
        document.getElementById(
            'dashboard-cruce-principal'
        );


    const secundaria =
        document.getElementById(
            'dashboard-cruce-secundaria'
        );


    if (
        !principal
        || !secundaria
    ) {

        return;
    }


    /* =====================================================
       CAMBIO PRINCIPAL
    ===================================================== */

    principal.addEventListener(
        'change',
        () => {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.set(
                'cruce_principal',
                principal.value
            );


            /*
             * Dejamos que backend determine
             * la secundaria válida para esa principal.
             */

            url.searchParams.delete(
                'cruce_secundaria'
            );


            window.location.href =
                url.toString();

        }
    );


    /* =====================================================
       CAMBIO SECUNDARIO
    ===================================================== */

    secundaria.addEventListener(
        'change',
        () => {

            const url =
                new URL(
                    window.location.href
                );


            url.searchParams.set(
                'cruce_principal',
                principal.value
            );


            url.searchParams.set(
                'cruce_secundaria',
                secundaria.value
            );


            window.location.href =
                url.toString();

        }
    );

}


/* =========================================================
   GRÁFICA
========================================================= */

function inicializarGraficaCruce() {

    const canvas =
        document.querySelector(
            '#dashboard-cruce-chart'
        );


    const datosElemento =
        document.querySelector(
            '#dashboard-cruce-datos'
        );


    const contenedor =
        document.querySelector(
            '#dashboard-cruce-chart-container'
        );


    if (
        !canvas
        || !datosElemento
        || !contenedor
        || typeof Chart === 'undefined'
    ) {

        return;
    }


    /* =====================================================
       DATOS
    ===================================================== */

    let datos;


    try {

        datos =
            JSON.parse(
                datosElemento.textContent
                || '{}'
            );

    } catch (error) {

        console.error(
            'No fue posible interpretar los datos del análisis cruzado:',
            error
        );

        return;
    }


    const categorias =
        Array.isArray(
            datos.categorias
        )
            ? datos.categorias.map(
                categoria =>
                    String(
                        categoria
                        || ''
                    ).trim()
            )
            : [];


    const series =
        Array.isArray(
            datos.series
        )
            ? datos.series
            : [];


    const total =
        Number(
            datos.total
            || 0
        );


    const tipo =
        String(
            datos.tipo
            || 'queja'
        )
            .trim()
            .toLowerCase();


    const esFelicitacion =
        tipo === 'felicitacion';


    const singularRegistro =
        esFelicitacion
            ? 'felicitación'
            : 'queja';


    const pluralRegistro =
        esFelicitacion
            ? 'felicitaciones'
            : 'quejas';


    if (
        categorias.length === 0
        || series.length === 0
        || total <= 0
    ) {

        return;
    }


    /* =====================================================
       ALTURA DINÁMICA

       La ventana visible permanece controlada.
       Si hay muchas categorías aparece scroll.
    ===================================================== */

    const alturaPorCategoria =
        58;


    const alturaMinima =
        360;


    const alturaCalculada =
        Math.max(
            alturaMinima,
            categorias.length
            * alturaPorCategoria
        );


    contenedor.style.height =
        `${alturaCalculada}px`;


    /* =====================================================
       DESTRUIR GRÁFICA ANTERIOR
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
       PALETA
    ===================================================== */

    const colores = [
        'rgba(45, 104, 155, 0.84)',
        'rgba(24, 158, 112, 0.82)',
        'rgba(209, 157, 69, 0.82)',
        'rgba(196, 100, 93, 0.80)',
        'rgba(111, 88, 153, 0.80)',
        'rgba(61, 137, 137, 0.80)',
        'rgba(110, 127, 142, 0.76)',
    ];


    const coloresHover = [
        '#285f8c',
        '#0d8f65',
        '#b98535',
        '#b75b54',
        '#65518f',
        '#357f7f',
        '#657581',
    ];


    /* =====================================================
       DATASETS
    ===================================================== */

    const datasets =
        series.map(
            (
                serie,
                indice
            ) => {

                const nombre =
                    String(
                        serie.nombre
                        || ''
                    ).trim();


                const valores =
                    Array.isArray(
                        serie.datos
                    )
                        ? serie.datos.map(
                            valor =>
                                Number(
                                    valor
                                )
                                || 0
                        )
                        : [];


                return {

                    label:
                        nombre,


                    data:
                        valores,


                    backgroundColor:
                        colores[
                            indice
                            % colores.length
                        ],


                    hoverBackgroundColor:
                        coloresHover[
                            indice
                            % coloresHover.length
                        ],


                    borderWidth:
                        0,


                    borderSkipped:
                        false,


                    borderRadius:
                        6,


                    barPercentage:
                        0.72,


                    categoryPercentage:
                        0.78,


                    maxBarThickness:
                        18,

                };

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
                    categorias,


                datasets:
                    datasets,

            },


            options: {

                responsive:
                    true,


                maintainAspectRatio:
                    false,


                indexAxis:
                    'y',


                animation: {

                    duration:
                        700,


                    easing:
                        'easeOutQuart',

                },


                interaction: {

                    mode:
                        'nearest',


                    axis:
                        'y',


                    intersect:
                        false,

                },


                layout: {

                    padding: {

                        top:
                            6,

                        right:
                            16,

                        bottom:
                            4,

                        left:
                            0,

                    },

                },


                scales: {

                    x: {

                        beginAtZero:
                            true,


                        stacked:
                            false,


                        border: {

                            display:
                                false,

                        },


                        ticks: {

                            precision:
                                0,


                            color:
                                '#87958f',


                            padding:
                                8,


                            font: {

                                size:
                                    9,

                            },

                        },


                        grid: {

                            color:
                                'rgba(102, 128, 118, 0.10)',


                            borderDash: [
                                4,
                                5,
                            ],


                            drawTicks:
                                false,

                        },

                    },


                    y: {

                        stacked:
                            false,


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
                                9,


                            font: {

                                size:
                                    9,


                                weight:
                                    '700',

                            },


                            callback(
                                value
                            ) {

                                const etiqueta =
                                    this.getLabelForValue(
                                        value
                                    );


                                if (
                                    etiqueta.length <= 28
                                ) {

                                    return etiqueta;

                                }


                                return (
                                    etiqueta.substring(
                                        0,
                                        27
                                    )
                                    + '…'
                                );

                            },

                        },

                    },

                },


                plugins: {

                    legend: {

                        display:
                            true,


                        position:
                            'top',


                        align:
                            'start',


                        labels: {

                            usePointStyle:
                                true,


                            pointStyle:
                                'circle',


                            boxWidth:
                                7,


                            boxHeight:
                                7,


                            padding:
                                14,


                            color:
                                '#60746c',


                            font: {

                                size:
                                    9,


                                weight:
                                    '600',

                            },

                        },

                    },


                    tooltip: {

                        enabled:
                            true,


                        displayColors:
                            true,


                        backgroundColor:
                            'rgba(255, 255, 255, 0.98)',


                        titleColor:
                            '#31453d',


                        bodyColor:
                            '#52685f',


                        borderColor:
                            'rgba(34, 99, 76, 0.14)',


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
                                        ?.dataIndex;


                                return (
                                    categorias[
                                        indice
                                    ]
                                    || ''
                                );

                            },


                            label(
                                contexto
                            ) {

                                const valor =
                                    Number(
                                        contexto.raw
                                        || 0
                                    );


                                const serie =
                                    contexto.dataset.label
                                    || '';


                                const porcentaje =
                                    total > 0
                                        ? (
                                            (
                                                valor
                                                / total
                                            )
                                            * 100
                                        )
                                        : 0;


                                return (
                                    `${serie}: `
                                    + `${valor} `
                                    + (
                                        valor === 1
                                            ? singularRegistro
                                            : pluralRegistro
                                    )
                                    + ` (${porcentaje.toFixed(1)}%)`
                                );

                            },

                        },

                    },

                },

            },

        }
    );

}