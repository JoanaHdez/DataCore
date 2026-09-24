import {
    asignarValor,
    obtenerFechaActual,
    asignarTexto,
} from './utils.js';

import {
    actualizarCampoOtroSancion,
} from './sanciones.js';

/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Formulario
========================================================= */

/* =========================================================
   INICIALIZAR MAYÚSCULAS
========================================================= */

export function inicializarMayusculasSeguimiento(
    formulario
) {

    if (!formulario) {
        return;
    }


    if (
        formulario.dataset
            .mayusculasInicializadas === '1'
    ) {
        return;
    }


    formulario.dataset
        .mayusculasInicializadas = '1';


    const camposMayusculas = [
        '#seguimiento-folio-ip',
        '#seguimiento-observaciones',
        '#seguimiento-sancion-otro',
    ];


    formulario.addEventListener(
        'input',
        (evento) => {

            const campo =
                evento.target;


            if (
                !(campo instanceof HTMLInputElement)
                && !(campo instanceof HTMLTextAreaElement)
            ) {
                return;
            }


            const aplicaMayusculas =
                camposMayusculas.some(
                    (selector) =>
                        campo.matches(
                            selector
                        )
                );


            if (!aplicaMayusculas) {
                return;
            }


            const valorActual =
                String(
                    campo.value
                    || ''
                );


            const valorMayusculas =
                valorActual
                    .toLocaleUpperCase(
                        'es-MX'
                    );


            if (
                valorActual === valorMayusculas
            ) {
                return;
            }


            const inicioSeleccion =
                campo.selectionStart;


            const finSeleccion =
                campo.selectionEnd;


            campo.value =
                valorMayusculas;


            if (
                inicioSeleccion !== null
                && finSeleccion !== null
            ) {

                try {

                    campo.setSelectionRange(
                        inicioSeleccion,
                        finSeleccion
                    );

                } catch (error) {
                    // No requiere acción.
                }

            }

        }
    );

}


/* =========================================================
   CATÁLOGOS PERSONALIZADOS DE SEGUIMIENTO
========================================================= */

export function inicializarCatalogosSeguimiento(
    modal
) {

    if (!modal) {
        return;
    }


    inicializarCatalogoSeguimiento(
        modal,
        {
            inputSelector:
                '#seguimiento-tipo',

            botonSelector:
                '#seguimiento-tipo-select',

            textoSelector:
                '#seguimiento-tipo-select-texto',

            resultadosSelector:
                '#seguimiento-tipo-resultados',

            opcionSelector:
                '[data-seguimiento-tipo-opcion]',

            textoVacio:
                'Selecciona',
        }
    );


    inicializarCatalogoSeguimiento(
        modal,
        {
            inputSelector:
                '#seguimiento-estado',

            botonSelector:
                '#seguimiento-estado-select',

            textoSelector:
                '#seguimiento-estado-select-texto',

            resultadosSelector:
                '#seguimiento-estado-resultados',

            opcionSelector:
                '[data-seguimiento-estado-opcion]',

            textoVacio:
                'Selecciona',
        }
    );


    inicializarCatalogoSeguimiento(
        modal,
        {
            inputSelector:
                '#seguimiento-sancion',

            botonSelector:
                '#seguimiento-sancion-select',

            textoSelector:
                '#seguimiento-sancion-select-texto',

            resultadosSelector:
                '#seguimiento-sancion-resultados',

            opcionSelector:
                '[data-seguimiento-sancion-opcion]',

            textoVacio:
                'Sin cambio',
        }
    );

}


/* =========================================================
   INICIALIZAR UN CATÁLOGO
========================================================= */

