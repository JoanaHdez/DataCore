/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Modal reutilizable de resultado
========================================================= */

export function mostrarResultado({
    tipo = 'success',
    titulo = 'Operación realizada',
    mensaje = 'La operación se realizó correctamente.',
} = {}) {

    const modal =
        document.querySelector(
            '#modal-resultado'
        );

    const tituloElemento =
        document.querySelector(
            '#modal-resultado-titulo'
        );

    const mensajeElemento =
        document.querySelector(
            '#modal-resultado-mensaje'
        );

    const iconoPath =
        document.querySelector(
            '#modal-resultado-icono-path'
        );


    if (
        !modal
        || !tituloElemento
        || !mensajeElemento
        || !iconoPath
    ) {
        console.warn(
            'No se encontró la estructura del modal de resultado.'
        );

        return;
    }


    /* =====================================================
       LIMPIAR ESTADOS ANTERIORES
    ===================================================== */

    modal.classList.remove(
        'modal-resultado--success',
        'modal-resultado--error',
        'modal-resultado--warning',
        'modal-resultado--info'
    );


    /* =====================================================
       TIPO
    ===================================================== */

    modal.classList.add(
        `modal-resultado--${tipo}`
    );


    /* =====================================================
       TEXTO
    ===================================================== */

    tituloElemento.textContent =
        titulo;

    mensajeElemento.textContent =
        mensaje;


    /* =====================================================
       ICONO
    ===================================================== */

    actualizarIconoResultado(
        iconoPath,
        tipo
    );


    /* =====================================================
       MOSTRAR
    ===================================================== */

    modal.classList.add(
        'modal-resultado--visible'
    );

    modal.setAttribute(
        'aria-hidden',
        'false'
    );

    document.body.classList.add(
        'modal-resultado-abierto'
    );
}


/* =========================================================
   CERRAR RESULTADO
========================================================= */

export function cerrarResultado() {

    const modal =
        document.querySelector(
            '#modal-resultado'
        );


    if (!modal) {
        return;
    }


    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(elementoActivo)
    ) {
        elementoActivo.blur();
    }


    modal.classList.remove(
        'modal-resultado--visible'
    );

    modal.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.classList.remove(
        'modal-resultado-abierto'
    );
}


/* =========================================================
   EVENTOS DEL MODAL
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '#modal-resultado'
            );


        if (!modal) {
            return;
        }


        modal.addEventListener(
            'click',
            (evento) => {

                const cerrar =
                    evento.target.closest(
                        '[data-cerrar-resultado]'
                    );


                if (!cerrar) {
                    return;
                }


                cerrarResultado();
            }
        );


        document.addEventListener(
            'keydown',
            (evento) => {

                if (
                    evento.key === 'Escape'
                    && modal.classList.contains(
                        'modal-resultado--visible'
                    )
                ) {
                    cerrarResultado();
                }

            }
        );

    }
);


/* =========================================================
   ICONOS
========================================================= */

function actualizarIconoResultado(
    path,
    tipo
) {

    switch (tipo) {

        case 'error':

            path.setAttribute(
                'd',
                'M6 6l12 12M18 6L6 18'
            );

            break;


        case 'warning':

            path.setAttribute(
                'd',
                'M12 4v9M12 17h.01'
            );

            break;


        case 'info':

            path.setAttribute(
                'd',
                'M12 10v7M12 7h.01'
            );

            break;


        default:

            path.setAttribute(
                'd',
                'M20 6L9 17l-5-5'
            );

            break;
    }
}