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
    editando
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


    if (eyebrow) {

        eyebrow.textContent =
            editando
                ? 'Corrección de movimiento'
                : 'Nuevo movimiento';
    }


    if (titulo) {

        titulo.textContent =
            editando
                ? 'Editar seguimiento'
                : 'Registrar seguimiento';
    }


    if (botonGuardar) {

        botonGuardar.textContent =
            editando
                ? 'Guardar cambios'
                : 'Registrar seguimiento';
    }


    if (accionesEdicion) {

        accionesEdicion.hidden =
            !editando;


        if (editando) {

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
        !Number.isInteger(idSeguimiento)
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


    /*
     * actualizarCampoOtroSancion() puede limpiar el input
     * cuando la opción no es Otro.
     *
     * Si sí es Otro, aseguramos nuevamente el valor.
     */

    if (
        sancion?.tipo === 'Otro'
        && inputOtro
    ) {

        inputOtro.value =
            sancion.descripcion_otro
            || '';
    }


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