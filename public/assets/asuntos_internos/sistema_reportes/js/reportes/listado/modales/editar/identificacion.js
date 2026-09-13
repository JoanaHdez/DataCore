/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   IDENTIFICACIÓN DEL REGISTRO
========================================================= */

import {
    asignarValorEditar,
} from './utilidades.js';


/* =========================================================
   CARGAR IDENTIFICACIÓN
========================================================= */

export function cargarIdentificacionEditar(
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
       FOLIO IP
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio-ip',
        reporte.folio_ip
    );


    /* =====================================================
       FECHA DE QUEJA
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-fecha-queja',
        reporte.fecha_queja
    );


    /* =====================================================
       FECHA DE ACUERDO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-fecha-acuerdo',
        reporte.fecha_acuerdo
    );


    /* =====================================================
       EXPEDIENTE
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-expediente',
        reporte.expediente
    );


    /* =====================================================
       NOMENCLATURA
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-nomenclatura',
        reporte.nomenclatura
    );


    /* =====================================================
       NÚMERO DE OFICIO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-no-oficio',
        reporte.no_oficio
    );
}