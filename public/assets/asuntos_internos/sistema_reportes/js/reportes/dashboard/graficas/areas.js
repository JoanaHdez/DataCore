document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaSectoresTurnos();

    }
);


/* =========================================================
   GRÁFICA
   QUEJAS POR SECTORES Y TURNOS
========================================================= */

function inicializarGraficaSectoresTurnos() {

    const canvas =
        document.querySelector(
            '#grafica-sectores-turnos'
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

    /* =====================================================
   DATOS REALES DEL BACKEND
===================================================== */

const fuenteDatos =
    document.querySelector(
        '#datos-grafica-sectores-turnos'
    );


if (!fuenteDatos) {
    return;
}


let datosBackend;


try {

    datosBackend =
        JSON.parse(
            fuenteDatos.textContent
            || '{}'
        );

} catch (error) {

    console.error(
        'No fue posible interpretar los datos de sectores y turnos:',
        error
    );

    return;
}


const sectores =
    Array.isArray(
        datosBackend.sectores
    )
        ? datosBackend.sectores
        : [];


const turnosBackend =
    datosBackend.turnos
    || {};


const datos = {

    primerTurno:
        turnosBackend[
            'Primer turno'
        ]
        || [],

    segundoTurno:
        turnosBackend[
            'Segundo turno'
        ]
        || [],

    tercerTurno:
        turnosBackend[
            'Tercer turno'
        ]
        || [],

    alfa:
        turnosBackend[
            'Alfa'
        ]
        || [],

    beta:
        turnosBackend[
            'Beta'
        ]
        || [],

    diario:
        turnosBackend[
            'Diario'
        ]
        || [],

    noRefiere:
        turnosBackend[
            'No refiere ni fecha ni horario'
        ]
        || [],

};


    /* =====================================================
       CONFIGURACIÓN COMÚN DE BARRAS
    ===================================================== */

    const configuracionBarra = {

        borderWidth:
            0,

        borderSkipped:
            false,

        borderRadius:
            14,

        categoryPercentage:
            0.76,

        barPercentage:
            0.72,

        maxBarThickness:
            24,

        hoverBorderWidth:
            0,

    };


    /* =====================================================
       DATASETS
    ===================================================== */

    const datasets = [

    {
        label:
            'Primer turno',

        data:
            datos.primerTurno,

        backgroundColor:
            'rgba(30, 88, 138, 0.88)',

        hoverBackgroundColor:
            '#174a78',

        ...configuracionBarra,
    },

    {
        label:
            'Segundo turno',

        data:
            datos.segundoTurno,

        backgroundColor:
            'rgba(48, 126, 184, 0.82)',

        hoverBackgroundColor:
            '#2670aa',

        ...configuracionBarra,
    },

    {
        label:
            'Tercer turno',

        data:
            datos.tercerTurno,

        backgroundColor:
            'rgba(78, 157, 207, 0.78)',

        hoverBackgroundColor:
            '#3d8fc4',

        ...configuracionBarra,
    },

    {
        label:
            'Alfa',

        data:
            datos.alfa,

        backgroundColor:
            'rgba(89, 112, 176, 0.78)',

        hoverBackgroundColor:
            '#485f9f',

        ...configuracionBarra,
    },

    {
        label:
            'Beta',

        data:
            datos.beta,

        backgroundColor:
            'rgba(111, 92, 168, 0.72)',

        hoverBackgroundColor:
            '#625092',

        ...configuracionBarra,
    },

    {
        label:
            'Diario',

        data:
            datos.diario,

        backgroundColor:
            'rgba(71, 146, 158, 0.74)',

        hoverBackgroundColor:
            '#397f8b',

        ...configuracionBarra,
    },

    {
        label:
            'No refiere ni fecha ni horario',

        data:
            datos.noRefiere,

        backgroundColor:
            'rgba(145, 158, 172, 0.68)',

        hoverBackgroundColor:
            '#788896',

        ...configuracionBarra,
    },

];


    /* =====================================================
       CREAR GRÁFICA
    ===================================================== */

    const grafica =
        new Chart(
            canvas,
            {
                type:
                    'bar',

                data: {

                    labels:
                        sectores,

                    datasets:
                        datasets,

                },

                options: {

                    responsive:
                        true,

                    maintainAspectRatio:
                        false,


                    /* =============================================
                       ANIMACIÓN
                    ============================================= */

                    animation: {

                        duration:
                            700,

                        easing:
                            'easeOutQuart',

                    },


                    /* =============================================
                       INTERACCIÓN
                    ============================================= */

                    interaction: {

                        mode:
                            'index',

                        intersect:
                            false,

                    },


                    /* =============================================
                       ESPACIADO
                    ============================================= */

                    layout: {

                        padding: {

                            top:
                                14,

                            right:
                                8,

                            bottom:
                                0,

                            left:
                                4,

                        },

                    },


                    /* =============================================
                       EJES
                    ============================================= */

                    scales: {

                        x: {

                            stacked:
                                false,

                            offset:
                                true,

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

                                maxRotation:
                                    40,

                                minRotation:
                                    40,

                                padding:
                                    10,

                                color:
                                    '#788984',

                                font: {

                                    size:
                                        8,

                                    weight:
                                        '600',

                                },

                            },

                        },


                        y: {

                            beginAtZero:
                                true,

                            suggestedMax:
                                3,

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
                                    10,

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
                                    'rgba(102, 127, 118, 0.13)',

                                lineWidth:
                                    1,

                                borderDash: [
                                    4,
                                    4,
                                ],

                                drawTicks:
                                    false,

                            },

                        },

                    },


                    /* =============================================
                       PLUGINS
                    ============================================= */

                    plugins: {

                        /* =========================================
                           LEYENDA
                        ========================================= */

                        legend: {

                            display:
                                true,

                            position:
                                'bottom',

                            align:
                                'center',

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
                                    17,

                                color:
                                    '#667873',

                                font: {

                                    size:
                                        9,

                                    weight:
                                        '600',

                                },

                            },

                        },


                        /* =========================================
                           TOOLTIP
                        ========================================= */

                        tooltip: {

                            enabled:
                                true,

                            displayColors:
                                true,

                            backgroundColor:
                                'rgba(255, 255, 255, 0.98)',

                            titleColor:
                                '#2b3d38',

                            bodyColor:
                                '#53645f',

                            borderColor:
                                'rgba(24, 137, 98, 0.16)',

                            borderWidth:
                                1,

                            cornerRadius:
                                12,

                            padding:
                                12,

                            boxPadding:
                                5,

                            caretPadding:
                                10,

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
                                        `${contexto.dataset.label}: `
                                        + `${valor}`
                                    );

                                },

                            },

                        },

                    },

                },

            }
        );


    /* =====================================================
       CONTROLES SUPERIORES
    ===================================================== */

    inicializarPeriodosGrafica(
        grafica
    );

}


/* =========================================================
   CONTROLES DE PERIODO
========================================================= */

function inicializarPeriodosGrafica(
    grafica
) {

    const botones =
        document.querySelectorAll(
            '[data-periodo-grafica]'
        );


    botones.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                () => {

                    botones.forEach(
                        (elemento) => {

                            elemento.classList.remove(
                                'dashboard-grafica__periodo--activo'
                            );

                        }
                    );


                    boton.classList.add(
                        'dashboard-grafica__periodo--activo'
                    );


                    /*
                     * De momento solo actualizamos
                     * el estado visual.
                     *
                     * Cuando conectemos filtros reales
                     * usaremos el periodo seleccionado.
                     */
                    grafica.update();

                }
            );

        }
    );

}