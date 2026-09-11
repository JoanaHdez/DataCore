/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   UTILIDADES DE FOLIO
========================================================= */


/* =========================================================
   PREFIJO
========================================================= */

export function obtenerPrefijoFolio(
    folio
) {

    const texto =
        String(
            folio || ''
        ).trim();


    if (!texto) {
        return 'QJ';
    }


    const partes =
        texto.split(
            '-'
        );


    return partes.length > 1
        ? partes[0]
        : 'QJ';
}


/* =========================================================
   NÚMERO
========================================================= */

export function obtenerNumeroFolio(
    folio
) {

    const texto =
        String(
            folio || ''
        ).trim();


    if (!texto) {
        return '';
    }


    const partes =
        texto.split(
            '-'
        );


    return partes.length > 1
        ? partes
            .slice(1)
            .join('-')
        : texto;
}