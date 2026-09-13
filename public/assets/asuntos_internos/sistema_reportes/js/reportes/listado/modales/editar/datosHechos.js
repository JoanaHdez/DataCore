/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   DATOS DE LOS HECHOS
========================================================= */

import {
    asignarValorEditar,
} from './utilidades.js';


/* =========================================================
   CARGAR DATOS DE LOS HECHOS
========================================================= */

export function cargarDatosHechosEditar(
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

    asignarValorEditar(
        modal,
        '#editar-fecha-hechos',
        reporte.fecha_hechos
    );


    /* =====================================================
       HORA DE LOS HECHOS
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-hora-hechos',
        reporte.hora_hechos
    );


    /* =====================================================
       DESCRIPCIÓN DE LOS HECHOS
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-descripcion',
        reporte.descripcion
    );
}