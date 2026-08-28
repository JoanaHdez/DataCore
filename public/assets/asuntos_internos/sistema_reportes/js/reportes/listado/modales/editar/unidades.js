/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Unidades involucradas
========================================================= */

import {
    estadoUnidades,
    agregarUnidad,
    eliminarUnidad,
    establecerUnidadSeleccionada,
} from './estado.js';

import {
    asignarValorEditar,
    escaparHTML,
    normalizarMayuscula,
} from './utilidades.js';


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarUnidades(
    modal
) {

    const inputBusqueda =
        modal.querySelector(
            '#editar-unidad-busqueda'
        );

    const resultados =
        modal.querySelector(
            '#editar-unidad-resultados'
        );

    const btnAgregar =
        modal.querySelector(
            '#btn-editar-agregar-unidad'
        );

    const selectOrigen =
        modal.querySelector(
            '#editar-unidad-origen'
        );

    const tablaBody =
        modal.querySelector(
            '#editar-unidades-agregadas-body'
        );


    if (
        !inputBusqueda
        || !resultados
        || !btnAgregar
        || !selectOrigen
        || !tablaBody
    ) {
        return;
    }


    /* =====================================================
       BUSCAR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            const termino =
                inputBusqueda.value.trim();


            establecerUnidadSeleccionada(
                null
            );


            limpiarUnidadSeleccionada(
                modal
            );


            if (
                estadoUnidades
                    .temporizadorBusqueda
            ) {

                clearTimeout(
                    estadoUnidades
                        .temporizadorBusqueda
                );

            }


            if (
                termino.length < 1
            ) {

                ocultarResultadosUnidad(
                    modal
                );

                return;
            }


            estadoUnidades
                .temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarUnidades(
                            modal,
                            termino
                        );

                    },
                    300
                );

        }
    );


    /* =====================================================
       AGREGAR
    ===================================================== */

    btnAgregar.addEventListener(
        'click',
        () => {

            const unidad =
                estadoUnidades.seleccionada;


            if (
                !unidad
                || !unidad.id
            ) {
                return;
            }


            const origen =
                String(
                    selectOrigen.value
                    || ''
                )
                    .trim()
                    .toUpperCase();


            if (!origen) {

                selectOrigen.focus();

                return;
            }


            const yaExiste =
                estadoUnidades.elementos
                    .some(
                        (item) =>
                            Number(item.id)
                            === Number(
                                unidad.id
                            )
                    );


            if (yaExiste) {

                mostrarMensajeUnidad(
                    modal,
                    'Esta unidad ya fue agregada al reporte.'
                );

                return;
            }


            agregarUnidad({
                ...unidad,
                origen,
            });


            renderizarUnidadesEditar(
                modal
            );


            limpiarSelectorUnidad(
                modal
            );

        }
    );


    /* =====================================================
       QUITAR
    ===================================================== */

    tablaBody.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-editar-eliminar-unidad]'
                );


            if (!boton) {
                return;
            }


            const indice =
                Number(
                    boton
                        .dataset
                        .editarEliminarUnidad
                );


            if (
                !eliminarUnidad(
                    indice
                )
            ) {
                return;
            }


            renderizarUnidadesEditar(
                modal
            );

        }
    );


    /* =====================================================
       CERRAR RESULTADOS AL HACER CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                evento.target ===
                inputBusqueda
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            ocultarResultadosUnidad(
                modal
            );

        }
    );

}


/* =========================================================
   BUSCAR UNIDADES
========================================================= */

async function buscarUnidades(
    modal,
    termino
) {

    if (
        estadoUnidades
            .controladorBusqueda
    ) {

        estadoUnidades
            .controladorBusqueda
            .abort();

    }


    estadoUnidades
        .controladorBusqueda =
        new AbortController();


    try {

        const baseUrl =
            document
                .querySelector('base')
                ?.href
            || `${window.location.origin}/`;


        const url =
            new URL(
                'asuntos-internos/reportes/unidades/buscar',
                baseUrl
            );


        url.searchParams.set(
            'q',
            termino
        );


        const respuesta =
            await fetch(
                url.toString(),
                {
                    method: 'GET',

                    headers: {
                        Accept:
                            'application/json',
                    },

                    signal:
                        estadoUnidades
                            .controladorBusqueda
                            .signal,
                }
            );


        if (!respuesta.ok) {

            throw new Error(
                'No fue posible consultar las unidades.'
            );

        }


        const datos =
            await respuesta.json();


        renderizarResultadosUnidad(
            modal,
            Array.isArray(
                datos.unidades
            )
                ? datos.unidades
                : []
        );


    } catch (error) {

        if (
            error.name ===
            'AbortError'
        ) {
            return;
        }


        console.error(
            'Error buscando unidades:',
            error
        );


        mostrarMensajeUnidad(
            modal,
            'No fue posible consultar las unidades.'
        );

    }

}