function inicializarCatalogoSeguimiento(
    modal,
    configuracion
) {

    const input =
        modal.querySelector(
            configuracion.inputSelector
        );


    const boton =
        modal.querySelector(
            configuracion.botonSelector
        );


    const texto =
        modal.querySelector(
            configuracion.textoSelector
        );


    const resultados =
        modal.querySelector(
            configuracion.resultadosSelector
        );


    const opciones =
        modal.querySelectorAll(
            configuracion.opcionSelector
        );


    if (
        !input
        || !boton
        || !texto
        || !resultados
    ) {
        return;
    }


    if (
        boton.dataset
            .catalogoInicializado === '1'
    ) {
        return;
    }


    boton.dataset
        .catalogoInicializado = '1';


    /* =====================================================
       ABRIR
    ===================================================== */

    function abrir() {

        if (boton.disabled) {
            return;
        }


        cerrarCatalogosSeguimiento(
            modal,
            resultados
        );


        resultados.hidden =
            false;


        boton.setAttribute(
            'aria-expanded',
            'true'
        );


        boton.classList.add(
            'seguimiento-select--activo'
        );
    }


    /* =====================================================
       CERRAR
    ===================================================== */

    function cerrar() {

        resultados.hidden =
            true;


        boton.setAttribute(
            'aria-expanded',
            'false'
        );


        boton.classList.remove(
            'seguimiento-select--activo'
        );
    }


    /* =====================================================
       BOTÓN
    ===================================================== */

    boton.addEventListener(
        'click',
        () => {

            if (resultados.hidden) {

                abrir();

            } else {

                cerrar();
            }
        }
    );


    /* =====================================================
       OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    const valor =
                        String(
                            opcion.dataset.valor
                            ?? ''
                        ).trim();


                    actualizarCatalogoSeguimiento(
                        modal,
                        configuracion.inputSelector,
                        configuracion.botonSelector,
                        configuracion.textoSelector,
                        configuracion.opcionSelector,
                        valor,
                        configuracion.textoVacio,
                        true
                    );


                    cerrar();
                }
            );
        }
    );


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                boton.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrar();
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
            ) {

                cerrar();
            }
        }
    );

}


/* =========================================================
   ACTUALIZAR VALOR Y TEXTO DEL CATÁLOGO
========================================================= */

function actualizarCatalogoSeguimiento(
    modal,
    inputSelector,
    botonSelector,
    textoSelector,
    opcionSelector,
    valor,
    textoVacio,
    dispararCambio = false
) {

    if (!modal) {
        return;
    }


    const input =
        modal.querySelector(
            inputSelector
        );


    const boton =
        modal.querySelector(
            botonSelector
        );


    const texto =
        modal.querySelector(
            textoSelector
        );


    const opciones =
        Array.from(
            modal.querySelectorAll(
                opcionSelector
            )
        );


    if (
        !input
        || !texto
    ) {
        return;
    }


    const valorNormalizado =
        String(
            valor
            ?? ''
        ).trim();


    input.value =
        valorNormalizado;


    const opcionEncontrada =
        opciones.find(
            (opcion) => {

                return String(
                    opcion.dataset.valor
                    ?? ''
                ).trim() === valorNormalizado;
            }
        );


    const tituloOpcion =
        opcionEncontrada
            ?.querySelector(
                'strong'
            )
            ?.textContent
            ?.trim()
        || '';


    texto.textContent =
        tituloOpcion
        || textoVacio;


    if (boton) {

        boton.setAttribute(
            'aria-expanded',
            'false'
        );


        boton.classList.remove(
            'seguimiento-select--activo'
        );
    }


    if (dispararCambio) {

        input.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles: true,
                }
            )
        );
    }

}


/* =========================================================
   CERRAR LOS DEMÁS CATÁLOGOS
========================================================= */

function cerrarCatalogosSeguimiento(
    modal,
    excepcion = null
) {

    if (!modal) {
        return;
    }


    const catalogos =
        modal.querySelectorAll(
            '.seguimiento-resultados'
        );


    catalogos.forEach(
        (catalogo) => {

            if (
                excepcion
                && catalogo === excepcion
            ) {
                return;
            }


            catalogo.hidden =
                true;
        }
    );


    const botones =
        modal.querySelectorAll(
            '.seguimiento-select'
        );


    botones.forEach(
        (boton) => {

            boton.setAttribute(
                'aria-expanded',
                'false'
            );


            boton.classList.remove(
                'seguimiento-select--activo'
            );
        }
    );

}


/* =========================================================
   PREPARAR FORMULARIO
========================================================= */

