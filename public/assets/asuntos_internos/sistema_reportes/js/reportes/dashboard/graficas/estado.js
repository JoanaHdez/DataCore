document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaEstadoDashboard();

    }
);


/* =========================================================
   ESTADO DE LAS QUEJAS
========================================================= */

function inicializarGraficaEstadoDashboard() {

    const canvas =
        document.querySelector(
            '#dashboard-estado-chart'
        );


    const fuenteDatos =
        document.querySelector(
            '#dashboard-estado-datos'
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
            'Chart.js no está disponible para la gráfica de estado de las quejas.'
        );

        return;
    }


    /* =====================================================
       LEER DATOS
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
            'No fue posible interpretar los datos de estado de las quejas:',
            error
        );

        return;
    }


    const estados =
        Array.isArray(
            datosBackend.estados
        )
            ? datosBackend.estados
            : [];


    const totales =
        Array.isArray(
            datosBackend.totales
        )
            ? datosBackend.totales.map(
                total =>
                    Number(total) || 0
            )
            : [];


    const porcentajes =
        Array.isArray(
            datosBackend.porcentajes
        )
            ? datosBackend.porcentajes.map(
                porcentaje =>
                    Number(porcentaje) || 0
            )
            : [];


    const totalGeneral =
        Number(
            datosBackend.total
            || 0
        );


    if (
        estados.length === 0
        || totales.length === 0
        || totalGeneral <= 0
    ) {

        return;
    }


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
       COLORES SEMÁNTICOS
    ===================================================== */

    const colores =
        estados.map(
            estado =>
                obtenerColorEstadoDashboard(
                    estado
                )
        );


    const coloresHover =
        estados.map(
            estado =>
                obtenerColorHoverEstadoDashboard(
                    estado
                )
        );


    /* =====================================================
       GRÁFICA
    ===================================================== */

    new Chart(
        canvas,
        {
            type:
                'doughnut',


            data: {

                labels:
                    estados,


                datasets: [
                    {

                        data:
                            totales,


                        backgroundColor:
                            colores,


                        hoverBackgroundColor:
                            coloresHover,


                        borderColor:
                            '#ffffff',


                        borderWidth:
                            4,


                        hoverBorderWidth:
                            4,

                    },
                ],

            },


            options: {

                responsive:
                    true,


                maintainAspectRatio:
                    false,


                cutout:
                    '68%',


                layout: {

                    padding:
                        8,

                },


                animation: {

                    duration:
                        700,


                    easing:
                        'easeOutQuart',

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
                            '#243d34',


                        bodyColor:
                            '#173554',


                        borderColor:
                            'rgba(23, 53, 84, 0.12)',


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

                                const indice =
                                    context.dataIndex;


                                const total =
                                    Number(
                                        context.raw
                                    )
                                    || 0;


                                const porcentaje =
                                    Number(
                                        porcentajes[
                                            indice
                                        ]
                                        || 0
                                    );


                                return (
                                    `${total} quejas · ${porcentaje}%`
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
   COLOR POR ESTADO
========================================================= */

function obtenerColorEstadoDashboard(
    estado
) {

    const valor =
        String(
            estado
            || ''
        )
            .trim()
            .toUpperCase();


    if (
        valor === 'PENDIENTE'
    ) {

        return '#d69c25';
    }


    if (
        valor === 'EN PROCESO'
    ) {

        return '#527da4';
    }


    if (
        valor === 'FINALIZADO'
    ) {

        return '#159668';
    }


    return '#889792';
}


/* =========================================================
   COLOR HOVER POR ESTADO
========================================================= */

function obtenerColorHoverEstadoDashboard(
    estado
) {

    const valor =
        String(
            estado
            || ''
        )
            .trim()
            .toUpperCase();


    if (
        valor === 'PENDIENTE'
    ) {

        return '#bf8617';
    }


    if (
        valor === 'EN PROCESO'
    ) {

        return '#426f97';
    }


    if (
        valor === 'FINALIZADO'
    ) {

        return '#0d8a5f';
    }


    return '#71807b';
}
