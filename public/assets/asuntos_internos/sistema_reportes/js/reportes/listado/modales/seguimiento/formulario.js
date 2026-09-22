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
   PREPARAR FORMULARIO
========================================================= */

export function prepararFormularioSeguimiento(
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

        const existe =
            Array.from(
                estado.options
            ).some(
                (opcion) =>
                    opcion.value
                    === estadoActual
            );


        estado.value =
            existe
                ? estadoActual
                : '';
    }
}


/* =========================================================
   INTERFAZ NUEVO / EDITAR
========================================================= */

export function actualizarInterfazModoFormulario(
    modal,
    editando,
    detalle = false
) {

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
       CAMPOS DEL FORMULARIO
    ===================================================== */

    const campos =
        modal.querySelectorAll(
            '#form-seguimiento-reporte input, '
            + '#form-seguimiento-reporte select, '
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
       ID EN EL FORMULARIO
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

    asignarValor(
        formulario,
        '#seguimiento-tipo',
        seguimiento.tipo
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-estado',
        seguimiento.estado
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
       FOLIO IP ACTUAL DEL REPORTE
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
       SANCIÓN DEL SEGUIMIENTO
    ===================================================== */

    const sancion =
        seguimiento.sancion;


    const selectSancion =
        modal.querySelector(
            '#seguimiento-sancion'
        );


    const inputOtro =
        modal.querySelector(
            '#seguimiento-sancion-otro'
        );


    if (selectSancion) {

        selectSancion.value =
            sancion?.tipo
            || '';

    }


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


    /* =====================================================
       ACTUALIZAR CAMPO OTRO
    ===================================================== */

    actualizarCampoOtroSancion(
        modal
    );


    /*
     * actualizarCampoOtroSancion() puede limpiar
     * #seguimiento-sancion-otro dependiendo de la opción.
     *
     * Si la sanción es Otro, restauramos el valor ya
     * normalizado a mayúsculas.
     */

    if (
        sancion?.tipo === 'Otro'
        && inputOtro
    ) {

        inputOtro.value =
            descripcionOtro;

    }


    /* =====================================================
       INTERFAZ MODO EDICIÓN
    ===================================================== */

    actualizarInterfazModoFormulario(
        modal,
        estadoSeguimiento.modoEdicion
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
        !Number.isInteger(idSeguimiento)
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
       DATOS
    ===================================================== */

    asignarValor(
        formulario,
        '#seguimiento-fecha',
        seguimiento.fecha
    );


    asignarValor(
        formulario,
        '#seguimiento-tipo',
        seguimiento.tipo
    );


    asignarValor(
        formulario,
        '#seguimiento-estado',
        seguimiento.estado
    );


    asignarValor(
        formulario,
        '#seguimiento-observaciones',
        seguimiento.observaciones
    );


    /* =====================================================
       FOLIO IP ACTUAL DEL REPORTE
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
       SANCIÓN DEL SEGUIMIENTO
    ===================================================== */

    const sancion =
        seguimiento.sancion;


    const selectSancion =
        modal.querySelector(
            '#seguimiento-sancion'
        );


    const inputOtro =
        modal.querySelector(
            '#seguimiento-sancion-otro'
        );


    if (selectSancion) {

        selectSancion.value =
            sancion?.tipo
            || '';
    }


    if (inputOtro) {

        inputOtro.value =
            sancion?.tipo === 'Otro'
                ? sancion.descripcion_otro
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
       INTERFAZ MODO DETALLE
    ===================================================== */

    actualizarInterfazModoFormulario(
        modal,
        false,
        true
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