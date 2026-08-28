/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Seguimiento
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarSeguimientoReporte();
});


/*
 * Historial temporal.
 *
 * Cuando conectemos la BD,
 * este Map se sustituirá por los
 * registros reales del backend.
 */
const seguimientosTemporales =
    new Map();


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


    if (!modal || !formulario) {
        return;
    }


    let filaActual = null;
    let folioActual = '';


    /* =====================================================
       ABRIR MODAL
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="seguimiento"]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest('tr');


            if (!fila) {
                return;
            }


            const celdas =
                fila.querySelectorAll('td');


            if (celdas.length < 8) {
                return;
            }


            filaActual =
                fila;


            folioActual =
                celdas[0]
                    .textContent
                    .trim();


            /*
             * Si la fila ya tiene historial
             * serializado, lo recuperamos.
             */
            restaurarSeguimientosDesdeFila(
                filaActual,
                folioActual
            );


            cargarDatosSeguimiento(
                modal,
                formulario,
                filaActual
            );


            cargarHistorialSeguimiento(
                modal,
                folioActual
            );


            abrirModalSeguimiento(
                modal
            );

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


            filaActual = null;
            folioActual = '';

        }
    );


    /* =====================================================
       CERRAR CON ESCAPE
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


                filaActual = null;
                folioActual = '';

            }

        }
    );


    /* =====================================================
       REGISTRAR SEGUIMIENTO
    ===================================================== */

    formulario.addEventListener(
        'submit',
        (evento) => {

            evento.preventDefault();


            if (
                !filaActual
                || !folioActual
            ) {
                return;
            }


            if (!formulario.checkValidity()) {

                formulario.reportValidity();

                return;
            }


            const datos =
                new FormData(
                    formulario
                );


            const seguimiento = {

                fecha:
                    obtenerDatoFormulario(
                        datos,
                        'fecha'
                    ),

                tipo:
                    obtenerDatoFormulario(
                        datos,
                        'tipo'
                    ),

                estado:
                    obtenerDatoFormulario(
                        datos,
                        'estado'
                    ),

                observaciones:
                    obtenerDatoFormulario(
                        datos,
                        'observaciones'
                    ),
            };


            /* =============================================
               GUARDAR EN MEMORIA
            ============================================== */

            guardarSeguimientoTemporal(
                folioActual,
                seguimiento
            );


            /*
             * También copiamos el historial a la fila,
             * para que Tarjeta pueda leerlo.
             */
            guardarSeguimientosEnFila(
                filaActual,
                folioActual
            );


            /* =============================================
               ACTUALIZAR ESTADO DE LA TABLA
            ============================================== */

            actualizarEstadoReporte(
                filaActual,
                seguimiento.estado
            );


            /* =============================================
               ACTUALIZAR HEADER DEL MODAL
            ============================================== */

            asignarTexto(
                modal,
                '#seguimiento-estado-actual',
                seguimiento.estado
            );


            /* =============================================
               ACTUALIZAR HISTORIAL
            ============================================== */

            cargarHistorialSeguimiento(
                modal,
                folioActual
            );


            /* =============================================
               ACTUALIZAR LISTADO
            ============================================== */

            actualizarListadoRelacionado();


            /* =============================================
               EVENTO GENERAL
            ============================================== */

            document.dispatchEvent(
                new CustomEvent(
                    'seguimientoReporteActualizado',
                    {
                        detail: {
                            folio:
                                folioActual,

                            estado:
                                seguimiento.estado,

                            seguimiento:
                                seguimiento,
                        },
                    }
                )
            );


            /* =============================================
               CERRAR AUTOMÁTICAMENTE
            ============================================== */

            cerrarModalSeguimiento(
                modal
            );


            filaActual = null;
            folioActual = '';

        }
    );

}


/* =========================================================
   CARGAR DATOS DEL REPORTE
========================================================= */

