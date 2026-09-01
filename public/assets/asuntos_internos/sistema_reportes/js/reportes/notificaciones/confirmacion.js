/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Modal reutilizable de confirmación
========================================================= */

let resolverConfirmacion =
    null;


/* =========================================================
   MOSTRAR CONFIRMACIÓN
========================================================= */

export function confirmarAccion({
    titulo = 'Confirmar acción',
    mensaje = '¿Deseas continuar con esta acción?',
    textoConfirmar = 'Continuar',
    textoCancelar = 'Cancelar',
} = {}) {

    const modal =
        document.querySelector(
            '#modal-confirmacion'
        );

    const tituloElemento =
        document.querySelector(
            '#modal-confirmacion-titulo'
        );

    const mensajeElemento =
        document.querySelector(
            '#modal-confirmacion-mensaje'
        );

    const botonConfirmar =
        document.querySelector(
            '#btn-confirmar-accion'
        );

    const botonCancelar =
        modal?.querySelector(
            '[data-cerrar-modal-confirmacion]:not(.modal-reporte__close):not(.modal-reporte__overlay)'
        );


    if (
        !modal
        || !tituloElemento
        || !mensajeElemento
        || !botonConfirmar
    ) {

        console.warn(
            'No se encontró la estructura del modal de confirmación.'
        );

        return Promise.resolve(
            false
        );
    }


    /* =====================================================
       SI HABÍA OTRA CONFIRMACIÓN ABIERTA
    ===================================================== */

    if (
        typeof resolverConfirmacion
        === 'function'
    ) {

        resolverConfirmacion(
            false
        );


        resolverConfirmacion =
            null;

    }


    /* =====================================================
       TEXTO
    ===================================================== */

    tituloElemento.textContent =
        titulo;


    mensajeElemento.textContent =
        mensaje;


    botonConfirmar.textContent =
        textoConfirmar;


    if (botonCancelar) {

        botonCancelar.textContent =
            textoCancelar;

    }


    /* =====================================================
       MOSTRAR
    ===================================================== */

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


    /* =====================================================
       PROMESA
    ===================================================== */

    return new Promise(
        (resolve) => {

            resolverConfirmacion =
                resolve;

        }
    );

}


/* =========================================================
   CERRAR CONFIRMACIÓN
========================================================= */

export function cerrarConfirmacion(
    resultado = false
) {

    const modal =
        document.querySelector(
            '#modal-confirmacion'
        );


    if (!modal) {
        return;
    }


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


    if (
        typeof resolverConfirmacion
        === 'function'
    ) {

        const resolver =
            resolverConfirmacion;


        resolverConfirmacion =
            null;


        resolver(
            resultado
        );

    }

}


/* =========================================================
   EVENTOS DEL MODAL
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const modal =
            document.querySelector(
                '#modal-confirmacion'
            );

        const botonConfirmar =
            document.querySelector(
                '#btn-confirmar-accion'
            );


        if (
            !modal
            || !botonConfirmar
        ) {
            return;
        }


        /* =================================================
           CONFIRMAR
        ================================================= */

        botonConfirmar.addEventListener(
            'click',
            () => {

                cerrarConfirmacion(
                    true
                );

            }
        );


        /* =================================================
           CANCELAR
        ================================================= */

        modal.addEventListener(
            'click',
            (evento) => {

                const cerrar =
                    evento.target.closest(
                        '[data-cerrar-modal-confirmacion]'
                    );


                if (!cerrar) {
                    return;
                }


                cerrarConfirmacion(
                    false
                );

            }
        );


        /* =================================================
           ESCAPE
        ================================================= */

        document.addEventListener(
            'keydown',
            (evento) => {

                if (
                    evento.key !== 'Escape'
                    || !modal.classList.contains(
                        'modal-reporte--visible'
                    )
                ) {
                    return;
                }


                cerrarConfirmacion(
                    false
                );

            }
        );

    }
);