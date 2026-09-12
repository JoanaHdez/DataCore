/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - DATOS DEL QUEJOSO
========================================================= */

import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR DATOS DEL QUEJOSO
========================================================= */

export function cargarQuejosoDetalle(
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
       QUEJA ANÓNIMA
    ===================================================== */

    const esAnonima =
        convertirBooleano(
            reporte.anonimo
            ?? reporte.es_anonimo
            ?? reporte.queja_anonima
            ?? false
        );


    asignarTextoDetalle(
        modal,
        '#detalle-anonimo',
        esAnonima
            ? 'Sí'
            : 'No'
    );


    /* =====================================================
       NÚMERO ANÓNIMO
    ===================================================== */

    const numeroAnonimo =
        String(
            reporte.numero_anonimo
            ?? reporte.no_numerico
            ?? ''
        ).trim();


    const contenedorNumeroAnonimo =
        modal.querySelector(
            '#detalle-numero-anonimo-contenedor'
        );


    if (contenedorNumeroAnonimo) {

        contenedorNumeroAnonimo.hidden =
            !esAnonima;
    }


    asignarTextoDetalle(
        modal,
        '#detalle-numero-anonimo',
        numeroAnonimo
    );


    /* =====================================================
       DATOS PERSONALES
    ===================================================== */

    asignarTextoDetalle(
        modal,
        '#detalle-quejoso',
        reporte.nombre_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-edad',
        reporte.edad_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-genero',
        reporte.genero_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-telefono',
        reporte.telefono_quejoso
    );


    asignarTextoDetalle(
        modal,
        '#detalle-correo',
        reporte.correo_quejoso
    );


    /* =====================================================
       CANALIZACIÓN
    ===================================================== */

    const canalizacion =
        String(
            reporte.canalizacion_area
            ?? ''
        ).trim();


    asignarTextoDetalle(
        modal,
        '#detalle-canalizacion',
        canalizacion
            || 'Sin canalización'
    );


    /* =====================================================
       CANALIZACIÓN - OTRO
    ===================================================== */

    const canalizacionOtro =
        String(
            reporte.canalizacion_otro
            ?? ''
        ).trim();


    const esOtro =
        canalizacion
            .toUpperCase()
        === 'OTRO';


    const contenedorOtro =
        modal.querySelector(
            '#detalle-canalizacion-otro-contenedor'
        );


    if (contenedorOtro) {

        contenedorOtro.hidden =
            !esOtro;
    }


    asignarTextoDetalle(
        modal,
        '#detalle-canalizacion-otro',
        canalizacionOtro
    );
}


/* =========================================================
   CONVERTIR VALOR A BOOLEANO
========================================================= */

function convertirBooleano(
    valor
) {

    if (
        valor === true
        || valor === 1
        || valor === '1'
    ) {
        return true;
    }


    const texto =
        String(
            valor
            ?? ''
        )
            .trim()
            .toUpperCase();


    return (
        texto === 'SI'
        || texto === 'SÍ'
        || texto === 'TRUE'
    );
}