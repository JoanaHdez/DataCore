/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   NUEVO REPORTE
   DATOS DEL QUEJOSO
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarGeneroQuejoso();

        inicializarCanalizacionQuejoso();

        inicializarQuejosoAnonimo();
    }
);

/* =========================================================
   GÉNERO
========================================================= */

function inicializarGeneroQuejoso() {

    const selector =
        document.querySelector(
            '#genero-select'
        );


    const textoSelector =
        document.querySelector(
            '#genero-select-texto'
        );


    const inputGenero =
        document.querySelector(
            '#genero'
        );


    const resultados =
        document.querySelector(
            '#genero-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-genero-opcion]'
        );


    if (
        !selector
        || !textoSelector
        || !inputGenero
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    function abrirCatalogo() {

        if (
            selector.disabled
        ) {
            return;
        }


        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'genero-select--activo'
        );
    }


    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'genero-select--activo'
        );
    }


    /* =====================================================
       SELECCIONAR GÉNERO
    ===================================================== */

    function seleccionarGenero(
        valor
    ) {

        const genero =
            String(
                valor
                || ''
            ).trim();


        inputGenero.value =
            genero;


        textoSelector.textContent =
            genero !== ''
                ? genero
                : 'Selecciona una opción';


        cerrarCatalogo();
    }


    /* =====================================================
       CLICK SELECTOR
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

                    seleccionarGenero(
                        opcion.dataset.genero
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


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    seleccionarGenero(
        inputGenero.value
    );
}


/* =========================================================
   CANALIZACIÓN
========================================================= */

function inicializarCanalizacionQuejoso() {

    const selector =
        document.querySelector(
            '#canalizacion-select'
        );


    const textoSelector =
        document.querySelector(
            '#canalizacion-select-texto'
        );


    const inputCanalizacion =
        document.querySelector(
            '#canalizacion'
        );


    const resultados =
        document.querySelector(
            '#canalizacion-resultados'
        );


    const opciones =
        document.querySelectorAll(
            '[data-canalizacion-opcion]'
        );


    const contenedorOtro =
        document.querySelector(
            '#canalizacion-otro-contenedor'
        );


    const inputOtro =
        document.querySelector(
            '#canalizacion_otro'
        );


    if (
        !selector
        || !textoSelector
        || !inputCanalizacion
        || !resultados
        || !contenedorOtro
        || !inputOtro
    ) {
        return;
    }


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    function abrirCatalogo() {

        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'canalizacion-select--activo'
        );
    }


    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'canalizacion-select--activo'
        );
    }


    /* =====================================================
       SELECCIONAR
    ===================================================== */

    function seleccionarCanalizacion(
        valor
    ) {

        const nombre =
            String(
                valor
                || ''
            ).trim();


        inputCanalizacion.value =
            nombre;


        textoSelector.textContent =
            nombre !== ''
                ? nombre
                : 'Sin canalización';


        const esOtro =
            nombre === 'Otro';


        contenedorOtro.hidden =
            !esOtro;


        inputOtro.disabled =
            !esOtro;


        inputOtro.required =
            esOtro;


        if (
            !esOtro
        ) {

            inputOtro.value =
                '';
        }


        cerrarCatalogo();
    }


    /* =====================================================
       CLICK SELECTOR
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

                    seleccionarCanalizacion(
                        opcion.dataset.canalizacionNombre
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


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    seleccionarCanalizacion(
        ''
    );
}


/* =========================================================
   QUEJOSO ANÓNIMO
========================================================= */

