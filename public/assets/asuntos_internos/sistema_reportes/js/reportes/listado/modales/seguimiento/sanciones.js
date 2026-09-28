/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Sanciones
========================================================= */

import {
    formatearFecha,
} from './utils.js';

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


export function actualizarCampoOtroSancion(
    modal
) {

    void modal;
}


export function validarSancionSeguimiento() {

    return true;
}


export function obtenerSancionSeleccionada(
    modal
) {

    const valor =
        String(
            modal.querySelector(
                '#seguimiento-sancion'
            )?.value
            || ''
        ).trim();


    const opcion =
        Array.from(
            modal.querySelectorAll(
                '[data-seguimiento-sancion-opcion]'
            )
        ).find(
            (elemento) =>
                String(
                    elemento.dataset.valor
                    || ''
                ).trim() === valor
        );


    const tipo =
        String(
            opcion?.dataset?.nombre
            || opcion?.querySelector(
                'strong'
            )?.textContent
            || ''
        ).trim();


    const idSancionSeguimiento =
        Number(
            valor
            || 0
        );


    return {

        id_sancion_seguimiento:
            idSancionSeguimiento,

        tipo,

        descripcion_otro:
            '',

        texto:
            tipo,
    };
}


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


    const idSancionSeguimiento =
        Number(
            sancion.id_sancion_seguimiento
            || 0
        );


    if (
        !tipo
        && idSancionSeguimiento <= 0
    ) {
        return null;
    }


    const descripcionOtro =
        String(
            sancion.descripcion_otro
            || ''
        ).trim();


    const texto =
        String(
            sancion.texto
            || tipo
            || descripcionOtro
            || ''
        ).trim();


    return {

        id_sancion:
            Number(
                sancion.id_sancion
                || 0
            ),

        id_sancion_seguimiento:
            idSancionSeguimiento,

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


export function existeCambioRealSancion(
    sancionActual,
    sancionNueva
) {

    if (
        !sancionNueva
        || (
            !sancionNueva.id_sancion_seguimiento
            && !sancionNueva.tipo
        )
    ) {
        return false;
    }


    if (!sancionActual) {
        return true;
    }


    const idActual =
        Number(
            sancionActual.id_sancion_seguimiento
            || 0
        );


    const idNuevo =
        Number(
            sancionNueva.id_sancion_seguimiento
            || 0
        );


    if (
        idActual > 0
        || idNuevo > 0
    ) {

        return idActual !== idNuevo;
    }


    return String(
        sancionActual.tipo
        || ''
    ).trim() !== String(
        sancionNueva.tipo
        || ''
    ).trim();
}


export function determinarAccionSancionEdicion(
    sancionAnterior,
    sancionNueva
) {

    const idAnterior =
        Number(
            sancionAnterior?.id_sancion_seguimiento
            || 0
        );


    const idNuevo =
        Number(
            sancionNueva?.id_sancion_seguimiento
            || 0
        );


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


    if (
        idAnterior <= 0
        && idNuevo <= 0
        && tipoAnterior === ''
        && tipoNuevo === ''
    ) {

        return 'sin_cambio';
    }


    if (
        (
            idAnterior > 0
            || tipoAnterior !== ''
        )
        && idNuevo <= 0
        && tipoNuevo === ''
    ) {

        return 'quitar';
    }


    if (
        idAnterior <= 0
        && idNuevo > 0
    ) {

        return 'cambiar';
    }


    if (
        idAnterior !== idNuevo
    ) {

        return 'cambiar';
    }


    if (
        idNuevo <= 0
        && tipoAnterior !== tipoNuevo
    ) {

        return 'cambiar';
    }


    return 'mantener';
}


export function obtenerTextoSancion(
    sancion
) {

    if (
        !sancion
        || (
            !sancion.tipo
            && !sancion.texto
        )
    ) {
        return 'Sin sanción registrada';
    }


    return String(
        sancion.texto
        || sancion.tipo
    ).trim();
}


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
