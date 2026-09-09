/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   UTILIDADES
========================================================= */


/* =========================================================
   ESCAPAR HTML
========================================================= */

export function escaparHtml(
    valor
) {

    return String(
        valor
        ?? ''
    )
        .replaceAll(
            '&',
            '&amp;'
        )
        .replaceAll(
            '<',
            '&lt;'
        )
        .replaceAll(
            '>',
            '&gt;'
        )
        .replaceAll(
            '"',
            '&quot;'
        )
        .replaceAll(
            "'",
            '&#039;'
        );
}


/* =========================================================
   OBTENER INICIAL DEL PRIMER APELLIDO
========================================================= */

export function obtenerInicialApellido(
    nombreCompleto
) {

    const nombre =
        String(
            nombreCompleto
            || ''
        ).trim();


    if (
        nombre === ''
    ) {
        return '?';
    }


    const partes =
        nombre
            .split(/\s+/)
            .filter(Boolean);


    if (
        partes.length === 0
    ) {
        return '?';
    }


    return partes[0]
        .charAt(0)
        .toUpperCase();
}


/* =========================================================
   OBTENER FOTO DEL PERSONAL
========================================================= */

export function obtenerFotoPersonal(
    persona
) {

    return String(
        persona?.foto_url
        || persona?.foto
        || persona?.fotografia
        || persona?.imagen
        || ''
    ).trim();
}