function inicializarQuejosoAnonimo() {

    const radioAnonimo =
        document.querySelector(
            '#quejoso-anonimo'
        );


    const radioNoAnonimo =
        document.querySelector(
            '#quejoso-no-anonimo'
        );


    const contenedorNumero =
        document.querySelector(
            '#numero-anonimo-contenedor'
        );


    const inputNumero =
        document.querySelector(
            '#numero_anonimo'
        );


    /*
     * Solo se deshabilitan los datos personales.
     *
     * La canalización debe permanecer disponible
     * aunque la queja sea anónima.
     */
    const camposQuejoso = [

        document.querySelector(
            '#quejoso'
        ),

        document.querySelector(
            '#edad'
        ),

        document.querySelector(
            '#genero'
        ),

        document.querySelector(
            '#telefono'
        ),

        document.querySelector(
            '#correo'
        ),

        document.querySelector(
            '#direccion_quejoso'
        ),
    ];

    /* =====================================================
    DIRECCIÓN PARA NOTIFICACIÓN
    ===================================================== */

    const seccionNotificacion =
        document.querySelector(
            '#seccion-direccion-notificacion'
        );


    const advertenciaForaneo =
        document.querySelector(
            '#notificacion-advertencia-foraneo'
        );


    const controlesNotificacion =
        seccionNotificacion
            ? Array.from(
                seccionNotificacion.querySelectorAll(
                    'input, select, textarea, button'
                )
            )
            : [];

    if (
        !radioAnonimo
        || !radioNoAnonimo
        || !contenedorNumero
        || !inputNumero
    ) {
        return;
    }


    /* =====================================================
       ACTUALIZAR ESTADO
    ===================================================== */

    function actualizarEstadoAnonimo() {

        const esAnonimo =
            radioAnonimo.checked;


        /* =================================================
           NÚMERO ANÓNIMO
        ================================================= */

        contenedorNumero.hidden =
            !esAnonimo;


        inputNumero.disabled =
            !esAnonimo;


        inputNumero.required =
            esAnonimo;


        /* =================================================
           DATOS PERSONALES DEL QUEJOSO
        ================================================= */

        camposQuejoso.forEach(
            (campo) => {

                if (
                    !campo
                ) {
                    return;
                }


                campo.disabled =
                    esAnonimo;


                if (
                    esAnonimo
                ) {

                    /*
                     * Guardamos si originalmente era
                     * obligatorio antes de deshabilitarlo.
                     */
                    if (
                        campo.dataset.requiredOriginal
                        === undefined
                    ) {

                        campo.dataset.requiredOriginal =
                            campo.required
                                ? '1'
                                : '0';
                    }


                    campo.required =
                        false;

                } else {

                    /*
                     * Restauramos el required original.
                     */
                    campo.required =
                        campo.dataset.requiredOriginal
                        === '1';


                    delete campo.dataset.requiredOriginal;
                }
            }
        );


        /* =================================================
   DIRECCIÓN PARA NOTIFICACIÓN
================================================= */

        if (seccionNotificacion) {

            seccionNotificacion.classList.toggle(
                'report-section--disabled',
                esAnonimo
            );
        }


        controlesNotificacion.forEach(
            (control) => {

                if (!control) {
                    return;
                }


                /*
                 * Guardamos si originalmente estaba
                 * deshabilitado o era obligatorio.
                 */
                if (
                    control.dataset.disabledOriginal
                    === undefined
                ) {

                    control.dataset.disabledOriginal =
                        control.disabled
                            ? '1'
                            : '0';
                }


                if (
                    control.dataset.requiredOriginal
                    === undefined
                ) {

                    control.dataset.requiredOriginal =
                        control.required
                            ? '1'
                            : '0';
                }


                if (esAnonimo) {

                    control.disabled =
                        true;


                    control.required =
                        false;

                } else {

                    control.disabled =
                        control.dataset.disabledOriginal
                        === '1';


                    control.required =
                        control.dataset.requiredOriginal
                        === '1';


                    delete control.dataset.disabledOriginal;

                    delete control.dataset.requiredOriginal;
                }
            }
        );


        /* =================================================
           OCULTAR ADVERTENCIA CUANDO ES ANÓNIMO
        ================================================= */

        if (
            advertenciaForaneo
            && esAnonimo
        ) {

            advertenciaForaneo.hidden =
                true;
        }


        /* =================================================
           SI DEJA DE SER ANÓNIMO
        ================================================= */

        if (
            !esAnonimo
        ) {

            inputNumero.value =
                '';
        }
    }


    /* =====================================================
       EVENTOS
    ===================================================== */

    radioAnonimo.addEventListener(
        'change',
        actualizarEstadoAnonimo
    );


    radioNoAnonimo.addEventListener(
        'change',
        actualizarEstadoAnonimo
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarEstadoAnonimo();
}