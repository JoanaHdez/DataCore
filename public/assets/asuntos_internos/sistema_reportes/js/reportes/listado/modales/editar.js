document.addEventListener('DOMContentLoaded', () => {
    inicializarEditarReporte();
});


function inicializarEditarReporte() {

    const modal = document.querySelector(
        '#modal-editar-reporte'
    );

    const formulario = document.querySelector(
        '#form-editar-reporte'
    );

    if (!modal || !formulario) {
        return;
    }

    let filaActual = null;


    /*
     * Abrir modal desde el botón Editar
     */
    document.addEventListener('click', (evento) => {

        const boton = evento.target.closest(
            '[data-accion="editar"]'
        );

        if (!boton) {
            return;
        }

        const fila = boton.closest('tr');

        if (!fila) {
            return;
        }

        filaActual = fila;

        cargarDatosFormulario(
            modal,
            fila
        );

        abrirModalEditar(modal);
    });


    /*
     * Cerrar modal
     */
    modal.addEventListener('click', (evento) => {

        const botonCerrar = evento.target.closest(
            '[data-cerrar-modal-editar]'
        );

        if (!botonCerrar) {
            return;
        }

        cerrarModalEditar(modal);

        filaActual = null;
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
            cerrarModalEditar(modal);

            filaActual = null;
        }

    });


    /*
     * Guardar cambios
     */
    formulario.addEventListener('submit', (evento) => {

        evento.preventDefault();

        if (!filaActual) {
            return;
        }

        guardarCambiosEnFila(
            filaActual,
            formulario
        );

        cerrarModalEditar(modal);

        filaActual = null;
    });

}


/**
 * Carga en el formulario los datos
 * de la fila seleccionada.
 */
function cargarDatosFormulario(
    modal,
    fila
) {

    const celdas = fila.querySelectorAll('td');

    if (celdas.length < 8) {
        return;
    }

    const folio =
        celdas[0].textContent.trim();

    const fechaQueja =
        convertirFechaInput(
            celdas[1].textContent.trim()
        );

    const expediente =
        celdas[2].textContent.trim();

    const clasificacion =
        celdas[3].textContent.trim();

    const quejoso =
        celdas[4].textContent.trim();

    const area =
        celdas[5].textContent.trim();

    const turno =
        celdas[6].textContent.trim();

    const resolucion =
        celdas[7].textContent.trim();


    asignarValor(
        '#editar-folio',
        folio
    );

    asignarValor(
        '#editar-fecha-queja',
        fechaQueja
    );

    asignarValor(
        '#editar-expediente',
        expediente
    );

    asignarValor(
        '#editar-clasificacion',
        clasificacion
    );

    asignarValor(
        '#editar-quejoso',
        quejoso
    );

    asignarValor(
        '#editar-area',
        area
    );

    asignarValor(
        '#editar-turno',
        turno
    );

    asignarValor(
        '#editar-resolucion',
        resolucion
    );


    /*
     * Actualizamos el título.
     */
    const titulo = modal.querySelector(
        '#modal-editar-titulo'
    );

    if (titulo) {
        titulo.textContent =
            `Editar ${folio}`;
    }
}


/**
 * Abre el modal.
 */
function abrirModalEditar(modal) {

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
function cerrarModalEditar(modal) {

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
 * Actualiza temporalmente la fila
 * con los valores del formulario.
 */
function guardarCambiosEnFila(
    fila,
    formulario
) {

    const celdas = fila.querySelectorAll('td');

    if (celdas.length < 8) {
        return;
    }

    const datos =
        new FormData(formulario);

    const folio =
        datos.get('folio')?.trim() || '';

    const fechaQueja =
        datos.get('fecha_queja') || '';

    const expediente =
        datos.get('expediente')?.trim() || '';

    const clasificacion =
        datos.get('clasificacion')?.trim() || '';

    const quejoso =
        datos.get('quejoso')?.trim() || '';

    const area =
        datos.get('area')?.trim() || '';

    const turno =
        datos.get('turno') || '';

    const resolucion =
        datos.get('resolucion') || '';


    celdas[0].innerHTML =
        `<strong>${escaparHTML(folio)}</strong>`;

    celdas[1].textContent =
        formatearFechaTabla(fechaQueja);

    celdas[2].textContent =
        expediente;

    celdas[3].textContent =
        clasificacion;

    celdas[4].textContent =
        quejoso;

    celdas[5].textContent =
        area;

    celdas[6].textContent =
        turno;


    /*
     * Resolución con su etiqueta visual.
     */
    celdas[7].innerHTML = '';

    const estado =
        document.createElement('span');

    estado.className =
        `reportes-tabla__estado ${obtenerClaseEstado(
            resolucion
        )}`;

    estado.textContent =
        resolucion || 'Pendiente';

    celdas[7].appendChild(estado);
}


/**
 * Convierte dd/mm/aaaa a aaaa-mm-dd
 * para utilizarlo en input[type="date"].
 */
function convertirFechaInput(fecha) {

    if (!fecha) {
        return '';
    }

    const partes =
        fecha.split('/');

    if (partes.length !== 3) {
        return '';
    }

    const [dia, mes, anio] =
        partes;

    return `${anio}-${mes}-${dia}`;
}


/**
 * Convierte aaaa-mm-dd a dd/mm/aaaa.
 */
function formatearFechaTabla(fecha) {

    if (!fecha) {
        return '';
    }

    const partes =
        fecha.split('-');

    if (partes.length !== 3) {
        return fecha;
    }

    const [anio, mes, dia] =
        partes;

    return `${dia}/${mes}/${anio}`;
}


/**
 * Devuelve la clase visual correspondiente
 * a la resolución.
 */
function obtenerClaseEstado(resolucion) {

    switch (resolucion) {

        case 'Finalizado':
            return 'estado--finalizado';

        case 'En proceso':
            return 'estado--proceso';

        default:
            return 'estado--pendiente';
    }
}


/**
 * Asigna un valor a un campo.
 */
function asignarValor(
    selector,
    valor
) {

    const elemento =
        document.querySelector(selector);

    if (!elemento) {
        return;
    }

    elemento.value =
        valor || '';
}


/**
 * Evita insertar HTML desde valores
 * utilizados mediante innerHTML.
 */
function escaparHTML(valor) {

    const elemento =
        document.createElement('div');

    elemento.textContent =
        valor ?? '';

    return elemento.innerHTML;
}