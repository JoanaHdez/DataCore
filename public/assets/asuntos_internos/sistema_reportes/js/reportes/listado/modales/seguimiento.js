/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Seguimiento real
========================================================= */


/*
 * =========================================================
 * MAPA / UBICACIÓN
 * =========================================================
 *
 * PENDIENTE.
 *
 * Este módulo queda preparado para que posteriormente
 * podamos integrar la ubicación del reporte y/o un mapa
 * dentro del seguimiento.
 *
 * Por ahora no se implementa para no mezclar alcances.
 */


/* =========================================================
   ESTADO DEL MÓDULO
========================================================= */

const estadoSeguimiento = {

    idReporte: 0,

    filaActual: null,

    reporte: null,

    sancionActual: null,

    seguimientos: [],

};


/* =========================================================
   INICIALIZACIÓN
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarSeguimientoReporte();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarSeguimientoReporte() {

    const modal =
        document.querySelector(
            '#modal-seguimiento-reporte'
        );


    const formulario =
        document.querySelector(
            '#form-seguimiento-reporte'
        );


    if (
        !modal
        || !formulario
    ) {
        return;
    }


    inicializarSancionSeguimiento(
        modal
    );


    /* =====================================================
       ABRIR MODAL
    ===================================================== */

    document.addEventListener(
        'click',
        async (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="seguimiento"]'
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


            const idReporte =
                Number(
                    boton.dataset.idReporte
                    || fila.dataset.idReporte
                    || 0
                );


            if (
                !Number.isInteger(idReporte)
                || idReporte <= 0
            ) {

                console.error(
                    'No fue posible identificar el reporte.'
                );

                return;
            }


            boton.disabled =
                true;


            try {

                const datos =
                    await consultarSeguimientos(
                        idReporte
                    );


                if (
                    !datos
                    || datos.success !== true
                    || !datos.reporte
                ) {

                    throw new Error(
                        datos?.message
                        || 'No fue posible consultar el seguimiento.'
                    );
                }


                estadoSeguimiento.idReporte =
                    idReporte;


                estadoSeguimiento.filaActual =
                    fila;


                estadoSeguimiento.reporte =
                    datos.reporte;


                estadoSeguimiento.sancionActual =
                    normalizarSancion(
                        datos.sancion
                    );


                estadoSeguimiento.seguimientos =
                    normalizarSeguimientos(
                        datos.seguimientos
                    );


                cargarDatosSeguimiento(
                    modal,
                    formulario,
                    datos.reporte,
                    estadoSeguimiento.sancionActual
                );


                cargarHistorialSeguimiento(
                    modal,
                    estadoSeguimiento.seguimientos
                );


                abrirModalSeguimiento(
                    modal
                );


            } catch (error) {

                console.error(
                    'Error cargando seguimiento:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible consultar el seguimiento.'
                );


            } finally {

                boton.disabled =
                    false;

            }

        }
    );


    /* =====================================================
       CERRAR MODAL
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonCerrar =
                evento.target.closest(
                    '[data-cerrar-modal-seguimiento]'
                );


            if (!botonCerrar) {
                return;
            }


            cerrarModalSeguimiento(
                modal
            );


            limpiarEstadoSeguimiento();

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModalSeguimiento(
                    modal
                );


                limpiarEstadoSeguimiento();

            }

        }
    );


    /* =====================================================
       REGISTRAR SEGUIMIENTO
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            const idReporte =
                estadoSeguimiento.idReporte;


            if (
                !Number.isInteger(idReporte)
                || idReporte <= 0
            ) {

                window.alert(
                    'No fue posible identificar el reporte.'
                );

                return;
            }


            /* =================================================
               VALIDACIÓN SANCIÓN
            ================================================= */

            if (
                !validarSancionSeguimiento(
                    modal
                )
            ) {
                return;
            }


            /* =================================================
               VALIDACIÓN NATIVA
            ================================================= */

            if (
                !formulario.checkValidity()
            ) {

                formulario.reportValidity();

                return;
            }


            /* =================================================
               SANCIÓN SELECCIONADA
            ================================================= */

            const sancionSeleccionada =
                obtenerSancionSeleccionada(
                    modal
                );


            const hayCambioSancion =
                existeCambioRealSancion(
                    estadoSeguimiento.sancionActual,
                    sancionSeleccionada
                );


            /* =================================================
               CONFIRMACIÓN DE CAMBIO DE SANCIÓN
            ================================================= */

            if (hayCambioSancion) {

                const textoActual =
                    obtenerTextoSancion(
                        estadoSeguimiento.sancionActual
                    );


                const textoNueva =
                    obtenerTextoSancion(
                        sancionSeleccionada
                    );


                const confirmado =
                    window.confirm(
                        `La sanción seleccionada no coincide con la sanción actualmente registrada: ${textoActual}.\n\n`
                        + `Si continúas, la sanción vigente cambiará a ${textoNueva} y el cambio quedará registrado en este seguimiento.`
                    );


                if (!confirmado) {
                    return;
                }

            }


            /* =================================================
               DATOS
            ================================================= */

            const datos =
                new FormData(
                    formulario
                );


            /*
             * Si el usuario seleccionó exactamente la misma
             * sanción vigente, la convertimos a "Sin cambio".
             *
             * El backend también valida esto, pero así evitamos
             * enviar información innecesaria.
             */

            if (
                sancionSeleccionada.tipo
                && !hayCambioSancion
            ) {

                datos.set(
                    'sancion_disciplinaria',
                    ''
                );


                datos.set(
                    'sancion_otro',
                    ''
                );

            }


            /* =================================================
               BOTÓN
            ================================================= */

            const botonGuardar =
                formulario.querySelector(
                    '[type="submit"]'
                );


            const textoOriginal =
                botonGuardar
                    ? botonGuardar.innerHTML
                    : '';


            if (botonGuardar) {

                botonGuardar.disabled =
                    true;


                botonGuardar.innerHTML =
                    'Guardando...';

            }


            try {

                /* =================================================
                   GUARDAR EN BD
                ================================================= */

                const resultado =
                    await registrarSeguimiento(
                        idReporte,
                        datos
                    );


                if (
                    !resultado
                    || resultado.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible registrar el seguimiento.'
                    );
                }


                /* =================================================
                   ACTUALIZAR ESTADO
                ================================================= */

                const nuevoEstado =
                    String(
                        resultado.estado_actual
                        || resultado.seguimiento
                            ?.estado_resultante
                        || ''
                    ).trim();


                if (
                    estadoSeguimiento.reporte
                ) {

                    estadoSeguimiento
                        .reporte
                        .estado_actual =
                        nuevoEstado;

                }


                /* =================================================
                   ACTUALIZAR LISTADO
                ================================================= */

                if (
                    estadoSeguimiento.filaActual
                    && nuevoEstado
                ) {

                    actualizarEstadoReporte(
                        estadoSeguimiento.filaActual,
                        nuevoEstado
                    );

                }


                /* =================================================
                   VOLVER A CONSULTAR
                ================================================= */

                const datosActualizados =
                    await consultarSeguimientos(
                        idReporte
                    );


                if (
                    !datosActualizados
                    || datosActualizados.success !== true
                ) {

                    throw new Error(
                        datosActualizados?.message
                        || 'El seguimiento fue registrado, pero no fue posible actualizar la información del modal.'
                    );
                }


                estadoSeguimiento.reporte =
                    datosActualizados.reporte
                    || estadoSeguimiento.reporte;


                estadoSeguimiento.sancionActual =
                    normalizarSancion(
                        datosActualizados.sancion
                    );


                estadoSeguimiento.seguimientos =
                    normalizarSeguimientos(
                        datosActualizados.seguimientos
                    );


                /* =================================================
                   ACTUALIZAR HEADER
                ================================================= */

                asignarTexto(
                    modal,
                    '#seguimiento-estado-actual',
                    estadoSeguimiento.reporte
                        ?.estado_actual
                    || nuevoEstado
                );


                cargarSancionActual(
                    modal,
                    estadoSeguimiento.sancionActual
                );


                /* =================================================
                   ACTUALIZAR HISTORIAL
                ================================================= */

                cargarHistorialSeguimiento(
                    modal,
                    estadoSeguimiento.seguimientos
                );


                /* =================================================
                   LIMPIAR FORMULARIO
                ================================================= */

                prepararFormularioSeguimiento(
                    formulario,
                    estadoSeguimiento.reporte
                        ?.estado_actual
                    || nuevoEstado
                );


                actualizarCampoOtroSancion(
                    modal
                );


                /* =================================================
                   ACTUALIZAR FILTROS
                ================================================= */

                actualizarListadoRelacionado();


                /* =================================================
                   EVENTO GENERAL
                ================================================= */

                document.dispatchEvent(
                    new CustomEvent(
                        'seguimientoReporteActualizado',
                        {
                            detail: {

                                idReporte,

                                estado:
                                    nuevoEstado,

                                sancion:
                                    estadoSeguimiento
                                        .sancionActual,

                                sancionModificada:
                                    resultado
                                        .sancion_modificada
                                    === true,

                                seguimiento:
                                    resultado.seguimiento
                                    || null,

                            },
                        }
                    )
                );


            } catch (error) {

                console.error(
                    'Error registrando seguimiento:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible registrar el seguimiento.'
                );


            } finally {

                if (botonGuardar) {

                    botonGuardar.disabled =
                        false;


                    botonGuardar.innerHTML =
                        textoOriginal;

                }

            }

        }
    );

}


