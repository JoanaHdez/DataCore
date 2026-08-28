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
       ABRIR MODAL
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
       CERRAR MODAL
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
       CERRAR CON ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
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

            }

        }
    );


    /* =====================================================
       ELIMINAR TEMPORALMENTE
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


            /* =============================================
               VALIDAR CONTRASEÑA
            ============================================== */

            if (!password) {

                mostrarMensajeEliminar(
                    mensaje,
                    'Ingresa tu contraseña para continuar.',
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
             * Por ahora cualquier contraseña no vacía
             * se considera válida.
             *
             * Cuando conectemos usuarios y BD,
             * este bloque será sustituido por una
             * petición al backend.
             *
             * El backend deberá comprobar:
             *
             * 1. Usuario autenticado.
             * 2. Usuario con permiso para eliminar.
             * 3. Contraseña correcta.
             * 4. Borrado lógico del registro.
             *
             * La contraseña real nunca se validará
             * únicamente desde JavaScript.
             */


            /*
             * Guardamos el folio antes de limpiar
             * las variables del modal.
             */
            const folioEliminado =
                folioActual;


            /* =============================================
               ELIMINAR FILA DEL DOM
            ============================================== */

            eliminarFilaTemporal(
                filaActual
            );


            /* =============================================
               ACTUALIZAR LISTADO
            ============================================== */

            actualizarListadoDespuesEliminar();


            /* =============================================
               EVENTO GENERAL
            ============================================== */

            document.dispatchEvent(
                new CustomEvent(
                    'reporteEliminadoTemporalmente',
                    {
                        detail: {
                            folio:
                                folioEliminado,
                        },
                    }
                )
            );


            /* =============================================
               CERRAR MODAL DE ELIMINACIÓN
            ============================================== */

            cerrarModalEliminar(
                modal
            );


            /* =============================================
               LIMPIAR MODAL
            ============================================== */

            limpiarModalEliminar(
                inputPassword,
                mensaje
            );


            filaActual = null;
            folioActual = '';


            /* =============================================
               NOTIFICACIÓN REUTILIZABLE
            ============================================== */

            mostrarResultado({
                tipo: 'success',
                titulo: 'Reporte eliminado',
                mensaje:
                    `El reporte ${folioEliminado} fue eliminado correctamente.`,
            });


            window.setTimeout(
                () => {

                    cerrarResultado();

                },
                2000
            );

        }
    );

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

    /*
     * filtros.js ya concentra la lógica relacionada
     * con filtros, resumen y paginación.
     *
     * Volvemos a disparar el evento del buscador
     * para recalcular el listado.
     */

    const busqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


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


    /*
     * Revisamos si la tabla quedó
     * completamente vacía.
     */
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


    /*
     * Todavía existen reportes.
     */
    if (filas.length > 0) {
        return;
    }


    /*
     * Evitamos duplicar la fila vacía.
     */
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
   PREPARAR MODAL
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


    /*
     * Colocamos el foco en contraseña
     * al abrir el modal.
     */
    setTimeout(
        () => {

            inputPassword.focus();

        },
        100
    );

}


/* =========================================================
   LIMPIAR MODAL
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
   MOSTRAR MENSAJE DEL MODAL
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
   ABRIR MODAL
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
   CERRAR MODAL
========================================================= */

function cerrarModalEliminar(
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