function cargarDatosSeguimiento(
    modal,
    formulario,
    fila
) {

    const celdas =
        fila.querySelectorAll('td');


    if (celdas.length < 8) {
        return;
    }


    const folio =
        celdas[0]
            .textContent
            .trim();


    const expediente =
        celdas[2]
            .textContent
            .trim();


    const estado =
        celdas[7]
            .textContent
            .trim();


    /* =====================================================
       TÍTULO
    ===================================================== */

    const titulo =
        modal.querySelector(
            '#modal-seguimiento-titulo'
        );


    if (titulo) {

        titulo.textContent =
            `Seguimiento ${folio}`;

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
       PREPARAR FORMULARIO
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
   GUARDAR SEGUIMIENTO TEMPORAL
========================================================= */

function guardarSeguimientoTemporal(
    folio,
    seguimiento
) {

    const historial =
        seguimientosTemporales.get(
            folio
        )
        || [];


    historial.unshift({
        ...seguimiento,

        registradoEn:
            new Date(),
    });


    seguimientosTemporales.set(
        folio,
        historial
    );

}


/* =========================================================
   RESTAURAR HISTORIAL DESDE FILA
========================================================= */

function restaurarSeguimientosDesdeFila(
    fila,
    folio
) {

    /*
     * Si ya existe en memoria,
     * no lo sobrescribimos.
     */
    if (
        seguimientosTemporales.has(
            folio
        )
    ) {
        return;
    }


    if (!fila?.dataset.seguimientos) {
        return;
    }


    try {

        const datos =
            JSON.parse(
                fila.dataset.seguimientos
            );


        if (!Array.isArray(datos)) {
            return;
        }


        seguimientosTemporales.set(
            folio,
            datos
        );

    } catch (error) {

        console.error(
            'No fue posible recuperar el historial temporal:',
            error
        );

    }

}


/* =========================================================
   GUARDAR HISTORIAL EN LA FILA
========================================================= */

function guardarSeguimientosEnFila(
    fila,
    folio
) {

    if (!fila) {
        return;
    }


    const historial =
        seguimientosTemporales.get(
            folio
        )
        || [];


    const datos =
        historial.map(
            (seguimiento) => ({
                fecha:
                    seguimiento.fecha
                    || '',

                tipo:
                    seguimiento.tipo
                    || '',

                estado:
                    seguimiento.estado
                    || '',

                observaciones:
                    seguimiento.observaciones
                    || '',
            })
        );


    fila.dataset.seguimientos =
        JSON.stringify(
            datos
        );

}


/* =========================================================
   CARGAR HISTORIAL
========================================================= */

function cargarHistorialSeguimiento(
    modal,
    folio
) {

    const lista =
        modal.querySelector(
            '#seguimiento-historial-lista'
        );


    if (!lista) {
        return;
    }


    const historial =
        seguimientosTemporales.get(
            folio
        )
        || [];


    lista.innerHTML = '';


    /* =====================================================
       SIN MOVIMIENTOS
    ===================================================== */

    if (!historial.length) {

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
   CREAR MOVIMIENTO DE HISTORIAL
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


    return item;

}


/* =========================================================
   ACTUALIZAR ESTADO DEL REPORTE
========================================================= */

function actualizarEstadoReporte(
    fila,
    estado
) {

    const celdas =
        fila.querySelectorAll('td');


    if (celdas.length < 8) {
        return;
    }


    const celdaEstado =
        celdas[7];


    celdaEstado.innerHTML = '';


    const etiqueta =
        document.createElement(
            'span'
        );


    etiqueta.className =
        `reportes-tabla__estado ${obtenerClaseEstado(
            estado
        )}`;


    etiqueta.textContent =
        estado;


    celdaEstado.appendChild(
        etiqueta
    );

}


/* =========================================================
   ACTUALIZAR FILTROS / RESUMEN / PAGINACIÓN
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

    if (!fecha) {
        return '—';
    }


    const partes =
        fecha.split('-');


    if (partes.length !== 3) {
        return fecha;
    }


    const [
        anio,
        mes,
        dia
    ] = partes;


    return `${dia}/${mes}/${anio}`;

}


/* =========================================================
   CLASE VISUAL DEL ESTADO
========================================================= */

function obtenerClaseEstado(
    estado
) {

    switch (estado) {

        case 'Finalizado':

            return 'estado--finalizado';


        case 'En proceso':

            return 'estado--proceso';


        default:

            return 'estado--pendiente';

    }

}


/* =========================================================
   OBTENER DATO DEL FORMULARIO
========================================================= */

function obtenerDatoFormulario(
    datos,
    nombre
) {

    const valor =
        datos.get(
            nombre
        );


    if (
        typeof valor
        !== 'string'
    ) {

        return '';

    }


    return valor.trim();

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
            valor ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';

}