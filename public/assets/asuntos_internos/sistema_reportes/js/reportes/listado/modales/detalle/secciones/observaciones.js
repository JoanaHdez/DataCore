/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - OBSERVACIONES
========================================================= */

import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR OBSERVACIONES
========================================================= */

export function cargarObservacionesDetalle(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    asignarTextoDetalle(
        modal,
        '#detalle-observaciones',
        reporte.observaciones
    );
}