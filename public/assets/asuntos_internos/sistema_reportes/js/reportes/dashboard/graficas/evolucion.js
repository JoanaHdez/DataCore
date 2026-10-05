document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGraficaEvolucionDashboard();

    }
);


/* =========================================================
   EVOLUCIÓN TEMPORAL
========================================================= */

function inicializarGraficaEvolucionDashboard() {

    const canvas =
        document.querySelector(
            '#dashboard-evolucion-chart'
        );


    const fuenteDatos =
        document.querySelector(
            '#dashboard-evolucion-datos'
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
            'Chart.js no está disponible para la gráfica de evolución temporal.'
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
            'No fue posible interpretar los datos de evolución temporal:',
            error
        );

        return;
    }


    const tipo =
        String(
            datosBackend.tipo
            || 'reporte'
        )
            .trim()
            .toLowerCase();


    const agrupacion =
        String(
            datosBackend.agrupacion
            || 'dia'
        )
            .trim()
            .toLowerCase();


    const datos =
        Array.isArray(
            datosBackend.datos
        )
            ? datosBackend.datos
            : [];


    if (
        datos.length === 0
    ) {

        return;
    }


    /* =====================================================
       NORMALIZAR DATOS
    ===================================================== */

    const registros =
        datos
            .map(
                registro => {

                    const fecha =
                        String(
                            registro.fecha
                            || ''
                        ).trim();


                    const total =
                        Number(
                            registro.total
                            || 0
                        );


                    return {
                        fecha,
                        total,
                    };

                }
            )
            .filter(
                registro =>
                    registro.fecha !== ''
            );


    if (
        registros.length === 0
    ) {

        return;
    }


    /* =====================================================
       ETIQUETAS
    ===================================================== */

    const etiquetas =
        registros.map(
            registro =>
                formatearEtiquetaEvolucionDashboard(
                    registro.fecha,
                    agrupacion
                )
        );


    const totales =
        registros.map(
            registro =>
                registro.total
        );


    /* =====================================================
       TEXTO DEL DATASET
    ===================================================== */

    const etiquetaDataset =
        tipo === 'felicitacion'
            ? 'Felicitaciones'
            : 'Quejas';


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
       GRADIENTE
    ===================================================== */

    const contexto =
        canvas.getContext(
            '2d'
        );


    const gradiente =
        contexto.createLinearGradient(
            0,
            0,
            0,
            canvas.clientHeight || 300
        );


    gradiente.addColorStop(
        0,
        'rgba(22, 129, 196, 0.22)'
    );


    gradiente.addColorStop(
        1,
        'rgba(22, 129, 196, 0.02)'
    );


    /* =====================================================
       GRÁFICA
    ===================================================== */

    new Chart(
        canvas,
        {
            type:
                'line',


            data: {

                labels:
                    etiquetas,


                datasets: [
                    {

                        label:
                            etiquetaDataset,


                        data:
                            totales,


                        borderColor:
                            '#1681c4',


                        backgroundColor:
                            gradiente,


                        pointBackgroundColor:
                            '#1681c4',


                        pointBorderColor:
                            '#ffffff',


                        pointBorderWidth:
                            2,


                        pointRadius:
                            4,


                        pointHoverRadius:
                            6,


                        pointHoverBorderWidth:
                            3,


                        borderWidth:
                            2.5,


                        tension:
                            0.35,


                        fill:
                            true,

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
                        700,


                    easing:
                        'easeOutQuart',

                },


                /* =================================================
                   INTERACCIÓN
                ================================================= */

                interaction: {

                    mode:
                        'index',


                    intersect:
                        false,

                },


                /* =================================================
                   ESPACIADO
                ================================================= */

                layout: {

                    padding: {

                        top:
                            12,


                        right:
                            14,


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
                                        'cantidad',
                                    offsetLinea:
                                        14,
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
                            '#1681c4',


                        borderColor:
                            'rgba(22, 129, 196, 0.16)',


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
                                '700',

                        },


                        bodyFont: {

                            size:
                                13,


                            weight:
                                '700',

                        },


                        callbacks: {

                            title(
                                context
                            ) {

                                const indice =
                                    context[0]
                                        ?.dataIndex
                                    ?? 0;


                                const registro =
                                    registros[
                                        indice
                                    ];


                                return (
                                    formatearTooltipFechaEvolucionDashboard(
                                        registro?.fecha
                                        || '',
                                        agrupacion
                                    )
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


                                const singular =
                                    tipo === 'felicitacion'
                                        ? 'felicitación'
                                        : 'queja';


                                const plural =
                                    tipo === 'felicitacion'
                                        ? 'felicitaciones'
                                        : 'quejas';


                                return (
                                    `${total} `
                                    + (
                                        total === 1
                                            ? singular
                                            : plural
                                    )
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
                                '#7f8f89',


                            padding:
                                10,


                            maxRotation:
                                0,


                            minRotation:
                                0,


                            autoSkip:
                                true,


                            maxTicksLimit:
                                8,


                            font: {

                                size:
                                    12,


                                weight:
                                    '600',

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


                            stepSize:
                                1,


                            padding:
                                10,


                            color:
                                '#8a9994',


                            font: {

                                size:
                                    12,


                                weight:
                                    '500',

                            },

                        },


                        grid: {

                            color:
                                'rgba(103, 130, 119, 0.09)',


                            borderDash: [
                                4,
                                4,
                            ],


                            drawTicks:
                                false,

                        },

                    },

                },

            },

        }
    );

}


/* =========================================================
   ETIQUETA DEL EJE X
========================================================= */

function formatearEtiquetaEvolucionDashboard(
    fecha,
    agrupacion
) {

    const valor =
        String(
            fecha
            || ''
        ).trim();


    if (
        valor === ''
    ) {

        return '';
    }


    /* =====================================================
       AGRUPACIÓN MENSUAL

       Esperamos normalmente:
       YYYY-MM
       o YYYY-MM-DD
    ===================================================== */

    if (
        agrupacion === 'mes'
    ) {

        const partes =
            valor.split(
                '-'
            );


        const anio =
            Number(
                partes[0]
            );


        const mes =
            Number(
                partes[1]
            );


        if (
            anio > 0
            && mes >= 1
            && mes <= 12
        ) {

            const fechaMes =
                new Date(
                    anio,
                    mes - 1,
                    1
                );


            return fechaMes
                .toLocaleDateString(
                    'es-MX',
                    {
                        month:
                            'short',
                        year:
                            '2-digit',
                    }
                )
                .replace(
                    '.',
                    ''
                );

        }

    }


    /* =====================================================
       AGRUPACIÓN SEMANAL
    ===================================================== */

    if (
        agrupacion === 'semana'
    ) {

        const fechaObjeto =
            crearFechaEvolucionDashboard(
                valor
            );


        if (
            fechaObjeto
        ) {

            return fechaObjeto
                .toLocaleDateString(
                    'es-MX',
                    {
                        day:
                            '2-digit',
                        month:
                            'short',
                    }
                )
                .replace(
                    '.',
                    ''
                );
        }

    }


    /* =====================================================
       AGRUPACIÓN DIARIA
    ===================================================== */

    const fechaObjeto =
        crearFechaEvolucionDashboard(
            valor
        );


    if (
        fechaObjeto
    ) {

        return fechaObjeto
            .toLocaleDateString(
                'es-MX',
                {
                    day:
                        '2-digit',
                    month:
                        'short',
                }
            )
            .replace(
                '.',
                ''
            );

    }


    return valor;
}


/* =========================================================
   FECHA PARA TOOLTIP
========================================================= */

function formatearTooltipFechaEvolucionDashboard(
    fecha,
    agrupacion
) {

    const valor =
        String(
            fecha
            || ''
        ).trim();


    if (
        valor === ''
    ) {

        return '';
    }


    if (
        agrupacion === 'mes'
    ) {

        const partes =
            valor.split(
                '-'
            );


        const anio =
            Number(
                partes[0]
            );


        const mes =
            Number(
                partes[1]
            );


        if (
            anio > 0
            && mes >= 1
            && mes <= 12
        ) {

            return new Date(
                anio,
                mes - 1,
                1
            )
                .toLocaleDateString(
                    'es-MX',
                    {
                        month:
                            'long',
                        year:
                            'numeric',
                    }
                );
        }

    }


    const fechaObjeto =
        crearFechaEvolucionDashboard(
            valor
        );


    if (
        fechaObjeto
    ) {

        const textoFecha =
            fechaObjeto
                .toLocaleDateString(
                    'es-MX',
                    {
                        day:
                            '2-digit',
                        month:
                            'long',
                        year:
                            'numeric',
                    }
                );


        if (
            agrupacion === 'semana'
        ) {

            return (
                `Semana de ${textoFecha}`
            );
        }


        return textoFecha;
    }


    return valor;
}


/* =========================================================
   CREAR FECHA LOCAL

   Evita desplazamientos de día provocados por interpretar
   YYYY-MM-DD directamente como UTC.
========================================================= */

function crearFechaEvolucionDashboard(
    valor
) {

    const partes =
        String(
            valor
            || ''
        )
            .split(
                '-'
            );


    if (
        partes.length < 3
    ) {

        return null;
    }


    const anio =
        Number(
            partes[0]
        );


    const mes =
        Number(
            partes[1]
        );


    const dia =
        Number(
            partes[2]
        );


    if (
        anio <= 0
        || mes < 1
        || mes > 12
        || dia < 1
        || dia > 31
    ) {

        return null;
    }


    return new Date(
        anio,
        mes - 1,
        dia
    );

}