/* =========================================================
   RESULTADOS
========================================================= */

function renderizarResultadosUnidad(
    modal,
    unidades
) {

    const resultados =
        modal.querySelector(
            '#editar-unidad-resultados'
        );


    if (!resultados) {
        return;
    }


    resultados.innerHTML =
        '';


    if (!unidades.length) {

        mostrarMensajeUnidad(
            modal,
            'No se encontraron unidades.'
        );

        return;
    }


    unidades.forEach(
        (unidad) => {

            const boton =
                document.createElement(
                    'button'
                );


            boton.type =
                'button';

            boton.className =
                'editar-unidad-resultados__item';


            boton.innerHTML = `
                <span class="editar-unidad-resultados__icono">
                    U
                </span>

                <span class="editar-unidad-resultados__datos">

                    <strong>
                        ${escaparHTML(
                            unidad.no_economico
                            || 'SIN NÚMERO'
                        )}
                    </strong>

                    <small>
                        Placas:
                        ${escaparHTML(
                            unidad.placas
                            || '—'
                        )}
                    </small>

                    <small>
                        ${escaparHTML(
                            unidad.marca
                            || '—'
                        )}
                        ${escaparHTML(
                            unidad.submarca
                            || ''
                        )}
                    </small>

                </span>
            `;


            boton.addEventListener(
                'click',
                () => {

                    seleccionarUnidad(
                        modal,
                        unidad
                    );

                }
            );


            resultados.appendChild(
                boton
            );

        }
    );


    resultados.hidden =
        false;

}


/* =========================================================
   SELECCIONAR UNIDAD
========================================================= */

function seleccionarUnidad(
    modal,
    unidad
) {

    const unidadNormalizada = {

        id:
            Number(
                unidad.id
            ) || 0,

        no_economico:
            normalizarMayuscula(
                unidad.no_economico
            ),

        placas:
            normalizarMayuscula(
                unidad.placas
            ),

        marca:
            normalizarMayuscula(
                unidad.marca
            ),

        submarca:
            normalizarMayuscula(
                unidad.submarca
            ),

        color:
            normalizarMayuscula(
                unidad.color
            ),

        estatus:
            normalizarMayuscula(
                unidad.estatus
            ),

        servicio:
            normalizarMayuscula(
                unidad.servicio
            ),

        tipo:
            normalizarMayuscula(
                unidad.tipo
            ),

        modelo:
            normalizarMayuscula(
                unidad.modelo
            ),

        serie:
            normalizarMayuscula(
                unidad.serie
            ),

        origen:
            '',

    };


    establecerUnidadSeleccionada(
        unidadNormalizada
    );


    asignarValorEditar(
        modal,
        '#editar-unidad-parque-id',
        unidadNormalizada.id
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-no-economico',
        unidadNormalizada.no_economico
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-placas',
        unidadNormalizada.placas
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-marca',
        unidadNormalizada.marca
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-submarca',
        unidadNormalizada.submarca
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-color',
        unidadNormalizada.color
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-estatus',
        unidadNormalizada.estatus
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-servicio',
        unidadNormalizada.servicio
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-tipo',
        unidadNormalizada.tipo
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-origen',
        ''
    );

    asignarValorEditar(
        modal,
        '#editar-unidad-busqueda',
        unidadNormalizada.no_economico
        || unidadNormalizada.placas
    );


    const seleccionado =
        modal.querySelector(
            '#editar-unidad-seleccionada'
        );


    if (seleccionado) {

        seleccionado.hidden =
            false;

    }


    ocultarResultadosUnidad(
        modal
    );

}


/* =========================================================
   RENDER UNIDADES AGREGADAS
========================================================= */

