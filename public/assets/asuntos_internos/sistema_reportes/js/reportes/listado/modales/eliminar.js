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

    const pagina =
        document.querySelector(
            '.reportes-page[data-usuario-rol]'
        );

    const modalUsuario =
        document.querySelector(
            '#modal-eliminar-reporte'
        );

    const formularioUsuario =
        document.querySelector(
            '#form-eliminar-reporte'
        );

    const inputPassword =
        document.querySelector(
            '#eliminar-reporte-password'
        );

    const mensajeUsuario =
        document.querySelector(
            '#eliminar-reporte-mensaje'
        );

    const modalAdmin =
        document.querySelector(
            '#modal-confirmar-eliminacion-reporte'
        );

    const formularioAdmin =
        document.querySelector(
            '#form-confirmar-eliminacion-reporte'
        );


    if (
        !pagina
        || !modalUsuario
        || !formularioUsuario
        || !inputPassword
        || !mensajeUsuario
        || !modalAdmin
        || !formularioAdmin
    ) {
        return;
    }


    const rolUsuario =
        pagina.dataset.usuarioRol
        ?? 'usuario';


    let filaActual = null;
    let folioActual = '';


    /* =====================================================
       ABRIR MODAL SEGÚN ROL
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


            /* =============================================
               ADMIN
            ============================================== */

            if (rolUsuario === 'admin') {

                prepararModalConfirmacionAdmin(
                    modalAdmin,
                    folioActual
                );


                abrirModal(
                    modalAdmin
                );


                return;
            }


            /* =============================================
               USUARIO NORMAL
            ============================================== */

            prepararModalUsuario(
                modalUsuario,
                inputPassword,
                mensajeUsuario,
                folioActual
            );


            abrirModal(
                modalUsuario
            );

        }
    );


    /* =====================================================
       CERRAR MODAL USUARIO
    ===================================================== */

    modalUsuario.addEventListener(
        'click',
        (evento) => {

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal-eliminar]'
                );


            if (!cerrar) {
                return;
            }


            cerrarModal(
                modalUsuario
            );


            limpiarModalUsuario(
                inputPassword,
                mensajeUsuario
            );


            filaActual = null;
            folioActual = '';

        }
    );


    /* =====================================================
       CERRAR MODAL ADMIN
    ===================================================== */

    modalAdmin.addEventListener(
        'click',
        (evento) => {

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal-confirmar-eliminacion]'
                );


            if (!cerrar) {
                return;
            }


            cerrarModal(
                modalAdmin
            );


            filaActual = null;
            folioActual = '';

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (evento.key !== 'Escape') {
                return;
            }


            if (
                modalUsuario.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModal(
                    modalUsuario
                );


                limpiarModalUsuario(
                    inputPassword,
                    mensajeUsuario
                );


                filaActual = null;
                folioActual = '';

                return;
            }


            if (
                modalAdmin.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModal(
                    modalAdmin
                );


                filaActual = null;
                folioActual = '';

            }

        }
    );


    /* =====================================================
    USUARIO NORMAL
    VALIDAR CONTRASEÑA ADMIN
    ===================================================== */

    formularioUsuario.addEventListener(
        'submit',
        async (evento) => {

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

                mostrarMensajeUsuario(
                    mensajeUsuario,
                    'Ingresa la contraseña del administrador.',
                    'error'
                );


                inputPassword.focus();

                return;
            }


            const botonSubmit =
                formularioUsuario.querySelector(
                    'button[type="submit"]'
                );


            const textoOriginal =
                botonSubmit
                    ? botonSubmit.textContent
                    : 'Eliminar reporte';


            if (botonSubmit) {

                botonSubmit.disabled =
                    true;

                botonSubmit.textContent =
                    'Validando...';

            }


            mensajeUsuario.hidden =
                true;


            try {

                const formData =
                    new FormData();


                formData.append(
                    'password_admin',
                    password
                );


                /*
                 * CSRF
                 */
                const csrf =
                    formularioUsuario.querySelector(
                        'input[type="hidden"]'
                    );


                if (
                    csrf
                    && csrf.name
                    && csrf.value
                ) {

                    formData.append(
                        csrf.name,
                        csrf.value
                    );

                }


                const respuesta =
                    await fetch(
                        `${window.location.origin}/asuntos-internos/reportes/listado/autorizar-eliminacion`,
                        {
                            method: 'POST',

                            body: formData,

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    );


                let datos = null;


                try {

                    datos =
                        await respuesta.json();

                } catch {

                    throw new Error(
                        'El servidor devolvió una respuesta no válida.'
                    );

                }


                /* =============================================
                   AUTORIZACIÓN RECHAZADA
                ============================================== */

                if (
                    !respuesta.ok
                    || !datos.success
                ) {

                    mostrarMensajeUsuario(
                        mensajeUsuario,
                        datos.message
                        ?? 'No fue posible autorizar la eliminación.',
                        'error'
                    );


                    inputPassword.value =
                        '';


                    inputPassword.focus();

                    return;
                }


                /* =============================================
                   AUTORIZACIÓN CORRECTA
                ============================================== */

                const folioEliminado =
                    folioActual;


                eliminarFilaTemporal(
                    filaActual
                );


                actualizarListadoDespuesEliminar();


                cerrarModal(
                    modalUsuario
                );


                limpiarModalUsuario(
                    inputPassword,
                    mensajeUsuario
                );


                filaActual = null;
                folioActual = '';


                mostrarNotificacionEliminado(
                    folioEliminado
                );


            } catch (error) {

                console.error(
                    'Error autorizando eliminación:',
                    error
                );


                mostrarMensajeUsuario(
                    mensajeUsuario,
                    'No fue posible validar la autorización. Inténtalo nuevamente.',
                    'error'
                );


            } finally {

                if (botonSubmit) {

                    botonSubmit.disabled =
                        false;

                    botonSubmit.textContent =
                        textoOriginal;

                }

            }

        }
    );


    /* =====================================================
       ADMIN
       CONFIRMACIÓN DIRECTA
    ===================================================== */

    formularioAdmin.addEventListener(
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


            eliminarFilaTemporal(
                filaActual
            );


            actualizarListadoDespuesEliminar();


            cerrarModal(
                modalAdmin
            );


            filaActual = null;
            folioActual = '';


            mostrarNotificacionEliminado(
                folioEliminado
            );

        }
    );

}


/* =========================================================
   PREPARAR MODAL USUARIO
========================================================= */

function prepararModalUsuario(
    modal,
    inputPassword,
    mensaje,
    folio
) {

    limpiarModalUsuario(
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
   PREPARAR MODAL ADMIN
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
   LIMPIAR MODAL USUARIO
========================================================= */

function limpiarModalUsuario(
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
   MENSAJE USUARIO
========================================================= */

function mostrarMensajeUsuario(
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

function abrirModal(
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

function cerrarModal(
    modal
) {

    const activo =
        document.activeElement;


    if (
        activo
        && modal.contains(
            activo
        )
    ) {

        activo.blur();

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
   TABLA VACÍA
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
   NOTIFICACIÓN
========================================================= */

function mostrarNotificacionEliminado(
    folio
) {

    mostrarResultado({
        tipo: 'success',
        titulo: 'Reporte eliminado',
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