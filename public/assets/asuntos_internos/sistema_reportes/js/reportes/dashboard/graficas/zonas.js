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


    const datosElemento =
        document.querySelector(
            '#dashboard-datos-zonas'
        );


    if (
        !canvas
        || !datosElemento
        || typeof Chart === 'undefined'
    ) {

        return;
    }


    /* =====================================================
       DATOS DEL BACKEND
    ===================================================== */

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
       TIPO ACTIVO
    ===================================================== */

    const tipo =
        String(
            datosServidor.tipo
            || 'queja'
        )
            .trim()
            .toLowerCase();


    const esFelicitacion =
        tipo === 'felicitacion';


    const etiquetaDataset =
        esFelicitacion
            ? 'Felicitaciones'
            : 'Quejas';


    const singularRegistro =
        esFelicitacion
            ? 'felicitación'
            : 'queja';


    const pluralRegistro =
        esFelicitacion
            ? 'felicitaciones'
            : 'quejas';


    /* =====================================================
       NORMALIZAR DATOS
    ===================================================== */

    const labels =
        Array.isArray(
            datosServidor.zonas
        )
            ? datosServidor.zonas.map(
                zona =>
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
                valor => {

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


    /* =====================================================
       TOTAL
    ===================================================== */

    const totalCalculado =
        valores.reduce(
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


    const totalBackend =
        Number(
            datosServidor.total
        );


    const total =
        Number.isFinite(
            totalBackend
        )
            ? totalBackend
            : totalCalculado;


    const totalElemento =
        document.querySelector(
            '#grafica-zonas-total'
        );


    if (
        totalElemento
    ) {

        totalElemento.textContent =
            String(
                total
            );

    }


    /* =====================================================
       SIN DATOS
    ===================================================== */

    if (
        labels.length === 0
        || valores.length === 0
        || total <= 0
    ) {

        mostrarPlaceholderGraficaDashboard(
            canvas
        );

        return;
    }


    /* =====================================================
       PORCENTAJES
    ===================================================== */

    const porcentajes =
        valores.map(
            valor => {

                if (
                    total <= 0
                ) {

                    return 0;
                }


                return (
                    (
                        Number(
                            valor
                        )
                        / total
                    )
                    * 100
                );

            }
        );


    /* =====================================================
       DESTRUIR GRÁFICA PREVIA
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
                    labels,


                datasets: [
                    {

                        label:
                            etiquetaDataset,


                        data:
                            valores,


                        /* =========================================
                           COLORES POR ZONA
                        ========================================= */

                        backgroundColor: [
                            'rgba(55, 112, 165, 0.84)',
                            'rgba(52, 151, 151, 0.82)',
                            'rgba(211, 158, 72, 0.82)',
                            'rgba(202, 105, 96, 0.80)',
                        ],


                        hoverBackgroundColor: [
                            '#2d6498',
                            '#2c8585',
                            '#b98535',
                            '#b75b54',
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
                                    12,


                                weight:
                                    '700',

                            },

                        },

                    },


                    y: {

                        beginAtZero:
                            true,


                        border: {

                            display:
                                false,

                        },


                        ticks: {

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
                                    12,

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
                                13,


                            weight:
                                '600',

                        },


                        bodyFont: {

                            size:
                                14,


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
                                    `${valor} `
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


function mostrarPlaceholderGraficaDashboard(
    canvas
) {

    const contenedor =
        canvas?.parentElement
        ?? null;


    if (!contenedor) {
        return;
    }


    canvas.hidden =
        true;


    if (
        contenedor.querySelector(
            '.dashboard-grafica__placeholder'
        )
    ) {
        return;
    }


    const placeholder =
        document.createElement(
            'div'
        );


    placeholder.className =
        'dashboard-grafica__placeholder';


    placeholder.innerHTML =
        '<strong>Sin datos</strong><span>Sin datos para los filtros seleccionados.</span>';


    contenedor.appendChild(
        placeholder
    );
}
