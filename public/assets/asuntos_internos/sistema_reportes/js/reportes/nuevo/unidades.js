/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Nuevo reporte - Unidades involucradas
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarUnidades();
});


function inicializarUnidades() {

    const inputBusqueda =
        document.querySelector('#unidad_busqueda');

    const contenedorResultados =
        document.querySelector('#unidad-resultados');

    const contenedorSeleccionada =
        document.querySelector('#unidad-seleccionada');

    const inputParqueId =
        document.querySelector('#unidad-parque-id');

    const inputNoEconomico =
        document.querySelector('#unidad_no_economico');

    const inputPlacas =
        document.querySelector('#unidad_placas');

    const inputMarca =
        document.querySelector('#unidad_marca');

    const inputSubmarca =
        document.querySelector('#unidad_submarca');

    const inputColor =
        document.querySelector('#unidad_color');

    const inputEstatus =
        document.querySelector('#unidad_estatus');

    const inputServicio =
        document.querySelector('#unidad_servicio');

    const inputTipo =
        document.querySelector('#unidad_tipo');

    const selectOrigen =
        document.querySelector('#unidad_origen');

    const btnAgregar =
        document.querySelector('#btn-agregar-unidad');

    const contenedorAgregadas =
        document.querySelector('#unidades-agregadas');

    const tablaBody =
        document.querySelector('#unidades-agregadas-body');

    const hiddenInputs =
        document.querySelector('#unidades-hidden-inputs');


    if (
        !inputBusqueda
        || !contenedorResultados
        || !contenedorSeleccionada
        || !inputParqueId
        || !inputNoEconomico
        || !inputPlacas
        || !inputMarca
        || !inputSubmarca
        || !inputColor
        || !inputEstatus
        || !inputServicio
        || !inputTipo
        || !selectOrigen
        || !btnAgregar
        || !contenedorAgregadas
        || !tablaBody
        || !hiddenInputs
    ) {
        return;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    const unidadesAgregadas = [];

    let temporizadorBusqueda = null;
    let controladorBusqueda = null;
    let unidadSeleccionada = null;


    /* =====================================================
       BUSCAR
    ===================================================== */

    inputBusqueda.addEventListener('input', () => {

        const termino =
            inputBusqueda.value.trim();


        unidadSeleccionada = null;

        limpiarUnidadSeleccionada();


        if (temporizadorBusqueda) {
            clearTimeout(
                temporizadorBusqueda
            );
        }


        if (termino.length < 1) {

            ocultarResultados();

            return;
        }


        temporizadorBusqueda =
            window.setTimeout(
                () => {
                    buscarUnidades(
                        termino
                    );
                },
                300
            );

    });


    /* =====================================================
       CONSULTAR BACKEND
    ===================================================== */

    async function buscarUnidades(
        termino
    ) {

        if (controladorBusqueda) {
            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

            /* const baseUrl =
                document
                    .querySelector('base')
                    ?.href
                || `${window.location.origin}/`;


            const url =
                new URL(
                    'asuntos-internos/reportes/unidades/buscar',
                    baseUrl
                ); */

                const url =
    new URL(
        'DataCore/public/asuntos-internos/reportes/unidades/buscar',
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
                            controladorBusqueda.signal,
                    }
                );


            if (!respuesta.ok) {

                throw new Error(
                    'No fue posible consultar las unidades.'
                );

            }


            const datos =
                await respuesta.json();


            renderizarResultados(
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


            mostrarMensajeResultados(
                'No fue posible consultar las unidades.'
            );

        }

    }


    /* =====================================================
       RESULTADOS
    ===================================================== */

    function renderizarResultados(
        unidades
    ) {

        contenedorResultados.innerHTML =
            '';


        if (!unidades.length) {

            mostrarMensajeResultados(
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
                    'unidad-resultados__item';


                boton.innerHTML = `
                    <span class="unidad-resultados__icono">
                        U
                    </span>

                    <span class="unidad-resultados__datos">

                        <strong>
                            ${escaparHtml(
                                unidad.no_economico
                                || 'SIN NÚMERO'
                            )}
                        </strong>

                        <small>
                            Placas:
                            ${escaparHtml(
                                unidad.placas
                                || '—'
                            )}
                        </small>

                        <small>
                            ${escaparHtml(
                                unidad.marca
                                || '—'
                            )}
                            ${escaparHtml(
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
                            unidad
                        );

                    }
                );


                contenedorResultados
                    .appendChild(
                        boton
                    );

            }
        );


        contenedorResultados.hidden =
            false;

    }


    function mostrarMensajeResultados(
        mensaje
    ) {

        contenedorResultados.innerHTML = `
            <div class="unidad-resultados__vacio">
                ${escaparHtml(mensaje)}
            </div>
        `;


        contenedorResultados.hidden =
            false;

    }


    function ocultarResultados() {

        contenedorResultados.hidden =
            true;

        contenedorResultados.innerHTML =
            '';

    }


    /* =====================================================
       SELECCIONAR UNIDAD
    ===================================================== */

    function seleccionarUnidad(
        unidad
    ) {

        unidadSeleccionada = {

            id:
                Number(unidad.id) || 0,

            no_economico:
                String(
                    unidad.no_economico
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            placas:
                String(
                    unidad.placas
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            marca:
                String(
                    unidad.marca
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            submarca:
                String(
                    unidad.submarca
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            color:
                String(
                    unidad.color
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            estatus:
                String(
                    unidad.estatus
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            servicio:
                String(
                    unidad.servicio
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            tipo:
                String(
                    unidad.tipo
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            modelo:
                String(
                    unidad.modelo
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            serie:
                String(
                    unidad.serie
                    || ''
                )
                    .trim()
                    .toUpperCase(),

            origen:
                '',
        };


        inputParqueId.value =
            unidadSeleccionada.id;

        inputNoEconomico.value =
            unidadSeleccionada.no_economico;

        inputPlacas.value =
            unidadSeleccionada.placas;

        inputMarca.value =
            unidadSeleccionada.marca;

        inputSubmarca.value =
            unidadSeleccionada.submarca;

        inputColor.value =
            unidadSeleccionada.color;

        inputEstatus.value =
            unidadSeleccionada.estatus;

        inputServicio.value =
            unidadSeleccionada.servicio;

        inputTipo.value =
            unidadSeleccionada.tipo;


        selectOrigen.value =
            '';


        inputBusqueda.value =
            unidadSeleccionada.no_economico
            || unidadSeleccionada.placas;


        contenedorSeleccionada.hidden =
            false;


        ocultarResultados();

    }


    /* =====================================================
       AGREGAR UNIDAD
    ===================================================== */

    btnAgregar.addEventListener(
        'click',
        () => {

            if (
                !unidadSeleccionada
                || !unidadSeleccionada.id
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
                unidadesAgregadas.some(
                    (unidad) =>
                        unidad.id ===
                        unidadSeleccionada.id
                );


            if (yaExiste) {

                mostrarMensajeResultados(
                    'Esta unidad ya fue agregada al reporte.'
                );

                return;

            }


            unidadesAgregadas.push({
                ...unidadSeleccionada,
                origen,
            });


            renderizarUnidadesAgregadas();

            limpiarSelector();

        }
    );


    /* =====================================================
       TABLA
    ===================================================== */

    function renderizarUnidadesAgregadas() {

        tablaBody.innerHTML =
            '';

        hiddenInputs.innerHTML =
            '';


        unidadesAgregadas.forEach(
            (unidad, indice) => {

                const fila =
                    document.createElement(
                        'tr'
                    );


                fila.innerHTML = `

                    <td>

                        <strong>
                            ${escaparHtml(
                                unidad.no_economico
                                || '—'
                            )}
                        </strong>

                        <small class="unidad-tabla__placas">
                            Placas:
                            ${escaparHtml(
                                unidad.placas
                                || '—'
                            )}
                        </small>

                    </td>

                    <td>
                        ${escaparHtml(
                            unidad.marca
                            || '—'
                        )}
                        ${escaparHtml(
                            unidad.submarca
                            || ''
                        )}
                    </td>

                    <td>
                        ${escaparHtml(
                            unidad.color
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHtml(
                            unidad.estatus
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHtml(
                            unidad.servicio
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHtml(
                            unidad.tipo
                            || '—'
                        )}
                    </td>

                    <td>
                        ${escaparHtml(
                            unidad.origen
                            || '—'
                        )}
                    </td>

                    <td>

                        <button
                            type="button"
                            class="unidad-tabla__eliminar"
                            data-eliminar-unidad="${indice}"
                        >
                            Quitar
                        </button>

                    </td>
                `;


                tablaBody.appendChild(
                    fila
                );


                crearInputsOcultos(
                    unidad,
                    indice
                );

            }
        );


        contenedorAgregadas.hidden =
            unidadesAgregadas.length === 0;


        contenedorAgregadas
            .dataset
            .totalUnidades =
            String(
                unidadesAgregadas.length
            );

    }


    /* =====================================================
       QUITAR UNIDAD
    ===================================================== */

    tablaBody.addEventListener(
        'click',
        (event) => {

            const boton =
                event.target.closest(
                    '[data-eliminar-unidad]'
                );


            if (!boton) {
                return;
            }


            const indice =
                Number(
                    boton
                        .dataset
                        .eliminarUnidad
                );


            if (
                !Number.isInteger(indice)
                || !unidadesAgregadas[indice]
            ) {
                return;
            }


            unidadesAgregadas.splice(
                indice,
                1
            );


            renderizarUnidadesAgregadas();

        }
    );


    /* =====================================================
       INPUTS OCULTOS
    ===================================================== */

    function crearInputsOcultos(
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


        Object.entries(campos)
            .forEach(
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


                    hiddenInputs
                        .appendChild(
                            input
                        );

                }
            );

    }


    /* =====================================================
       LIMPIAR SELECTOR
    ===================================================== */

    function limpiarSelector() {

        unidadSeleccionada =
            null;


        inputBusqueda.value =
            '';


        limpiarUnidadSeleccionada();

        ocultarResultados();

    }


    function limpiarUnidadSeleccionada() {

        inputParqueId.value =
            '';

        inputNoEconomico.value =
            '';

        inputPlacas.value =
            '';

        inputMarca.value =
            '';

        inputSubmarca.value =
            '';

        inputColor.value =
            '';

        inputEstatus.value =
            '';

        inputServicio.value =
            '';

        inputTipo.value =
            '';

        selectOrigen.value =
            '';


        contenedorSeleccionada.hidden =
            true;

    }


    /* =====================================================
       CERRAR RESULTADOS AL HACER CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (event) => {

            if (
                event.target === inputBusqueda
                || contenedorResultados.contains(
                    event.target
                )
            ) {
                return;
            }


            ocultarResultados();

        }
    );

}


/* =========================================================
   UTILIDADES
========================================================= */

function escaparHtml(valor) {

    return String(valor ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

}