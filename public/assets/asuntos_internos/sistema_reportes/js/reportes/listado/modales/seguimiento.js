document.addEventListener('DOMContentLoaded', () => {
    inicializarSeguimientoReporte();
});


function inicializarSeguimientoReporte() {

    const modal = document.querySelector(
        '#modal-seguimiento-reporte'
    );

    const formulario = document.querySelector(
        '#form-seguimiento-reporte'
    );

    if (!modal || !formulario) {
        return;
    }

    let filaActual = null;


    /* =========================================================
       ABRIR MODAL
    ========================================================= */

    document.addEventListener('click', (evento) => {

        const boton = evento.target.closest(
            '[data-accion="seguimiento"]'
        );

        if (!boton) {
            return;
        }

        const fila = boton.closest('tr');

        if (!fila) {
            return;
        }

        filaActual = fila;

        cargarDatosSeguimiento(
            modal,
            formulario,
            fila
        );

        abrirModalSeguimiento(modal);
    });


    /* =========================================================
       CERRAR MODAL
    ========================================================= */

    modal.addEventListener('click', (evento) => {

        const cerrar = evento.target.closest(
            '[data-cerrar-modal-seguimiento]'
        );

        if (!cerrar) {
            return;
        }

        cerrarModalSeguimiento(
            modal,
            formulario
        );

        filaActual = null;
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
            cerrarModalSeguimiento(
                modal,
                formulario
            );

            filaActual = null;
        }
    });


    /* =========================================================
       REGISTRAR SEGUIMIENTO
    ========================================================= */

    formulario.addEventListener('submit', (evento) => {

        evento.preventDefault();

        if (!filaActual) {
            return;
        }

        const datos =
            new FormData(formulario);

        const fecha =
            datos.get('fecha') || '';

        const tipo =
            datos.get('tipo') || '';

        const estado =
            datos.get('estado') || '';

        const observaciones =
            datos.get('observaciones')?.trim() || '';


        if (
            !fecha
            || !tipo
            || !estado
            || !observaciones
        ) {
            return;
        }


        /* -----------------------------------------------------
           GUARDAR MOVIMIENTO TEMPORAL
        ----------------------------------------------------- */

        guardarSeguimientoTemporal(
            filaActual,
            {
                fecha,
                tipo,
                estado,
                observaciones,
            }
        );


        /* -----------------------------------------------------
           ACTUALIZAR ESTADO EN TABLA
        ----------------------------------------------------- */

        actualizarEstadoFila(
            filaActual,
            estado
        );


        /* -----------------------------------------------------
           ACTUALIZAR HISTORIAL
        ----------------------------------------------------- */

        renderizarHistorialSeguimiento(
            filaActual
        );


        cerrarModalSeguimiento(
            modal,
            formulario
        );

        filaActual = null;
    });

}


/* =============================================================
   CARGAR INFORMACIÓN DEL REPORTE
============================================================= */

function cargarDatosSeguimiento(
    modal,
    formulario,
    fila
) {

    const celdas =
        fila.querySelectorAll('td');

    if (celdas.length < 8) {
        return;
    }


    const folio =
        celdas[0].textContent.trim();

    const expediente =
        celdas[2].textContent.trim();

    const estado =
        celdas[7].textContent.trim();


    asignarTextoSeguimiento(
        '#seguimiento-folio',
        folio
    );

    asignarTextoSeguimiento(
        '#seguimiento-expediente',
        expediente
    );

    asignarTextoSeguimiento(
        '#seguimiento-estado-actual',
        estado
    );


    /* ---------------------------------------------------------
       LIMPIAR FORMULARIO
    --------------------------------------------------------- */

    formulario.reset();


    /* ---------------------------------------------------------
       FECHA ACTUAL
    --------------------------------------------------------- */

    const inputFecha =
        formulario.querySelector(
            '#seguimiento-fecha'
        );

    if (inputFecha) {
        inputFecha.value =
            obtenerFechaActual();
    }


    /* ---------------------------------------------------------
       ESTADO ACTUAL COMO VALOR INICIAL
    --------------------------------------------------------- */

    const selectEstado =
        formulario.querySelector(
            '#seguimiento-estado'
        );

    if (selectEstado) {
        selectEstado.value = estado;
    }


    /* ---------------------------------------------------------
       TÍTULO DINÁMICO
    --------------------------------------------------------- */

    const titulo =
        modal.querySelector(
            '#modal-seguimiento-titulo'
        );

    if (titulo) {
        titulo.textContent =
            `Seguimiento ${folio}`;
    }


    /* ---------------------------------------------------------
       HISTORIAL
    --------------------------------------------------------- */

    renderizarHistorialSeguimiento(
        fila
    );
}


/* =============================================================
   ABRIR MODAL
============================================================= */

