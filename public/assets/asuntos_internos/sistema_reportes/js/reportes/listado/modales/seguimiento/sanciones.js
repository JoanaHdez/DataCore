/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Sanciones
========================================================= */

import {
    formatearFecha,
} from './utils.js';

/* =========================================================
   SANCIÓN - INICIALIZAR
========================================================= */

export function inicializarSancionSeguimiento(
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

export function actualizarCampoOtroSancion(
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

export function validarSancionSeguimiento(
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


    if (
        select.value !== 'Otro'
    ) {
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


    if (
        descripcion.length > 255
    ) {

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

export function obtenerSancionSeleccionada(
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

export function normalizarSancion(
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
   SANCIÓN - COMPARAR NUEVO SEGUIMIENTO
========================================================= */

export function existeCambioRealSancion(
    sancionActual,
    sancionNueva
) {

    if (
        !sancionNueva
        || !sancionNueva.tipo
    ) {
        return false;
    }


    if (!sancionActual) {
        return true;
    }


    if (
        sancionActual.tipo
        !== sancionNueva.tipo
    ) {
        return true;
    }


    if (
        sancionNueva.tipo === 'Otro'
    ) {

        return normalizarTextoComparacion(
            sancionActual.descripcion_otro
        ) !== normalizarTextoComparacion(
            sancionNueva.descripcion_otro
        );
    }


    return false;

}


/* =========================================================
   DETERMINAR ACCIÓN DE SANCIÓN EN EDICIÓN
========================================================= */

export function determinarAccionSancionEdicion(
    sancionAnterior,
    sancionNueva
) {

    const tipoAnterior =
        String(
            sancionAnterior?.tipo
            || ''
        ).trim();


    const tipoNuevo =
        String(
            sancionNueva?.tipo
            || ''
        ).trim();


    /* =====================================================
       NO TENÍA SANCIÓN Y SIGUE SIN TENER
    ===================================================== */

    if (
        tipoAnterior === ''
        && tipoNuevo === ''
    ) {

        return 'sin_cambio';
    }


    /* =====================================================
       TENÍA SANCIÓN Y SE QUITÓ
    ===================================================== */

    if (
        tipoAnterior !== ''
        && tipoNuevo === ''
    ) {

        return 'quitar';
    }


    /* =====================================================
       NO TENÍA SANCIÓN Y SE AGREGÓ
    ===================================================== */

    if (
        tipoAnterior === ''
        && tipoNuevo !== ''
    ) {

        return 'cambiar';
    }


    /* =====================================================
       CAMBIÓ EL TIPO
    ===================================================== */

    if (
        tipoAnterior !== tipoNuevo
    ) {

        return 'cambiar';
    }


    /* =====================================================
       AMBAS SON "OTRO"
    ===================================================== */

    if (
        tipoAnterior === 'Otro'
        && tipoNuevo === 'Otro'
    ) {

        const otroAnterior =
            normalizarTextoComparacion(
                sancionAnterior?.descripcion_otro
            );


        const otroNuevo =
            normalizarTextoComparacion(
                sancionNueva?.descripcion_otro
            );


        if (
            otroAnterior
            !== otroNuevo
        ) {

            return 'cambiar';
        }
    }


    /* =====================================================
       NO HUBO CAMBIO
    ===================================================== */

    return 'mantener';

}


/* =========================================================
   TEXTO SANCIÓN
========================================================= */

export function obtenerTextoSancion(
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
   SANCIÓN ACTUAL
========================================================= */

export function cargarSancionActual(
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
