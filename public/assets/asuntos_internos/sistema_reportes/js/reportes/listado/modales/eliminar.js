import {
    mostrarResultado,
    cerrarResultado
} from '../../notificaciones/resultado.js';


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Eliminar reporte
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarEliminarReporte();
});


function inicializarEliminarReporte() {

    /* =====================================================
       MODAL USUARIO - REQUIERE CONTRASEÑA
    ===================================================== */

    const modal =
        document.querySelector(
            '#modal-eliminar-reporte'
        );

    const formulario =
        document.querySelector(
            '#form-eliminar-reporte'
        );

    const inputPassword =
        document.querySelector(
            '#eliminar-reporte-password'
        );

    const mensaje =
        document.querySelector(
            '#eliminar-reporte-mensaje'
        );


    /* =====================================================
       MODAL ADMIN - SOLO CONFIRMACIÓN
    ===================================================== */

    const modalConfirmacion =
        document.querySelector(
            '#modal-confirmar-eliminacion-reporte'
        );

    const formularioConfirmacion =
        document.querySelector(
            '#form-confirmar-eliminacion-reporte'
        );


    if (
        !modal
        || !formulario
        || !inputPassword
        || !mensaje
    ) {
        return;
    }


    let filaActual = null;
    let folioActual = '';


    /* =====================================================
       ABRIR ELIMINACIÓN
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="eliminar"]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest('tr');


            if (!fila) {
                return;
            }


            filaActual =
                fila;


            folioActual =
                boton.dataset.folio
                || fila
                    .querySelector('td')
                    ?.textContent
                    .trim()
                || '';


            /*
             * =================================================
             * TEMPORAL
             * =================================================
             *
             * Mientras todavía no conectamos sesión/roles,
             * abrimos el modal de ADMIN para probarlo.
             *
             * Después esto se sustituirá por algo como:
             *
             * if (usuarioEsAdmin) {
             *     confirmación
             * } else {
             *     contraseña de administrador
             * }
             */

            if (modalConfirmacion) {

                prepararModalConfirmacionAdmin(
                    modalConfirmacion,
                    folioActual
                );


                abrirModalConfirmacionAdmin(
                    modalConfirmacion
                );


                return;
            }


            /*
             * Respaldo:
             * si por alguna razón no existe el modal
             * de confirmación, abrimos el modal anterior.
             */

            prepararModalEliminar(
                modal,
                inputPassword,
                mensaje,
                folioActual
            );


            abrirModalEliminar(
                modal
            );

        }
    );


    /* =====================================================
       CERRAR MODAL CON CONTRASEÑA
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonCerrar =
                evento.target.closest(
                    '[data-cerrar-modal-eliminar]'
                );


            if (!botonCerrar) {
                return;
            }


            cerrarModalEliminar(
                modal
            );


            limpiarModalEliminar(
                inputPassword,
                mensaje
            );


            filaActual = null;
            folioActual = '';

        }
    );


    /* =====================================================
       CERRAR MODAL ADMIN
    ===================================================== */

    if (modalConfirmacion) {

        modalConfirmacion.addEventListener(
            'click',
            (evento) => {

                const botonCerrar =
                    evento.target.closest(
                        '[data-cerrar-modal-confirmar-eliminacion]'
                    );


                if (!botonCerrar) {
                    return;
                }


                cerrarModalConfirmacionAdmin(
                    modalConfirmacion
                );


                filaActual = null;
                folioActual = '';

            }
        );

    }


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Escape'
            ) {
                return;
            }


            /*
             * Modal contraseña
             */
            if (
                modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModalEliminar(
                    modal
                );


                limpiarModalEliminar(
                    inputPassword,
                    mensaje
                );


                filaActual = null;
                folioActual = '';


                return;
            }


            /*
             * Modal admin
             */
            if (
                modalConfirmacion
                && modalConfirmacion.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModalConfirmacionAdmin(
                    modalConfirmacion
                );


                filaActual = null;
                folioActual = '';

            }

        }
    );


    /* =====================================================
       USUARIO NORMAL
       ELIMINACIÓN CON CONTRASEÑA
    ===================================================== */

    formulario.addEventListener(
        'submit',
        (evento) => {

            evento.preventDefault();


            if (
                !filaActual
                || !folioActual
            ) {
                return;
            }


            const password =
                inputPassword.value.trim();


            if (!password) {

                mostrarMensajeEliminar(
                    mensaje,
                    'Ingresa la contraseña del administrador para continuar.',
                    'error'
                );


                inputPassword.focus();

                return;
            }


            /*
             * =================================================
             * TEMPORAL
             * =================================================
             *
             * Mientras no conectemos el backend,
             * cualquier contraseña no vacía funciona.
             *
             * Después:
             *
             * backend → plantilla_general.plantilla
             * administrador → ID 758
             * password → CURP del administrador
             */


            const folioEliminado =
                folioActual;


            eliminarFilaTemporal(
                filaActual
            );


            actualizarListadoDespuesEliminar();


            document.dispatchEvent(
                new CustomEvent(
                    'reporteEliminadoTemporalmente',
                    {
                        detail: {
                            folio:
                                folioEliminado,

                            autorizacion:
                                'admin_password',
                        },
                    }
                )
            );


            cerrarModalEliminar(
                modal
            );


            limpiarModalEliminar(
                inputPassword,
                mensaje
            );


            filaActual = null;
            folioActual = '';


            mostrarNotificacionEliminado(
                folioEliminado
            );

        }
    );


    /* =====================================================
       ADMINISTRADOR
       CONFIRMAR ELIMINACIÓN SIN CONTRASEÑA
    ===================================================== */

    if (formularioConfirmacion) {

        formularioConfirmacion.addEventListener(
            'submit',
            (evento) => {

                evento.preventDefault();


                if (
                    !filaActual
                    || !folioActual
                ) {
                    return;
                }


                const folioEliminado =
                    folioActual;


                /*
                 * TEMPORAL:
                 *
                 * después el backend comprobará que
                 * la sesión realmente pertenece al
                 * administrador plantilla ID = 758.
                 */


                eliminarFilaTemporal(
                    filaActual
                );


                actualizarListadoDespuesEliminar();


                document.dispatchEvent(
                    new CustomEvent(
                        'reporteEliminadoTemporalmente',
                        {
                            detail: {
                                folio:
                                    folioEliminado,

                                autorizacion:
                                    'admin_directo',
                            },
                        }
                    )
                );


                cerrarModalConfirmacionAdmin(
                    modalConfirmacion
                );


                filaActual = null;
                folioActual = '';


                mostrarNotificacionEliminado(
                    folioEliminado
                );

            }
        );

    }

}