function abrirModalSeguimiento(modal) {

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

function cerrarModalSeguimiento(
    modal,
    formulario
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

    formulario.reset();
}


/* =============================================================
   GUARDAR SEGUIMIENTO TEMPORAL
============================================================= */

function guardarSeguimientoTemporal(
    fila,
    seguimiento
) {

    let seguimientos = [];

    if (fila.dataset.seguimientos) {

        try {

            seguimientos =
                JSON.parse(
                    fila.dataset.seguimientos
                );

        } catch (error) {

            seguimientos = [];

        }
    }


    seguimientos.push(
        seguimiento
    );


    fila.dataset.seguimientos =
        JSON.stringify(
            seguimientos
        );
}


/* =============================================================
   OBTENER SEGUIMIENTOS TEMPORALES
============================================================= */

function obtenerSeguimientosTemporales(
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
   RENDERIZAR HISTORIAL
============================================================= */

function renderizarHistorialSeguimiento(
    fila
) {

    const lista =
        document.querySelector(
            '#seguimiento-historial-lista'
        );

    if (!lista) {
        return;
    }


    const seguimientos =
        obtenerSeguimientosTemporales(
            fila
        );


    lista.innerHTML = '';


    /* ---------------------------------------------------------
       SIN SEGUIMIENTOS
    --------------------------------------------------------- */

    if (seguimientos.length === 0) {

        const vacio =
            document.createElement('div');

        vacio.className =
            'seguimiento-historial__vacio';


        const titulo =
            document.createElement('strong');

        titulo.textContent =
            'Sin seguimientos registrados';


        const descripcion =
            document.createElement('span');

        descripcion.textContent =
            'Los movimientos del reporte aparecerán aquí.';


        vacio.appendChild(
            titulo
        );

        vacio.appendChild(
            descripcion
        );


        lista.appendChild(
            vacio
        );

        return;
    }


    /* ---------------------------------------------------------
       MOVIMIENTOS
       El más reciente aparece primero
    --------------------------------------------------------- */

    [...seguimientos]
        .reverse()
        .forEach((seguimiento) => {

            const item =
                crearElementoSeguimiento(
                    seguimiento
                );

            lista.appendChild(
                item
            );
        });
}


/* =============================================================
   CREAR ELEMENTO DEL HISTORIAL
============================================================= */

function crearElementoSeguimiento(
    seguimiento
) {

    const item =
        document.createElement('article');

    item.className =
        'seguimiento-historial__item';


    /* ---------------------------------------------------------
       HEADER
    --------------------------------------------------------- */

    const header =
        document.createElement('div');

    header.className =
        'seguimiento-historial__item-header';


    const tipo =
        document.createElement('strong');

    tipo.className =
        'seguimiento-historial__tipo';

    tipo.textContent =
        seguimiento.tipo
        || 'Seguimiento';


    const fecha =
        document.createElement('span');

    fecha.className =
        'seguimiento-historial__fecha';

    fecha.textContent =
        formatearFechaSeguimiento(
            seguimiento.fecha
        );


    header.appendChild(
        tipo
    );

    header.appendChild(
        fecha
    );


    /* ---------------------------------------------------------
       ESTADO
    --------------------------------------------------------- */

    const estado =
        document.createElement('span');

    estado.className =
        `seguimiento-historial__estado ${obtenerClaseEstadoSeguimiento(
            seguimiento.estado
        )}`;

    estado.textContent =
        seguimiento.estado
        || 'Pendiente';


    /* ---------------------------------------------------------
       OBSERVACIONES
    --------------------------------------------------------- */

    const observaciones =
        document.createElement('p');

    observaciones.className =
        'seguimiento-historial__observaciones';

    observaciones.textContent =
        seguimiento.observaciones
        || '—';


    /* ---------------------------------------------------------
       ARMAR ITEM
    --------------------------------------------------------- */

    item.appendChild(
        header
    );

    item.appendChild(
        estado
    );

    item.appendChild(
        observaciones
    );


    return item;
}


/* =============================================================
   ACTUALIZAR ESTADO EN TABLA
============================================================= */

function actualizarEstadoFila(
    fila,
    estado
) {

    const celdas =
        fila.querySelectorAll('td');

    if (celdas.length < 8) {
        return;
    }


    const celdaEstado =
        celdas[7];


    celdaEstado.innerHTML = '';


    const etiqueta =
        document.createElement('span');


    etiqueta.className =
        `reportes-tabla__estado ${obtenerClaseEstadoSeguimiento(
            estado
        )}`;


    etiqueta.textContent =
        estado || 'Pendiente';


    celdaEstado.appendChild(
        etiqueta
    );
}


/* =============================================================
   CLASE VISUAL DEL ESTADO
============================================================= */

function obtenerClaseEstadoSeguimiento(
    estado
) {

    switch (estado) {

        case 'Finalizado':
            return 'estado--finalizado';

        case 'En proceso':
            return 'estado--proceso';

        default:
            return 'estado--pendiente';
    }
}


/* =============================================================
   FECHA ACTUAL
============================================================= */

function obtenerFechaActual() {

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


/* =============================================================
   FORMATEAR FECHA
   YYYY-MM-DD -> DD/MM/YYYY
============================================================= */

function formatearFechaSeguimiento(
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

function asignarTextoSeguimiento(
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