/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Personal involucrado
========================================================= */

import {
    estadoPersonal,
    agregarPersonal,
    eliminarPersonal,
    establecerPersonaSeleccionada,
} from './estado.js';

import {
    asignarValorEditar,
    escaparHTML,
    obtenerInicialEditar,
} from './utilidades.js';


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarPersonal(
    modal
) {

    const inputBusqueda =
        modal.querySelector(
            '#editar-personal-busqueda'
        );

    const resultados =
        modal.querySelector(
            '#editar-personal-resultados'
        );

    const inputTurno =
        modal.querySelector(
            '#editar-personal-turno'
        );

    const btnAgregar =
        modal.querySelector(
            '#btn-editar-agregar-personal'
        );

    const tablaBody =
        modal.querySelector(
            '#editar-personal-agregado-body'
        );


    if (
        !inputBusqueda
        || !resultados
        || !inputTurno
        || !btnAgregar
        || !tablaBody
    ) {
        return;
    }


    /* =====================================================
       TURNO EN MAYÚSCULAS
    ===================================================== */

    inputTurno.addEventListener(
        'input',
        () => {

            const inicio =
                inputTurno.selectionStart;

            const fin =
                inputTurno.selectionEnd;


            inputTurno.value =
                inputTurno.value
                    .toUpperCase();


            if (
                inicio !== null
                && fin !== null
            ) {

                inputTurno.setSelectionRange(
                    inicio,
                    fin
                );

            }

        }
    );


    /* =====================================================
       BUSCAR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            const termino =
                inputBusqueda.value.trim();


            establecerPersonaSeleccionada(
                null
            );


            limpiarPersonaSeleccionada(
                modal
            );


            if (
                estadoPersonal
                    .temporizadorBusqueda
            ) {

                clearTimeout(
                    estadoPersonal
                        .temporizadorBusqueda
                );

            }


            if (
                termino.length < 2
            ) {

                ocultarResultadosPersonal(
                    modal
                );

                return;
            }


            estadoPersonal
                .temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarPersonal(
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

            const persona =
                estadoPersonal.seleccionado;


            if (
                !persona
                || !persona.id
            ) {
                return;
            }


            const turno =
                String(
                    inputTurno.value
                    || ''
                )
                    .trim()
                    .toUpperCase();


            inputTurno.value =
                turno;


            const yaExiste =
                estadoPersonal.elementos
                    .some(
                        (item) =>
                            Number(item.id)
                            === Number(
                                persona.id
                            )
                    );


            if (yaExiste) {

                mostrarMensajePersonal(
                    modal,
                    'Esta persona ya fue agregada al reporte.'
                );

                return;
            }


            agregarPersonal({
                ...persona,
                turno,
            });


            renderizarPersonalEditar(
                modal
            );


            limpiarSelectorPersonal(
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
                    '[data-editar-eliminar-personal]'
                );


            if (!boton) {
                return;
            }


            const indice =
                Number(
                    boton
                        .dataset
                        .editarEliminarPersonal
                );


            if (
                !eliminarPersonal(
                    indice
                )
            ) {
                return;
            }


            renderizarPersonalEditar(
                modal
            );

        }
    );


    /* =====================================================
   EDITAR TURNO DE PERSONAL AGREGADO
===================================================== */

    tablaBody.addEventListener(
        'input',
        (evento) => {

            const input =
                evento.target.closest(
                    '[data-editar-turno-personal]'
                );


            if (!input) {
                return;
            }


            const indice =
                Number(
                    input.dataset
                        .editarTurnoPersonal
                );


            if (
                !Number.isInteger(indice)
                || indice < 0
                || !estadoPersonal.elementos[indice]
            ) {
                return;
            }


            const inicio =
                input.selectionStart;


            const fin =
                input.selectionEnd;


            const turno =
                String(
                    input.value
                    || ''
                ).toUpperCase();


            input.value =
                turno;


            estadoPersonal
                .elementos[indice]
                .turno =
                turno;


            if (
                inicio !== null
                && fin !== null
            ) {

                input.setSelectionRange(
                    inicio,
                    fin
                );

            }

        }
    );


    /* =====================================================
       VALIDAR TURNO AL SALIR DEL CAMPO
    ===================================================== */

    tablaBody.addEventListener(
        'blur',
        (evento) => {

            const input =
                evento.target.closest(
                    '[data-editar-turno-personal]'
                );


            if (!input) {
                return;
            }


            const indice =
                Number(
                    input.dataset
                        .editarTurnoPersonal
                );


            if (
                !Number.isInteger(indice)
                || indice < 0
                || !estadoPersonal.elementos[indice]
            ) {
                return;
            }


            const turno =
                String(
                    input.value
                    || ''
                )
                    .trim()
                    .toUpperCase();


            input.value =
                turno;


            estadoPersonal
                .elementos[indice]
                .turno =
                turno;

        },
        true
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


            ocultarResultadosPersonal(
                modal
            );

        }
    );

}