/* =========================================================
   ELIMINAR FILA TEMPORAL
========================================================= */

function eliminarFilaTemporal(
    fila
) {

    if (!fila) {
        return;
    }


    fila.remove();

}


/* =========================================================
   ACTUALIZAR LISTADO
========================================================= */

function actualizarListadoDespuesEliminar() {

    const busqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


    /*
     * filtros.js concentra la lógica de:
     *
     * - filtros
     * - búsqueda
     * - tarjetas resumen
     * - paginación
     */

    if (busqueda) {

        busqueda.dispatchEvent(
            new Event(
                'input',
                {
                    bubbles: true,
                }
            )
        );

    }


    actualizarTablaVacia();

}


/* =========================================================
   ESTADO VACÍO DE TABLA
========================================================= */

function actualizarTablaVacia() {

    const tbody =
        document.querySelector(
            '#tabla-reportes-body'
        );


    if (!tbody) {
        return;
    }


    const filas =
        Array.from(
            tbody.querySelectorAll('tr')
        ).filter((fila) => {

            return !fila.classList.contains(
                'reportes-tabla__empty'
            );

        });


    if (filas.length > 0) {
        return;
    }


    if (
        tbody.querySelector(
            '.reportes-tabla__empty'
        )
    ) {
        return;
    }


    const fila =
        document.createElement(
            'tr'
        );


    fila.className =
        'reportes-tabla__empty';


    fila.innerHTML = `
        <td colspan="9">

            <div class="reportes-tabla__empty-content">

                <strong>
                    No hay reportes para mostrar
                </strong>

                <span>
                    Los registros aparecerán aquí cuando existan reportes disponibles.
                </span>

            </div>

        </td>
    `;


    tbody.appendChild(
        fila
    );

}