export function prepararFormularioSeguimiento(
    formulario,
    estadoActual
) {

    if (!formulario) {
        return;
    }


    formulario.reset();


    const modal =
        formulario.closest(
            '#modal-seguimiento-reporte'
        );


    const fecha =
        formulario.querySelector(
            '#seguimiento-fecha'
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


    if (!modal) {
        return;
    }


    /* =====================================================
       TIPO
    ===================================================== */

    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-tipo',
        '#seguimiento-tipo-select',
        '#seguimiento-tipo-select-texto',
        '[data-seguimiento-tipo-opcion]',
        '',
        'Selecciona'
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    const estadosPermitidos = [
        'Pendiente',
        'En proceso',
        'Finalizado',
    ];


    const estadoNormalizado =
        String(
            estadoActual
            || ''
        ).trim();


    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-estado',
        '#seguimiento-estado-select',
        '#seguimiento-estado-select-texto',
        '[data-seguimiento-estado-opcion]',
        estadosPermitidos.includes(
            estadoNormalizado
        )
            ? estadoNormalizado
            : '',
        'Selecciona'
    );


    /* =====================================================
       SANCIÓN
    ===================================================== */

    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-sancion',
        '#seguimiento-sancion-select',
        '#seguimiento-sancion-select-texto',
        '[data-seguimiento-sancion-opcion]',
        '',
        'Sin cambio'
    );


    cerrarCatalogosSeguimiento(
        modal
    );

}


/* =========================================================
   INTERFAZ NUEVO / EDITAR / DETALLE
========================================================= */

export function actualizarInterfazModoFormulario(
    modal,
    editando,
    detalle = false
) {

    if (!modal) {
        return;
    }


    const eyebrow =
        modal.querySelector(
            '#seguimiento-form-eyebrow'
        );


    const titulo =
        modal.querySelector(
            '#seguimiento-form-titulo'
        );


    const botonGuardar =
        modal.querySelector(
            '#seguimiento-boton-guardar'
        );


    const accionesEdicion =
        modal.querySelector(
            '#seguimiento-acciones-edicion'
        );


    /* =====================================================
       TEXTOS
    ===================================================== */

    if (eyebrow) {

        eyebrow.textContent =
            detalle
                ? 'Consulta de movimiento'
                : (
                    editando
                        ? 'Corrección de movimiento'
                        : 'Nuevo movimiento'
                );
    }


    if (titulo) {

        titulo.textContent =
            detalle
                ? 'Detalle del seguimiento'
                : (
                    editando
                        ? 'Editar seguimiento'
                        : 'Registrar seguimiento'
                );
    }


    /* =====================================================
       BOTÓN GUARDAR
    ===================================================== */

    if (botonGuardar) {

        if (detalle) {

            botonGuardar.hidden =
                true;


            botonGuardar.style
                .setProperty(
                    'display',
                    'none',
                    'important'
                );

        } else {

            botonGuardar.hidden =
                false;


            botonGuardar.style
                .removeProperty(
                    'display'
                );


            botonGuardar.textContent =
                editando
                    ? 'Guardar cambios'
                    : 'Registrar seguimiento';
        }
    }


    /* =====================================================
       ACCIONES DE EDICIÓN
    ===================================================== */

    if (accionesEdicion) {

        const mostrarAcciones =
            editando
            && !detalle;


        accionesEdicion.hidden =
            !mostrarAcciones;


        if (mostrarAcciones) {

            accionesEdicion.style
                .removeProperty(
                    'display'
                );

        } else {

            accionesEdicion.style
                .setProperty(
                    'display',
                    'none',
                    'important'
                );
        }
    }


    /* =====================================================
       INPUTS / TEXTAREA
    ===================================================== */

    const campos =
        modal.querySelectorAll(
            '#form-seguimiento-reporte input, '
            + '#form-seguimiento-reporte textarea'
        );


    campos.forEach(
        (campo) => {

            if (
                campo.type === 'hidden'
            ) {
                return;
            }


            campo.disabled =
                detalle;
        }
    );


    /* =====================================================
       SELECTORES PERSONALIZADOS
    ===================================================== */

    const selectores =
        modal.querySelectorAll(
            '.seguimiento-select'
        );


    selectores.forEach(
        (selector) => {

            selector.disabled =
                detalle;
        }
    );


    if (detalle) {

        cerrarCatalogosSeguimiento(
            modal
        );
    }

}

/* =========================================================
   CARGAR DATOS DEL SEGUIMIENTO
========================================================= */

