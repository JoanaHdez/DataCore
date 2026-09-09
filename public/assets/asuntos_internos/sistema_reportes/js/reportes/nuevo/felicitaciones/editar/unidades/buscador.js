/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   BUSCADOR DE UNIDADES
========================================================= */

import {
    escaparHtml,
} from '../utilidades.js';


/* =========================================================
   INICIALIZAR BUSCADOR DE UNIDADES
========================================================= */

export function inicializarBuscadorUnidadesEditar(
    modal
) {

    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const inputBusqueda =
        modal.querySelector(
            '#editar-felicitacion-buscar-unidad'
        );


    const contenedorResultados =
        modal.querySelector(
            '#editar-felicitacion-unidades-resultados'
        );


    /* =====================================================
       UNIDAD SELECCIONADA
    ===================================================== */

    const contenedorSeleccionada =
        modal.querySelector(
            '#editar-felicitacion-unidad-seleccionada'
        );


    const inputParqueId =
        modal.querySelector(
            '#editar-felicitacion-unidad-parque-id'
        );


    const inputNoEconomico =
        modal.querySelector(
            '#editar-felicitacion-unidad-no-economico'
        );


    const inputPlacas =
        modal.querySelector(
            '#editar-felicitacion-unidad-placas'
        );


    const inputMarca =
        modal.querySelector(
            '#editar-felicitacion-unidad-marca'
        );


    const inputSubmarca =
        modal.querySelector(
            '#editar-felicitacion-unidad-submarca'
        );


    const inputColor =
        modal.querySelector(
            '#editar-felicitacion-unidad-color'
        );


    const inputEstatus =
        modal.querySelector(
            '#editar-felicitacion-unidad-estatus'
        );


    const inputServicio =
        modal.querySelector(
            '#editar-felicitacion-unidad-servicio'
        );


    const inputTipo =
        modal.querySelector(
            '#editar-felicitacion-unidad-tipo'
        );


    const btnAgregar =
        modal.querySelector(
            '#btn-editar-agregar-unidad-felicitacion'
        );


    /* =====================================================
       TABLA
    ===================================================== */

    const contenedorAgregadas =
        modal.querySelector(
            '#editar-felicitacion-unidades-agregadas'
        );


    const tbody =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    const contenedorInputs =
        modal.querySelector(
            '#editar-felicitacion-unidades-inputs'
        );


    if (
        !radioConUnidad
        || !inputBusqueda
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
        || !btnAgregar
        || !contenedorAgregadas
        || !tbody
        || !contenedorInputs
    ) {

        console.error(
            'No se encontraron todos los elementos de unidades para editar felicitación.'
        );

        return;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


    let unidadSeleccionada =
        null;


    /* =====================================================
       BUSCAR AL ESCRIBIR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            if (!radioConUnidad.checked) {
                return;
            }


            const termino =
                inputBusqueda.value.trim();


            if (temporizadorBusqueda) {

                clearTimeout(
                    temporizadorBusqueda
                );
            }


            limpiarUnidadSeleccionada();


            if (
                termino.length < 1
            ) {

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
        }
    );


    /* =====================================================
       CONSULTAR BACKEND
    ===================================================== */

    async function buscarUnidades(
        termino
    ) {

        if (!radioConUnidad.checked) {
            return;
        }


        if (controladorBusqueda) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

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
                        method:
                            'GET',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        credentials:
                            'same-origin',

                        signal:
                            controladorBusqueda.signal,
                    }
                );


            const resultado =
                await respuesta.json();


            if (!respuesta.ok) {

                throw new Error(
                    resultado?.message
                    || 'No fue posible consultar las unidades.'
                );
            }


            const unidades =
                Array.isArray(
                    resultado.unidades
                )
                    ? resultado.unidades
                    : [];


            renderizarResultados(
                unidades
            );


        } catch (error) {

            if (
                error.name === 'AbortError'
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
       MOSTRAR RESULTADOS
    ===================================================== */

    function renderizarResultados(
        unidades
    ) {

        contenedorResultados.innerHTML =
            '';


        if (
            unidades.length === 0
        ) {

            mostrarMensajeResultados(
                'No se encontraron unidades.'
            );

            return;
        }


        unidades.forEach(
            (unidad) => {

                const numeroEconomico =
                    String(
                        unidad.no_economico
                        || ''
                    ).trim();


                const placas =
                    String(
                        unidad.placas
                        || ''
                    ).trim();


                const marca =
                    String(
                        unidad.marca
                        || ''
                    ).trim();


                const submarca =
                    String(
                        unidad.submarca
                        || ''
                    ).trim();


                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'modal-felicitacion-unidades-editar__resultado';


                boton.innerHTML = `

                    <strong>
                        ${escaparHtml(
                            numeroEconomico
                            || 'SIN NÚMERO'
                        )}
                    </strong>

                    <span>
                        Placas:
                        ${escaparHtml(
                            placas
                            || 'SIN PLACAS'
                        )}
                    </span>

                    <small>
                        ${escaparHtml(
                            [
                                marca,
                                submarca
                            ]
                                .filter(Boolean)
                                .join(' ')
                            || 'Sin información'
                        )}
                    </small>
                `;


                boton.addEventListener(
                    'click',
                    () => {

                        seleccionarUnidad(
                            unidad
                        );
                    }
                );


                contenedorResultados.appendChild(
                    boton
                );
            }
        );


        contenedorResultados.hidden =
            false;
    }


    /* =====================================================
       SELECCIONAR UNIDAD
    ===================================================== */

    function seleccionarUnidad(
        unidad
    ) {

        const parqueVehicularId =
            Number(
                unidad.id
                || unidad.parque_vehicular_id
                || 0
            );


        if (
            parqueVehicularId <= 0
        ) {
            return;
        }


        unidadSeleccionada = {

            id:
                parqueVehicularId,

            no_economico:
                String(
                    unidad.no_economico
                    || ''
                ).trim(),

            placas:
                String(
                    unidad.placas
                    || ''
                ).trim(),

            marca:
                String(
                    unidad.marca
                    || ''
                ).trim(),

            submarca:
                String(
                    unidad.submarca
                    || ''
                ).trim(),

            color:
                String(
                    unidad.color
                    || ''
                ).trim(),

            estatus:
                String(
                    unidad.estatus
                    || ''
                ).trim(),

            servicio:
                String(
                    unidad.servicio
                    || ''
                ).trim(),

            tipo:
                String(
                    unidad.tipo
                    || ''
                ).trim(),
        };


        inputParqueId.value =
            String(
                unidadSeleccionada.id
            );


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


        contenedorSeleccionada.hidden =
            false;


        inputBusqueda.value =
            unidadSeleccionada.no_economico;


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
                || unidadSeleccionada.id <= 0
            ) {
                return;
            }


            const parqueVehicularId =
                unidadSeleccionada.id;


            const existente =
                tbody.querySelector(
                    `tr[data-parque-vehicular-id="${parqueVehicularId}"]`
                );


            if (existente) {

                alert(
                    'Esta unidad ya está relacionada con la felicitación.'
                );

                return;
            }


            /* =================================================
               QUITAR FILA VACÍA
            ================================================= */

            tbody
                .querySelectorAll(
                    'tr'
                )
                .forEach(
                    (fila) => {

                        if (
                            !fila.dataset.parqueVehicularId
                        ) {

                            fila.remove();
                        }
                    }
                );


            /* =================================================
               CREAR FILA
            ================================================= */

            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.parqueVehicularId =
                String(
                    parqueVehicularId
                );


            fila.dataset.unidadNueva =
                '1';


            const marcaSubmarca =
                [
                    unidadSeleccionada.marca,
                    unidadSeleccionada.submarca
                ]
                    .filter(Boolean)
                    .join(' ');


            fila.innerHTML = `

                <td>

                    <strong class="modal-felicitacion-unidades-editar__unidad-nombre">

                        ${escaparHtml(
                            unidadSeleccionada.no_economico
                            || '—'
                        )}

                    </strong>

                    <span class="modal-felicitacion-unidades-editar__unidad-detalle">

                        Placas:
                        ${escaparHtml(
                            unidadSeleccionada.placas
                            || 'SIN PLACAS'
                        )}

                    </span>

                </td>


                <td>
                    ${escaparHtml(
                        marcaSubmarca
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.color
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.estatus
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.servicio
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        unidadSeleccionada.tipo
                        || '—'
                    )}
                </td>


                <td>

                    <button
                        type="button"
                        class="button--remove"
                        data-quitar-unidad-felicitacion
                    >
                        Quitar
                    </button>

                </td>
            `;


            tbody.appendChild(
                fila
            );


            contenedorAgregadas.hidden =
                false;


            actualizarInputsUnidades();


            limpiarSelectorCompleto();
        }
    );


    /* =====================================================
       QUITAR UNIDAD
    ===================================================== */

    tbody.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-quitar-unidad-felicitacion]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest(
                    'tr[data-parque-vehicular-id]'
                );


            if (!fila) {
                return;
            }


            fila.remove();


            actualizarInputsUnidades();


            const filasRestantes =
                tbody.querySelectorAll(
                    'tr[data-parque-vehicular-id]'
                );


            if (
                filasRestantes.length === 0
            ) {

                contenedorAgregadas.hidden =
                    true;
            }
        }
    );


    /* =====================================================
       LIMPIAR UNIDAD SELECCIONADA
    ===================================================== */

    function limpiarUnidadSeleccionada() {

        unidadSeleccionada =
            null;


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


        contenedorSeleccionada.hidden =
            true;
    }


    /* =====================================================
       LIMPIAR SELECTOR COMPLETO
    ===================================================== */

    function limpiarSelectorCompleto() {

        inputBusqueda.value =
            '';


        ocultarResultados();


        limpiarUnidadSeleccionada();
    }


    /* =====================================================
       INPUTS OCULTOS
    ===================================================== */

    function actualizarInputsUnidades() {

        contenedorInputs.innerHTML =
            '';


        const filas =
            tbody.querySelectorAll(
                'tr[data-parque-vehicular-id]'
            );


        filas.forEach(
            (fila, indice) => {

                const parqueVehicularId =
                    Number(
                        fila.dataset.parqueVehicularId
                        || 0
                    );


                if (
                    parqueVehicularId <= 0
                ) {
                    return;
                }


                const input =
                    document.createElement(
                        'input'
                    );


                input.type =
                    'hidden';


                input.name =
                    `unidades[${indice}][parque_vehicular_id]`;


                input.value =
                    String(
                        parqueVehicularId
                    );


                contenedorInputs.appendChild(
                    input
                );
            }
        );
    }


    /* =====================================================
       MENSAJE DE RESULTADOS
    ===================================================== */

    function mostrarMensajeResultados(
        mensaje
    ) {

        contenedorResultados.innerHTML = `

            <div class="modal-felicitacion-unidades-editar__resultado-vacio">

                ${escaparHtml(
                    mensaje
                )}

            </div>
        `;


        contenedorResultados.hidden =
            false;
    }


    /* =====================================================
       OCULTAR RESULTADOS
    ===================================================== */

    function ocultarResultados() {

        contenedorResultados.hidden =
            true;


        contenedorResultados.innerHTML =
            '';
    }


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                evento.target === inputBusqueda
                || contenedorResultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            ocultarResultados();
        }
    );
}