/* =========================================================
   SANCIÓN - INICIALIZAR
========================================================= */

function inicializarSancionSeguimiento(
    modal
) {

    const select =
        modal.querySelector(
            '#seguimiento-sancion'
        );


    if (!select) {
        return;
    }


    select.addEventListener(
        'change',
        () => {

            actualizarCampoOtroSancion(
                modal
            );

        }
    );


    actualizarCampoOtroSancion(
        modal
    );

}


/* =========================================================
   SANCIÓN - CAMPO OTRO
========================================================= */

function actualizarCampoOtroSancion(
    modal
) {

    const select =
        modal.querySelector(
            '#seguimiento-sancion'
        );


    const contenedor =
        modal.querySelector(
            '#seguimiento-campo-sancion-otro'
        );


    const input =
        modal.querySelector(
            '#seguimiento-sancion-otro'
        );


    if (
        !select
        || !contenedor
        || !input
    ) {
        return;
    }


    const mostrar =
        select.value === 'Otro';


    if (mostrar) {

        contenedor.hidden =
            false;


        /*
         * Igual que en Editar:
         * evitamos que CSS con display interfiera
         * con el atributo hidden.
         */

        contenedor.style
            .removeProperty(
                'display'
            );


        input.disabled =
            false;


        input.required =
            true;


        return;
    }


    contenedor.hidden =
        true;


    contenedor.style
        .setProperty(
            'display',
            'none',
            'important'
        );


    input.disabled =
        true;


    input.required =
        false;


    input.value =
        '';

}


