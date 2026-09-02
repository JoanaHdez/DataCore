/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Dashboard - Autorización administrativa
========================================================= */

document.addEventListener('DOMContentLoaded', () => {
    inicializarAutorizacionAdmin();
});


function inicializarAutorizacionAdmin() {

    const dashboard =
        document.querySelector(
            '[data-dashboard-requiere-autorizacion]'
        );

    const modal =
        document.querySelector(
            '#modal-autorizacion-admin'
        );

    const formulario =
        document.querySelector(
            '#form-autorizacion-admin'
        );

    const password =
        document.querySelector(
            '#autorizacion-admin-password'
        );

    const mensaje =
        document.querySelector(
            '#autorizacion-admin-mensaje'
        );

    const botonAutorizar =
        document.querySelector(
            '#btn-autorizar-acceso'
        );


    if (
        !dashboard
        || !modal
        || !formulario
        || !password
        || !mensaje
        || !botonAutorizar
    ) {
        return;
    }


    /* =====================================================
       ¿REQUIERE AUTORIZACIÓN?
    ===================================================== */

    const requiereAutorizacion =
        dashboard.dataset
            .dashboardRequiereAutorizacion
        === '1';


    /*
     * ADMINISTRADOR O USUARIO YA AUTORIZADO
     */
    if (!requiereAutorizacion) {
        return;
    }


    /* =====================================================
       USUARIO NORMAL
    ===================================================== */

    prepararAutorizacionDashboard(
        modal,
        password,
        mensaje
    );


    abrirAutorizacionAdmin(
        modal
    );


    /* =====================================================
       CERRAR / CANCELAR
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal-autorizacion]'
                );


            if (!cerrar) {
                return;
            }


            salirDashboardRestringido();

        }
    );


    /* =====================================================
       ESCAPE
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

                salirDashboardRestringido();

            }

        }
    );


    /* =====================================================
       AUTORIZAR
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            const valor =
                password.value.trim();


            if (!valor) {

                mostrarMensajeAutorizacion(
                    mensaje,
                    'Ingresa la contraseña del administrador.',
                    'error'
                );


                password.focus();

                return;
            }


            /* =================================================
               PREPARAR ENVÍO
            ================================================= */

            botonAutorizar.disabled = true;

            const textoOriginal =
                botonAutorizar.textContent;

            botonAutorizar.textContent =
                'Validando...';


            limpiarMensajeAutorizacion(
                mensaje
            );


            try {

                const formData =
                    new FormData();


                formData.append(
                    'password_admin',
                    valor
                );


                /*
                 * CSRF
                 *
                 * Tomamos el token generado por CodeIgniter
                 * desde el propio formulario si existe.
                 */
                const csrf =
                    formulario.querySelector(
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


                /* const respuesta =
                    await fetch(
                        `${window.location.origin}/asuntos-internos/reportes/dashboard/autorizar`,
                        {
                            method: 'POST',

                            body: formData,

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',
                            },
                        }
                    ); */

                    const respuesta =
    await fetch(
        `${window.location.origin}/DataCore/public/asuntos-internos/reportes/dashboard/autorizar`,
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


                /* =================================================
                   AUTORIZACIÓN RECHAZADA
                ================================================= */

                if (
                    !respuesta.ok
                    || !datos.success
                ) {

                    mostrarMensajeAutorizacion(
                        mensaje,
                        datos.message
                        ?? 'No fue posible autorizar el acceso.',
                        'error'
                    );


                    password.value = '';

                    password.focus();

                    return;
                }


                /* =================================================
                   AUTORIZACIÓN CORRECTA
                ================================================= */

                mostrarMensajeAutorizacion(
                    mensaje,
                    'Acceso autorizado.',
                    'success'
                );


                password.value = '';


                /*
                 * Recargamos el Dashboard.
                 *
                 * Ahora la sesión contiene:
                 *
                 * reportes_dashboard_autorizado = true
                 *
                 * Por lo tanto PHP entregará el Dashboard
                 * sin volver a solicitar autorización.
                 */
                window.setTimeout(
                    () => {

                        window.location.reload();

                    },
                    400
                );


            } catch (error) {

                console.error(
                    'Error autorizando Dashboard:',
                    error
                );


                mostrarMensajeAutorizacion(
                    mensaje,
                    'No fue posible validar la autorización. Inténtalo nuevamente.',
                    'error'
                );

            } finally {

                botonAutorizar.disabled =
                    false;


                botonAutorizar.textContent =
                    textoOriginal;

            }

        }
    );

}


/* =========================================================
   PREPARAR DASHBOARD
========================================================= */

function prepararAutorizacionDashboard(
    modal,
    password,
    mensaje
) {

    password.value = '';

    limpiarMensajeAutorizacion(
        mensaje
    );


    const titulo =
        modal.querySelector(
            '#modal-autorizacion-titulo'
        );


    const descripcion =
        modal.querySelector(
            '#autorizacion-admin-descripcion'
        );


    if (titulo) {

        titulo.textContent =
            'Acceso restringido';

    }


    if (descripcion) {

        descripcion.textContent =
            'Para acceder al Dashboard, ingresa la contraseña del administrador.';

    }

}


/* =========================================================
   SALIR DEL DASHBOARD RESTRINGIDO
========================================================= */

/* function salirDashboardRestringido() {

    window.location.href =
        `${window.location.origin}/asuntos-internos/reportes/listado`;

} */

function salirDashboardRestringido() {

    window.location.href =
        `${window.location.origin}/DataCore/public/asuntos-internos/reportes/listado`;

}

/* =========================================================
   MOSTRAR MENSAJE
========================================================= */

function mostrarMensajeAutorizacion(
    mensaje,
    texto,
    tipo
) {

    mensaje.textContent =
        texto;


    mensaje.hidden =
        false;


    mensaje.classList.remove(
        'autorizacion-admin__mensaje--error',
        'autorizacion-admin__mensaje--info',
        'autorizacion-admin__mensaje--success'
    );


    mensaje.classList.add(
        `autorizacion-admin__mensaje--${tipo}`
    );

}


/* =========================================================
   LIMPIAR MENSAJE
========================================================= */

function limpiarMensajeAutorizacion(
    mensaje
) {

    mensaje.textContent = '';

    mensaje.hidden = true;


    mensaje.classList.remove(
        'autorizacion-admin__mensaje--error',
        'autorizacion-admin__mensaje--info',
        'autorizacion-admin__mensaje--success'
    );

}


/* =========================================================
   ABRIR
========================================================= */

function abrirAutorizacionAdmin(
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


    window.setTimeout(
        () => {

            const password =
                modal.querySelector(
                    '#autorizacion-admin-password'
                );


            password?.focus();

        },
        100
    );

}