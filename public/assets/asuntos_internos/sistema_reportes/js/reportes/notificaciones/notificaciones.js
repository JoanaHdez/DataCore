/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Notificaciones reutilizables
========================================================= */


/**
 * Muestra una notificación visual reutilizable.
 *
 * Tipos disponibles:
 * - success
 * - error
 * - warning
 * - info
 */
export function mostrarNotificacion({
    tipo = 'info',
    titulo = '',
    mensaje = '',
    duracion = 4500,
} = {}) {

    const contenedor =
        document.querySelector(
            '#notificaciones-contenedor'
        );

    if (!contenedor) {
        console.warn(
            'No se encontró el contenedor de notificaciones.'
        );

        return null;
    }


    const notificacion =
        document.createElement('div');


    notificacion.className =
        `notificacion notificacion--${tipo}`;


    notificacion.setAttribute(
        'role',
        tipo === 'error'
            ? 'alert'
            : 'status'
    );


    /* =====================================================
       ICONO
    ===================================================== */

    const icono =
        document.createElement('div');


    icono.className =
        'notificacion__icono';


    icono.innerHTML =
        obtenerIconoNotificacion(tipo);


    /* =====================================================
       CONTENIDO
    ===================================================== */

    const contenido =
        document.createElement('div');


    contenido.className =
        'notificacion__contenido';


    if (titulo) {

        const tituloElemento =
            document.createElement('strong');


        tituloElemento.className =
            'notificacion__titulo';


        tituloElemento.textContent =
            titulo;


        contenido.appendChild(
            tituloElemento
        );
    }


    if (mensaje) {

        const mensajeElemento =
            document.createElement('span');


        mensajeElemento.className =
            'notificacion__mensaje';


        mensajeElemento.textContent =
            mensaje;


        contenido.appendChild(
            mensajeElemento
        );
    }


    /* =====================================================
       CERRAR
    ===================================================== */

    const botonCerrar =
        document.createElement('button');


    botonCerrar.type =
        'button';


    botonCerrar.className =
        'notificacion__cerrar';


    botonCerrar.setAttribute(
        'aria-label',
        'Cerrar notificación'
    );


    botonCerrar.innerHTML =
        '&times;';


    botonCerrar.addEventListener(
        'click',
        () => {
            cerrarNotificacion(
                notificacion
            );
        }
    );


    /* =====================================================
       ARMAR NOTIFICACIÓN
    ===================================================== */

    notificacion.appendChild(
        icono
    );

    notificacion.appendChild(
        contenido
    );

    notificacion.appendChild(
        botonCerrar
    );


    contenedor.appendChild(
        notificacion
    );


    /* =====================================================
       ANIMACIÓN DE ENTRADA
    ===================================================== */

    requestAnimationFrame(() => {

        notificacion.classList.add(
            'notificacion--visible'
        );

    });


    /* =====================================================
       CIERRE AUTOMÁTICO
    ===================================================== */

    if (duracion > 0) {

        window.setTimeout(
            () => {

                cerrarNotificacion(
                    notificacion
                );

            },
            duracion
        );

    }


    return notificacion;
}

/* =========================================================
   CERRAR NOTIFICACIÓN
========================================================= */

function cerrarNotificacion(
    notificacion
) {

    if (!notificacion) {
        return;
    }


    notificacion.classList.remove(
        'notificacion--visible'
    );


    notificacion.classList.add(
        'notificacion--saliendo'
    );


    window.setTimeout(
        () => {

            notificacion.remove();

        },
        250
    );
}


/* =========================================================
   ICONOS
========================================================= */

function obtenerIconoNotificacion(
    tipo
) {

    switch (tipo) {

        case 'success':

            return `
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        d="M20 6L9 17l-5-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            `;


        case 'error':

            return `
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        d="M6 6l12 12M18 6L6 18"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                    />
                </svg>
            `;


        case 'warning':

            return `
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        d="M12 3l9 17H3L12 3z"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M12 9v5M12 17h.01"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
            `;


        default:

            return `
                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <path
                        d="M12 11v6M12 7h.01"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
            `;
    }
}