/* =========================================================
   SANCIÓN - VALIDAR
========================================================= */

function validarSancionSeguimiento(
    modal
) {

    const select =
        modal.querySelector(
            '#seguimiento-sancion'
        );


    const inputOtro =
        modal.querySelector(
            '#seguimiento-sancion-otro'
        );


    if (!select) {
        return true;
    }


    const tipo =
        String(
            select.value
            || ''
        ).trim();


    if (tipo !== 'Otro') {
        return true;
    }


    const descripcion =
        String(
            inputOtro?.value
            || ''
        ).trim();


    if (!descripcion) {

        window.alert(
            'Debes especificar la sanción disciplinaria.'
        );


        inputOtro?.focus();


        return false;
    }


    if (descripcion.length > 255) {

        window.alert(
            'La descripción de la sanción no puede exceder 255 caracteres.'
        );


        inputOtro?.focus();


        return false;
    }


    return true;

}


/* =========================================================
   SANCIÓN - OBTENER SELECCIONADA
========================================================= */

function obtenerSancionSeleccionada(
    modal
) {

    const tipo =
        String(
            modal.querySelector(
                '#seguimiento-sancion'
            )?.value
            || ''
        ).trim();


    const descripcionOtro =
        tipo === 'Otro'
            ? String(
                modal.querySelector(
                    '#seguimiento-sancion-otro'
                )?.value
                || ''
            ).trim()
            : '';


    return {

        tipo,

        descripcion_otro:
            descripcionOtro,

        texto:
            tipo === 'Otro'
                ? descripcionOtro
                : tipo,

    };

}