/* =========================================================
   NOTIFICACIÓN DE ÉXITO
========================================================= */

function mostrarNotificacionEliminado(
    folio
) {

    mostrarResultado({
        tipo: 'success',

        titulo:
            'Reporte eliminado',

        mensaje:
            `El reporte ${folio} fue eliminado correctamente.`,
    });


    window.setTimeout(
        () => {

            cerrarResultado();

        },
        2000
    );

}


/* =========================================================
   PREPARAR MODAL CON CONTRASEÑA
========================================================= */

function prepararModalEliminar(
    modal,
    inputPassword,
    mensaje,
    folio
) {

    limpiarModalEliminar(
        inputPassword,
        mensaje
    );


    const folioElemento =
        modal.querySelector(
            '#eliminar-reporte-folio'
        );


    if (folioElemento) {

        folioElemento.textContent =
            folio || '—';

    }


    const titulo =
        modal.querySelector(
            '#modal-eliminar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Eliminar ${folio}`
                : 'Eliminar reporte';

    }


    window.setTimeout(
        () => {

            inputPassword.focus();

        },
        100
    );

}


/* =========================================================
   LIMPIAR MODAL CON CONTRASEÑA
========================================================= */

function limpiarModalEliminar(
    inputPassword,
    mensaje
) {

    inputPassword.value = '';


    mensaje.textContent = '';

    mensaje.hidden = true;


    mensaje.classList.remove(
        'eliminar-reporte__mensaje--error',
        'eliminar-reporte__mensaje--info',
        'eliminar-reporte__mensaje--success'
    );

}


/* =========================================================
   MENSAJE DEL MODAL
========================================================= */

function mostrarMensajeEliminar(
    mensaje,
    texto,
    tipo
) {

    mensaje.textContent =
        texto;


    mensaje.hidden =
        false;


    mensaje.classList.remove(
        'eliminar-reporte__mensaje--error',
        'eliminar-reporte__mensaje--info',
        'eliminar-reporte__mensaje--success'
    );


    mensaje.classList.add(
        `eliminar-reporte__mensaje--${tipo}`
    );

}


/* =========================================================
   ABRIR MODAL CON CONTRASEÑA
========================================================= */

function abrirModalEliminar(
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


/* =========================================================
   CERRAR MODAL CON CONTRASEÑA
========================================================= */

function cerrarModalEliminar(
    modal
) {

    desenfocarElementoModal(
        modal
    );


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


/* =========================================================
   PREPARAR CONFIRMACIÓN ADMIN
========================================================= */

function prepararModalConfirmacionAdmin(
    modal,
    folio
) {

    const folioElemento =
        modal.querySelector(
            '#confirmar-eliminacion-folio'
        );


    if (folioElemento) {

        folioElemento.textContent =
            folio || '—';

    }


    const titulo =
        modal.querySelector(
            '#titulo-confirmar-eliminacion'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Eliminar ${folio}`
                : 'Eliminar reporte';

    }

}


/* =========================================================
   ABRIR CONFIRMACIÓN ADMIN
========================================================= */

function abrirModalConfirmacionAdmin(
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


/* =========================================================
   CERRAR CONFIRMACIÓN ADMIN
========================================================= */

function cerrarModalConfirmacionAdmin(
    modal
) {

    desenfocarElementoModal(
        modal
    );


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


/* =========================================================
   DESENFOCAR ELEMENTO DEL MODAL
========================================================= */

function desenfocarElementoModal(
    modal
) {

    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(
            elementoActivo
        )
    ) {

        elementoActivo.blur();

    }

}