/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   CLASIFICACIÓN
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarClasificacion(
    modal
) {

    if (!modal) {
        return;
    }


    /*
     * Evitamos registrar los eventos más de una vez.
     */
    if (
        modal.dataset.editarClasificacionInicializada
        === '1'
    ) {
        return;
    }


    modal.dataset.editarClasificacionInicializada =
        '1';


    const selector =
        modal.querySelector(
            '#editar-clasificacion-select'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-clasificacion-select-texto'
        );


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    const resultados =
        modal.querySelector(
            '#editar-clasificacion-resultados'
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
       ABRIR / CERRAR SELECTOR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            if (selector.disabled) {
                return;
            }


            const estaAbierto =
                !resultados.hidden;


            if (estaAbierto) {

                cerrarClasificacionEditar(
                    modal
                );

                return;
            }


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
    );


    /* =====================================================
       SELECCIONAR OPCIÓN
    ===================================================== */

    resultados.addEventListener(
        'click',
        (evento) => {

            const opcion =
                evento.target.closest(
                    '[data-editar-clasificacion-opcion]'
                );


            if (!opcion) {
                return;
            }


            const nombre =
                String(
                    opcion.dataset.clasificacionNombre
                    || ''
                ).trim();


            if (nombre === '') {
                return;
            }


            seleccionarClasificacionEditar(
                modal,
                nombre
            );
        }
    );


    /* =====================================================
       CERRAR AL HACER CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            const objetivo =
                evento.target;


            if (
                selector.contains(
                    objetivo
                )
                || resultados.contains(
                    objetivo
                )
            ) {
                return;
            }


            cerrarClasificacionEditar(
                modal
            );
        }
    );


    /* =====================================================
       CERRAR CON ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Escape'
            ) {
                return;
            }


            cerrarClasificacionEditar(
                modal
            );
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarClasificacionEditar(
        modal
    );
}


/* =========================================================
   SELECCIONAR CLASIFICACIÓN
========================================================= */

export function seleccionarClasificacionEditar(
    modal,
    valor
) {

    if (!modal) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-clasificacion-select-texto'
        );


    const nombre =
        String(
            valor
            || ''
        ).trim();


    if (nombre === '') {
        return;
    }


    if (inputClasificacion) {

        inputClasificacion.value =
            nombre;
    }


    if (textoSelector) {

        textoSelector.textContent =
            nombre;
    }


    cerrarClasificacionEditar(
        modal
    );
}


/* =========================================================
   ACTUALIZAR INTERFAZ
========================================================= */

export function actualizarClasificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-clasificacion-select-texto'
        );


    if (
        !inputClasificacion
        || !textoSelector
    ) {
        return;
    }


    const valor =
        String(
            inputClasificacion.value
            || ''
        ).trim();


    textoSelector.textContent =
        valor !== ''
            ? valor
            : 'Selecciona una clasificación';
}


/* =========================================================
   CARGAR CLASIFICACIÓN EXISTENTE
========================================================= */

export function cargarClasificacionEditar(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    if (!inputClasificacion) {
        return;
    }


    const clasificacion =
        String(
            reporte.clasificacion
            || ''
        ).trim();


    inputClasificacion.value =
        clasificacion;


    actualizarClasificacionEditar(
        modal
    );
}


/* =========================================================
   LIMPIAR
========================================================= */

export function limpiarClasificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const inputClasificacion =
        modal.querySelector(
            '#editar-clasificacion'
        );


    if (inputClasificacion) {

        inputClasificacion.value =
            '';
    }


    actualizarClasificacionEditar(
        modal
    );


    cerrarClasificacionEditar(
        modal
    );
}


/* =========================================================
   CERRAR CATÁLOGO
========================================================= */

function cerrarClasificacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const selector =
        modal.querySelector(
            '#editar-clasificacion-select'
        );


    const resultados =
        modal.querySelector(
            '#editar-clasificacion-resultados'
        );


    if (resultados) {

        resultados.hidden =
            true;
    }


    if (selector) {

        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'clasificacion-select--activo'
        );
    }
}