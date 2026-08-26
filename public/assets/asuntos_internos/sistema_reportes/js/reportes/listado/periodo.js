document.addEventListener('DOMContentLoaded', () => {
    inicializarPeriodo();
});

function inicializarPeriodo() {
    const fechaInicio = document.querySelector('#fecha_inicio');
    const fechaFin = document.querySelector('#fecha_fin');
    const botonAplicar = document.querySelector('#btn-aplicar-periodo');

    if (!fechaInicio || !fechaFin || !botonAplicar) {
        return;
    }

    botonAplicar.addEventListener('click', () => {
        const inicio = fechaInicio.value;
        const fin = fechaFin.value;

        limpiarErroresPeriodo();

        if (!inicio && !fin) {
            mostrarErrorPeriodo(
                'Selecciona al menos una fecha para realizar la consulta.'
            );

            return;
        }

        if (inicio && fin && inicio > fin) {
            mostrarErrorPeriodo(
                'La fecha inicial no puede ser posterior a la fecha final.'
            );

            return;
        }

        const detalle = {
            fechaInicio: inicio,
            fechaFin: fin,
        };

        document.dispatchEvent(
            new CustomEvent(
                'periodoReportesAplicado',
                {
                    detail: detalle,
                }
            )
        );

        mostrarPeriodoSeleccionado(
            inicio,
            fin
        );
    });
}


function mostrarPeriodoSeleccionado(
    fechaInicio,
    fechaFin
) {
    const contenedor =
        document.querySelector('.reportes-periodo');

    if (!contenedor) {
        return;
    }

    let mensaje =
        contenedor.querySelector('[data-periodo-resultado]');

    if (!mensaje) {
        mensaje = document.createElement('div');

        mensaje.className =
            'reportes-periodo__resultado';

        mensaje.dataset.periodoResultado = '';

        const body =
            contenedor.querySelector(
                '.reportes-periodo__body'
            );

        body?.after(mensaje);
    }

    if (fechaInicio && fechaFin) {
        mensaje.textContent =
            `Periodo seleccionado: `
            + `${formatearFecha(fechaInicio)} al `
            + `${formatearFecha(fechaFin)}`;

        return;
    }

    if (fechaInicio) {
        mensaje.textContent =
            `Desde: ${formatearFecha(fechaInicio)}`;

        return;
    }

    mensaje.textContent =
        `Hasta: ${formatearFecha(fechaFin)}`;
}


function mostrarErrorPeriodo(mensaje) {
    const contenedor =
        document.querySelector('.reportes-periodo');

    if (!contenedor) {
        return;
    }

    let error =
        contenedor.querySelector('[data-periodo-error]');

    if (!error) {
        error = document.createElement('div');

        error.className =
            'reportes-periodo__error';

        error.dataset.periodoError = '';

        const body =
            contenedor.querySelector(
                '.reportes-periodo__body'
            );

        body?.after(error);
    }

    error.textContent = mensaje;
}


function limpiarErroresPeriodo() {
    const error =
        document.querySelector('[data-periodo-error]');

    error?.remove();
}


function formatearFecha(fecha) {
    if (!fecha) {
        return '';
    }

    const partes = fecha.split('-');

    if (partes.length !== 3) {
        return fecha;
    }

    const [anio, mes, dia] = partes;

    return `${dia}/${mes}/${anio}`;
}