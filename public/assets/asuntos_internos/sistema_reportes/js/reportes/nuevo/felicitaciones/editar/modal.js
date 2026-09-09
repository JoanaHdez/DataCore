/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   MODAL
========================================================= */


/* =========================================================
   ABRIR MODAL
========================================================= */

export function abrirModalEditar(
    modal
) {

    modal.hidden =
        false;


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );
}


/* =========================================================
   CERRAR MODAL
========================================================= */

export function cerrarModalEditar(
    modal
) {

    const activo =
        document.activeElement;


    if (
        activo
        && modal.contains(
            activo
        )
    ) {

        activo.blur();
    }


    modal.hidden =
        true;


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.classList.remove(
        'modal-abierto'
    );
}