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


    inicializarSelectorVisualCruce(
        principal
    );

    inicializarSelectorVisualCruce(
        secundaria
    );


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

function inicializarSelectorVisualCruce(
    select
) {

    const contenedor =
        select.closest(
            '.dashboard-cruce__selector'
        );


    const etiqueta =
        contenedor?.querySelector(
            'label'
        );


    if (
        !contenedor
        || contenedor.dataset.cruceSelectorCustom === '1'
    ) {

        return;
    }


    contenedor.dataset.cruceSelectorCustom =
        '1';

    contenedor.classList.add(
        'dashboard-cruce__selector--customizado'
    );


    if (
        etiqueta
        && !etiqueta.id
    ) {

        etiqueta.id =
            `${select.id}-label`;
    }


    const boton =
        document.createElement(
            'button'
        );


    boton.type =
        'button';

    boton.className =
        'dashboard-cruce__selector-boton';

    boton.setAttribute(
        'aria-haspopup',
        'listbox'
    );

    boton.setAttribute(
        'aria-expanded',
        'false'
    );


    if (
        etiqueta?.id
    ) {

        boton.setAttribute(
            'aria-labelledby',
            etiqueta.id
        );

        boton.dataset.label =
            etiqueta.textContent.trim();
    }


    const texto =
        document.createElement(
            'span'
        );


    texto.className =
        'dashboard-cruce__selector-texto';


    const flecha =
        document.createElement(
            'span'
        );


    flecha.className =
        'dashboard-cruce__selector-flecha';

    flecha.setAttribute(
        'aria-hidden',
        'true'
    );


    boton.append(
        texto,
        flecha
    );


    const lista =
        document.createElement(
            'div'
        );


    lista.className =
        'dashboard-cruce__selector-menu';

    lista.id =
        `${select.id}-menu`;

    lista.setAttribute(
        'role',
        'listbox'
    );

    boton.setAttribute(
        'aria-controls',
        lista.id
    );

    lista.hidden =
        true;


    Array.from(
        select.options
    ).forEach(
        opcion => {

            const item =
                document.createElement(
                    'button'
                );


            item.type =
                'button';

            item.className =
                'dashboard-cruce__selector-opcion';

            item.dataset.value =
                opcion.value;

            item.textContent =
                opcion.textContent.trim();

            item.setAttribute(
                'role',
                'option'
            );

            item.setAttribute(
                'aria-selected',
                opcion.selected
                    ? 'true'
                    : 'false'
            );


            item.addEventListener(
                'click',
                () => {

                    seleccionarOpcionCruce(
                        select,
                        opcion.value
                    );

                }
            );


            lista.appendChild(
                item
            );

        }
    );


    boton.addEventListener(
        'click',
        () => {

            alternarSelectorCruce(
                contenedor,
                boton,
                lista
            );

        }
    );


    boton.addEventListener(
        'keydown',
        evento => {

            manejarTecladoBotonCruce(
                evento,
                contenedor,
                boton,
                lista
            );

        }
    );


    lista.addEventListener(
        'keydown',
        evento => {

            manejarTecladoListaCruce(
                evento,
                select,
                contenedor,
                boton,
                lista
            );

        }
    );


    etiqueta?.addEventListener(
        'click',
        evento => {

            evento.preventDefault();

            boton.focus();

        }
    );


    document.addEventListener(
        'click',
        evento => {

            if (
                !contenedor.contains(
                    evento.target
                )
            ) {

                cerrarSelectorCruce(
                    contenedor,
                    boton,
                    lista
                );

            }

        }
    );


    contenedor.append(
        boton,
        lista
    );


    sincronizarSelectorVisualCruce(
        select,
        boton,
        lista
    );
}


function sincronizarSelectorVisualCruce(
    select,
    boton,
    lista
) {

    const opcionSeleccionada =
        select.options[
            select.selectedIndex
        ];


    const texto =
        boton.querySelector(
            '.dashboard-cruce__selector-texto'
        );


    if (
        texto
    ) {

        const textoActual =
            opcionSeleccionada?.textContent
                ?.trim()
            || '';


        texto.textContent =
            textoActual;


        boton.setAttribute(
            'aria-label',
            boton.dataset.label
                ? `${boton.dataset.label}: ${textoActual}`
                : textoActual
        );
    }


    lista.querySelectorAll(
        '.dashboard-cruce__selector-opcion'
    ).forEach(
        opcion => {

            const seleccionada =
                opcion.dataset.value === select.value;


            opcion.setAttribute(
                'aria-selected',
                seleccionada
                    ? 'true'
                    : 'false'
            );

        }
    );
}


