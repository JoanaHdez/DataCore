document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaCatalogo();

    }
);


/* =========================================================
   GRÁFICA
   CATÁLOGO GENERAL
========================================================= */

function inicializarGraficaCatalogo() {

    const canvas =
        document.querySelector(
            '#grafica-catalogo'
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

    const datosCatalogo = [

        {
            nombre:
                'Daño a los bienes',

            valor:
                9,
        },

        {
            nombre:
                'Agresiones físicas y/o verbales',

            valor:
                61,
        },

        {
            nombre:
                'Omisión de apoyo',

            valor:
                56,
        },

        {
            nombre:
                'Poner por menor en peligro o riesgo la integridad física y moral de las personas así como sus bienes',

            valor:
                1,
        },

        {
            nombre:
                'Violencia de género',

            valor:
                1,
        },

        {
            nombre:
                'Extorsión',

            valor:
                5,
        },

        {
            nombre:
                'Detención ilegal',

            valor:
                2,
        },

        {
            nombre:
                'No abstenerse de realizar conductas que desacrediten su persona o la imagen de la institución dentro y fuera del servicio portando el uniforme institucional',

            valor:
                103,
        },

        {
            nombre:
                'Infringir el reglamento de tránsito sin causa que lo justifique, estando franco o en servicio',

            valor:
                2,
        },

        {
            nombre:
                'No dirigirse con respeto, educación y profesionalismo o por alterar o incitar el orden público en el ejercicio de sus funciones al primer contacto con la ciudadanía',

            valor:
                4,
        },

        {
            nombre:
                'Presentar a cualquier persona ante la autoridad competente y no hacer entrega de su documentación',

            valor:
                2,
        },

        {
            nombre:
                'Robo',

            valor:
                30,
        },

    ];


    /* =====================================================
       ORDENAR DE MAYOR A MENOR
    ===================================================== */

    const datosOrdenados =
        [...datosCatalogo]
            .sort(
                (
                    a,
                    b
                ) => {

                    return (
                        Number(
                            b.valor
                        )
                        - Number(
                            a.valor
                        )
                    );

                }
            );


    /* =====================================================
       TOTAL
    ===================================================== */

    const total =
        datosOrdenados.reduce(
            (
                acumulado,
                elemento
            ) => {

                return (
                    acumulado
                    + Number(
                        elemento.valor
                        || 0
                    )
                );

            },
            0
        );


    actualizarTexto(
        '#catalogo-total',
        total
    );


    /* =====================================================
       TOP 3
    ===================================================== */

    const primero =
        datosOrdenados[0]
        || null;


    const segundo =
        datosOrdenados[1]
        || null;


    const tercero =
        datosOrdenados[2]
        || null;


    actualizarTexto(
        '#catalogo-principal-nombre',
        primero?.nombre
        || 'Sin información'
    );


    actualizarTexto(
        '#catalogo-principal-total',
        primero?.valor
        ?? 0
    );


    actualizarTexto(
        '#catalogo-segundo-nombre',
        segundo?.nombre
        || 'Sin información'
    );


    actualizarTexto(
        '#catalogo-segundo-total',
        segundo?.valor
        ?? 0
    );


    actualizarTexto(
        '#catalogo-tercero-nombre',
        tercero?.nombre
        || 'Sin información'
    );


    actualizarTexto(
        '#catalogo-tercero-total',
        tercero?.valor
        ?? 0
    );


    /* =====================================================
       LABELS CORTOS
    ===================================================== */

    const labels =
        datosOrdenados.map(
            (
                elemento
            ) => {

                return abreviarNombreCatalogo(
                    elemento.nombre
                );

            }
        );


    const valores =
        datosOrdenados.map(
            (
                elemento
            ) => {

                return Number(
                    elemento.valor
                    || 0
                );

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
                    labels,

                datasets: [
                    {
                        label:
                            'Registros',

                        data:
                            valores,

                        backgroundColor:
                            datosOrdenados.map(
                                (
                                    _,
                                    indice
                                ) => {

                                    if (
                                        indice === 0
                                    ) {

                                        return (
                                            'rgba(12, 144, 99, 0.96)'
                                        );

                                    }


                                    if (
                                        indice === 1
                                    ) {

                                        return (
                                            'rgba(45, 161, 122, 0.82)'
                                        );

                                    }


                                    if (
                                        indice === 2
                                    ) {

                                        return (
                                            'rgba(73, 177, 140, 0.72)'
                                        );

                                    }


                                    return (
                                        'rgba(133, 198, 175, 0.54)'
                                    );

                                }
                            ),

                        hoverBackgroundColor:
                            datosOrdenados.map(
                                (
                                    _,
                                    indice
                                ) => {

                                    if (
                                        indice === 0
                                    ) {

                                        return (
                                            '#087d58'
                                        );

                                    }


                                    if (
                                        indice === 1
                                    ) {

                                        return (
                                            '#1f906b'
                                        );

                                    }


                                    if (
                                        indice === 2
                                    ) {

                                        return (
                                            '#3c9f7d'
                                        );

                                    }


                                    return (
                                        '#77bfa5'
                                    );

                                }
                            ),

                        borderWidth:
                            0,

                        borderSkipped:
                            false,

                        borderRadius:
                            10,

                        barPercentage:
                            0.62,

                        categoryPercentage:
                            0.74,

                        maxBarThickness:
                            24,

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
                        800,

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
                            8,

                        right:
                            18,

                        bottom:
                            6,

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
                            110,

                        border: {

                            display:
                                false,

                        },

                        ticks: {

                            precision:
                                0,

                            stepSize:
                                20,

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
                                '600',

                        },

                        callbacks: {

                            title(
                                elementos
                            ) {

                                const indice =
                                    elementos[0]
                                        ?.dataIndex
                                    ?? 0;


                                return (
                                    datosOrdenados[
                                        indice
                                    ]
                                        ?.nombre
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
                                    `${valor} registros `
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


/* =========================================================
   ABREVIAR ETIQUETAS
========================================================= */

function abreviarNombreCatalogo(
    nombre
) {

    const equivalencias = {

        'No abstenerse de realizar conductas que desacrediten su persona o la imagen de la institución dentro y fuera del servicio portando el uniforme institucional':
            'Conducta institucional',

        'Poner por menor en peligro o riesgo la integridad física y moral de las personas así como sus bienes':
            'Riesgo a personas o bienes',

        'Infringir el reglamento de tránsito sin causa que lo justifique, estando franco o en servicio':
            'Infracción de tránsito',

        'No dirigirse con respeto, educación y profesionalismo o por alterar o incitar el orden público en el ejercicio de sus funciones al primer contacto con la ciudadanía':
            'Falta de respeto / profesionalismo',

        'Presentar a cualquier persona ante la autoridad competente y no hacer entrega de su documentación':
            'Falta de documentación',

    };


    return (
        equivalencias[
            nombre
        ]
        || nombre
    );

}


/* =========================================================
   ACTUALIZAR TEXTO
========================================================= */

function actualizarTexto(
    selector,
    valor
) {

    const elemento =
        document.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    elemento.textContent =
        String(
            valor
            ?? ''
        );

}