/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - UBICACIÓN DE LOS HECHOS
========================================================= */

import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR UBICACIÓN
========================================================= */

export function cargarUbicacionDetalle(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    /* =====================================================
       CALLE
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-calle',
        reporte.calle
    );


    /* =====================================================
       NÚMERO EXTERIOR
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-numero',
        reporte.numero_exterior
    );


    /* =====================================================
       COLONIA
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-colonia',
        reporte.colonia
    );


    /* =====================================================
       ENTRE CALLE
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-entre-calle',
        reporte.entre_calle
    );


    /* =====================================================
       Y CALLE
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-y-calle',
        reporte.y_calle
    );


    /* =====================================================
       MUNICIPIO
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-municipio',
        reporte.municipio
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-estado',
        reporte.estado
    );


    /* =====================================================
       SECTOR
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-sector',
        reporte.sector
    );


    /* =====================================================
       CUADRANTE
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-cuadrante',
        reporte.cuadrante
    );


    /* =====================================================
       ID DE CUADRA / CALLE
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-id-cuadra',
        reporte.id_cuadra
    );


    /* =====================================================
       LATITUD
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-latitud',
        reporte.latitud
    );


    /* =====================================================
       LONGITUD
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-longitud',
        reporte.longitud
    );
}