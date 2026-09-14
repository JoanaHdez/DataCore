/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   DIRECCIÓN PARA NOTIFICACIÓN
========================================================= */

import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR DIRECCIÓN PARA NOTIFICACIÓN
========================================================= */

export function cargarDireccionNotificacionDetalle(
    modal,
    direccion
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       SIN DIRECCIÓN REGISTRADA
    ===================================================== */

    if (
        !direccion
        || typeof direccion !== 'object'
    ) {

        limpiarDireccionNotificacionDetalle(
            modal
        );


        return;
    }


    /* =====================================================
       PERTENECE A NEZAHUALCÓYOTL
    ===================================================== */

    let perteneceNeza =
        '—';


    if (
        direccion.pertenece_neza !== null
        && direccion.pertenece_neza !== undefined
        && String(
            direccion.pertenece_neza
        ).trim() !== ''
    ) {

        perteneceNeza =
            Number(
                direccion.pertenece_neza
            ) === 1
                ? 'Sí'
                : 'No';
    }


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-pertenece-neza',
        perteneceNeza
    );


    /* =====================================================
       DIRECCIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-calle',
        direccion.calle
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-numero',
        direccion.numero_exterior
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-colonia',
        direccion.colonia
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-entre-calle',
        direccion.entre_calle
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-y-calle',
        direccion.y_calle
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-municipio',
        direccion.municipio
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-estado',
        direccion.estado
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-sector',
        direccion.sector
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-cuadrante',
        direccion.cuadrante
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-id-cuadra',
        direccion.id_cuadra
    );


    /* =====================================================
       COORDENADAS
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-latitud',
        direccion.latitud
    );


    asignarTextoDetalle(
        modal,
        '#detalle-notificacion-longitud',
        direccion.longitud
    );
}


/* =========================================================
   LIMPIAR
========================================================= */

function limpiarDireccionNotificacionDetalle(
    modal
) {

    const campos = [

        '#detalle-notificacion-pertenece-neza',
        '#detalle-notificacion-calle',
        '#detalle-notificacion-numero',
        '#detalle-notificacion-colonia',
        '#detalle-notificacion-entre-calle',
        '#detalle-notificacion-y-calle',
        '#detalle-notificacion-municipio',
        '#detalle-notificacion-estado',
        '#detalle-notificacion-sector',
        '#detalle-notificacion-cuadrante',
        '#detalle-notificacion-id-cuadra',
        '#detalle-notificacion-latitud',
        '#detalle-notificacion-longitud',

    ];


    campos.forEach(
        (selector) => {

            asignarTextoDetalle(
                modal,
                selector,
                ''
            );

        }
    );
}