/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Nueva felicitación - Unidades relacionadas
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarUnidadesFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarUnidadesFelicitacion() {

    /* =====================================================
       MODALIDAD
    ===================================================== */

    const modalidadConUnidad =
        document.querySelector(
            '#felicitacion-modalidad-con-unidad'
        );

    const modalidadSinUnidad =
        document.querySelector(
            '#felicitacion-modalidad-sin-unidad'
        );

    const contenedorConUnidad =
        document.querySelector(
            '#felicitacion-contenedor-unidades-con-unidad'
        );

    const contenedorSinUnidad =
        document.querySelector(
            '#felicitacion-unidad-sin-unidad'
        );


    /* =====================================================
       BÚSQUEDA Y SELECCIÓN
    ===================================================== */

    const inputBusqueda =
        document.querySelector(
            '#felicitacion-unidad-busqueda'
        );

    const contenedorResultados =
        document.querySelector(
            '#felicitacion-unidad-resultados'
        );

    const contenedorSeleccionada =
        document.querySelector(
            '#felicitacion-unidad-seleccionada'
        );

    const inputParqueId =
        document.querySelector(
            '#felicitacion-unidad-parque-id'
        );

    const inputNoEconomico =
        document.querySelector(
            '#felicitacion-unidad-no-economico'
        );

    const inputPlacas =
        document.querySelector(
            '#felicitacion-unidad-placas'
        );

    const inputMarca =
        document.querySelector(
            '#felicitacion-unidad-marca'
        );

    const inputSubmarca =
        document.querySelector(
            '#felicitacion-unidad-submarca'
        );

    const inputColor =
        document.querySelector(
            '#felicitacion-unidad-color'
        );

    const inputEstatus =
        document.querySelector(
            '#felicitacion-unidad-estatus'
        );

    const inputServicio =
        document.querySelector(
            '#felicitacion-unidad-servicio'
        );

    const inputTipo =
        document.querySelector(
            '#felicitacion-unidad-tipo'
        );

    const btnAgregar =
        document.querySelector(
            '#btn-agregar-unidad-felicitacion'
        );

    const contenedorAgregadas =
        document.querySelector(
            '#felicitacion-unidades-agregadas'
        );

    const tablaBody =
        document.querySelector(
            '#felicitacion-unidades-agregadas-body'
        );

    const hiddenInputs =
        document.querySelector(
            '#felicitacion-unidades-hidden-inputs'
        );


    if (
        !modalidadConUnidad
        || !modalidadSinUnidad
        || !contenedorConUnidad
        || !contenedorSinUnidad
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
        || !tablaBody
        || !hiddenInputs
    ) {
        return;
    }


    /* =====================================================
       ESTADO
    ===================================================== */

    const unidadesAgregadas = [];

    let temporizadorBusqueda =
        null;

    let controladorBusqueda =
        null;

    let unidadSeleccionada =
        null;


    /* =====================================================
       MODALIDAD
    ===================================================== */

    modalidadConUnidad.addEventListener(
        'change',
        () => {

            if (!modalidadConUnidad.checked) {
                return;
            }

            activarConUnidad();
        }
    );


    modalidadSinUnidad.addEventListener(
        'change',
        () => {

            if (!modalidadSinUnidad.checked) {
                return;
            }

            activarSinUnidad();
        }
    );


    function activarConUnidad() {

        contenedorConUnidad.hidden =
            false;

        contenedorSinUnidad.hidden =
            true;
    }


    function activarSinUnidad() {

        /* Cancelar búsqueda pendiente */

        if (temporizadorBusqueda) {

            clearTimeout(
                temporizadorBusqueda
            );

            temporizadorBusqueda =
                null;
        }


        /* Cancelar petición activa */

        if (controladorBusqueda) {

            controladorBusqueda.abort();

            controladorBusqueda =
                null;
        }


        /* Limpiar selección */

        limpiarSelector();


        /* Quitar unidades previamente agregadas */

        unidadesAgregadas.splice(
            0,
            unidadesAgregadas.length
        );

        renderizarUnidadesAgregadas();


        /* Cambiar contenido */

        contenedorConUnidad.hidden =
            true;

        contenedorSinUnidad.hidden =
            false;
    }


    /* =====================================================
       BUSCAR
    ===================================================== */

    inputBusqueda.addEventListener(
        'input',
        () => {

            if (!modalidadConUnidad.checked) {
                return;
            }


            const termino =
                inputBusqueda.value.trim();


            unidadSeleccionada =
                null;

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
        }
    );


    /* =====================================================
       CONSULTAR BACKEND
    ===================================================== */

    async function buscarUnidades(
        termino
    ) {

        if (!modalidadConUnidad.checked) {
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

        if (!modalidadConUnidad.checked) {
            return;
        }


        unidadSeleccionada = {

            id:
                Number(
                    unidad.id
                ) || 0,

            no_economico:
                normalizarMayusculas(
                    unidad.no_economico
                ),

            placas:
                normalizarMayusculas(
                    unidad.placas
                ),

            marca:
                normalizarMayusculas(
                    unidad.marca
                ),

            submarca:
                normalizarMayusculas(
                    unidad.submarca
                ),

            color:
                normalizarMayusculas(
                    unidad.color
                ),

            estatus:
                normalizarMayusculas(
                    unidad.estatus
                ),

            servicio:
                normalizarMayusculas(
                    unidad.servicio
                ),

            tipo:
                normalizarMayusculas(
                    unidad.tipo
                ),

            modelo:
                normalizarMayusculas(
                    unidad.modelo
                ),

            serie:
                normalizarMayusculas(
                    unidad.serie
                ),
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


        inputBusqueda.value =
            unidadSeleccionada.no_economico
            || unidadSeleccionada.placas;


        contenedorSeleccionada.hidden =
            false;


        ocultarResultados();
    }


    /* =====================================================
       AGREGAR
    ===================================================== */

    btnAgregar.addEventListener(
        'click',
        () => {

            if (!modalidadConUnidad.checked) {
                return;
            }


            if (
                !unidadSeleccionada
                || !unidadSeleccionada.id
            ) {
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
                    'Esta unidad ya fue agregada a la felicitación.'
                );

                return;
            }


            unidadesAgregadas.push({
                ...unidadSeleccionada,
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

                        <button
                            type="button"
                            class="unidad-tabla__eliminar"
                            data-eliminar-unidad-felicitacion="${indice}"
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
       QUITAR
    ===================================================== */

    tablaBody.addEventListener(
        'click',
        (event) => {

            const boton =
                event.target.closest(
                    '[data-eliminar-unidad-felicitacion]'
                );


            if (!boton) {
                return;
            }


            const indice =
                Number(
                    boton
                        .dataset
                        .eliminarUnidadFelicitacion
                );


            if (
                !Number.isInteger(
                    indice
                )
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
       INPUTS PARA BACKEND
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
        };


        Object.entries(
            campos
        )
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


        contenedorSeleccionada.hidden =
            true;
    }


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (event) => {

            if (
                event.target ===
                inputBusqueda
                || contenedorResultados.contains(
                    event.target
                )
            ) {
                return;
            }


            ocultarResultados();
        }
    );


    /* =====================================================
   LIMPIAR DESDE EL FORMULARIO
===================================================== */

    document.addEventListener(
        'limpiarUnidadesFelicitacion',
        () => {

            if (temporizadorBusqueda) {

                clearTimeout(
                    temporizadorBusqueda
                );

                temporizadorBusqueda =
                    null;
            }


            if (controladorBusqueda) {

                controladorBusqueda.abort();

                controladorBusqueda =
                    null;
            }


            unidadesAgregadas.splice(
                0,
                unidadesAgregadas.length
            );


            modalidadConUnidad.checked =
                true;


            modalidadSinUnidad.checked =
                false;


            renderizarUnidadesAgregadas();

            limpiarSelector();

            activarConUnidad();
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    if (modalidadSinUnidad.checked) {

        activarSinUnidad();

    } else {

        activarConUnidad();
    }
}


/* =========================================================
   UTILIDADES
========================================================= */

function normalizarMayusculas(
    valor
) {

    return String(
        valor ?? ''
    )
        .trim()
        .toUpperCase();
}


function escaparHtml(
    valor
) {

    return String(
        valor ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );
}

