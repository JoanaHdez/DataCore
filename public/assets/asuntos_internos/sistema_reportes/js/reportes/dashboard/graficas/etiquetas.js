/* =========================================================
   DASHBOARD
   Etiquetas visibles para graficas Chart.js
========================================================= */

const COLOR_TEXTO_FUERA =
    '#31453d';

const COLOR_TEXTO_DENTRO =
    '#ffffff';


const pluginEtiquetasDashboard = {
    id:
        'dashboardEtiquetasVisibles',

    afterDatasetsDraw(
        chart,
        args,
        opcionesPlugin
    ) {

        const opciones =
            {
                ...(opcionesPlugin || {}),
                ...(
                    chart.options.plugins
                        ?.dashboardEtiquetasVisibles
                    || {}
                ),
            };


        if (
            opciones.enabled !== true
        ) {

            return;
        }


        const entradas =
            obtenerEntradasVisibles(
                chart
            );


        if (
            entradas.length === 0
        ) {

            return;
        }


        const total =
            Number(
                opciones.total
                ?? entradas.reduce(
                    (
                        acumulado,
                        entrada
                    ) =>
                        acumulado
                        + entrada.valor,
                    0
                )
            );


        const contexto =
            chart.ctx;


        contexto.save();

        contexto.font =
            opciones.fuente
            || '700 13px Inter, system-ui, sans-serif';

        contexto.textBaseline =
            'middle';


        entradas.forEach(
            (
                entrada,
                indice
            ) => {

                dibujarEtiqueta(
                    chart,
                    contexto,
                    entrada,
                    total,
                    opciones,
                    indice,
                    entradas
                );

            }
        );


        contexto.restore();
    },
};


function obtenerEntradasVisibles(
    chart
) {

    const entradas = [];


    chart.data.datasets.forEach(
        (
            dataset,
            datasetIndex
        ) => {

            const meta =
                chart.getDatasetMeta(
                    datasetIndex
                );


            if (
                meta.hidden
            ) {

                return;
            }


            meta.data.forEach(
                (
                    elemento,
                    dataIndex
                ) => {

                    const valor =
                        Number(
                            dataset.data[
                                dataIndex
                            ]
                            ?? 0
                        );


                    if (
                        !Number.isFinite(
                            valor
                        )
                        || valor <= 0
                    ) {

                        return;
                    }


                    entradas.push(
                        {
                            elemento,
                            valor,
                            datasetIndex,
                            dataIndex,
                            tipo:
                                meta.type
                                || chart.config.type,
                        }
                    );

                }
            );

        }
    );


    return entradas;
}


function dibujarEtiqueta(
    chart,
    contexto,
    entrada,
    total,
    opciones,
    indice,
    entradas
) {

    if (
        entrada.tipo === 'line'
    ) {

        dibujarEtiquetaLinea(
            chart,
            contexto,
            entrada,
            opciones,
            indice,
            entradas
        );

        return;
    }


    if (
        entrada.tipo === 'doughnut'
        || entrada.tipo === 'pie'
    ) {

        dibujarEtiquetaDona(
            chart,
            contexto,
            entrada,
            total,
            opciones
        );

        return;
    }


    dibujarEtiquetaBarra(
        chart,
        contexto,
        entrada,
        total,
        opciones
    );
}


function dibujarEtiquetaBarra(
    chart,
    contexto,
    entrada,
    total,
    opciones
) {

    const propiedades =
        entrada.elemento.getProps(
            [
                'x',
                'y',
                'base',
                'width',
                'height',
            ],
            true
        );


    const esHorizontal =
        chart.options.indexAxis === 'y';


    const lineas =
        formatearLineasEtiqueta(
            entrada.valor,
            total,
            opciones,
            esHorizontal,
            chart
        );


    if (
        lineas.length === 0
    ) {

        return;
    }


    const anchoTexto =
        medirAnchoMaximo(
            contexto,
            lineas
        );


    if (
        esHorizontal
    ) {

        dibujarEtiquetaBarraHorizontal(
            chart,
            contexto,
            propiedades,
            lineas.join(
                ' '
            ),
            anchoTexto,
            opciones
        );

        return;
    }


    dibujarEtiquetaBarraVertical(
        chart,
        contexto,
        propiedades,
        lineas,
        anchoTexto,
        opciones
    );
}