export function cargarDatosSeguimiento(
    modal,
    formulario,
    reporte
) {

    /* =====================================================
       FOLIO
    ===================================================== */

    const folio =
        String(
            reporte.folio
            || ''
        ).trim();


    /* =====================================================
       NOMENCLATURA
    ===================================================== */

    const nomenclatura =
        String(
            reporte.nomenclatura
            || ''
        ).trim();


    /* =====================================================
       FOLIO IP
    ===================================================== */

    const folioIp =
        String(
            reporte.folio_ip
            || ''
        ).trim();


    /* =====================================================
       ESTADO
    ===================================================== */

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
       INFORMACIÓN SUPERIOR
    ===================================================== */

    asignarTexto(
        modal,
        '#seguimiento-folio',
        folio
    );


    asignarTexto(
        modal,
        '#seguimiento-nomenclatura',
        nomenclatura
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


    /* =====================================================
       FOLIO IP DEL REPORTE
    ===================================================== */

    const inputFolioIp =
        formulario.querySelector(
            '#seguimiento-folio-ip'
        );


    if (inputFolioIp) {

        inputFolioIp.value =
            folioIp;
    }


    /* =====================================================
       CAMPO OTRO DE SANCIÓN
    ===================================================== */

    actualizarCampoOtroSancion(
        modal
    );

}

/* =========================================================
   INICIAR EDICIÓN
========================================================= */

export function iniciarEdicionSeguimiento(
    modal,
    formulario,
    seguimiento,
    estadoSeguimiento
) {

    const idSeguimiento =
        Number(
            seguimiento?.id_seguimiento
            || 0
        );


    if (
        !Number.isInteger(
            idSeguimiento
        )
        || idSeguimiento <= 0
    ) {

        window.alert(
            'No fue posible identificar el seguimiento que deseas editar.'
        );


        return;
    }


    /* =====================================================
       ESTADO DE EDICIÓN
    ===================================================== */

    estadoSeguimiento.modoEdicion =
        true;


    estadoSeguimiento.modoDetalle =
        false;


    estadoSeguimiento.idSeguimientoEdicion =
        idSeguimiento;


    estadoSeguimiento.seguimientoEdicion =
        seguimiento;


    /* =====================================================
       ID
    ===================================================== */

    const inputId =
        formulario.querySelector(
            '#seguimiento-id-edicion'
        );


    if (inputId) {

        inputId.value =
            String(
                idSeguimiento
            );
    }


    /* =====================================================
       FECHA
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-fecha',
        seguimiento.fecha
    );


    /* =====================================================
       TIPO
    ===================================================== */

    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-tipo',
        '#seguimiento-tipo-select',
        '#seguimiento-tipo-select-texto',
        '[data-seguimiento-tipo-opcion]',
        seguimiento.tipo,
        'Selecciona'
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-estado',
        '#seguimiento-estado-select',
        '#seguimiento-estado-select-texto',
        '[data-seguimiento-estado-opcion]',
        seguimiento.estado,
        'Selecciona'
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-observaciones',
        String(
            seguimiento.observaciones
            || ''
        )
            .trim()
            .toLocaleUpperCase(
                'es-MX'
            )
    );


    /* =====================================================
       FOLIO IP
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-folio-ip',
        String(
            estadoSeguimiento
                .reporte
                ?.folio_ip
            || ''
        )
            .trim()
            .toLocaleUpperCase(
                'es-MX'
            )
    );


    /* =====================================================
       SANCIÓN
    ===================================================== */

    const sancion =
        seguimiento.sancion;


    const inputOtro =
        modal.querySelector(
            '#seguimiento-sancion-otro'
        );


    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-sancion',
        '#seguimiento-sancion-select',
        '#seguimiento-sancion-select-texto',
        '[data-seguimiento-sancion-opcion]',
        sancion?.tipo
        || '',
        'Sin cambio'
    );


    /* =====================================================
       SANCIÓN OTRO
    ===================================================== */

    const descripcionOtro =
        sancion?.tipo === 'Otro'
            ? String(
                sancion.descripcion_otro
                || ''
            )
                .trim()
                .toLocaleUpperCase(
                    'es-MX'
                )
            : '';


    if (inputOtro) {

        inputOtro.value =
            descripcionOtro;
    }


    actualizarCampoOtroSancion(
        modal
    );


    if (
        sancion?.tipo === 'Otro'
        && inputOtro
    ) {

        inputOtro.value =
            descripcionOtro;
    }


    /* =====================================================
       INTERFAZ
    ===================================================== */

    actualizarInterfazModoFormulario(
        modal,
        true,
        false
    );


    cerrarCatalogosSeguimiento(
        modal
    );


    /* =====================================================
       SUBIR AL FORMULARIO
    ===================================================== */

    const seccion =
        modal.querySelector(
            '.seguimiento-reporte__section'
        );


    if (seccion) {

        seccion.scrollIntoView({

            behavior:
                'smooth',

            block:
                'start',
        });
    }

}


/* =========================================================
   INICIALIZAR CANCELAR EDICIÓN
========================================================= */