/* =========================================================
   SANCIÓN - NORMALIZAR
========================================================= */

function normalizarSancion(
    sancion
) {

    if (
        !sancion
        || typeof sancion !== 'object'
    ) {
        return null;
    }


    const tipo =
        String(
            sancion.tipo
            || ''
        ).trim();


    if (!tipo) {
        return null;
    }


    const descripcionOtro =
        String(
            sancion.descripcion_otro
            || ''
        ).trim();


    let texto =
        String(
            sancion.texto
            || ''
        ).trim();


    if (!texto) {

        texto =
            tipo === 'Otro'
                ? descripcionOtro
                : tipo;

    }


    return {

        id_sancion:
            Number(
                sancion.id_sancion
                || 0
            ),

        tipo,

        descripcion_otro:
            descripcionOtro,

        texto,

        origen:
            String(
                sancion.origen
                || ''
            ).trim(),

        id_seguimiento:
            sancion.id_seguimiento
                ? Number(
                    sancion.id_seguimiento
                )
                : null,

        actualizada_desde_seguimiento:
            sancion.actualizada_desde_seguimiento
            === true,

        fecha_actualizacion:
            String(
                sancion.fecha_actualizacion
                || ''
            ).trim(),

        es_actual:
            sancion.es_actual === true
            || Number(
                sancion.es_actual
                || 0
            ) === 1,

    };

}


/* =========================================================
   SANCIÓN - COMPARAR
========================================================= */

function existeCambioRealSancion(
    sancionActual,
    sancionSeleccionada
) {

    /*
     * Vacío significa "Sin cambio".
     */

    if (
        !sancionSeleccionada
        || !sancionSeleccionada.tipo
    ) {
        return false;
    }


    /*
     * No había sanción y ahora sí.
     */

    if (!sancionActual) {
        return true;
    }


    if (
        sancionActual.tipo
        !== sancionSeleccionada.tipo
    ) {
        return true;
    }


    if (
        sancionSeleccionada.tipo === 'Otro'
    ) {

        return normalizarTextoComparacion(
            sancionActual.descripcion_otro
        ) !== normalizarTextoComparacion(
            sancionSeleccionada.descripcion_otro
        );

    }


    return false;

}


/* =========================================================
   NORMALIZAR TEXTO PARA COMPARACIÓN
========================================================= */

function normalizarTextoComparacion(
    valor
) {

    return String(
        valor
        || ''
    )
        .trim()
        .replace(
            /\s+/g,
            ' '
        )
        .toLocaleLowerCase(
            'es-MX'
        );

}


/* =========================================================
   SANCIÓN - TEXTO
========================================================= */

function obtenerTextoSancion(
    sancion
) {

    if (
        !sancion
        || !sancion.tipo
    ) {
        return 'Sin sanción registrada';
    }


    if (
        sancion.tipo === 'Otro'
    ) {

        return String(
            sancion.descripcion_otro
            || sancion.texto
            || 'Otra sanción'
        ).trim();

    }


    return String(
        sancion.tipo
    ).trim();

}


/* =========================================================
   SANCIÓN - CARGAR ACTUAL
========================================================= */

function cargarSancionActual(
    modal,
    sancion
) {

    const elemento =
        modal.querySelector(
            '#seguimiento-sancion-actual'
        );


    const origen =
        modal.querySelector(
            '#seguimiento-sancion-origen'
        );


    if (elemento) {

        elemento.textContent =
            obtenerTextoSancion(
                sancion
            );

    }


    if (!origen) {
        return;
    }


    origen.hidden =
        true;


    origen.style
        .setProperty(
            'display',
            'none',
            'important'
        );


    origen.textContent =
        '';


    if (
        !sancion
        || sancion.origen !== 'seguimiento'
    ) {
        return;
    }


    let texto =
        'Actualizada desde seguimiento';


    if (
        sancion.fecha_actualizacion
    ) {

        texto +=
            ` el ${formatearFecha(
                sancion.fecha_actualizacion
            )}`;

    }


    origen.textContent =
        texto;


    origen.hidden =
        false;


    origen.style
        .removeProperty(
            'display'
        );

}


