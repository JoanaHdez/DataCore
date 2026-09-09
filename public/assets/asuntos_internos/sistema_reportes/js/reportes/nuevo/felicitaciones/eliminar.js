import {
    mostrarResultado,
    cerrarResultado
} from '../../notificaciones/resultado.js';


document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarEliminarFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarEliminarFelicitacion() {

    const pagina =
        document.querySelector(
            '.reportes-page[data-usuario-rol]'
        );

    const modalUsuario =
        document.getElementById(
            'modal-eliminar-felicitacion'
        );

    const formUsuario =
        document.getElementById(
            'form-eliminar-felicitacion'
        );

    const password =
        document.getElementById(
            'eliminar-felicitacion-password'
        );

    const mensaje =
        document.getElementById(
            'eliminar-felicitacion-mensaje'
        );


    const modalAdmin =
        document.getElementById(
            'modal-confirmar-eliminacion-felicitacion'
        );

    const formAdmin =
        document.getElementById(
            'form-confirmar-eliminacion-felicitacion'
        );


    if (
        !pagina
        || !modalUsuario
        || !formUsuario
        || !modalAdmin
        || !formAdmin
    ) {
        return;
    }


    /* =====================================================
       CLICK EN ELIMINAR
    ===================================================== */

    document.addEventListener(
        'click',
        (event) => {

            const boton =
                event.target.closest(
                    '[data-accion-felicitacion="eliminar"]'
                );


            if (!boton) {
                return;
            }


            const idFelicitacion =
                Number(
                    boton.dataset.idFelicitacion
                    || 0
                );


            const folio =
                String(
                    boton.dataset.folio
                    || ''
                ).trim();


            if (
                idFelicitacion <= 0
            ) {
                return;
            }


            const rol =
                String(
                    pagina.dataset.usuarioRol
                    || ''
                )
                    .trim()
                    .toLowerCase();


            /* =============================================
               ADMIN
            ============================================= */

            if (
                rol === 'admin'
            ) {

                prepararModalAdmin(
                    modalAdmin,
                    idFelicitacion,
                    folio
                );

                abrirModal(
                    modalAdmin
                );

                return;
            }


            /* =============================================
               USUARIO NORMAL
            ============================================= */

            prepararModalUsuario(
                modalUsuario,
                idFelicitacion,
                folio,
                password,
                mensaje
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
        (event) => {

            if (
                event.target.closest(
                    '[data-cerrar-modal-eliminar-felicitacion]'
                )
            ) {

                cerrarModal(
                    modalUsuario
                );
            }
        }
    );


    /* =====================================================
       CERRAR MODAL ADMIN
    ===================================================== */

    modalAdmin.addEventListener(
        'click',
        (event) => {

            if (
                event.target.closest(
                    '[data-cerrar-modal-confirmar-eliminacion-felicitacion]'
                )
            ) {

                cerrarModal(
                    modalAdmin
                );
            }
        }
    );


    /* =====================================================
       SUBMIT USUARIO NORMAL
    ===================================================== */

    formUsuario.addEventListener(
        'submit',
        async (event) => {

            event.preventDefault();


            const idFelicitacion =
                Number(
                    modalUsuario.dataset.idFelicitacion
                    || 0
                );


            const folio =
                String(
                    modalUsuario.dataset.folio
                    || ''
                ).trim();


            const passwordAdmin =
                String(
                    password?.value
                    || ''
                ).trim();


            if (
                idFelicitacion <= 0
            ) {
                return;
            }


            if (
                passwordAdmin === ''
            ) {

                mostrarMensajeModal(
                    mensaje,
                    'Ingresa la contraseña del administrador.'
                );

                password?.focus();

                return;
            }


            limpiarMensajeModal(
                mensaje
            );


            const botonSubmit =
                formUsuario.querySelector(
                    '[type="submit"]'
                );


            bloquearBoton(
                botonSubmit,
                true
            );


            try {

                const formData =
                    new FormData(
                        formUsuario
                    );


                formData.set(
                    'password_admin',
                    passwordAdmin
                );


                const respuesta =
                    await solicitarEliminacion(
                        idFelicitacion,
                        formData
                    );


                if (
                    !respuesta.success
                ) {

                    mostrarMensajeModal(
                        mensaje,
                        respuesta.message
                        || 'No fue posible eliminar la felicitación.'
                    );

                    return;
                }


                cerrarModal(
                    modalUsuario
                );


                eliminarFila(
                    idFelicitacion
                );


                mostrarNotificacionEliminado(
                    folio
                );

            } catch (error) {

                console.error(
                    'Error al eliminar felicitación:',
                    error
                );


                mostrarMensajeModal(
                    mensaje,
                    error.message
                    || 'Ocurrió un error al eliminar la felicitación.'
                );

            } finally {

                bloquearBoton(
                    botonSubmit,
                    false
                );
            }
        }
    );


    /* =====================================================
       SUBMIT ADMIN
    ===================================================== */

    formAdmin.addEventListener(
        'submit',
        async (event) => {

            event.preventDefault();


            const idFelicitacion =
                Number(
                    modalAdmin.dataset.idFelicitacion
                    || 0
                );


            const folio =
                String(
                    modalAdmin.dataset.folio
                    || ''
                ).trim();


            if (
                idFelicitacion <= 0
            ) {
                return;
            }


            const botonSubmit =
                formAdmin.querySelector(
                    '[type="submit"]'
                );


            bloquearBoton(
                botonSubmit,
                true
            );


            try {

                const formData =
                    new FormData(
                        formAdmin
                    );


                const respuesta =
                    await solicitarEliminacion(
                        idFelicitacion,
                        formData
                    );


                if (
                    !respuesta.success
                ) {

                    cerrarModal(
                        modalAdmin
                    );


                    mostrarResultado({
                        tipo: 'error',

                        titulo:
                            'No fue posible eliminar',

                        mensaje:
                            respuesta.message
                            || 'No fue posible eliminar la felicitación.',
                    });

                    return;
                }


                cerrarModal(
                    modalAdmin
                );


                eliminarFila(
                    idFelicitacion
                );


                mostrarNotificacionEliminado(
                    folio
                );

            } catch (error) {

                console.error(
                    'Error al eliminar felicitación:',
                    error
                );


                cerrarModal(
                    modalAdmin
                );


                mostrarResultado({
                    tipo: 'error',

                    titulo:
                        'No fue posible eliminar',

                    mensaje:
                        error.message
                        || 'Ocurrió un error al eliminar la felicitación.',
                });

            } finally {

                bloquearBoton(
                    botonSubmit,
                    false
                );
            }
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (event) => {

            if (
                event.key !== 'Escape'
            ) {
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
            }
        }
    );
}


/* =========================================================
   PREPARAR MODAL USUARIO
========================================================= */

function prepararModalUsuario(
    modal,
    idFelicitacion,
    folio,
    password,
    mensaje
) {

    modal.dataset.idFelicitacion =
        String(
            idFelicitacion
        );


    modal.dataset.folio =
        folio;


    const folioElemento =
        document.getElementById(
            'eliminar-felicitacion-folio'
        );


    if (
        folioElemento
    ) {

        folioElemento.textContent =
            folio || '—';
    }


    if (
        password
    ) {

        password.value =
            '';
    }


    limpiarMensajeModal(
        mensaje
    );
}


/* =========================================================
   PREPARAR MODAL ADMIN
========================================================= */

function prepararModalAdmin(
    modal,
    idFelicitacion,
    folio
) {

    modal.dataset.idFelicitacion =
        String(
            idFelicitacion
        );


    modal.dataset.folio =
        folio;


    const folioElemento =
        document.getElementById(
            'confirmar-eliminacion-felicitacion-folio'
        );


    if (
        folioElemento
    ) {

        folioElemento.textContent =
            folio || '—';
    }
}


/* =========================================================
   SOLICITUD BACKEND
========================================================= */

async function solicitarEliminacion(
    idFelicitacion,
    formData
) {

    const baseUrl =
        obtenerBaseUrl();


    const url =
        new URL(
            `asuntos-internos/reportes/felicitaciones/eliminar/${idFelicitacion}`,
            baseUrl
        );


    const response =
        await fetch(
            url,
            {
                method: 'POST',

                body:
                    formData,

                headers: {
                    'X-Requested-With':
                        'XMLHttpRequest',
                },
            }
        );


    let data;


    try {

        data =
            await response.json();

    } catch {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );
    }


    if (
        !response.ok
    ) {

        return {
            success: false,

            message:
                data.message
                || 'No fue posible eliminar la felicitación.',
        };
    }


    return data;
}


/* =========================================================
   ELIMINAR FILA
========================================================= */

function eliminarFila(
    idFelicitacion
) {

    const fila =
        document.querySelector(
            `tr[data-id-felicitacion="${idFelicitacion}"]`
        );


    if (
        fila
    ) {

        fila.remove();
    }


    actualizarEstadoTabla();
}


/* =========================================================
   ACTUALIZAR TABLA VACÍA
========================================================= */

function actualizarEstadoTabla() {

    const tbody =
        document.getElementById(
            'tabla-felicitaciones-body'
        );


    if (
        !tbody
    ) {
        return;
    }


    const filas =
        tbody.querySelectorAll(
            'tr[data-id-felicitacion]'
        );


    if (
        filas.length > 0
    ) {
        return;
    }


    /*
     * No insertamos aquí una estructura nueva porque
     * el listado ya controla su propio estado vacío.
     *
     * Si se elimina el último registro, actualizamos
     * la página para que PHP reconstruya correctamente
     * resumen, paginación y estado vacío.
     */

    window.location.reload();
}


/* =========================================================
   NOTIFICACIÓN
========================================================= */

function mostrarNotificacionEliminado(
    folio
) {

    mostrarResultado({
        tipo: 'exito',

        titulo:
            'Felicitación eliminada',

        mensaje:
            folio
                ? `La felicitación ${folio} fue eliminada correctamente.`
                : 'La felicitación fue eliminada correctamente.',
    });


    window.setTimeout(
        () => {

            cerrarResultado();

        },
        2000
    );
}


/* =========================================================
   MENSAJE MODAL
========================================================= */

function mostrarMensajeModal(
    elemento,
    texto
) {

    if (
        !elemento
    ) {
        return;
    }


    elemento.textContent =
        texto;

    elemento.hidden =
        false;
}


function limpiarMensajeModal(
    elemento
) {

    if (
        !elemento
    ) {
        return;
    }


    elemento.textContent =
        '';

    elemento.hidden =
        true;
}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModal(
    modal
) {

    if (!modal) {
        return;
    }


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

    if (!modal) {
        return;
    }


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


    delete modal.dataset.idFelicitacion;
    delete modal.dataset.folio;
}

/* =========================================================
   BOTÓN
========================================================= */

function bloquearBoton(
    boton,
    bloquear
) {

    if (
        !boton
    ) {
        return;
    }


    boton.disabled =
        bloquear;
}


/* =========================================================
   BASE URL
========================================================= */

function obtenerBaseUrl() {

    return `${window.location.origin}/DataCore/public/`;
}