/* =========================================================
   BUSCAR PERSONAL
========================================================= */

async function buscarPersonal(
    modal,
    termino
) {

    if (
        estadoPersonal
            .controladorBusqueda
    ) {

        estadoPersonal
            .controladorBusqueda
            .abort();

    }


    estadoPersonal
        .controladorBusqueda =
        new AbortController();


    try {

        /* const baseUrl =
            document
                .querySelector('base')
                ?.href
            || `${window.location.origin}/`;


        const url =
            new URL(
                'asuntos-internos/reportes/personal/buscar',
                baseUrl
            ); */

            const url =
    new URL(
        'DataCore/public/asuntos-internos/reportes/personal/buscar',
        `${window.location.origin}/`
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
                        estadoPersonal
                            .controladorBusqueda
                            .signal,
                }
            );


        if (!respuesta.ok) {

            throw new Error(
                'No fue posible consultar el personal.'
            );

        }


        const datos =
            await respuesta.json();


        renderizarResultadosPersonal(
            modal,
            Array.isArray(
                datos.personal
            )
                ? datos.personal
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
            'Error buscando personal:',
            error
        );


        mostrarMensajePersonal(
            modal,
            'No fue posible consultar el personal.'
        );

    }

}


/* =========================================================
   RESULTADOS
========================================================= */

