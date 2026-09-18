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
       TIPO DE FOLIO
    ===================================================== */

    const selectTipoFolio =
        modal.querySelector(
            '#editar-tipo-folio'
        );


    const folioActual =
        String(
            reporte.folio
            || ''
        )
            .trim()
            .toUpperCase();


    let claveFolio =
        'QJ';


    if (
        folioActual.startsWith(
            'QJV-'
        )
    ) {

        claveFolio =
            'QJV';

    } else if (
        folioActual.startsWith(
            'QJF-'
        )
    ) {

        claveFolio =
            'QJF';

    } else if (
        folioActual.startsWith(
            'QJ-'
        )
    ) {

        claveFolio =
            'QJ';
    }


    if (selectTipoFolio) {

        selectTipoFolio.value =
            claveFolio;
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
       FOLIO IMP
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-folio-imp',
        reporte.folio_imp
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
        ?? reporte.numero_oficio
    );
}