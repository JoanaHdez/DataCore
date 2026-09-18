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

    const inputParte =
        modal.querySelector(
            '#editar-nomenclatura-parte'
        );


    const inputCompleto =
        modal.querySelector(
            '#editar-nomenclatura'
        );


    const PREFIJO =
        'CGSC/CAI/QJ/';


    const nomenclaturaGuardada =
        String(
            reporte.nomenclatura
            || ''
        ).trim();


    let parteVariable =
        nomenclaturaGuardada;


    if (
        nomenclaturaGuardada.startsWith(
            PREFIJO
        )
    ) {

        parteVariable =
            nomenclaturaGuardada
                .substring(
                    PREFIJO.length
                )
                .trim();
    }


    if (inputParte) {

        inputParte.value =
            parteVariable;
    }


    if (inputCompleto) {

        inputCompleto.value =
            nomenclaturaGuardada;
    }


    /* =====================================================
       ACTUALIZAR NOMENCLATURA AL EDITAR
    ===================================================== */

    function actualizarNomenclatura() {

        if (
            !inputParte
            || !inputCompleto
        ) {
            return;
        }


        let parte =
            String(
                inputParte.value
                || ''
            ).trim();


        /* Evitar doble diagonal al inicio */

        parte =
            parte.replace(
                /^\/+/,
                ''
            );


        inputParte.value =
            parte;


        inputCompleto.value =
            parte !== ''
                ? `${PREFIJO}${parte}`
                : '';
    }


    /* =====================================================
       EVITAR LISTENERS DUPLICADOS
    ===================================================== */

    if (
        inputParte
        && inputParte.dataset
            .nomenclaturaInicializada
        !== '1'
    ) {

        inputParte.dataset
            .nomenclaturaInicializada =
            '1';


        inputParte.addEventListener(
            'input',
            actualizarNomenclatura
        );


        inputParte.addEventListener(
            'change',
            actualizarNomenclatura
        );
    }


    /* =====================================================
       NÚMERO DE OFICIO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-no-oficio',
        reporte.no_oficio
    );
}