function formatearLineasEtiqueta(
    valor,
    total,
    opciones,
    esHorizontal,
    chart
) {

    const cantidad =
        Number(
            valor
        );


    if (
        opciones.modo === 'cantidad'
        || !total
    ) {

        return [
            String(
                cantidad
            ),
        ];
    }


    const porcentaje =
        (
            cantidad * 100
        )
        / total;


    const decimales =
        porcentaje >= 10
            ? 0
            : 1;


    const porcentajeTexto =
        `${porcentaje.toFixed(decimales)}%`;


    const muchasCategorias =
        obtenerEntradasVisibles(
            chart
        ).length > (
            opciones.compactarDesde
            ?? 16
        );


    if (
        muchasCategorias
    ) {

        return [
            String(
                cantidad
            ),
        ];
    }


    if (
        esHorizontal
    ) {

        return [
            `${cantidad} (${porcentajeTexto})`,
        ];
    }


    return [
        String(
            cantidad
        ),
        porcentajeTexto,
    ];
}


function dibujarEtiquetaBarraHorizontal(
    chart,
    contexto,
    propiedades,
    texto,
    anchoTexto,
    opciones
) {

    const margen =
        opciones.margen
        ?? 8;


    const longitudBarra =
        Math.abs(
            propiedades.x
            - propiedades.base
        );


    const cabeDentro =
        longitudBarra >= anchoTexto
            + (
                margen * 2
            );


    let x =
        propiedades.x
        + margen;


    contexto.textAlign =
        'left';

    contexto.fillStyle =
        opciones.colorFuera
        || COLOR_TEXTO_FUERA;


    if (
        cabeDentro
    ) {

        x =
            propiedades.x
            - margen;

        contexto.textAlign =
            'right';

        contexto.fillStyle =
            opciones.colorDentro
            || COLOR_TEXTO_DENTRO;
    } else if (
        x + anchoTexto > chart.chartArea.right
    ) {

        x =
            chart.chartArea.right;

        contexto.textAlign =
            'right';
    }


    contexto.fillText(
        texto,
        x,
        propiedades.y
    );
}


function dibujarEtiquetaBarraVertical(
    chart,
    contexto,
    propiedades,
    lineas,
    anchoTexto,
    opciones
) {

    const margen =
        opciones.margen
        ?? 8;


    const altoLinea =
        opciones.altoLinea
        ?? 15;


    const altoTexto =
        (
            lineas.length - 1
        )
        * altoLinea;


    const alturaBarra =
        Math.abs(
            propiedades.y
            - propiedades.base
        );


    const cabeDentro =
        alturaBarra >= altoTexto
            + (
                margen * 2
            )
            && anchoTexto <= propiedades.width
                + (
                    margen * 1.5
                );


    let y =
        propiedades.y
        - margen
        - altoTexto;


    contexto.textAlign =
        'center';

    contexto.fillStyle =
        opciones.colorFuera
        || COLOR_TEXTO_FUERA;


    if (
        cabeDentro
    ) {

        y =
            propiedades.y
            + margen;

        contexto.fillStyle =
            opciones.colorDentro
            || COLOR_TEXTO_DENTRO;
    } else if (
        y < chart.chartArea.top
    ) {

        y =
            propiedades.y
            + margen;
    }


    dibujarLineas(
        contexto,
        lineas,
        propiedades.x,
        y,
        altoLinea
    );
}