function renderizarResultadosPersonal(
    modal,
    personal
) {

    const resultados =
        modal.querySelector(
            '#editar-personal-resultados'
        );


    if (!resultados) {
        return;
    }


    resultados.innerHTML =
        '';


    if (!personal.length) {

        mostrarMensajePersonal(
            modal,
            'No se encontraron coincidencias.'
        );

        return;
    }


    personal.forEach(
        (persona) => {

            const boton =
                document.createElement(
                    'button'
                );


            boton.type =
                'button';

            boton.className =
                'editar-personal-resultados__item';


            const inicial =
                obtenerInicialEditar(
                    persona.nombre
                );


            boton.innerHTML = `
                <span class="editar-personal-resultados__avatar">
                    ${escaparHTML(inicial)}
                </span>

                <span class="editar-personal-resultados__datos">

                    <strong>
                        ${escaparHTML(
                persona.nombre
                || 'Sin nombre'
            )}
                    </strong>

                    <small>
                        Nómina:
                        ${escaparHTML(
                persona.nomina
                || '—'
            )}
                    </small>

                    <small>
                        ${escaparHTML(
                persona.area
                || 'Sin área'
            )}
                    </small>

                </span>
            `;


            boton.addEventListener(
                'click',
                () => {

                    seleccionarPersona(
                        modal,
                        persona
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
   SELECCIONAR PERSONA
========================================================= */

function seleccionarPersona(
    modal,
    persona
) {

    const personaNormalizada = {

        id:
            Number(
                persona.id
            ) || 0,

        perscod:
            String(
                persona.perscod
                || ''
            ).trim(),

        nombre:
            String(
                persona.nombre
                || ''
            )
                .trim()
                .toUpperCase(),

        nomina:
            String(
                persona.nomina
                || ''
            ).trim(),

        area:
            String(
                persona.area
                || ''
            )
                .trim()
                .toUpperCase(),

        turno:
            String(
                persona.turno
                || ''
            )
                .trim()
                .toUpperCase(),

        foto:
            String(
                persona.foto
                || ''
            ).trim(),

    };


    establecerPersonaSeleccionada(
        personaNormalizada
    );


    asignarValorEditar(
        modal,
        '#editar-personal-plantilla-id',
        personaNormalizada.id
    );

    asignarValorEditar(
        modal,
        '#editar-personal-perscod',
        personaNormalizada.perscod
    );

    asignarValorEditar(
        modal,
        '#editar-personal-nombre',
        personaNormalizada.nombre
    );

    asignarValorEditar(
        modal,
        '#editar-personal-area',
        personaNormalizada.area
    );

    asignarValorEditar(
        modal,
        '#editar-personal-turno',
        personaNormalizada.turno
    );

    asignarValorEditar(
        modal,
        '#editar-personal-busqueda',
        personaNormalizada.nombre
    );


    cargarFotoPersonal(
        modal,
        personaNormalizada
    );


    const seleccionado =
        modal.querySelector(
            '#editar-personal-seleccionado'
        );


    if (seleccionado) {

        seleccionado.hidden =
            false;

    }


    ocultarResultadosPersonal(
        modal
    );

}


/* =========================================================
   FOTO
========================================================= */

function cargarFotoPersonal(
    modal,
    persona
) {

    const foto =
        modal.querySelector(
            '#editar-personal-foto'
        );

    const fallback =
        modal.querySelector(
            '#editar-personal-foto-fallback'
        );


    const inicial =
        obtenerInicialEditar(
            persona.nombre
        );


    if (fallback) {

        fallback.textContent =
            inicial;

        fallback.hidden =
            false;

    }


    if (!foto) {
        return;
    }


    foto.hidden =
        true;

    foto.removeAttribute(
        'src'
    );


    if (!persona.foto) {
        return;
    }


    foto.onload = () => {

        foto.hidden =
            false;


        if (fallback) {

            fallback.hidden =
                true;

        }

    };


    foto.onerror = () => {

        foto.hidden =
            true;

        foto.removeAttribute(
            'src'
        );


        if (fallback) {

            fallback.hidden =
                false;

        }

    };


    foto.src =
        persona.foto;

}


/* =========================================================
   RENDER PERSONAL AGREGADO
========================================================= */

export function renderizarPersonalEditar(
    modal
) {

    const contenedor =
        modal.querySelector(
            '#editar-personal-agregado'
        );

    const body =
        modal.querySelector(
            '#editar-personal-agregado-body'
        );

    const hidden =
        modal.querySelector(
            '#editar-personal-hidden-inputs'
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


    estadoPersonal.elementos
        .forEach(
            (persona, indice) => {

                const fila =
                    document.createElement(
                        'tr'
                    );


                const inicial =
                    obtenerInicialEditar(
                        persona.nombre
                    );


                const fotoHtml =
                    persona.foto
                        ? `
                            <div class="editar-personal-tabla__foto">

                                <img
                                    src="${escaparHTML(
                            persona.foto
                        )}"
                                    alt=""
                                    onerror="
                                        this.style.display='none';
                                        this.nextElementSibling.style.display='flex';
                                    "
                                >

                                <span style="display:none;">
                                    ${escaparHTML(inicial)}
                                </span>

                            </div>
                        `
                        : `
                            <div class="editar-personal-tabla__foto">

                                <span>
                                    ${escaparHTML(inicial)}
                                </span>

                            </div>
                        `;


                fila.innerHTML = `

                    <td>
                        ${fotoHtml}
                    </td>

                    <td>
                        <strong>
                            ${escaparHTML(
                    persona.nombre
                    || '—'
                )}
                        </strong>
                    </td>

                    <td>
                        ${escaparHTML(
                    persona.nomina
                    || '—'
                )}
                    </td>

                    <td>
                        ${escaparHTML(
                    persona.area
                    || '—'
                )}
                    </td>

                    <td>

    <input
        type="text"
        class="editar-personal-tabla__turno"
        data-editar-turno-personal="${indice}"
        value="${escaparHTML(
                    persona.turno
                    || ''
                )}"
        placeholder="TURNO"
        autocomplete="off"
        required
    >

</td>

                    <td>

                        <button
                            type="button"
                            class="editar-personal-tabla__eliminar"
                            data-editar-eliminar-personal="${indice}"
                        >
                            Quitar
                        </button>

                    </td>
                `;


                body.appendChild(
                    fila
                );


                crearInputsOcultosPersonal(
                    hidden,
                    persona,
                    indice
                );

            }
        );


    contenedor.hidden =
        estadoPersonal.elementos
            .length === 0;


    contenedor.dataset.totalPersonal =
        String(
            estadoPersonal.elementos
                .length
        );

}


/* =========================================================
   INPUTS OCULTOS
========================================================= */

function crearInputsOcultosPersonal(
    contenedor,
    persona,
    indice
) {

    const campos = {

        plantilla_id:
            persona.id,

        perscod:
            persona.perscod,

        nombre:
            persona.nombre,

        nomina:
            persona.nomina,

        area:
            persona.area,

        turno:
            String(
                persona.turno
                || ''
            ).toUpperCase(),

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
                `personal[${indice}][${campo}]`;

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

export function limpiarSelectorPersonal(
    modal
) {

    establecerPersonaSeleccionada(
        null
    );


    asignarValorEditar(
        modal,
        '#editar-personal-busqueda',
        ''
    );


    limpiarPersonaSeleccionada(
        modal
    );


    ocultarResultadosPersonal(
        modal
    );

}


/* =========================================================
   LIMPIAR PERSONA SELECCIONADA
========================================================= */

function limpiarPersonaSeleccionada(
    modal
) {

    [
        '#editar-personal-plantilla-id',
        '#editar-personal-perscod',
        '#editar-personal-nombre',
        '#editar-personal-area',
        '#editar-personal-turno',
    ].forEach(
        (selector) => {

            asignarValorEditar(
                modal,
                selector,
                ''
            );

        }
    );


    const foto =
        modal.querySelector(
            '#editar-personal-foto'
        );


    if (foto) {

        foto.hidden =
            true;

        foto.removeAttribute(
            'src'
        );

    }


    const fallback =
        modal.querySelector(
            '#editar-personal-foto-fallback'
        );


    if (fallback) {

        fallback.textContent =
            '—';

        fallback.hidden =
            false;

    }


    const seleccionado =
        modal.querySelector(
            '#editar-personal-seleccionado'
        );


    if (seleccionado) {

        seleccionado.hidden =
            true;

    }

}


/* =========================================================
   RESULTADOS
========================================================= */

function ocultarResultadosPersonal(
    modal
) {

    const resultados =
        modal.querySelector(
            '#editar-personal-resultados'
        );


    if (!resultados) {
        return;
    }


    resultados.hidden =
        true;

    resultados.innerHTML =
        '';

}


function mostrarMensajePersonal(
    modal,
    mensaje
) {

    const resultados =
        modal.querySelector(
            '#editar-personal-resultados'
        );


    if (!resultados) {
        return;
    }


    resultados.innerHTML = `
        <div class="editar-personal-resultados__vacio">
            ${escaparHTML(mensaje)}
        </div>
    `;


    resultados.hidden =
        false;

}