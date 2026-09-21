import {
    obtenerFechaActual,
} from './utils.js';


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Seguimiento - Formulario
========================================================= */


/* =========================================================
   PREPARAR FORMULARIO
========================================================= */

export function prepararFormularioSeguimiento(
    formulario,
    estadoActual
) {

    formulario.reset();


    const fecha =
        formulario.querySelector(
            '#seguimiento-fecha'
        );


    const estado =
        formulario.querySelector(
            '#seguimiento-estado'
        );


    const idEdicion =
        formulario.querySelector(
            '#seguimiento-id-edicion'
        );


    if (idEdicion) {

        idEdicion.value =
            '';
    }


    if (fecha) {

        fecha.value =
            obtenerFechaActual();
    }


    if (estado) {

        const existe =
            Array.from(
                estado.options
            ).some(
                (opcion) =>
                    opcion.value
                    === estadoActual
            );


        estado.value =
            existe
                ? estadoActual
                : '';
    }
}


/* =========================================================
   INTERFAZ NUEVO / EDITAR
========================================================= */

export function actualizarInterfazModoFormulario(
    modal,
    editando
) {

    const eyebrow =
        modal.querySelector(
            '#seguimiento-form-eyebrow'
        );


    const titulo =
        modal.querySelector(
            '#seguimiento-form-titulo'
        );


    const botonGuardar =
        modal.querySelector(
            '#seguimiento-boton-guardar'
        );


    const accionesEdicion =
        modal.querySelector(
            '#seguimiento-acciones-edicion'
        );


    if (eyebrow) {

        eyebrow.textContent =
            editando
                ? 'Corrección de movimiento'
                : 'Nuevo movimiento';
    }


    if (titulo) {

        titulo.textContent =
            editando
                ? 'Editar seguimiento'
                : 'Registrar seguimiento';
    }


    if (botonGuardar) {

        botonGuardar.textContent =
            editando
                ? 'Guardar cambios'
                : 'Registrar seguimiento';
    }


    if (accionesEdicion) {

        accionesEdicion.hidden =
            !editando;


        if (editando) {

            accionesEdicion.style
                .removeProperty(
                    'display'
                );

        } else {

            accionesEdicion.style
                .setProperty(
                    'display',
                    'none',
                    'important'
                );
        }
    }
}