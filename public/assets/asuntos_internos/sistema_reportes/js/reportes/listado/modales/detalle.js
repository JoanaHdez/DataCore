document.addEventListener('DOMContentLoaded', () => {
    inicializarDetalleReporte();
});


function inicializarDetalleReporte() {

    const modal = document.querySelector(
        '#modal-detalle-reporte'
    );

    if (!modal) {
        return;
    }


    /*
     * Botones "Ver" de la tabla
     */
    document.addEventListener('click', (evento) => {

        const boton = evento.target.closest(
            '[data-accion="ver"]'
        );

        if (!boton) {
            return;
        }

        const fila = boton.closest('tr');

        if (!fila) {
            return;
        }

        abrirDetalleReporte(
            modal,
            fila
        );
    });


    /*
     * Botones para cerrar modal
     */
    modal.addEventListener('click', (evento) => {

        const cerrar = evento.target.closest(
            '[data-cerrar-modal]'
        );

        if (!cerrar) {
            return;
        }

        cerrarDetalleReporte(modal);
    });


    /*
     * Cerrar con Escape
     */
    document.addEventListener('keydown', (evento) => {

        if (
            evento.key === 'Escape'
            && modal.classList.contains(
                'modal-reporte--visible'
            )
        ) {
            cerrarDetalleReporte(modal);
        }

    });

}


/**
 * Abre el modal y carga la información
 * correspondiente a la fila seleccionada.
 */
function abrirDetalleReporte(
    modal,
    fila
) {

    const celdas = fila.querySelectorAll('td');

    if (celdas.length < 8) {
        return;
    }

    asignarTexto(
        '#detalle-folio',
        celdas[0].textContent
    );

    asignarTexto(
        '#detalle-fecha-queja',
        celdas[1].textContent
    );

    asignarTexto(
        '#detalle-expediente',
        celdas[2].textContent
    );

    asignarTexto(
        '#detalle-clasificacion',
        celdas[3].textContent
    );

    asignarTexto(
        '#detalle-quejoso',
        celdas[4].textContent
    );

    asignarTexto(
        '#detalle-area',
        celdas[5].textContent
    );

    asignarTexto(
        '#detalle-turno',
        celdas[6].textContent
    );

    asignarTexto(
        '#detalle-resolucion',
        celdas[7].textContent
    );


    /*
     * Título dinámico
     */
    const titulo = modal.querySelector(
        '#modal-detalle-titulo'
    );

    const folio =
        celdas[0].textContent.trim();

    if (titulo) {
        titulo.textContent =
            `Reporte ${folio}`;
    }


    /*
     * Mostrar modal
     */
    modal.classList.add(
        'modal-reporte--visible'
    );

    modal.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.classList.add(
        'modal-abierto'
    );
}


/**
 * Cierra el modal.
 */
function cerrarDetalleReporte(modal) {

    modal.classList.remove(
        'modal-reporte--visible'
    );

    modal.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.classList.remove(
        'modal-abierto'
    );
}


/**
 * Asigna texto a un elemento.
 */
function asignarTexto(
    selector,
    valor
) {

    const elemento =
        document.querySelector(selector);

    if (!elemento) {
        return;
    }

    elemento.textContent =
        valor?.trim() || '—';
}