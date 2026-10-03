/* =========================================================
   DASHBOARD - RANKING TOP 5
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarRankingDashboard();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarRankingDashboard() {

    inicializarSelectorRanking();

    inicializarGraficaRanking();

    inicializarDetalleRanking();

}


/* =========================================================
   SELECTOR
========================================================= */

function inicializarSelectorRanking() {

    const botones =
        document.querySelectorAll(
            '.dashboard-ranking__tab[data-ranking]'
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

                    const ranking =
                        String(
                            boton.dataset.ranking
                            || ''
                        ).trim();


                    if (
                        ranking === ''
                    ) {

                        return;
                    }


                    if (
                        boton.classList.contains(
                            'dashboard-ranking__tab--activo'
                        )
                    ) {

                        return;
                    }


                    const url =
                        new URL(
                            window.location.href
                        );


                    url.searchParams.set(
                        'ranking',
                        ranking
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

function inicializarGraficaRanking() {

    const canvas =
        document.querySelector(
            '#dashboard-ranking-chart'
        );


    const datosElemento =
        document.querySelector(
            '#dashboard-ranking-datos'
        );


    if (
        !canvas
        || !datosElemento
        || typeof Chart === 'undefined'
    ) {

        return;
    }


    let datos;


    try {

        datos =
            JSON.parse(
                datosElemento.textContent
                || '{}'
            );

    } catch (error) {

        console.error(
            'No fue posible interpretar los datos del ranking:',
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
                valor =>
                    Number(
                        valor
                    )
                    || 0
            )
            : [];


    const porcentajes =
        Array.isArray(
            datos.porcentajes
        )
            ? datos.porcentajes.map(
                valor =>
                    Number(
                        valor
                    )
                    || 0
            )
            : [];


    const totalTop =
        Number(
            datos.total_top
            || 0
        );


    const tipoRegistro =
        String(
            datos.tipo_registro
            || 'reporte'
        )
            .trim()
            .toLowerCase();


    const esFelicitacion =
        tipoRegistro === 'felicitacion';


    if (
        etiquetas.length === 0
        || totalTop <= 0
    ) {

        return;
    }


    /* =====================================================
       COLORES
    ===================================================== */

    const coloresQuejas = [
        'rgba(49, 95, 130, 0.92)',
        'rgba(82, 125, 164, 0.84)',
        'rgba(184, 126, 89, 0.78)',
        'rgba(112, 133, 145, 0.70)',
        'rgba(113, 103, 143, 0.70)',
    ];


    const hoverQuejas = [
        '#274f6e',
        '#426f97',
        '#9d6947',
        '#607481',
        '#5f567c',
    ];


    const coloresFelicitaciones = [
        'rgba(216, 174, 63, 0.92)',
        'rgba(184, 193, 196, 0.90)',
        'rgba(187, 120, 83, 0.88)',
        'rgba(37, 158, 112, 0.76)',
        'rgba(69, 139, 116, 0.72)',
    ];


    const hoverFelicitaciones = [
        '#c49a27',
        '#9fa9ad',
        '#a56342',
        '#158f65',
        '#397b65',
    ];


    const colores =
        esFelicitacion
            ? coloresFelicitaciones
            : coloresQuejas;


    const coloresHover =
        esFelicitacion
            ? hoverFelicitaciones
            : hoverQuejas;


    /* =====================================================
       DESTRUIR INSTANCIA
    ===================================================== */

    const existente =
        Chart.getChart(
            canvas
        );


    if (
        existente
    ) {

        existente.destroy();

    }


    /* =====================================================
       CREAR
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
                            colores,


                        hoverBackgroundColor:
                            coloresHover,


                        borderWidth:
                            0,


                        borderSkipped:
                            false,


                        borderRadius:
                            8,


                        barPercentage:
                            0.60,


                        categoryPercentage:
                            0.72,


                        maxBarThickness:
                            30,

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
                            6,

                        right:
                            14,

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
                                '#8b9994',


                            padding:
                                8,


                            font: {

                                size:
                                    9,

                            },

                        },


                        grid: {

                            color:
                                'rgba(105, 130, 120, 0.10)',


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
                                    etiqueta.length <= 25
                                ) {

                                    return etiqueta;
                                }


                                return (
                                    etiqueta.substring(
                                        0,
                                        24
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
                            esFelicitacion
                                ? '#087d59'
                                : '#315f82',


                        borderColor:
                            'rgba(34, 99, 76, 0.14)',


                        borderWidth:
                            1,


                        cornerRadius:
                            12,


                        padding:
                            12,


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


                                const singular =
                                    esFelicitacion
                                        ? 'felicitación'
                                        : 'queja';


                                const plural =
                                    esFelicitacion
                                        ? 'felicitaciones'
                                        : 'quejas';


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


/* =========================================================
   DETALLE DEL RANKING
========================================================= */

function inicializarDetalleRanking() {

    const detallesJson =
        document.getElementById(
            'dashboard-ranking-personal-detalles'
        );


    const modal =
        document.getElementById(
            'modal-ranking-personal-detalle'
        );


    if (
        !detallesJson
        || !modal
    ) {

        return;
    }


    if (
        modal.parentElement !== document.body
    ) {

        document.body.appendChild(
            modal
        );

    }


    let detalles = [];


    try {

        detalles =
            JSON.parse(
                detallesJson.textContent
                || '[]'
            );

    } catch (error) {

        detalles = [];

    }


    const nombre =
        document.getElementById(
            'ranking-personal-detalle-nombre'
        );


    const total =
        document.getElementById(
            'ranking-personal-detalle-total'
        );


    const folios =
        document.getElementById(
            'ranking-personal-detalle-folios'
        );


    const motivos =
        document.getElementById(
            'ranking-personal-detalle-motivos'
        );


    const motivosVacio =
        document.getElementById(
            'ranking-personal-detalle-motivos-vacio'
        );


    /* =====================================================
       LIMPIAR
    ===================================================== */

    const limpiarNodo =
        nodo => {

            if (!nodo) {
                return;
            }


            while (
                nodo.firstChild
            ) {

                nodo.removeChild(
                    nodo.firstChild
                );

            }

        };


    /* =====================================================
       LISTA
    ===================================================== */

    const agregarLista =
        (
            contenedor,
            valores
        ) => {

            limpiarNodo(
                contenedor
            );


            if (
                !contenedor
                || !Array.isArray(
                    valores
                )
            ) {

                return;
            }


            valores.forEach(
                valor => {

                    const item =
                        document.createElement(
                            'li'
                        );


                    item.textContent =
                        String(
                            valor
                        );


                    contenedor.appendChild(
                        item
                    );

                }
            );

        };


    /* =====================================================
       MOTIVOS
    ===================================================== */

    const renderizarMotivos =
        items => {

            limpiarNodo(
                motivos
            );


            const hayMotivos =
                Array.isArray(
                    items
                )
                && items.length > 0;


            if (
                motivosVacio
            ) {

                motivosVacio.hidden =
                    hayMotivos;


                motivosVacio.style.display =
                    hayMotivos
                        ? 'none'
                        : '';

            }


            if (
                !hayMotivos
                || !motivos
            ) {

                return;
            }


            items.forEach(
                item => {

                    const bloque =
                        document.createElement(
                            'article'
                        );


                    bloque.className =
                        'dashboard-personal-individual__motivo';


                    const titulo =
                        document.createElement(
                            'strong'
                        );


                    titulo.textContent =
                        item.motivo
                        || 'Sin información';


                    const variantes =
                        Array.isArray(
                            item.variantes
                        )
                            ? item.variantes
                            : [];


                    const variantesContenedor =
                        document.createElement(
                            'div'
                        );


                    variantesContenedor.className =
                        'dashboard-personal-individual__motivo-variantes';


                    variantes.forEach(
                        variante => {

                            const varianteBloque =
                                document.createElement(
                                    'div'
                                );


                            varianteBloque.className =
                                'dashboard-personal-individual__motivo-variante';


                            const sancion =
                                document.createElement(
                                    'strong'
                                );


                            sancion.textContent =
                                `Sanción: ${
                                    variante.sancion
                                    || 'Sin sanción'
                                }`;


                            varianteBloque.appendChild(
                                sancion
                            );


                            if (
                                variante.horas_arresto !== null
                                && variante.horas_arresto !== undefined
                                && variante.horas_arresto !== ''
                            ) {

                                const horas =
                                    document.createElement(
                                        'span'
                                    );


                                horas.textContent =
                                    `Horas de arresto: ${variante.horas_arresto}`;


                                varianteBloque.appendChild(
                                    horas
                                );

                            }


                            const cantidad =
                                document.createElement(
                                    'span'
                                );


                            cantidad.textContent =
                                `${
                                    Number(
                                        variante.cantidad_quejas
                                        || variante.cantidad
                                        || 0
                                    )
                                } quejas`;


                            varianteBloque.appendChild(
                                cantidad
                            );


                            if (
                                Array.isArray(
                                    variante.folios
                                )
                                && variante.folios.length > 0
                            ) {

                                const foliosVariante =
                                    document.createElement(
                                        'small'
                                    );


                                foliosVariante.textContent =
                                    `Folios: ${
                                        variante.folios.join(
                                            ', '
                                        )
                                    }`;


                                varianteBloque.appendChild(
                                    foliosVariante
                                );

                            }


                            variantesContenedor.appendChild(
                                varianteBloque
                            );

                        }
                    );


                    bloque.appendChild(
                        titulo
                    );


                    if (
                        variantes.length > 0
                    ) {

                        bloque.appendChild(
                            variantesContenedor
                        );

                    }


                    motivos.appendChild(
                        bloque
                    );

                }
            );

        };


    /* =====================================================
       ABRIR
    ===================================================== */

    const abrirModal =
        detalle => {

            if (
                !detalle
            ) {

                return;
            }


            if (
                nombre
            ) {

                nombre.textContent =
                    detalle.nombre
                    || '';

            }


            if (
                total
            ) {

                total.textContent =
                    String(
                        detalle.total_quejas
                        || 0
                    );

            }


            if (
                folios
            ) {

                agregarLista(
                    folios,
                    Array.isArray(
                        detalle.folios
                    )
                        ? detalle.folios
                        : []
                );

            }


            renderizarMotivos(
                detalle.motivos
                || []
            );


            modal.classList.add(
                'modal-reporte--visible'
            );


            modal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.classList.add(
                'modal-abierto'
            );

        };


    /* =====================================================
       CERRAR
    ===================================================== */

    const cerrarModal =
        () => {

            modal.classList.remove(
                'modal-reporte--visible'
            );


            modal.setAttribute(
                'aria-hidden',
                'true'
            );


            document.body.classList.remove(
                'modal-abierto'
            );

        };


    /* =====================================================
       BOTONES
    ===================================================== */

    document
        .querySelectorAll(
            '[data-ranking-personal-detalle]'
        )
        .forEach(
            boton => {

                boton.addEventListener(
                    'click',
                    () => {

                        const indice =
                            Number(
                                boton.dataset
                                    .rankingPersonalDetalleIndice
                            );


                        abrirModal(
                            detalles[
                                indice
                            ]
                        );

                    }
                );

            }
        );


    modal
        .querySelectorAll(
            '[data-ranking-personal-detalle-cerrar]'
        )
        .forEach(
            boton => {

                boton.addEventListener(
                    'click',
                    cerrarModal
                );

            }
        );


    document.addEventListener(
        'keydown',
        evento => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModal();

            }

        }
    );

}