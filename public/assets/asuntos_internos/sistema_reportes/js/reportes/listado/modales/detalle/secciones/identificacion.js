/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   SECCIÓN - IDENTIFICACIÓN
========================================================= */

import {
    formatearFechaDetalle,
} from '../utils/fechas.js';


import {
    asignarTextoDetalle,
} from '../utils/texto.js';


/* =========================================================
   CARGAR IDENTIFICACIÓN
========================================================= */

export function cargarIdentificacionDetalle(
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
        '#detalle-folio-ip',
        reporte.folio_ip
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-queja',
        formatearFechaDetalle(
            reporte.fecha_queja
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-fecha-acuerdo',
        formatearFechaDetalle(
            reporte.fecha_acuerdo
        )
    );


    asignarTextoDetalle(
        modal,
        '#detalle-expediente',
        reporte.expediente
    );


    asignarTextoDetalle(
        modal,
        '#detalle-nomenclatura',
        reporte.nomenclatura
    );


    asignarTextoDetalle(
        modal,
        '#detalle-no-oficio',
        reporte.numero_oficio
    );
}