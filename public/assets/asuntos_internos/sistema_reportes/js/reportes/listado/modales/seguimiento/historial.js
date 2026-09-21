/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Historial
========================================================= */

import {
    formatearFecha,
    formatearFechaHora,
    obtenerClaseEstado,
} from './utils.js';

/* =========================================================
   CARGAR HISTORIAL
========================================================= */

export function cargarHistorialSeguimiento(
    modal,
    historial
) {

    const lista =
        modal.querySelector(
            '#seguimiento-historial-lista'
        );


    if (!lista) {
        return;
    }


    lista.innerHTML =
        '';


    if (
        !Array.isArray(historial)
        || historial.length === 0
    ) {

        const vacio =
            document.createElement(
                'div'
            );


        vacio.className =
            'seguimiento-historial__vacio';


        const titulo =
            document.createElement(
                'strong'
            );


        titulo.textContent =
            'Sin seguimientos registrados';


        const descripcion =
            document.createElement(
                'span'
            );


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


    historial.forEach(
        (seguimiento) => {

            lista.appendChild(
                crearMovimientoHistorial(
                    seguimiento
                )
            );

        }
    );

}


/* =========================================================
   CREAR MOVIMIENTO
========================================================= */

function crearMovimientoHistorial(
    seguimiento
) {

    const item =
        document.createElement(
            'article'
        );


    item.className =
        'seguimiento-historial__item';


    item.dataset.idSeguimiento =
        String(
            seguimiento.id_seguimiento
            || ''
        );


    /* =====================================================
       HEADER
    ===================================================== */

    const header =
        document.createElement(
            'div'
        );


    header.className =
        'seguimiento-historial__item-header';


    const tipo =
        document.createElement(
            'strong'
        );


    tipo.className =
        'seguimiento-historial__tipo';


    tipo.textContent =
        seguimiento.tipo
        || 'Seguimiento';


    const fecha =
        document.createElement(
            'span'
        );


    fecha.className =
        'seguimiento-historial__fecha';


    fecha.textContent =
        formatearFecha(
            seguimiento.fecha
        );


    header.appendChild(
        tipo
    );


    header.appendChild(
        fecha
    );


    /* =====================================================
       ESTADO
    ===================================================== */

    const estado =
        document.createElement(
            'span'
        );


    estado.className =
        `seguimiento-historial__estado ${obtenerClaseEstado(
            seguimiento.estado
        )}`;


    estado.textContent =
        seguimiento.estado
        || 'Pendiente';


    /* =====================================================
       OBSERVACIONES
    ===================================================== */

    const observaciones =
        document.createElement(
            'p'
        );


    observaciones.className =
        'seguimiento-historial__observaciones';


    observaciones.textContent =
        seguimiento.observaciones
        || 'Sin observaciones.';


    item.appendChild(
        header
    );


    item.appendChild(
        estado
    );


    item.appendChild(
        observaciones
    );


    /* =====================================================
       SANCIÓN
    ===================================================== */

    if (
        seguimiento.sancion
        && seguimiento.sancion.tipo
    ) {

        const bloque =
            document.createElement(
                'div'
            );


        bloque.className =
            'seguimiento-historial__sancion';


        const etiqueta =
            document.createElement(
                'strong'
            );


        etiqueta.textContent =
            'Cambio de sanción disciplinaria';


        const valor =
            document.createElement(
                'span'
            );


        valor.textContent =
            obtenerTextoSancion(
                seguimiento.sancion
            );


        bloque.appendChild(
            etiqueta
        );


        bloque.appendChild(
            valor
        );


        item.appendChild(
            bloque
        );

    }


    /* =====================================================
       METADATA
    ===================================================== */

    if (
        seguimiento.created_at
    ) {

        const metadata =
            document.createElement(
                'small'
            );


        metadata.className =
            'seguimiento-historial__metadata';


        metadata.textContent =
            `Registrado: ${formatearFechaHora(
                seguimiento.created_at
            )}`;


        item.appendChild(
            metadata
        );

    }


    /* =====================================================
       ACCIONES
    ===================================================== */

    const acciones =
        document.createElement(
            'div'
        );


    acciones.className =
        'seguimiento-historial__acciones';


    const botonEditar =
        document.createElement(
            'button'
        );


    botonEditar.type =
        'button';


    botonEditar.className =
        'seguimiento-historial__editar';


    botonEditar.dataset.editarSeguimiento =
        String(
            seguimiento.id_seguimiento
        );


    botonEditar.textContent =
        'Editar';


    acciones.appendChild(
        botonEditar
    );


    item.appendChild(
        acciones
    );


    return item;

}


/* =========================================================
   TEXTO SANCIÓN
========================================================= */

function obtenerTextoSancion(
    sancion
) {

    if (
        !sancion
        || !sancion.tipo
    ) {
        return 'Sin sanción registrada';
    }


    if (
        sancion.tipo === 'Otro'
    ) {

        return String(
            sancion.descripcion_otro
            || sancion.texto
            || 'Otra sanción'
        ).trim();

    }


    return String(
        sancion.tipo
    ).trim();

}
