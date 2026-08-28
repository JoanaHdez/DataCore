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


    if (
        !dashboard
        || !modal
        || !formulario
        || !password
        || !mensaje
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
     * ADMINISTRADOR
     *
     * Si PHP determinó que no requiere autorización,
     * no hacemos absolutamente nada.
     */
    if (!requiereAutorizacion) {

        return;

    }


    /*
     * USUARIO NORMAL
     *
     * El Dashboard requiere autorización administrativa.
     */
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
       SUBMIT
    ===================================================== */

    formulario.addEventListener(
        'submit',
        (evento) => {

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


            /*
             * Todavía falta conectar aquí
             * la validación real en backend
             * contra el administrador ID 758.
             */
            mostrarMensajeAutorizacion(
                mensaje,
                'Validación administrativa pendiente de conexión con el backend.',
                'info'
            );

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

    limpiarAutorizacionAdmin(
        password,
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

function salirDashboardRestringido() {

    /*
     * Un usuario que no tiene autorización
     * NO debe poder cerrar el modal y quedarse
     * viendo el Dashboard.
     *
     * Lo regresamos al listado de reportes.
     */

    window.location.href =
        `${window.location.origin}/asuntos-internos/reportes/listado`;

}


/* =========================================================
   MENSAJE
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
   LIMPIAR
========================================================= */

function limpiarAutorizacionAdmin(
    password,
    mensaje
) {

    password.value = '';


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


    /*
     * Ponemos el foco directamente
     * en la contraseña.
     */
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


/* =========================================================
   CERRAR
========================================================= */

function cerrarAutorizacionAdmin(
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