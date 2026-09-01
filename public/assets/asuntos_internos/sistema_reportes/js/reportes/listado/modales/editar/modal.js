/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Modal
========================================================= */

import {
    limpiarReporteActual,
} from './estado.js';


/* =========================================================
   INICIALIZAR MODAL
========================================================= */

export function inicializarModalEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       NAVEGACIÓN
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonSeccion =
                evento.target.closest(
                    '[data-editar-seccion]'
                );


            if (botonSeccion) {

                mostrarSeccionEditar(
                    modal,
                    botonSeccion
                        .dataset
                        .editarSeccion
                );


                return;
            }


            /* =================================================
               CERRAR
            ================================================= */

            const botonCerrar =
                evento.target.closest(
                    '[data-cerrar-modal-editar]'
                );


            if (!botonCerrar) {
                return;
            }


            cerrarModalEditar(
                modal
            );

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

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


            cerrarModalEditar(
                modal
            );

        }
    );

}


/* =========================================================
   MOSTRAR SECCIÓN
========================================================= */

export function mostrarSeccionEditar(
    modal,
    seccion
) {

    const botones =
        modal.querySelectorAll(
            '[data-editar-seccion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-editar-panel]'
        );


    /* =====================================================
       BOTONES
    ===================================================== */

    botones.forEach(
        (boton) => {

            const activo =
                boton
                    .dataset
                    .editarSeccion
                === seccion;


            boton.classList.toggle(
                'detalle-reporte-nav__item--active',
                activo
            );

        }
    );


    /* =====================================================
       PANELES
    ===================================================== */

    paneles.forEach(
        (panel) => {

            const activo =
                panel
                    .dataset
                    .editarPanel
                === seccion;


            panel.classList.toggle(
                'detalle-reporte-seccion--active',
                activo
            );

        }
    );

    /* =====================================================
    ACTUALIZAR MAPA DE UBICACIÓN
    ===================================================== */

    if (
        seccion === 'hechos'
    ) {

        window.dispatchEvent(
            new CustomEvent(
                'editar:ubicacion-visible'
            )
        );

    }

    /* =====================================================
       REINICIAR SCROLL
    ===================================================== */

    const body =
        modal.querySelector(
            '.modal-reporte__body--editar'
        );


    if (body) {

        body.scrollTo({
            top: 0,
            behavior: 'smooth',
        });

    }

}


/* =========================================================
   ABRIR
========================================================= */

export function abrirModalEditar(
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
   CERRAR
========================================================= */

export function cerrarModalEditar(
    modal
) {

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


    limpiarReporteActual();

}