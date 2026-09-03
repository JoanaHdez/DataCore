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


    const fuenteDatos =
        document.querySelector(
            '#datos-grafica-catalogo'
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
            'No fue posible interpretar los datos del catálogo general:',
            error
        );

        return;
    }


    const clasificaciones =
        Array.isArray(
            datosBackend.clasificaciones
        )
            ? datosBackend.clasificaciones
            : [];


    const totales =
        Array.isArray(
            datosBackend.totales
        )
            ? datosBackend.totales.map(
                (total) =>
                    Number(total) || 0
            )
            : [];


    /* =====================================================
       CONVERTIR AL FORMATO DE LA GRÁFICA
    ===================================================== */

    const datosCatalogo =
        clasificaciones.map(
            (
                nombre,
                indice
            ) => {

                return {
                    nombre:
                        nombre,

                    valor:
                        totales[indice]
                        ?? 0,
                };

            }
        );


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

    const totalCalculado =
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
        datosOrdenados.length === 0
    ) {
        return;
    }


    /* =====================================================
       LABELS CORTOS
    ===================================================== */

    const labels =
        datosOrdenados.map(
            (elemento) => {

                return (
                    abreviarNombreCatalogo(
                        elemento.nombre
                    )
                );

            }
        );


    const valores =
        datosOrdenados.map(
            (elemento) => {

                return (
                    Number(
                        elemento.valor
                        || 0
                    )
                );

            }
        );


    /* =====================================================
       COLORES
    ===================================================== */

    const paletaCatalogo = [
        'rgba(28, 78, 121, 0.92)',   // Azul profundo
        'rgba(44, 104, 165, 0.86)',  // Azul institucional
        'rgba(72, 137, 190, 0.82)',  // Azul medio
        'rgba(76, 101, 168, 0.78)',  // Índigo
        'rgba(105, 91, 168, 0.74)',  // Violeta azulado
        'rgba(62, 137, 151, 0.76)',  // Azul petróleo
        'rgba(103, 157, 194, 0.72)', // Azul cielo
        'rgba(125, 116, 181, 0.70)', // Lavanda
        'rgba(92, 126, 163, 0.70)',  // Azul grisáceo
        'rgba(139, 164, 190, 0.66)', // Azul suave
    ];


    const paletaCatalogoHover = [
        '#174568',
        '#245c94',
        '#3b7faf',
        '#405a99',
        '#5b4f96',
        '#347987',
        '#568ead',
        '#6d63a2',
        '#536f8e',
        '#7893ad',
    ];


    const colores =
        datosOrdenados.map(
            (
                _,
                indice
            ) => {

                return (
                    paletaCatalogo[
                        indice
                        % paletaCatalogo.length
                    ]
                );

            }
        );


    const coloresHover =
        datosOrdenados.map(
            (
                _,
                indice
            ) => {

                return (
                    paletaCatalogoHover[
                        indice
                        % paletaCatalogoHover.length
                    ]
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
                            colores,

                        hoverBackgroundColor:
                            coloresHover,

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

                        /*
                         * Ya no usamos suggestedMax: 110
                         * ni stepSize: 20.
                         *
                         * Eran valores ajustados al Excel.
                         * Ahora Chart.js calcula la escala
                         * con los datos reales.
                         */

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


                                /*
                                 * El tooltip siempre conserva
                                 * la clasificación completa,
                                 * aunque visualmente la
                                 * abreviemos en el eje.
                                 */

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
                                    `${valor} ${
                                        valor === 1
                                            ? 'registro'
                                            : 'registros'
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


/* =========================================================
   ABREVIAR ETIQUETAS
========================================================= */

function abreviarNombreCatalogo(
    nombre
) {

    /*
     * Conservamos las abreviaciones que ya existían
     * para las clasificaciones institucionales largas
     * del Excel.
     *
     * Cuando aparezca cualquier clasificación nueva,
     * se utilizará automáticamente su nombre original.
     */

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


    if (
        equivalencias[
            nombre
        ]
    ) {

        return (
            equivalencias[
                nombre
            ]
        );

    }


    /*
     * Si llega una clasificación nueva y su nombre
     * es demasiado largo, evitamos que destruya
     * visualmente el eje.
     *
     * El nombre completo seguirá disponible
     * en el tooltip.
     */

    if (
        String(nombre).length
        > 34
    ) {

        return (
            String(nombre)
                .substring(
                    0,
                    31
                )
            + '...'
        );

    }


    return nombre;

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