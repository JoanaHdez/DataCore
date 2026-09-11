/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   DETALLE DE QUEJAS
   UTILIDADES DE TEXTO
========================================================= */


/* =========================================================
   MAYÚSCULAS
========================================================= */

export function mayusculas(
    valor
) {

    return String(
        valor ?? ''
    )
        .trim()
        .toUpperCase();
}


/* =========================================================
   ASIGNAR TEXTO EN EL MODAL
========================================================= */

export function asignarTextoDetalle(
    modal,
    selector,
    valor
) {

    if (!modal) {
        return;
    }


    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    const texto =
        String(
            valor ?? ''
        ).trim();


    elemento.textContent =
        texto || '—';
}


/* =========================================================
   ESCAPAR HTML
========================================================= */

export function escaparHtmlDetalle(
    valor
) {

    return String(
        valor ?? ''
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