export function inicializarCancelarEdicion(
    modal,
    formulario,
    estadoSeguimiento
) {

    const boton =
        modal.querySelector(
            '#seguimiento-cancelar-edicion'
        );


    if (!boton) {
        return;
    }


    boton.addEventListener(
        'click',
        () => {

            cancelarEdicionSeguimiento(
                modal,
                formulario,
                estadoSeguimiento
            );

        }
    );

}


/* =========================================================
   CANCELAR EDICIÓN
========================================================= */

export function cancelarEdicionSeguimiento(
    modal,
    formulario,
    estadoSeguimiento
) {

    /* =====================================================
       LIMPIAR MODO EDICIÓN
    ===================================================== */

    estadoSeguimiento.modoEdicion =
        false;


    estadoSeguimiento.idSeguimientoEdicion =
        0;


    estadoSeguimiento.seguimientoEdicion =
        null;


    /* =====================================================
       RESTAURAR FORMULARIO
    ===================================================== */

    prepararFormularioSeguimiento(
        formulario,
        estadoSeguimiento.reporte
            ?.estado_actual
        || 'Pendiente'
    );


    /* =====================================================
       SANCIÓN
    ===================================================== */

    actualizarCampoOtroSancion(
        modal
    );


    /* =====================================================
       INTERFAZ
    ===================================================== */

    actualizarInterfazModoFormulario(
        modal,
        estadoSeguimiento.modoEdicion
    );

}

/* =========================================================
   INICIAR DETALLE
========================================================= */

export function iniciarDetalleSeguimiento(
    modal,
    formulario,
    seguimiento,
    estadoSeguimiento
) {

    const idSeguimiento =
        Number(
            seguimiento?.id_seguimiento
            || 0
        );


    if (
        !Number.isInteger(
            idSeguimiento
        )
        || idSeguimiento <= 0
    ) {

        window.alert(
            'No fue posible identificar el seguimiento que deseas consultar.'
        );


        return;
    }


    /* =====================================================
       ESTADO DE DETALLE
    ===================================================== */

    estadoSeguimiento.modoEdicion =
        false;


    estadoSeguimiento.modoDetalle =
        true;


    estadoSeguimiento.idSeguimientoEdicion =
        0;


    estadoSeguimiento.seguimientoEdicion =
        null;


    /* =====================================================
       FECHA
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-fecha',
        seguimiento.fecha
    );


    /* =====================================================
       TIPO
    ===================================================== */

    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-tipo',
        '#seguimiento-tipo-select',
        '#seguimiento-tipo-select-texto',
        '[data-seguimiento-tipo-opcion]',
        seguimiento.tipo,
        'Selecciona'
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-estado',
        '#seguimiento-estado-select',
        '#seguimiento-estado-select-texto',
        '[data-seguimiento-estado-opcion]',
        seguimiento.estado,
        'Selecciona'
    );


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-observaciones',
        seguimiento.observaciones
    );


    /* =====================================================
       FOLIO IP
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-folio-ip',
        estadoSeguimiento
            .reporte
            ?.folio_ip
        || ''
    );


    /* =====================================================
       SANCIÓN
    ===================================================== */

    const sancion =
        seguimiento.sancion;


    const inputOtro =
        modal.querySelector(
            '#seguimiento-sancion-otro'
        );


    actualizarCatalogoSeguimiento(
        modal,
        '#seguimiento-sancion',
        '#seguimiento-sancion-select',
        '#seguimiento-sancion-select-texto',
        '[data-seguimiento-sancion-opcion]',
        sancion?.tipo
        || '',
        'Sin cambio'
    );


    if (inputOtro) {

        inputOtro.value =
            sancion?.tipo === 'Otro'
                ? sancion.descripcion_otro
                    || ''
                : '';
    }


    actualizarCampoOtroSancion(
        modal
    );


    if (
        sancion?.tipo === 'Otro'
        && inputOtro
    ) {

        inputOtro.value =
            sancion.descripcion_otro
            || '';
    }


    /* =====================================================
       INTERFAZ
    ===================================================== */

    actualizarInterfazModoFormulario(
        modal,
        false,
        true
    );


    cerrarCatalogosSeguimiento(
        modal
    );


    /* =====================================================
       SUBIR AL FORMULARIO
    ===================================================== */

    const seccion =
        modal.querySelector(
            '.seguimiento-reporte__section'
        );


    if (seccion) {

        seccion.scrollIntoView({

            behavior:
                'smooth',

            block:
                'start',
        });
    }

}