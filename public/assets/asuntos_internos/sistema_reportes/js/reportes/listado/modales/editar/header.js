/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   HEADER
========================================================= */

import {
    construirFolio,
    asignarTextoEditar,
} from './utilidades.js';


/* =========================================================
   ACTUALIZAR HEADER
========================================================= */

export function actualizarHeaderEditar(
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

    const folio =
        reporte.folio
        || construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


    /* =====================================================
       NOMENCLATURA
    ===================================================== */

    asignarTextoEditar(
        modal,
        '#editar-meta-nomenclatura',
        reporte.nomenclatura
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    asignarTextoEditar(
        modal,
        '#editar-meta-estado',
        reporte.estado_actual
    );


    /* =====================================================
       TÍTULO
    ===================================================== */

    const titulo =
        modal.querySelector(
            '#modal-editar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Editar ${folio}`
                : 'Editar reporte';
    }
}