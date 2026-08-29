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


document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarSeguimientoReporte();

    }
);


/* =========================================================
   ESTADO DEL MÓDULO
========================================================= */

const estadoSeguimiento = {

    idReporte:
        0,

    filaActual:
        null,

    reporte:
        null,

    seguimientos:
        [],

};


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

                /* =================================================
                   CONSULTAR BD
                ================================================= */

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


                /* =================================================
                   ESTADO
                ================================================= */

                estadoSeguimiento.idReporte =
                    idReporte;


                estadoSeguimiento.filaActual =
                    fila;


                estadoSeguimiento.reporte =
                    datos.reporte;


                estadoSeguimiento.seguimientos =
                    normalizarSeguimientos(
                        datos.seguimientos
                    );


                /* =================================================
                   CARGAR MODAL
                ================================================= */

                cargarDatosSeguimiento(
                    modal,
                    formulario,
                    datos.reporte
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
               VALIDACIÓN NATIVA
            ================================================= */

            if (
                !formulario.checkValidity()
            ) {

                formulario.reportValidity();

                return;
            }


            /* =================================================
               DATOS
            ================================================= */

            const datos =
                new FormData(
                    formulario
                );


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
                   ACTUALIZAR ESTADO DEL REPORTE
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
                   ACTUALIZAR HEADER
                ================================================= */

                asignarTexto(
                    modal,
                    '#seguimiento-estado-actual',
                    nuevoEstado
                );


                /* =================================================
                   VOLVER A CONSULTAR HISTORIAL
                ================================================= */

                const datosActualizados =
                    await consultarSeguimientos(
                        idReporte
                    );


                estadoSeguimiento.reporte =
                    datosActualizados.reporte
                    || estadoSeguimiento.reporte;


                estadoSeguimiento.seguimientos =
                    normalizarSeguimientos(
                        datosActualizados.seguimientos
                    );


                cargarHistorialSeguimiento(
                    modal,
                    estadoSeguimiento.seguimientos
                );


                /* =================================================
                   LIMPIAR FORMULARIO
                ================================================= */

                prepararFormularioSeguimiento(
                    formulario,
                    nuevoEstado
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

                                seguimiento:
                                    resultado.seguimiento
                                    || null,

                            },
                        }
                    )
                );


                /*
                 * Dejamos el modal abierto para que el usuario
                 * pueda ver inmediatamente el movimiento
                 * recién registrado en el historial.
                 *
                 * Si después prefieres que se cierre
                 * automáticamente, lo cambiamos.
                 */


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

            /*
             * La BD utiliza estado_resultante.
             *
             * Para el diseño actual seguimos utilizando
             * internamente la propiedad "estado".
             */
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

        })
    );

}


/* =========================================================
   CARGAR DATOS DEL REPORTE
========================================================= */

function cargarDatosSeguimiento(
    modal,
    formulario,
    reporte
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


    /* =====================================================
       FORMULARIO
    ===================================================== */

    prepararFormularioSeguimiento(
        formulario,
        estado
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
   CARGAR HISTORIAL REAL
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


    /* =====================================================
       SIN MOVIMIENTOS
    ===================================================== */

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


    /* =====================================================
       MOVIMIENTOS
    ===================================================== */

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
            header
        );


        item.appendChild(
            estado
        );


        item.appendChild(
            observaciones
        );


        item.appendChild(
            metadata
        );


        return item;

    }


    item.appendChild(
        header
    );


    item.appendChild(
        estado
    );


    item.appendChild(
        observaciones
    );


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