/* =========================================================
   CONSULTAR SEGUIMIENTOS
========================================================= */

async function consultarSeguimientos(
    idReporte
) {

    const baseUrl =
        obtenerBaseUrl();


    const url =
        new URL(
            `asuntos-internos/reportes/seguimientos/${idReporte}`,
            baseUrl
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
            }
        );


    const datos =
        await obtenerJsonRespuesta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            datos?.message
            || 'No fue posible consultar el seguimiento.'
        );

    }


    return datos;

}


/* =========================================================
   REGISTRAR SEGUIMIENTO
========================================================= */

async function registrarSeguimiento(
    idReporte,
    datos
) {

    const baseUrl =
        obtenerBaseUrl();


    const url =
        new URL(
            `asuntos-internos/reportes/seguimientos/${idReporte}`,
            baseUrl
        );


    const respuesta =
        await fetch(
            url.toString(),
            {
                method:
                    'POST',

                headers: {
                    Accept:
                        'application/json',
                },

                credentials:
                    'same-origin',

                body:
                    datos,
            }
        );


    const resultado =
        await obtenerJsonRespuesta(
            respuesta
        );


    if (!respuesta.ok) {

        throw new Error(
            resultado?.message
            || 'No fue posible registrar el seguimiento.'
        );

    }


    return resultado;

}


/* =========================================================
   JSON DE RESPUESTA
========================================================= */

async function obtenerJsonRespuesta(
    respuesta
) {

    try {

        return await respuesta.json();

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );

    }

}


/* =========================================================
   NORMALIZAR HISTORIAL
========================================================= */

function normalizarSeguimientos(
    seguimientos
) {

    if (
        !Array.isArray(
            seguimientos
        )
    ) {
        return [];
    }


    return seguimientos.map(
        (seguimiento) => ({

            id_seguimiento:
                Number(
                    seguimiento.id_seguimiento
                    || 0
                ),

            fecha:
                String(
                    seguimiento.fecha
                    || ''
                ).trim(),

            tipo:
                String(
                    seguimiento.tipo
                    || ''
                ).trim(),

            estado:
                String(
                    seguimiento.estado_resultante
                    || seguimiento.estado
                    || ''
                ).trim(),

            observaciones:
                String(
                    seguimiento.observaciones
                    || ''
                ).trim(),

            created_by:
                Number(
                    seguimiento.created_by
                    || 0
                ),

            created_at:
                String(
                    seguimiento.created_at
                    || ''
                ).trim(),

            sancion:
                normalizarSancion(
                    seguimiento.sancion
                ),

        })
    );

}


/* =========================================================
   CARGAR DATOS DEL REPORTE
========================================================= */