function dibujarEtiquetaLinea(
    chart,
    contexto,
    entrada,
    opciones,
    indice,
    entradas
) {

    const propiedades =
        entrada.elemento.getProps(
            [
                'x',
                'y',
            ],
            true
        );


    const offsetBase =
        opciones.offsetLinea
        ?? 14;


    const cercanas =
        entradas.some(
            (
                otraEntrada,
                otroIndice
            ) => {

                if (
                    otroIndice === indice
                    || otraEntrada.tipo !== 'line'
                ) {

                    return false;
                }


                const otra =
                    otraEntrada.elemento.getProps(
                        [
                            'x',
                            'y',
                        ],
                        true
                    );


                return Math.abs(
                    otra.x
                    - propiedades.x
                ) < 28
                    && Math.abs(
                        otra.y
                        - propiedades.y
                    ) < 20;
            }
        );


    const offset =
        cercanas
            ? offsetBase
                + (
                    indice % 2 === 0
                        ? 6
                        : -6
                )
            : offsetBase;


    let y =
        propiedades.y
        - offset;


    if (
        y < chart.chartArea.top
            + 8
    ) {

        y =
            propiedades.y
            + offset;
    }


    contexto.textAlign =
        'center';

    contexto.fillStyle =
        opciones.colorFuera
        || COLOR_TEXTO_FUERA;

    contexto.fillText(
        String(
            entrada.valor
        ),
        propiedades.x,
        y
    );
}


function dibujarEtiquetaDona(
    chart,
    contexto,
    entrada,
    total,
    opciones
) {

    const propiedades =
        entrada.elemento.getProps(
            [
                'x',
                'y',
                'startAngle',
                'endAngle',
                'innerRadius',
                'outerRadius',
            ],
            true
        );


    const porcentaje =
        total
            ? (
                entrada.valor * 100
            )
                / total
            : 0;


    const decimales =
        porcentaje >= 10
            ? 0
            : 1;


    const lineas = [
        String(
            entrada.valor
        ),
        `${porcentaje.toFixed(decimales)}%`,
    ];


    const altoLinea =
        opciones.altoLineaDona
        ?? 15;


    const angulo =
        (
            propiedades.startAngle
            + propiedades.endAngle
        )
        / 2;


    const amplitud =
        Math.abs(
            propiedades.endAngle
            - propiedades.startAngle
        );


    const grosor =
        propiedades.outerRadius
        - propiedades.innerRadius;


    const radioInternoEtiqueta =
        (
            propiedades.innerRadius
            + propiedades.outerRadius
        )
        / 2;


    const cabeDentro =
        amplitud >= 0.34
            && grosor >= 24;


    const radio =
        cabeDentro
            ? radioInternoEtiqueta
            : propiedades.outerRadius
                + 18;


    const x =
        propiedades.x
        + Math.cos(
            angulo
        )
        * radio;


    const y =
        propiedades.y
        + Math.sin(
            angulo
        )
        * radio
        - (
            (
                lineas.length - 1
            )
            * altoLinea
            / 2
        );


    contexto.textAlign =
        'center';

    contexto.fillStyle =
        cabeDentro
            ? opciones.colorDentro
                || COLOR_TEXTO_DENTRO
            : opciones.colorFuera
                || COLOR_TEXTO_FUERA;


    dibujarLineas(
        contexto,
        lineas,
        x,
        y,
        altoLinea
    );
}


function dibujarLineas(
    contexto,
    lineas,
    x,
    y,
    altoLinea
) {

    lineas.forEach(
        (
            linea,
            indice
        ) => {

            contexto.fillText(
                linea,
                x,
                y + (
                    indice
                    * altoLinea
                )
            );

        }
    );
}


function medirAnchoMaximo(
    contexto,
    lineas
) {

    return Math.max(
        ...lineas.map(
            linea =>
                contexto.measureText(
                    linea
                ).width
        )
    );
}


window.DashboardEtiquetasGraficas = {
    plugin:
        pluginEtiquetasDashboard,

    opciones(
        opciones = {}
    ) {

        return {
            enabled:
                true,
            ...opciones,
        };
    },
};


if (
    typeof Chart !== 'undefined'
) {

    Chart.register(
        pluginEtiquetasDashboard
    );
}
