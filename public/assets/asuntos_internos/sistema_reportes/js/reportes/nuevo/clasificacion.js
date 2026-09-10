/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   CLASIFICACIÓN
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarClasificacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarClasificacion() {

    const selector =
        document.querySelector(
            '#clasificacion-select'
        );


    const textoSelector =
        document.querySelector(
            '#clasificacion-select-texto'
        );


    const inputClasificacion =
        document.querySelector(
            '#clasificacion'
        );


    const resultados =
        document.querySelector(
            '#clasificacion-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-clasificacion-opcion]'
        );


    if (
        !selector
        || !textoSelector
        || !inputClasificacion
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       ABRIR
    ===================================================== */

    function abrirCatalogo() {

        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'clasificacion-select--activo'
        );
    }


    /* =====================================================
       CERRAR
    ===================================================== */

    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'clasificacion-select--activo'
        );
    }


    /* =====================================================
       SELECCIONAR
    ===================================================== */

    function seleccionarClasificacion(
        valor
    ) {

        const nombre =
            String(
                valor
                || ''
            ).trim();


        if (
            nombre === ''
        ) {
            return;
        }


        inputClasificacion.value =
            nombre;


        textoSelector.textContent =
            nombre;


        cerrarCatalogo();
    }


    /* =====================================================
       SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            if (
                resultados.hidden
            ) {

                abrirCatalogo();

            } else {

                cerrarCatalogo();
            }
        }
    );


    /* =====================================================
       OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    seleccionarClasificacion(
                        opcion.dataset.clasificacionNombre
                    );
                }
            );
        }
    );


    /* =====================================================
       CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                selector.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarCatalogo();
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
            ) {

                cerrarCatalogo();
            }
        }
    );
}