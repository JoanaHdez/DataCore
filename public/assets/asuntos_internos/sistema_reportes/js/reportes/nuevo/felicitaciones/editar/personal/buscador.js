/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   BUSCADOR DE PERSONAL
========================================================= */

import {
    escaparHtml,
    obtenerInicialApellido,
    obtenerFotoPersonal,
} from '../utilidades.js';


/* =========================================================
   INICIALIZAR BUSCADOR DE PERSONAL
========================================================= */

export function inicializarBuscadorPersonalEditar(
    modal
) {

    const inputBusqueda =
        modal.querySelector(
            '#editar-felicitacion-buscar-personal'
        );


    const contenedorResultados =
        modal.querySelector(
            '#editar-felicitacion-personal-resultados'
        );


    const contenedorSeleccionado =
        modal.querySelector(
            '#editar-felicitacion-personal-seleccionado'
        );


    const inputPlantillaId =
        modal.querySelector(
            '#editar-felicitacion-personal-plantilla-id'
        );


    const inputPerscod =
        modal.querySelector(
            '#editar-felicitacion-personal-perscod'
        );


    const inputNombre =
        modal.querySelector(
            '#editar-felicitacion-personal-nombre'
        );


    const inputNomina =
        modal.querySelector(
            '#editar-felicitacion-personal-nomina'
        );


    const inputArea =
        modal.querySelector(
            '#editar-felicitacion-personal-area'
        );


    const inputTurno =
        modal.querySelector(
            '#editar-felicitacion-personal-turno'
        );


    const inputAlias =
        modal.querySelector(
            '#editar-felicitacion-personal-alias'
        );


    const imagenFoto =
        modal.querySelector(
            '#editar-felicitacion-personal-foto'
        );


    const fotoFallback =
        modal.querySelector(
            '#editar-felicitacion-personal-foto-fallback'
        );


    const btnAgregar =
        modal.querySelector(
            '#btn-editar-agregar-personal-felicitacion'
        );


    const tbody =
        modal.querySelector(
            '#editar-felicitacion-personal'
        );


    const contenedorInputs =
        modal.querySelector(
            '#editar-felicitacion-personal-inputs'
        );


    if (
        !inputBusqueda
        || !contenedorResultados
        || !contenedorSeleccionado
        || !inputPlantillaId
        || !inputPerscod
        || !inputNombre
        || !inputNomina
        || !inputArea
        || !inputTurno
        || !inputAlias
        || !imagenFoto
        || !fotoFallback
        || !btnAgregar
        || !tbody
        || !contenedorInputs
    ) {

        console.error(
            'No se encontraron todos los elementos para editar personal.'
        );

        return;
    }


    let temporizadorBusqueda =
        null;


    let controladorBusqueda =
        null;


    let personaSeleccionada =
        null;


    /* =====================================================
       BUSCAR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            const termino =
                inputBusqueda.value.trim();


            if (temporizadorBusqueda) {

                clearTimeout(
                    temporizadorBusqueda
                );
            }


            limpiarPersonaSeleccionada();


            if (
                termino.length < 1
            ) {

                ocultarResultados();

                return;
            }


            temporizadorBusqueda =
                window.setTimeout(
                    () => {

                        buscarPersonal(
                            termino
                        );

                    },
                    300
                );
        }
    );


    /* =====================================================
       CONSULTAR PERSONAL
    ===================================================== */

    async function buscarPersonal(
        termino
    ) {

        if (controladorBusqueda) {

            controladorBusqueda.abort();
        }


        controladorBusqueda =
            new AbortController();


        try {

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
                    || 'No fue posible consultar el personal.'
                );
            }


            const personal =
                Array.isArray(
                    resultado.personal
                )
                    ? resultado.personal
                    : [];


            renderizarResultados(
                personal
            );


        } catch (error) {

            if (
                error.name === 'AbortError'
            ) {
                return;
            }


            console.error(
                'Error buscando personal:',
                error
            );


            mostrarMensajeResultados(
                'No fue posible consultar el personal.'
            );
        }
    }


    /* =====================================================
       RESULTADOS
    ===================================================== */

    function renderizarResultados(
        personal
    ) {

        contenedorResultados.innerHTML =
            '';


        if (
            personal.length === 0
        ) {

            mostrarMensajeResultados(
                'No se encontró personal.'
            );

            return;
        }


        personal.forEach(
            (persona) => {

                const nombre =
                    String(
                        persona.nombre
                        || persona.nombre_completo
                        || ''
                    ).trim();


                const nomina =
                    String(
                        persona.nomina
                        || persona.perscod
                        || ''
                    ).trim();


                const area =
                    String(
                        persona.area
                        || persona.adscripcion
                        || ''
                    ).trim();


                const inicial =
                    nombre !== ''
                        ? nombre.charAt(0).toUpperCase()
                        : '?';


                const boton =
                    document.createElement(
                        'button'
                    );


                boton.type =
                    'button';


                boton.className =
                    'modal-felicitacion-personal-editar__resultado';


                boton.dataset.inicial =
                    inicial;


                boton.innerHTML = `

                    <strong>
                        ${escaparHtml(
                            nombre
                            || 'SIN NOMBRE'
                        )}
                    </strong>

                    <span>
                        Nómina:
                        ${escaparHtml(
                            nomina
                            || '—'
                        )}
                    </span>

                    <small>
                        ${escaparHtml(
                            area
                            || 'Sin área'
                        )}
                    </small>
                `;


                boton.addEventListener(
                    'click',
                    () => {

                        seleccionarPersona(
                            persona
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
       SELECCIONAR PERSONA
    ===================================================== */

    function seleccionarPersona(
        persona
    ) {

        const plantillaId =
            Number(
                persona.plantilla_id
                || persona.id
                || 0
            );


        const perscod =
            String(
                persona.perscod
                || persona.nomina
                || ''
            ).trim();


        const nombre =
            String(
                persona.nombre
                || persona.nombre_completo
                || ''
            )
                .trim()
                .toUpperCase();


        const nomina =
            String(
                persona.nomina
                || perscod
                || ''
            ).trim();


        const area =
            String(
                persona.area
                || persona.adscripcion
                || ''
            )
                .trim()
                .toUpperCase();


        const turno =
            String(
                persona.turno
                || ''
            )
                .trim()
                .toUpperCase();


        const alias =
            String(
                persona.alias
                || ''
            ).trim();


        const foto =
            obtenerFotoPersonal(
                persona
            );


        const inicial =
            obtenerInicialApellido(
                nombre
            );


        /* =====================================================
        PERSONA SELECCIONADA
        ===================================================== */

        personaSeleccionada = {
            plantillaId,
            perscod,
            nombre,
            nomina,
            area,
            turno,
            alias,
            foto,
        };


        /* =====================================================
        DATOS
        ===================================================== */

        inputPlantillaId.value =
            String(
                plantillaId
            );


        inputPerscod.value =
            perscod;


        inputNombre.value =
            nombre;


        inputNomina.value =
            nomina;


        inputArea.value =
            area;


        inputTurno.value =
            turno;


        inputAlias.value =
            alias;


        /* =====================================================
        FOTO

        Igual que Quejas:
        - fallback visible inicialmente
        - si carga la foto, se muestra
        - si falla, permanece la inicial
        ===================================================== */

        fotoFallback.textContent =
            inicial;


        fotoFallback.hidden =
            false;


        fotoFallback.style.display =
            'flex';

        imagenFoto.hidden =
            true;


        imagenFoto.removeAttribute(
            'src'
        );


        imagenFoto.onload =
            null;


        imagenFoto.onerror =
            null;


        /* =====================================================
        INTENTAR CARGAR FOTO
        ===================================================== */

        if (
            foto !== ''
        ) {

            imagenFoto.onload =
                () => {

                    imagenFoto.hidden =
                        false;


                    fotoFallback.hidden =
                        true;


                    fotoFallback.style.display =
                        'none';
                };


            imagenFoto.onerror =
                () => {

                    imagenFoto.hidden =
                        true;


                    imagenFoto.removeAttribute(
                        'src'
                    );


                    fotoFallback.hidden =
                        false;


                    fotoFallback.style.display =
                        'flex';
                };


            imagenFoto.src =
                foto;
        }


        /* =====================================================
        MOSTRAR PERSONA SELECCIONADA
        ===================================================== */

        contenedorSeleccionado.hidden =
            false;


        inputBusqueda.value =
            nombre;


        ocultarResultados();
    }


    /* =====================================================
       AGREGAR PERSONAL
    ===================================================== */

    btnAgregar.addEventListener(
        'click',
        () => {

            if (!personaSeleccionada) {
                return;
            }


            const plantillaId =
                Number(
                    personaSeleccionada.plantillaId
                    || 0
                );


            const perscod =
                String(
                    personaSeleccionada.perscod
                    || ''
                ).trim();


            /* =================================================
               EVITAR DUPLICADOS
            ================================================= */

            const existente =
                Array.from(
                    tbody.querySelectorAll(
                        'tr[data-plantilla-id]'
                    )
                )
                    .some(
                        (fila) => {

                            return (
                                Number(
                                    fila.dataset.plantillaId
                                    || 0
                                ) === plantillaId
                                || (
                                    perscod !== ''
                                    && String(
                                        fila.dataset.perscod
                                        || ''
                                    ) === perscod
                                )
                            );
                        }
                    );


            if (existente) {

                alert(
                    'Esta persona ya está relacionada con la felicitación.'
                );

                return;
            }


            tbody
                .querySelectorAll(
                    'tr'
                )
                .forEach(
                    (fila) => {

                        if (
                            !fila.dataset.plantillaId
                            && !fila.dataset.perscod
                        ) {
                            fila.remove();
                        }
                    }
                );


            const nombre =
                personaSeleccionada.nombre;


            const nomina =
                personaSeleccionada.nomina;


            const area =
                personaSeleccionada.area;


            const turno =
                inputTurno.value.trim();


            const alias =
                inputAlias.value.trim();


            const foto =
                String(
                    personaSeleccionada.foto
                    || ''
                ).trim();


            const inicial =
                obtenerInicialApellido(
                    nombre
                );


            const fila =
                document.createElement(
                    'tr'
                );


            fila.dataset.plantillaId =
                String(
                    plantillaId
                );


            fila.dataset.perscod =
                perscod;


            fila.dataset.alias =
                alias;


            fila.dataset.personalNuevo =
                '1';


            fila.innerHTML = `

                <td>

                    <div class="modal-felicitacion-personal-editar__foto">

                        <img
                            alt=""
                            data-foto-personal-agregado
                            hidden
                        >

                        <span
                            data-fallback-personal-agregado
                        >
                            ${escaparHtml(inicial)}
                        </span>

                    </div>

                </td>


                <td>

                    <span class="modal-felicitacion-personal-editar__nombre">
                        ${escaparHtml(
                            nombre
                            || '—'
                        )}
                    </span>

                </td>


                <td>
                    ${escaparHtml(
                        nomina
                        || perscod
                        || '—'
                    )}
                </td>


                <td>
                    ${escaparHtml(
                        area
                        || '—'
                    )}
                </td>


                <td>

                    <input
                        type="text"
                        class="modal-felicitacion-personal-editar__turno"
                        value="${escaparHtml(turno)}"
                        data-turno-personal-felicitacion
                        autocomplete="off"
                    >

                </td>


                <td>

                    <button
                        type="button"
                        class="button--remove"
                        data-quitar-personal-felicitacion
                    >
                        Quitar
                    </button>

                </td>
            `;

            /* =================================================
            FOTO DEL PERSONAL AGREGADO

            Misma lógica que Quejas:
            - inicial visible de inicio
            - si carga foto, mostrarla
            - si falla, conservar inicial
            ================================================= */

            const imagenAgregada =
                fila.querySelector(
                    '[data-foto-personal-agregado]'
                );


            const fallbackAgregado =
                fila.querySelector(
                    '[data-fallback-personal-agregado]'
                );


            if (
                imagenAgregada
                && fallbackAgregado
            ) {

                /* =============================================
                ESTADO INICIAL
                ============================================== */

                imagenAgregada.hidden =
                    true;


                imagenAgregada.removeAttribute(
                    'src'
                );


                fallbackAgregado.hidden =
                    false;


                fallbackAgregado.style.display =
                    'flex';


                /* =============================================
                SI HAY FOTO, INTENTAR CARGAR
                ============================================== */

                if (
                    foto !== ''
                ) {

                    imagenAgregada.onload =
                        () => {

                            imagenAgregada.hidden =
                                false;


                            imagenAgregada.style.display =
                                'block';


                            fallbackAgregado.hidden =
                                true;


                            fallbackAgregado.style.display =
                                'none';
                        };


                    imagenAgregada.onerror =
                        () => {

                            imagenAgregada.hidden =
                                true;


                            imagenAgregada.style.display =
                                'none';


                            imagenAgregada.removeAttribute(
                                'src'
                            );


                            fallbackAgregado.hidden =
                                false;


                            fallbackAgregado.style.display =
                                'flex';
                        };


                    imagenAgregada.src =
                        foto;
                }
            }

            tbody.appendChild(
                fila
            );


            actualizarInputsPersonal();


            limpiarSelectorCompleto();
        }
    );


    /* =====================================================
       QUITAR PERSONAL
    ===================================================== */

    tbody.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-quitar-personal-felicitacion]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest(
                    'tr'
                );


            if (!fila) {
                return;
            }


            fila.remove();


            actualizarInputsPersonal();


            if (
                tbody.querySelectorAll(
                    'tr[data-plantilla-id], tr[data-perscod]'
                ).length === 0
            ) {

                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            Sin personal relacionado
                        </td>
                    </tr>
                `;
            }
        }
    );


    /* =====================================================
       INPUTS PARA BACKEND
    ===================================================== */

    function actualizarInputsPersonal() {

        contenedorInputs.innerHTML =
            '';


        const filas =
            tbody.querySelectorAll(
                'tr[data-plantilla-id], tr[data-perscod]'
            );


        filas.forEach(
            (fila, indice) => {

                const plantillaId =
                    String(
                        fila.dataset.plantillaId
                        || ''
                    );


                const perscod =
                    String(
                        fila.dataset.perscod
                        || ''
                    );


                const alias =
                    String(
                        fila.dataset.alias
                        || ''
                    );


                const inputTurnoFila =
                    fila.querySelector(
                        '[data-turno-personal-felicitacion]'
                    );


                const turno =
                    inputTurnoFila
                        ? inputTurnoFila.value.trim()
                        : '';


                contenedorInputs.insertAdjacentHTML(
                    'beforeend',
                    `
                        <input
                            type="hidden"
                            name="personal[${indice}][plantilla_id]"
                            value="${escaparHtml(plantillaId)}"
                        >

                        <input
                            type="hidden"
                            name="personal[${indice}][perscod]"
                            value="${escaparHtml(perscod)}"
                        >

                        <input
                            type="hidden"
                            name="personal[${indice}][turno]"
                            value="${escaparHtml(turno)}"
                        >

                        <input
                            type="hidden"
                            name="personal[${indice}][alias]"
                            value="${escaparHtml(alias)}"
                        >
                    `
                );
            }
        );
    }


    tbody.addEventListener(
        'input',
        (evento) => {

            if (
                evento.target.matches(
                    '[data-turno-personal-felicitacion]'
                )
            ) {

                actualizarInputsPersonal();
            }
        }
    );


    /* =====================================================
       LIMPIAR PERSONA
    ===================================================== */

    function limpiarPersonaSeleccionada() {

        personaSeleccionada =
            null;


        inputPlantillaId.value =
            '';


        inputPerscod.value =
            '';


        inputNombre.value =
            '';


        inputNomina.value =
            '';


        inputArea.value =
            '';


        inputTurno.value =
            '';


        inputAlias.value =
            '';


        imagenFoto.src =
            '';


        imagenFoto.hidden =
            true;


        fotoFallback.textContent =
            '—';


        fotoFallback.hidden =
            false;


        contenedorSeleccionado.hidden =
            true;
    }


    function limpiarSelectorCompleto() {

        inputBusqueda.value =
            '';


        ocultarResultados();


        limpiarPersonaSeleccionada();
    }


    /* =====================================================
       MENSAJES / RESULTADOS
    ===================================================== */

    function mostrarMensajeResultados(
        mensaje
    ) {

        contenedorResultados.innerHTML = `

            <div class="modal-felicitacion-personal-editar__resultado-vacio">
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

