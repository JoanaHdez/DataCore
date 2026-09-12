/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - DATOS DE LOS HECHOS
========================================================= */

import {
    formatearFechaDetalle,
    formatearHoraDetalle,
} from '../utils/fechas.js';


import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR DATOS DE LOS HECHOS
========================================================= */

export function cargarDatosHechosDetalle(
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
       FECHA DE LOS HECHOS
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-fecha-hechos',
        formatearFechaDetalle(
            reporte.fecha_hechos
        )
    );


    /* =====================================================
       HORA DE LOS HECHOS
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-hora-hechos',
        formatearHoraDetalle(
            reporte.hora_hechos
        )
    );


    /* =====================================================
       DESCRIPCIÓN
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-descripcion',
        reporte.descripcion_hechos
    );
}