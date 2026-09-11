/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - DATOS DEL REPORTE
========================================================= */

import {
    formatearFechaDetalle,
} from '../utils/fechas.js';


import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR DATOS DEL REPORTE
========================================================= */

export function cargarDatosReporteDetalle(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    const folio =
        String(
            reporte.folio
            || ''
        ).trim();


    asignarTextoDetalle(
        modal,
        '#detalle-folio',
        folio
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-registro',
        formatearFechaDetalle(
            reporte.fecha_registro
        )
    );
}