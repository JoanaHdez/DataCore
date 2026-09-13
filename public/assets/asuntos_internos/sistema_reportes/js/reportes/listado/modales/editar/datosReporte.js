/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   DATOS DEL REPORTE
========================================================= */

import {
    asignarValorEditar,
} from './utilidades.js';


/* =========================================================
   CARGAR DATOS DEL REPORTE
========================================================= */

export function cargarDatosReporteEditar(
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
       FOLIO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio',
        reporte.folio
    );


    /* =====================================================
       FECHA DE REGISTRO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-fecha-registro',
        reporte.fecha_registro
    );
}


/* =========================================================
   LIMPIAR DATOS DEL REPORTE
========================================================= */

export function limpiarDatosReporteEditar(
    modal
) {

    if (!modal) {
        return;
    }


    asignarValorEditar(
        modal,
        '#editar-folio',
        ''
    );


    asignarValorEditar(
        modal,
        '#editar-fecha-registro',
        ''
    );
}