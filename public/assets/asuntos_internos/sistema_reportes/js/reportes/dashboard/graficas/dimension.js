/* =========================================================
   DASHBOARD
   ANÁLISIS POR DIMENSIÓN
   ÁREA / UNIDAD
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarDimensionDashboard();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarDimensionDashboard() {

    inicializarSelectorDimension();

    inicializarGraficaDimension();

}


/* =========================================================
   SELECTOR ÁREA / UNIDAD
========================================================= */

function inicializarSelectorDimension() {

    const botones =
        document.querySelectorAll(
            '.dashboard-dimension__tab[data-dimension]'
        );


    if (
        botones.length === 0
    ) {

        return;
    }


    botones.forEach(
        boton => {

            boton.addEventListener(
                'click',
                () => {

                    const dimension =
                        String(
                            boton.dataset.dimension
                            || ''
                        ).trim();


                    if (
                        dimension === ''
                    ) {

                        return;
                    }


                    const activo =
                        boton.classList.contains(
                            'dashboard-dimension__tab--activo'
                        );


                    if (
                        activo
                    ) {

                        return;
                    }


                    const url =
                        new URL(
                            window.location.href
                        );


                    url.searchParams.set(
                        'dimension',
                        dimension
                    );


                    window.location.href =
                        url.toString();

                }
            );

        }
    );

}


/* =========================================================
   GRÁFICA
========================================================= */

function inicializarGraficaDimension() {

    const canvas =
        document.querySelector(
            '#dashboard-dimension-chart'
        );


    const datosElemento =
        document.querySelector(
            '#dashboard-dimension-datos'
        );


    const contenedor =
        document.querySelector(
            '#dashboard-dimension-chart-container'
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
       LEER DATOS
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
            'No fue posible interpretar los datos de dimensión:',
            error
        );

        return;
    }


    const etiquetas =
        Array.isArray(
            datos.etiquetas
        )
            ? datos.etiquetas.map(
                etiqueta =>
                    String(
                        etiqueta
                        || ''
                    ).trim()
            )
            : [];


    const totales =
        Array.isArray(
            datos.totales
        )
            ? datos.totales.map(
                total =>
                    Number(
                        total
                    )
                    || 0
            )
            : [];


    const porcentajes =
        Array.isArray(
            datos.porcentajes
        )
            ? datos.porcentajes.map(
                porcentaje =>
                    Number(
                        porcentaje
                    )
                    || 0
            )
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


    const singular =
        esFelicitacion
            ? 'felicitación'
            : 'queja';


    const plural =
        esFelicitacion
            ? 'felicitaciones'
            : 'quejas';


    if (
        etiquetas.length === 0
        || total <= 0
    ) {

        return;
    }


    /* =====================================================
       ALTURA DINÁMICA DEL CONTENIDO

       La ventana visible permanece limitada por CSS.
       Si existen muchas categorías, aparece scroll.
    ===================================================== */

    const alturaPorCategoria =
        42;


    const alturaMinima =
        380;


    const alturaCalculada =
        Math.max(
            alturaMinima,
            etiquetas.length
            * alturaPorCategoria
        );


    contenedor.style.height =
        `${alturaCalculada}px`;


    /* =====================================================
       DESTRUIR INSTANCIA PREVIA
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
       CREAR GRÁFICA
    ===================================================== */

    new Chart(
        canvas,
        {
            type:
                'bar',


            data: {

                labels:
                    etiquetas,


                datasets: [
                    {

                        label:
                            esFelicitacion
                                ? 'Felicitaciones'
                                : 'Quejas',


                        data:
                            totales,


                        backgroundColor:
                            'rgba(25, 160, 114, 0.78)',


                        hoverBackgroundColor:
                            '#0f8f65',


                        borderWidth:
                            0,


                        borderSkipped:
                            false,


                        borderRadius:
                            8,


                        barPercentage:
                            0.58,


                        categoryPercentage:
                            0.72,


                        maxBarThickness:
                            20,

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


                animation: {

                    duration:
                        700,


                    easing:
                        'easeOutQuart',

                },


                interaction: {

                    mode:
                        'nearest',


                    intersect:
                        false,

                },


                layout: {

                    padding: {

                        top:
                            4,

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


                        border: {

                            display:
                                false,

                        },


                        ticks: {

                            precision:
                                0,


                            color:
                                '#8c9994',


                            padding:
                                7,


                            font: {

                                size:
                                    9,

                            },

                        },


                        grid: {

                            color:
                                'rgba(104, 130, 120, 0.10)',


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
                                '#485c54',


                            padding:
                                8,


                            font: {

                                size:
                                    9,


                                weight:
                                    '600',

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

                                const indice =
                                    elementos[0]
                                        ?.dataIndex;


                                return (
                                    etiquetas[
                                        indice
                                    ]
                                    || ''
                                );

                            },


                            label(
                                contexto
                            ) {

                                const indice =
                                    contexto.dataIndex;


                                const cantidad =
                                    Number(
                                        contexto.raw
                                        || 0
                                    );


                                const porcentaje =
                                    Number(
                                        porcentajes[
                                            indice
                                        ]
                                        || 0
                                    );


                                return (
                                    `${cantidad} `
                                    + (
                                        cantidad === 1
                                            ? singular
                                            : plural
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
