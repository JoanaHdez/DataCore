/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   NAVEGACIÓN
========================================================= */


/* =========================================================
   INICIALIZAR NAVEGACIÓN
========================================================= */

export function inicializarNavegacionEditar(
    modal
) {

    const botonesSeccion =
        modal.querySelectorAll(
            '[data-seccion-editar-felicitacion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-panel-editar-felicitacion]'
        );


    if (
        botonesSeccion.length === 0
        || paneles.length === 0
    ) {
        return;
    }


    botonesSeccion.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                () => {

                    const seccion =
                        String(
                            boton.dataset
                                .seccionEditarFelicitacion
                            || ''
                        ).trim();


                    if (
                        seccion === ''
                    ) {
                        return;
                    }


                    /* =========================================
                       BOTONES
                    ========================================== */

                    botonesSeccion.forEach(
                        (botonActual) => {

                            const activo =
                                botonActual.dataset
                                    .seccionEditarFelicitacion
                                === seccion;


                            botonActual.classList.toggle(
                                'modal-felicitacion-editar__nav-btn--activo',
                                activo
                            );


                            botonActual.setAttribute(
                                'aria-selected',
                                activo
                                    ? 'true'
                                    : 'false'
                            );
                        }
                    );


                    /* =========================================
                       PANELES
                    ========================================== */

                    paneles.forEach(
                        (panel) => {

                            const activo =
                                panel.dataset
                                    .panelEditarFelicitacion
                                === seccion;


                            panel.hidden =
                                !activo;


                            panel.classList.toggle(
                                'modal-felicitacion-editar__panel--activo',
                                activo
                            );
                        }
                    );


                    /* =========================================
                       REGRESAR SCROLL ARRIBA
                    ========================================== */

                    const body =
                        modal.querySelector(
                            '.modal-felicitacion-editar__body'
                        );


                    if (body) {

                        body.scrollTop =
                            0;
                    }
                }
            );
        }
    );
}