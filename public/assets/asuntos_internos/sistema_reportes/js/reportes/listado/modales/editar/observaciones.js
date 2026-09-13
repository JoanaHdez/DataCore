/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   OBSERVACIONES
========================================================= */

import {
    asignarValorEditar,
} from './utilidades.js';


/* =========================================================
   CARGAR OBSERVACIONES
========================================================= */

export function cargarObservacionesEditar(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    asignarValorEditar(
        modal,
        '#editar-observaciones',
        reporte.observaciones
    );
}


/* =========================================================
   LIMPIAR OBSERVACIONES
========================================================= */

export function limpiarObservacionesEditar(
    modal
) {

    if (!modal) {
        return;
    }


    asignarValorEditar(
        modal,
        '#editar-observaciones',
        ''
    );
}