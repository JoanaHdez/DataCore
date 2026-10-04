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
            'No fue posible interpretar los datos de turnos:',
            error
        );

        return;
    }


    /* =====================================================
       TIPO ACTIVO
    ===================================================== */

    const tipo =
        String(
            datosBackend.tipo
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
       TURNOS / TOTALES
    ===================================================== */

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
                total =>
                    Number(total)
                    || 0
            )
            : [];


    /* =====================================================
       NORMALIZAR DATOS
    ===================================================== */

    const datosTurnos =
        turnos
            .map(
                (
                    nombre,
                    indice
                ) => {

                    return {

                        nombre:
                            String(
                                nombre
                                || ''
                            ).trim(),

                        valor:
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
                turno =>
                    turno.nombre !== ''
            );


    const sinTurnos =
        datosTurnos.length === 0;


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
        sinTurnos
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
        datosTurnos.map(
            turno => {

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
                )
                    * 100;

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
                        turno =>
                            turno.nombre
                    ),


                datasets: [
                    {

                        label:
                            etiquetaDataset,


                        data:
                            datosTurnos.map(
                                turno =>
                                    turno.valor
                            ),


                        backgroundColor: [
                            'rgba(47, 111, 164, 0.88)',
                            'rgba(53, 151, 151, 0.82)',
                            'rgba(116, 94, 164, 0.80)',
                            'rgba(211, 158, 72, 0.82)',
                            'rgba(202, 105, 96, 0.78)',
                            'rgba(77, 137, 116, 0.78)',
                            'rgba(132, 145, 160, 0.72)',
                        ],


                        hoverBackgroundColor: [
                            '#285f8c',
                            '#2d8585',
                            '#65518f',
                            '#b98535',
                            '#b75b54',
                            '#3f7864',
                            '#738190',
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
