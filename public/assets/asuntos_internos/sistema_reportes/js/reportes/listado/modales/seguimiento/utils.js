/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Utilidades
========================================================= */


/* =========================================================
   ASIGNAR VALOR
========================================================= */

export function asignarValor(
    contenedor,
    selector,
    valor
) {

    const campo =
        contenedor.querySelector(
            selector
        );


    if (!campo) {
        return;
    }


    campo.value =
        valor
        ?? '';
}


/* =========================================================
   ASIGNAR TEXTO
========================================================= */

export function asignarTexto(
    modal,
    selector,
    valor
) {

    const elemento =
        modal.querySelector(
            selector
        );


    if (!elemento) {
        return;
    }


    const texto =
        String(
            valor
            ?? ''
        ).trim();


    elemento.textContent =
        texto
        || '—';
}


/* =========================================================
   FECHA ACTUAL
========================================================= */

export function obtenerFechaActual() {

    const fecha =
        new Date();


    const anio =
        fecha.getFullYear();


    const mes =
        String(
            fecha.getMonth() + 1
        ).padStart(
            2,
            '0'
        );


    const dia =
        String(
            fecha.getDate()
        ).padStart(
            2,
            '0'
        );


    return `${anio}-${mes}-${dia}`;
}


/* =========================================================
   FORMATEAR FECHA
========================================================= */

export function formatearFecha(
    fecha
) {

    const valor =
        String(
            fecha
            || ''
        ).trim();


    if (!valor) {
        return '—';
    }


    const coincidencia =
        valor.match(
            /^(\d{4})-(\d{2})-(\d{2})/
        );


    if (!coincidencia) {
        return valor;
    }


    return `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]}`;
}


/* =========================================================
   FORMATEAR FECHA / HORA
========================================================= */

export function formatearFechaHora(
    valor
) {

    const texto =
        String(
            valor
            || ''
        ).trim();


    if (!texto) {
        return '—';
    }


    const coincidencia =
        texto.match(
            /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/
        );


    if (!coincidencia) {
        return texto;
    }


    return `${coincidencia[3]}/${coincidencia[2]}/${coincidencia[1]} ${coincidencia[4]}:${coincidencia[5]}`;
}


/* =========================================================
   CLASE DEL ESTADO
========================================================= */

export function obtenerClaseEstado(
    estado
) {

    switch (
        String(
            estado
            || ''
        ).trim()
    ) {

        case 'Finalizado':

            return 'estado--finalizado';


        case 'En proceso':

            return 'estado--proceso';


        default:

            return 'estado--pendiente';
    }
}