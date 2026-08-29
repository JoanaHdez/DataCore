/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Nuevo reporte - Personal involucrado
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarPersonalInvolucrado();
});


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarPersonalInvolucrado() {

    const inputBusqueda =
        document.querySelector('#oficial');

    const contenedorResultados =
        document.querySelector('#personal-resultados');

    const contenedorSeleccionado =
        document.querySelector('#personal-seleccionado');

    const inputPlantillaId =
        document.querySelector('#personal-plantilla-id');

    const inputPerscod =
        document.querySelector('#personal-perscod');

    const inputNombre =
        document.querySelector('#personal-nombre');

    const inputArea =
        document.querySelector('#personal-area');

    const inputTurno =
        document.querySelector('#personal-turno');

    const foto =
        document.querySelector('#personal-foto');

    const fotoFallback =
        document.querySelector('#personal-foto-fallback');

    const btnAgregar =
        document.querySelector('#btn-agregar-personal');

    const contenedorAgregado =
        document.querySelector('#personal-agregado');

    const tablaBody =
        document.querySelector('#personal-agregado-body');

    const hiddenInputs =
        document.querySelector('#personal-hidden-inputs');


    if (
        !inputBusqueda
        || !contenedorResultados
        || !contenedorSeleccionado
        || !inputPlantillaId
        || !inputPerscod
        || !inputNombre
        || !inputArea
        || !inputTurno
        || !btnAgregar
        || !contenedorAgregado
        || !tablaBody
        || !hiddenInputs
    ) {
        return;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    const personalAgregado = [];

    let temporizadorBusqueda = null;

    let controladorBusqueda = null;

    let personaSeleccionada = null;


    /* =====================================================
       TURNO EN MAYÚSCULAS
    ===================================================== */

    inputTurno.addEventListener('input', () => {

        const inicio =
            inputTurno.selectionStart;

        const fin =
            inputTurno.selectionEnd;


        inputTurno.value =
            inputTurno.value.toUpperCase();


        if (
            inicio !== null
            && fin !== null
        ) {

            inputTurno.setSelectionRange(
                inicio,
                fin
            );

        }

        inputTurno.setCustomValidity('');

    });


    /* =====================================================
       BUSCAR PERSONAL
    ===================================================== */

    inputBusqueda.addEventListener('input', () => {

        const termino =
            inputBusqueda.value.trim();


        personaSeleccionada = null;

        limpiarPersonaSeleccionada();


        if (temporizadorBusqueda) {

            clearTimeout(
                temporizadorBusqueda
            );

        }


        if (termino.length < 2) {

            ocultarResultados();

            return;
        }


        temporizadorBusqueda =
            window.setTimeout(
                () => {
                    buscarPersonal(termino);
                },
                300
            );

    });


    /* =====================================================
       CONSULTAR BACKEND
    ===================================================== */

    async function buscarPersonal(termino) {

        if (controladorBusqueda) {

            controladorBusqueda.abort();

        }


        controladorBusqueda =
            new AbortController();


        try {

            const baseUrl =
                document
                    .querySelector('base')
                    ?.href
                || `${window.location.origin}/`;


            const url =
                new URL(
                    'asuntos-internos/reportes/personal/buscar',
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
                            Accept: 'application/json',
                        },

                        signal:
                            controladorBusqueda.signal,
                    }
                );


            if (!respuesta.ok) {

                throw new Error(
                    'No fue posible consultar el personal.'
                );

            }


            const datos =
                await respuesta.json();


            renderizarResultados(
                Array.isArray(datos.personal)
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


            mostrarMensajeResultados(
                'No fue posible consultar el personal.'
            );

        }

    }


    /* =====================================================
       RENDERIZAR RESULTADOS
    ===================================================== */

    function renderizarResultados(personal) {

        contenedorResultados.innerHTML =
            '';


        if (!personal.length) {

            mostrarMensajeResultados(
                'No se encontraron coincidencias.'
            );

            return;
        }


        personal.forEach((persona) => {

            const boton =
                document.createElement(
                    'button'
                );


            boton.type =
                'button';

            boton.className =
                'personal-resultados__item';


            const inicial =
                obtenerInicial(
                    persona.nombre
                );


            boton.innerHTML = `
                <span class="personal-resultados__avatar">
                    ${escaparHtml(inicial)}
                </span>

                <span class="personal-resultados__datos">

                    <strong>
                        ${escaparHtml(
                persona.nombre
                || 'Sin nombre'
            )}
                    </strong>

                    <small>
                        Nómina:
                        ${escaparHtml(
                persona.nomina
                || '—'
            )}
                    </small>

                    <small>
                        ${escaparHtml(
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
                        persona
                    );

                }
            );


            contenedorResultados
                .appendChild(
                    boton
                );

        });


        contenedorResultados.hidden =
            false;

    }


    /* =====================================================
       MENSAJE DE RESULTADOS
    ===================================================== */

    function mostrarMensajeResultados(
        mensaje
    ) {

        contenedorResultados.innerHTML = `
            <div class="personal-resultados__vacio">
                ${escaparHtml(mensaje)}
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
       SELECCIONAR PERSONA
    ===================================================== */

    function seleccionarPersona(persona) {

        personaSeleccionada = {

            id:
                Number(
                    persona.id
                ) || 0,

            perscod:
                String(
                    persona.perscod || ''
                ).trim(),

            nombre:
                String(
                    persona.nombre || ''
                )
                    .trim()
                    .toUpperCase(),

            nomina:
                String(
                    persona.nomina || ''
                ).trim(),

            area:
                String(
                    persona.area || ''
                )
                    .trim()
                    .toUpperCase(),

            turno:
                String(
                    persona.turno || ''
                )
                    .trim()
                    .toUpperCase(),

            foto:
                String(
                    persona.foto || ''
                ).trim(),
        };


        inputPlantillaId.value =
            personaSeleccionada.id;

        inputPerscod.value =
            personaSeleccionada.perscod;

        inputNombre.value =
            personaSeleccionada.nombre;

        inputArea.value =
            personaSeleccionada.area;


        /*
 * El turno se obtiene inicialmente
 * desde plantilla, pero permanece editable.
 */
        inputTurno.value =
            personaSeleccionada.turno;

        inputTurno.setCustomValidity('');


        inputBusqueda.value =
            personaSeleccionada.nombre;


        cargarFoto(
            personaSeleccionada
        );


        contenedorSeleccionado.hidden =
            false;


        ocultarResultados();

    }


    /* =====================================================
       FOTO
    ===================================================== */

    function cargarFoto(persona) {

        const inicial =
            obtenerInicial(
                persona.nombre
            );


        if (fotoFallback) {

            fotoFallback.textContent =
                inicial;

            fotoFallback.hidden =
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


            if (fotoFallback) {

                fotoFallback.hidden =
                    true;

            }

        };


        foto.onerror = () => {

            foto.hidden =
                true;

            foto.removeAttribute(
                'src'
            );


            if (fotoFallback) {

                fotoFallback.hidden =
                    false;

            }

        };


        foto.src =
            persona.foto;

    }


    /* =====================================================
       AGREGAR PERSONA
    ===================================================== */

    btnAgregar.addEventListener(
        'click',
        () => {

            if (
                !personaSeleccionada
                || !personaSeleccionada.id
            ) {
                return;
            }


            /*
             * Normalizamos nuevamente el turno
             * antes de guardarlo.
             */
            const turno =
                inputTurno
                    .value
                    .trim()
                    .toUpperCase();


            inputTurno.value =
                turno;

            if (!turno) {

                inputTurno.setCustomValidity(
                    'El turno es obligatorio.'
                );

                inputTurno.reportValidity();

                inputTurno.focus();

                return;
            }


            inputTurno.setCustomValidity('');

            /*
             * Evitamos agregar dos veces
             * a la misma persona.
             */
            const yaExiste =
                personalAgregado.some(
                    (persona) =>
                        persona.id ===
                        personaSeleccionada.id
                );


            if (yaExiste) {

                mostrarMensajeResultados(
                    'Esta persona ya fue agregada al reporte.'
                );

                return;

            }


            personalAgregado.push({
                ...personaSeleccionada,
                turno,
            });


            renderizarPersonalAgregado();

            limpiarSelector();

        }
    );


    /* =====================================================
       RENDERIZAR PERSONAL AGREGADO
    ===================================================== */

    function renderizarPersonalAgregado() {

        tablaBody.innerHTML =
            '';

        hiddenInputs.innerHTML =
            '';


        personalAgregado.forEach(
            (persona, indice) => {

                const fila =
                    document.createElement(
                        'tr'
                    );


                const inicial =
                    obtenerInicial(
                        persona.nombre
                    );


                const fotoHtml =
                    persona.foto
                        ? `
                            <div class="personal-tabla__foto">

                                <img
                                    src="${escaparAtributo(
                            persona.foto
                        )}"
                                    alt=""
                                    onerror="
                                        this.style.display='none';
                                        this.nextElementSibling.style.display='flex';
                                    "
                                >

                                <span style="display:none;">
                                    ${escaparHtml(inicial)}
                                </span>

                            </div>
                        `
                        : `
                            <div class="personal-tabla__foto">

                                <span>
                                    ${escaparHtml(inicial)}
                                </span>

                            </div>
                        `;


                fila.innerHTML = `

                    <td>
                        ${fotoHtml}
                    </td>

                    <td>

                        <strong>
                            ${escaparHtml(
                    persona.nombre
                )}
                        </strong>

                        <small class="personal-tabla__nomina">
                            Nómina:
                            ${escaparHtml(
                    persona.nomina
                    || '—'
                )}
                        </small>

                    </td>

                    <td>
                        ${escaparHtml(
                    persona.area
                    || '—'
                )}
                    </td>

                    <td>
                        ${escaparHtml(
                    persona.turno
                    || '—'
                )}
                    </td>

                    <td>

                        <button
                            type="button"
                            class="personal-tabla__eliminar"
                            data-eliminar-personal="${indice}"
                        >
                            Quitar
                        </button>

                    </td>
                `;


                tablaBody.appendChild(
                    fila
                );


                crearInputsOcultos(
                    persona,
                    indice
                );

            }
        );


        contenedorAgregado.hidden =
            personalAgregado.length === 0;


        /*
         * Cantidad real de personas agregadas.
         * Puede utilizarse para validar el paso.
         */
        contenedorAgregado
            .dataset
            .totalPersonal =
            String(
                personalAgregado.length
            );

    }


    /* =====================================================
       QUITAR PERSONA
    ===================================================== */

    tablaBody.addEventListener(
        'click',
        (event) => {

            const boton =
                event.target.closest(
                    '[data-eliminar-personal]'
                );


            if (!boton) {
                return;
            }


            const indice =
                Number(
                    boton
                        .dataset
                        .eliminarPersonal
                );


            if (
                !Number.isInteger(indice)
                || !personalAgregado[indice]
            ) {
                return;
            }


            personalAgregado.splice(
                indice,
                1
            );


            renderizarPersonalAgregado();

        }
    );


    /* =====================================================
       INPUTS OCULTOS PARA BACKEND
    ===================================================== */

    function crearInputsOcultos(
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
                    persona.turno || ''
                ).toUpperCase(),
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
                        `personal[${indice}][${campo}]`;


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

        personaSeleccionada =
            null;


        inputBusqueda.value =
            '';


        limpiarPersonaSeleccionada();

        ocultarResultados();

    }


    /* =====================================================
       LIMPIAR PERSONA SELECCIONADA
    ===================================================== */

    function limpiarPersonaSeleccionada() {

        inputPlantillaId.value =
            '';

        inputPerscod.value =
            '';

        inputNombre.value =
            '';

        inputArea.value =
            '';

        inputTurno.value =
            '';

        inputTurno.setCustomValidity('');


        if (foto) {

            foto.hidden =
                true;

            foto.removeAttribute(
                'src'
            );

        }


        if (fotoFallback) {

            fotoFallback.textContent =
                '—';

            fotoFallback.hidden =
                false;

        }


        contenedorSeleccionado.hidden =
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

function obtenerInicial(nombre) {

    const texto =
        String(nombre || '')
            .trim();


    if (!texto) {
        return '?';
    }


    return texto
        .charAt(0)
        .toUpperCase();

}


function escaparHtml(valor) {

    return String(valor ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

}


function escaparAtributo(valor) {

    return escaparHtml(
        valor
    );

}