function alternarSelectorCruce(
    contenedor,
    boton,
    lista
) {

    const abierto =
        boton.getAttribute(
            'aria-expanded'
        ) === 'true';


    cerrarSelectoresCruce();


    if (
        abierto
    ) {

        return;
    }


    contenedor.classList.add(
        'dashboard-cruce__selector--abierto'
    );

    boton.setAttribute(
        'aria-expanded',
        'true'
    );

    lista.hidden =
        false;


    const seleccionada =
        lista.querySelector(
            '[aria-selected="true"]'
        );


    (
        seleccionada
        || lista.querySelector(
            '.dashboard-cruce__selector-opcion'
        )
    )?.focus();
}


function cerrarSelectorCruce(
    contenedor,
    boton,
    lista
) {

    contenedor.classList.remove(
        'dashboard-cruce__selector--abierto'
    );

    boton.setAttribute(
        'aria-expanded',
        'false'
    );

    lista.hidden =
        true;
}


function cerrarSelectoresCruce() {

    document.querySelectorAll(
        '.dashboard-cruce__selector--customizado'
    ).forEach(
        contenedor => {

            const boton =
                contenedor.querySelector(
                    '.dashboard-cruce__selector-boton'
                );


            const lista =
                contenedor.querySelector(
                    '.dashboard-cruce__selector-menu'
                );


            if (
                boton
                && lista
            ) {

                cerrarSelectorCruce(
                    contenedor,
                    boton,
                    lista
                );

            }

        }
    );
}


function seleccionarOpcionCruce(
    select,
    valor
) {

    if (
        select.value === valor
    ) {

        cerrarSelectoresCruce();

        return;
    }


    select.value =
        valor;

    select.dispatchEvent(
        new Event(
            'change',
            {
                bubbles:
                    true,
            }
        )
    );
}


function manejarTecladoBotonCruce(
    evento,
    contenedor,
    boton,
    lista
) {

    if (
        ![
            'Enter',
            ' ',
            'ArrowDown',
            'ArrowUp',
        ].includes(
            evento.key
        )
    ) {

        return;
    }


    evento.preventDefault();

    alternarSelectorCruce(
        contenedor,
        boton,
        lista
    );
}


function manejarTecladoListaCruce(
    evento,
    select,
    contenedor,
    boton,
    lista
) {

    const opciones =
        Array.from(
            lista.querySelectorAll(
                '.dashboard-cruce__selector-opcion'
            )
        );


    const indiceActual =
        opciones.indexOf(
            document.activeElement
        );


    if (
        evento.key === 'Escape'
    ) {

        evento.preventDefault();

        cerrarSelectorCruce(
            contenedor,
            boton,
            lista
        );

        boton.focus();

        return;
    }


    if (
        evento.key === 'Enter'
        || evento.key === ' '
    ) {

        evento.preventDefault();

        const opcion =
            opciones[
                indiceActual
            ];


        if (
            opcion
        ) {

            seleccionarOpcionCruce(
                select,
                opcion.dataset.value
            );

        }

        return;
    }


    if (
        evento.key !== 'ArrowDown'
        && evento.key !== 'ArrowUp'
    ) {

        return;
    }


    evento.preventDefault();

    const direccion =
        evento.key === 'ArrowDown'
            ? 1
            : -1;


    const siguienteIndice =
        (
            indiceActual
            + direccion
            + opciones.length
        )
        % opciones.length;


    opciones[
        siguienteIndice
    ]?.focus();
}


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


    const principal =
        String(
            datos.principal
            || ''
        )
            .trim()
            .toLowerCase();


    const secundaria =
        String(
            datos.secundaria
            || ''
        )
            .trim()
            .toLowerCase();


    const esSectorTurno =
        principal === 'sector'
        && secundaria === 'turno';


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
        esSectorTurno
            ? Math.max(
                140,
                (
                    series.length
                    * 30
                )
                + 32
            )
            : 58;


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


                    barThickness:
                        esSectorTurno
                            ? 20
                            : undefined,


                    barPercentage:
                        esSectorTurno
                            ? 0.78
                            : 0.72,


                    categoryPercentage:
                        esSectorTurno
                            ? 0.82
                            : 0.78,


                    maxBarThickness:
                        esSectorTurno
                            ? 22
                            : 18,

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
                                    12,

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
                                    12,


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

                    dashboardEtiquetasVisibles:
                        window.DashboardEtiquetasGraficas
                            ?.opciones(
                                {
                                    modo:
                                        'cantidad',
                                }
                            ),

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
                                    12,


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
                                13,


                            weight:
                                '700',

                        },


                        bodyFont: {

                            size:
                                13,


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
