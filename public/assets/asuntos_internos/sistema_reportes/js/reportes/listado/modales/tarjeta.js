document.addEventListener('DOMContentLoaded', () => {
    inicializarTarjetaReporte();
});


function inicializarTarjetaReporte() {

    const modal = document.querySelector(
        '#modal-tarjeta-reporte'
    );

    if (!modal) {
        return;
    }


    /* =========================================================
       ABRIR TARJETA
    ========================================================= */

    document.addEventListener('click', (evento) => {

        const boton = evento.target.closest(
            '[data-accion="tarjeta"]'
        );

        if (!boton) {
            return;
        }

        const fila = boton.closest('tr');

        if (!fila) {
            return;
        }

        cargarDatosTarjeta(
            modal,
            fila
        );

        abrirModalTarjeta(
            modal
        );
    });


    /* =========================================================
       CERRAR TARJETA
    ========================================================= */

    modal.addEventListener('click', (evento) => {

        const cerrar = evento.target.closest(
            '[data-cerrar-modal-tarjeta]'
        );

        if (!cerrar) {
            return;
        }

        cerrarModalTarjeta(
            modal
        );
    });


    /* =========================================================
       CERRAR CON ESCAPE
    ========================================================= */

    document.addEventListener('keydown', (evento) => {

        if (
            evento.key === 'Escape'
            && modal.classList.contains(
                'modal-reporte--visible'
            )
        ) {
            cerrarModalTarjeta(
                modal
            );
        }
    });
}


/* =============================================================
   CARGAR DATOS
============================================================= */

function cargarDatosTarjeta(
    modal,
    fila
) {

    const celdas =
        fila.querySelectorAll('td');

    if (celdas.length < 8) {
        return;
    }


    const folio =
        celdas[0].textContent.trim();

    const fechaQueja =
        celdas[1].textContent.trim();

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


    asignarTextoTarjeta(
        '#tarjeta-folio',
        folio
    );

    asignarTextoTarjeta(
        '#tarjeta-fecha-queja',
        fechaQueja
    );

    asignarTextoTarjeta(
        '#tarjeta-expediente',
        expediente
    );

    asignarTextoTarjeta(
        '#tarjeta-clasificacion',
        clasificacion
    );

    asignarTextoTarjeta(
        '#tarjeta-quejoso',
        quejoso
    );

    asignarTextoTarjeta(
        '#tarjeta-area',
        area
    );

    asignarTextoTarjeta(
        '#tarjeta-turno',
        turno
    );

    asignarTextoTarjeta(
        '#tarjeta-resolucion',
        resolucion
    );


    /* ---------------------------------------------------------
       ÚLTIMO SEGUIMIENTO
    --------------------------------------------------------- */

    cargarUltimoSeguimientoTarjeta(
        fila
    );


    /* ---------------------------------------------------------
       TÍTULO DEL MODAL
    --------------------------------------------------------- */

    const titulo =
        modal.querySelector(
            '#modal-tarjeta-titulo'
        );

    if (titulo) {
        titulo.textContent =
            `Tarjeta ${folio}`;
    }
}


/* =============================================================
   ÚLTIMO SEGUIMIENTO
============================================================= */

function cargarUltimoSeguimientoTarjeta(
    fila
) {

    const contenedor =
        document.querySelector(
            '#tarjeta-ultimo-seguimiento'
        );

    if (!contenedor) {
        return;
    }


    const seguimientos =
        obtenerSeguimientosTarjeta(
            fila
        );


    contenedor.innerHTML = '';


    /* ---------------------------------------------------------
       SIN SEGUIMIENTOS
    --------------------------------------------------------- */

    if (seguimientos.length === 0) {

        const mensaje =
            document.createElement('span');

        mensaje.textContent =
            'Sin seguimientos registrados.';

        contenedor.appendChild(
            mensaje
        );

        return;
    }


    /* ---------------------------------------------------------
       ÚLTIMO MOVIMIENTO
    --------------------------------------------------------- */

   const ultimo =
    seguimientos[0];


    const encabezado =
        document.createElement('strong');

    encabezado.textContent =
        `${ultimo.tipo || 'Seguimiento'} · ${formatearFechaTarjeta(
            ultimo.fecha
        )}`;


    const estado =
        document.createElement('span');

    estado.textContent =
        `Estado: ${ultimo.estado || 'Pendiente'}`;


    const observaciones =
        document.createElement('p');

    observaciones.textContent =
        ultimo.observaciones || '—';


    contenedor.appendChild(
        encabezado
    );

    contenedor.appendChild(
        estado
    );

    contenedor.appendChild(
        observaciones
    );
}


/* =============================================================
   OBTENER SEGUIMIENTOS
============================================================= */

function obtenerSeguimientosTarjeta(
    fila
) {

    if (!fila?.dataset.seguimientos) {
        return [];
    }

    try {

        const seguimientos =
            JSON.parse(
                fila.dataset.seguimientos
            );

        return Array.isArray(seguimientos)
            ? seguimientos
            : [];

    } catch (error) {

        return [];
    }
}


/* =============================================================
   ABRIR MODAL
============================================================= */

function abrirModalTarjeta(
    modal
) {

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


/* =============================================================
   CERRAR MODAL
============================================================= */

function cerrarModalTarjeta(
    modal
) {

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


/* =============================================================
   FORMATEAR FECHA
   YYYY-MM-DD -> DD/MM/YYYY
============================================================= */

function formatearFechaTarjeta(
    fecha
) {

    if (!fecha) {
        return '—';
    }

    const partes =
        fecha.split('-');

    if (partes.length !== 3) {
        return fecha;
    }

    const [
        anio,
        mes,
        dia
    ] = partes;

    return `${dia}/${mes}/${anio}`;
}


/* =============================================================
   ASIGNAR TEXTO
============================================================= */

function asignarTextoTarjeta(
    selector,
    valor
) {

    const elemento =
        document.querySelector(
            selector
        );

    if (!elemento) {
        return;
    }

    elemento.textContent =
        valor?.trim()
        || '—';
}