function cargarDatosSeguimiento(
    modal,
    formulario,
    reporte,
    sancionActual
) {

    const folio =
        String(
            reporte.folio
            || ''
        ).trim();


    const expediente =
        String(
            reporte.expediente
            || ''
        ).trim();


    const estado =
        String(
            reporte.estado_actual
            || 'Pendiente'
        ).trim();


    /* =====================================================
       TÍTULO
    ===================================================== */

    const titulo =
        modal.querySelector(
            '#modal-seguimiento-titulo'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Seguimiento ${folio}`
                : 'Seguimiento';

    }


    /* =====================================================
       DATOS
    ===================================================== */

    asignarTexto(
        modal,
        '#seguimiento-folio',
        folio
    );


    asignarTexto(
        modal,
        '#seguimiento-expediente',
        expediente
    );


    asignarTexto(
        modal,
        '#seguimiento-estado-actual',
        estado
    );


    cargarSancionActual(
        modal,
        sancionActual
    );


    /* =====================================================
       FORMULARIO
    ===================================================== */

    prepararFormularioSeguimiento(
        formulario,
        estado
    );


    actualizarCampoOtroSancion(
        modal
    );

}


/* =========================================================
   PREPARAR FORMULARIO
========================================================= */

function prepararFormularioSeguimiento(
    formulario,
    estadoActual
) {

    formulario.reset();


    const fecha =
        formulario.querySelector(
            '#seguimiento-fecha'
        );


    const estado =
        formulario.querySelector(
            '#seguimiento-estado'
        );


    const idEdicion =
        formulario.querySelector(
            '#seguimiento-id-edicion'
        );


    if (idEdicion) {

        idEdicion.value =
            '';

    }


    if (fecha) {

        fecha.value =
            obtenerFechaActual();

    }


    if (estado) {

        const opcionExiste =
            Array.from(
                estado.options
            ).some(
                (opcion) =>
                    opcion.value
                    === estadoActual
            );


        estado.value =
            opcionExiste
                ? estadoActual
                : '';

    }

}


/* =========================================================
   CARGAR HISTORIAL
========================================================= */

function cargarHistorialSeguimiento(
    modal,
    historial
) {

    const lista =
        modal.querySelector(
            '#seguimiento-historial-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML =
        '';


    if (
        !Array.isArray(historial)
        || historial.length === 0
    ) {

        const vacio =
            document.createElement(
                'div'
            );


        vacio.className =
            'seguimiento-historial__vacio';


        const titulo =
            document.createElement(
                'strong'
            );


        titulo.textContent =
            'Sin seguimientos registrados';


        const descripcion =
            document.createElement(
                'span'
            );


        descripcion.textContent =
            'Los movimientos del reporte aparecerán aquí.';


        vacio.appendChild(
            titulo
        );


        vacio.appendChild(
            descripcion
        );


        lista.appendChild(
            vacio
        );


        return;
    }


    historial.forEach(
        (seguimiento) => {

            const item =
                crearMovimientoHistorial(
                    seguimiento
                );


            lista.appendChild(
                item
            );

        }
    );

}


/* =========================================================
   CREAR MOVIMIENTO
========================================================= */

function crearMovimientoHistorial(
    seguimiento
) {

    const item =
        document.createElement(
            'article'
        );


    item.className =
        'seguimiento-historial__item';


    item.dataset.idSeguimiento =
        String(
            seguimiento.id_seguimiento
            || ''
        );


    /* =====================================================
       HEADER
    ===================================================== */

    const header =
        document.createElement(
            'div'
        );


    header.className =
        'seguimiento-historial__item-header';


    const tipo =
        document.createElement(
            'strong'
        );


    tipo.className =
        'seguimiento-historial__tipo';


    tipo.textContent =
        seguimiento.tipo
        || 'Seguimiento';


    const fecha =
        document.createElement(
            'span'
        );


    fecha.className =
        'seguimiento-historial__fecha';


    fecha.textContent =
        formatearFecha(
            seguimiento.fecha
        );


    header.appendChild(
        tipo
    );


    header.appendChild(
        fecha
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    const estado =
        document.createElement(
            'span'
        );


    estado.className =
        `seguimiento-historial__estado ${obtenerClaseEstado(
            seguimiento.estado
        )}`;


    estado.textContent =
        seguimiento.estado
        || 'Pendiente';


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    const observaciones =
        document.createElement(
            'p'
        );


    observaciones.className =
        'seguimiento-historial__observaciones';


    observaciones.textContent =
        seguimiento.observaciones
        || 'Sin observaciones.';


    /* =====================================================
       ARMAR
    ===================================================== */

    item.appendChild(
        header
    );


    item.appendChild(
        estado
    );


    item.appendChild(
        observaciones
    );


    /* =====================================================
       CAMBIO DE SANCIÓN
    ===================================================== */

    if (
        seguimiento.sancion
        && seguimiento.sancion.tipo
    ) {

        const cambioSancion =
            document.createElement(
                'div'
            );


        cambioSancion.className =
            'seguimiento-historial__sancion';


        const etiqueta =
            document.createElement(
                'strong'
            );


        etiqueta.textContent =
            'Cambio de sanción disciplinaria';


        const valor =
            document.createElement(
                'span'
            );


        valor.textContent =
            obtenerTextoSancion(
                seguimiento.sancion
            );


        cambioSancion.appendChild(
            etiqueta
        );


        cambioSancion.appendChild(
            valor
        );


        item.appendChild(
            cambioSancion
        );

    }


    /* =====================================================
       FECHA DE REGISTRO
    ===================================================== */

    if (
        seguimiento.created_at
    ) {

        const metadata =
            document.createElement(
                'small'
            );


        metadata.className =
            'seguimiento-historial__metadata';


        metadata.textContent =
            `Registrado: ${formatearFechaHora(
                seguimiento.created_at
            )}`;


        item.appendChild(
            metadata
        );

    }


    return item;

}


/* =========================================================
   ACTUALIZAR ESTADO DEL LISTADO
========================================================= */

function actualizarEstadoReporte(
    fila,
    estado
) {

    if (!fila) {
        return;
    }


    const celdas =
        fila.querySelectorAll(
            'td'
        );


    if (
        celdas.length < 8
    ) {
        return;
    }


    const celdaEstado =
        celdas[7];


    celdaEstado.innerHTML =
        '';


    const etiqueta =
        document.createElement(
            'span'
        );


    etiqueta.className =
        `reportes-tabla__estado ${obtenerClaseEstado(
            estado
        )}`;


    etiqueta.textContent =
        estado
        || 'Pendiente';


    celdaEstado.appendChild(
        etiqueta
    );

}


/* =========================================================
   ACTUALIZAR FILTROS
========================================================= */

function actualizarListadoRelacionado() {

    const busqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


    if (!busqueda) {
        return;
    }


    busqueda.dispatchEvent(
        new Event(
            'input',
            {
                bubbles: true,
            }
        )
    );

}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModalSeguimiento(
    modal
) {

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

}


/* =========================================================
   CERRAR MODAL
========================================================= */

function cerrarModalSeguimiento(
    modal
) {

    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(
            elementoActivo
        )
    ) {

        elementoActivo.blur();

    }


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

}


/* =========================================================
   LIMPIAR ESTADO
========================================================= */

function limpiarEstadoSeguimiento() {

    estadoSeguimiento.idReporte =
        0;


    estadoSeguimiento.filaActual =
        null;


    estadoSeguimiento.reporte =
        null;


    estadoSeguimiento.sancionActual =
        null;


    estadoSeguimiento.seguimientos =
        [];

}


/* =========================================================
   FECHA ACTUAL
========================================================= */

function obtenerFechaActual() {

    const fecha =
        new Date();


    const anio =
        fecha.getFullYear();


    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(
            2,
            '0'
        );


    const dia =
        String(
            fecha.getDate()
        ).padStart(
            2,
            '0'
        );


    return `${anio}-${mes}-${dia}`;

}


/* =========================================================
   FORMATEAR FECHA
========================================================= */

function formatearFecha(
    fecha
) {

    const valor =
        String(
            fecha
            || ''
        ).trim();


    if (!valor) {
        return '—';
    }


    const coincidencia =
        valor.match(
            /^(\d{4})-(\d{2})-(\d{2})/
        );


    if (!coincidencia) {
        return valor;
    }


    return `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]}`;

}


/* =========================================================
   FORMATEAR FECHA / HORA
========================================================= */

function formatearFechaHora(
    valor
) {

    const texto =
        String(
            valor
            || ''
        ).trim();


    if (!texto) {
        return '—';
    }


    const coincidencia =
        texto.match(
            /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/
        );


    if (!coincidencia) {
        return texto;
    }


    return `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]} ${coincidencia[4]}:${coincidencia[5]}`;

}


/* =========================================================
   CLASE DEL ESTADO
========================================================= */

function obtenerClaseEstado(
    estado
) {

    switch (
        String(
            estado
            || ''
        ).trim()
    ) {

        case 'Finalizado':

            return 'estado--finalizado';


        case 'En proceso':

            return 'estado--proceso';


        default:

            return 'estado--pendiente';

    }

}


/* =========================================================
   ASIGNAR TEXTO
========================================================= */

function asignarTexto(
    modal,
    selector,
    valor
) {

    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    const texto =
        String(
            valor
            ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';

}


/* =========================================================
   BASE URL
========================================================= */

function obtenerBaseUrl() {

    return (
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`
    );

}