export function renderizarUnidadesEditar(
    modal
) {

    const contenedor =
        modal.querySelector(
            '#editar-unidades-agregadas'
        );

    const body =
        modal.querySelector(
            '#editar-unidades-agregadas-body'
        );

    const hidden =
        modal.querySelector(
            '#editar-unidades-hidden-inputs'
        );


    if (
        !contenedor
        || !body
        || !hidden
    ) {
        return;
    }


    body.innerHTML =
        '';

    hidden.innerHTML =
        '';


    estadoUnidades.elementos
        .forEach(
            (unidad, indice) => {

                const fila =
                    document.createElement(
                        'tr'
                    );


                fila.innerHTML = `

                    <td>

                        <strong>
                            ${escaparHTML(
                                unidad.no_economico
                                || '—'
                            )}
                        </strong>

                        <small class="editar-unidad-tabla__placas">
                            Placas:
                            ${escaparHTML(
                                unidad.placas
                                || '—'
                            )}
                        </small>

                    </td>

                    <td>
                        ${escaparHTML(
                            unidad.marca
                            || '—'
                        )}
                        ${escaparHTML(
                            unidad.submarca
                            || ''
                        )}
                    </td>

                    <td>
                        ${escaparHTML(
                            unidad.color
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHTML(
                            unidad.estatus
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHTML(
                            unidad.servicio
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHTML(
                            unidad.tipo
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHTML(
                            unidad.origen
                            || '—'
                        )}
                    </td>

                    <td>

                        <button
                            type="button"
                            class="editar-unidad-tabla__eliminar"
                            data-editar-eliminar-unidad="${indice}"
                        >
                            Quitar
                        </button>

                    </td>
                `;


                body.appendChild(
                    fila
                );


                crearInputsOcultosUnidad(
                    hidden,
                    unidad,
                    indice
                );

            }
        );


    contenedor.hidden =
        estadoUnidades.elementos
            .length === 0;


    contenedor.dataset.totalUnidades =
        String(
            estadoUnidades.elementos
                .length
        );

}


/* =========================================================
   INPUTS OCULTOS
========================================================= */

function crearInputsOcultosUnidad(
    contenedor,
    unidad,
    indice
) {

    const campos = {

        parque_vehicular_id:
            unidad.id,

        no_economico:
            unidad.no_economico,

        placas:
            unidad.placas,

        marca:
            unidad.marca,

        submarca:
            unidad.submarca,

        color:
            unidad.color,

        estatus:
            unidad.estatus,

        servicio:
            unidad.servicio,

        tipo:
            unidad.tipo,

        modelo:
            unidad.modelo,

        serie:
            unidad.serie,

        origen:
            unidad.origen,

    };


    Object.entries(
        campos
    ).forEach(
        ([campo, valor]) => {

            const input =
                document.createElement(
                    'input'
                );


            input.type =
                'hidden';

            input.name =
                `unidades[${indice}][${campo}]`;

            input.value =
                valor ?? '';


            contenedor.appendChild(
                input
            );

        }
    );

}


/* =========================================================
   LIMPIAR SELECTOR
========================================================= */

export function limpiarSelectorUnidad(
    modal
) {

    establecerUnidadSeleccionada(
        null
    );


    asignarValorEditar(
        modal,
        '#editar-unidad-busqueda',
        ''
    );


    limpiarUnidadSeleccionada(
        modal
    );


    ocultarResultadosUnidad(
        modal
    );

}


/* =========================================================
   LIMPIAR UNIDAD SELECCIONADA
========================================================= */

function limpiarUnidadSeleccionada(
    modal
) {

    [
        '#editar-unidad-parque-id',
        '#editar-unidad-no-economico',
        '#editar-unidad-placas',
        '#editar-unidad-marca',
        '#editar-unidad-submarca',
        '#editar-unidad-color',
        '#editar-unidad-estatus',
        '#editar-unidad-servicio',
        '#editar-unidad-tipo',
        '#editar-unidad-origen',
    ].forEach(
        (selector) => {

            asignarValorEditar(
                modal,
                selector,
                ''
            );

        }
    );


    const seleccionado =
        modal.querySelector(
            '#editar-unidad-seleccionada'
        );


    if (seleccionado) {

        seleccionado.hidden =
            true;

    }

}


/* =========================================================
   RESULTADOS
========================================================= */

function ocultarResultadosUnidad(
    modal
) {

    const resultados =
        modal.querySelector(
            '#editar-unidad-resultados'
        );


    if (!resultados) {
        return;
    }


    resultados.hidden =
        true;

    resultados.innerHTML =
        '';

}


function mostrarMensajeUnidad(
    modal,
    mensaje
) {

    const resultados =
        modal.querySelector(
            '#editar-unidad-resultados'
        );


    if (!resultados) {
        return;
    }


    resultados.innerHTML = `
        <div class="editar-unidad-resultados__vacio">
            ${escaparHTML(mensaje)}
        </div>
    `;


    resultados.hidden =
        false;

}