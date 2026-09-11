/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   DATOS DEL QUEJOSO
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarQuejoso(
    modal
) {

    if (!modal) {
        return;
    }


    /*
     * Evitamos registrar los eventos
     * más de una vez.
     */

    if (
        modal.dataset
            .editarQuejosoInicializado
        === '1'
    ) {
        return;
    }


    modal.dataset
        .editarQuejosoInicializado =
        '1';


    /* =====================================================
       CAMBIOS GENERALES
    ===================================================== */

    modal.addEventListener(
        'change',
        (evento) => {

            const elemento =
                evento.target;


            if (!elemento) {
                return;
            }


            /* =================================================
               ANÓNIMO - NO
            ================================================= */

            if (
                elemento.id ===
                'editar-anonimo-no'
            ) {

                if (!elemento.checked) {
                    return;
                }


                establecerAnonimoEditar(
                    modal,
                    false
                );


                return;
            }


            /* =================================================
               ANÓNIMO - SÍ
            ================================================= */

            if (
                elemento.id ===
                'editar-anonimo-si'
            ) {

                if (!elemento.checked) {
                    return;
                }


                establecerAnonimoEditar(
                    modal,
                    true
                );


                return;
            }

        }
    );


    /* =====================================================
       CANALIZACIÓN - ABRIR / CERRAR
    ===================================================== */

    const botonCanalizacion =
        modal.querySelector(
            '#editar-canalizacion-select'
        );


    const resultadosCanalizacion =
        modal.querySelector(
            '#editar-canalizacion-resultados'
        );


    if (
        botonCanalizacion
        && resultadosCanalizacion
    ) {

        botonCanalizacion.addEventListener(
            'click',
            () => {

                const estaAbierto =
                    !resultadosCanalizacion.hidden;


                resultadosCanalizacion.hidden =
                    estaAbierto;


                botonCanalizacion.classList.toggle(
                    'canalizacion-select--activo',
                    !estaAbierto
                );


                botonCanalizacion.setAttribute(
                    'aria-expanded',
                    !estaAbierto
                        ? 'true'
                        : 'false'
                );
            }
        );


        /* =================================================
           SELECCIONAR OPCIÓN
        ================================================= */

        resultadosCanalizacion.addEventListener(
            'click',
            (evento) => {

                const opcion =
                    evento.target.closest(
                        '[data-editar-canalizacion-opcion]'
                    );


                if (!opcion) {
                    return;
                }


                seleccionarCanalizacionEditar(
                    modal,
                    opcion.dataset.canalizacionNombre
                );
            }
        );

    }


    /* =====================================================
       CERRAR CATÁLOGO AL HACER CLICK FUERA
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            if (
                !botonCanalizacion
                || !resultadosCanalizacion
            ) {
                return;
            }


            const objetivo =
                evento.target;


            if (
                botonCanalizacion.contains(
                    objetivo
                )
                || resultadosCanalizacion.contains(
                    objetivo
                )
            ) {
                return;
            }


            resultadosCanalizacion.hidden =
                true;


            botonCanalizacion.classList.remove(
                'canalizacion-select--activo'
            );


            botonCanalizacion.setAttribute(
                'aria-expanded',
                'false'
            );
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarEstadoQuejosoEditar(
        modal
    );
}


/* =========================================================
   ACTUALIZAR ESTADO GENERAL
========================================================= */

export function actualizarEstadoQuejosoEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const anonimoSi =
        modal.querySelector(
            '#editar-anonimo-si'
        );


    establecerAnonimoEditar(
        modal,
        Boolean(
            anonimoSi?.checked
        )
    );


    actualizarCanalizacionEditar(
        modal
    );
}


/* =========================================================
   ESTABLECER ANÓNIMO
========================================================= */

export function establecerAnonimoEditar(
    modal,
    esAnonimo
) {

    if (!modal) {
        return;
    }


    const anonimoNo =
        modal.querySelector(
            '#editar-anonimo-no'
        );


    const anonimoSi =
        modal.querySelector(
            '#editar-anonimo-si'
        );


    const contenedorNumero =
        modal.querySelector(
            '#editar-numero-anonimo-contenedor'
        );


    const numeroAnonimo =
        modal.querySelector(
            '#editar-numero-anonimo'
        );


    /* =====================================================
       RADIOS
    ===================================================== */

    if (anonimoNo) {

        anonimoNo.checked =
            !esAnonimo;
    }


    if (anonimoSi) {

        anonimoSi.checked =
            esAnonimo;
    }


    /* =====================================================
       NÚMERO ANÓNIMO
    ===================================================== */

    if (contenedorNumero) {

        if (esAnonimo) {

            contenedorNumero.hidden =
                false;


            contenedorNumero.style.removeProperty(
                'display'
            );

        } else {

            contenedorNumero.hidden =
                true;


            contenedorNumero.style.setProperty(
                'display',
                'none',
                'important'
            );
        }

    }


    if (numeroAnonimo) {

        numeroAnonimo.required =
            esAnonimo;


        numeroAnonimo.disabled =
            !esAnonimo;
    }


    /* =====================================================
       DATOS PERSONALES
    ===================================================== */

    const camposPersonales = [

        {
            selector:
                '#editar-quejoso',

            requerido:
                true,
        },

        {
            selector:
                '#editar-edad',

            requerido:
                true,
        },

        {
            selector:
                '#editar-genero',

            requerido:
                true,
        },

        {
            selector:
                '#editar-telefono',

            requerido:
                false,
        },

        {
            selector:
                '#editar-correo',

            requerido:
                false,
        },

    ];


    camposPersonales.forEach(
        (configuracion) => {

            const campo =
                modal.querySelector(
                    configuracion.selector
                );


            if (!campo) {
                return;
            }


            campo.disabled =
                esAnonimo;


            campo.required =
                configuracion.requerido
                && !esAnonimo;
        }
    );


    /* =====================================================
       CANALIZACIÓN EN QUEJA ANÓNIMA

       Cuando la queja es anónima,
       la canalización no debe enviarse.
    ===================================================== */

    const botonCanalizacion =
        modal.querySelector(
            '#editar-canalizacion-select'
        );


    const inputCanalizacion =
        modal.querySelector(
            '#editar-canalizacion'
        );


    if (botonCanalizacion) {

        botonCanalizacion.disabled =
            esAnonimo;
    }


    if (esAnonimo) {

        cerrarCanalizacionEditar(
            modal
        );


        ocultarCanalizacionOtroEditar(
            modal
        );

    } else {

        actualizarCanalizacionEditar(
            modal
        );
    }
}


/* =========================================================
   SELECCIONAR CANALIZACIÓN
========================================================= */

function seleccionarCanalizacionEditar(
    modal,
    nombre
) {

    if (!modal) {
        return;
    }


    const input =
        modal.querySelector(
            '#editar-canalizacion'
        );


    const texto =
        modal.querySelector(
            '#editar-canalizacion-select-texto'
        );


    const valor =
        String(
            nombre
            ?? ''
        ).trim();


    /* =====================================================
       VALOR REAL
    ===================================================== */

    if (input) {

        input.value =
            valor;
    }


    /* =====================================================
       TEXTO VISIBLE
    ===================================================== */

    if (texto) {

        texto.textContent =
            valor !== ''
                ? valor
                : 'Sin canalización';
    }


    /* =====================================================
       CERRAR CATÁLOGO
    ===================================================== */

    cerrarCanalizacionEditar(
        modal
    );


    /* =====================================================
       MOSTRAR / OCULTAR OTRO
    ===================================================== */

    actualizarCanalizacionEditar(
        modal
    );
}


/* =========================================================
   ACTUALIZAR CANALIZACIÓN
========================================================= */

export function actualizarCanalizacionEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const input =
        modal.querySelector(
            '#editar-canalizacion'
        );


    const contenedor =
        modal.querySelector(
            '#editar-canalizacion-otro-contenedor'
        );


    const otro =
        modal.querySelector(
            '#editar-canalizacion-otro'
        );


    const texto =
        modal.querySelector(
            '#editar-canalizacion-select-texto'
        );


    if (
        !input
        || !contenedor
        || !otro
    ) {
        return;
    }


    /* =====================================================
       VALOR ACTUAL
    ===================================================== */

    const valor =
        String(
            input.value
            || ''
        ).trim();


    const valorNormalizado =
        normalizarTextoCanalizacion(
            valor
        );


    const esOtro =
        valorNormalizado === 'OTRO';


    /* =====================================================
       TEXTO DEL SELECTOR
    ===================================================== */

    if (texto) {

        texto.textContent =
            valor !== ''
                ? valor
                : 'Sin canalización';
    }


    /* =====================================================
       OTRO
    ===================================================== */

    if (esOtro) {

        contenedor.hidden =
            false;


        contenedor.style.removeProperty(
            'display'
        );


        otro.disabled =
            false;


        otro.required =
            true;

    } else {

        ocultarCanalizacionOtroEditar(
            modal
        );
    }
}


/* =========================================================
   OCULTAR OTRA CANALIZACIÓN
========================================================= */

function ocultarCanalizacionOtroEditar(
    modal
) {

    const contenedor =
        modal.querySelector(
            '#editar-canalizacion-otro-contenedor'
        );


    const otro =
        modal.querySelector(
            '#editar-canalizacion-otro'
        );


    if (contenedor) {

        contenedor.hidden =
            true;


        contenedor.style.setProperty(
            'display',
            'none',
            'important'
        );
    }


    if (otro) {

        otro.required =
            false;


        otro.disabled =
            true;
    }
}


/* =========================================================
   CERRAR CATÁLOGO
========================================================= */

function cerrarCanalizacionEditar(
    modal
) {

    const resultados =
        modal.querySelector(
            '#editar-canalizacion-resultados'
        );


    const boton =
        modal.querySelector(
            '#editar-canalizacion-select'
        );


    if (resultados) {

        resultados.hidden =
            true;
    }


    if (boton) {

        boton.classList.remove(
            'canalizacion-select--activo'
        );


        boton.setAttribute(
            'aria-expanded',
            'false'
        );
    }
}


/* =========================================================
   NORMALIZAR TEXTO
========================================================= */

function normalizarTextoCanalizacion(
    valor
) {

    return String(
        valor
        || ''
    )
        .trim()
        .normalize(
            'NFD'
        )
        .replace(
            /[\u0300-\u036f]/g,
            ''
        )
        .toUpperCase();
}


/* =========================================================
   CARGAR DATOS EXISTENTES
========================================================= */

export function cargarQuejosoEditar(
    modal,
    reporte
) {

    if (
        !modal
        || !reporte
    ) {
        return;
    }


    /* =====================================================
       ANÓNIMO
    ===================================================== */

    const esAnonimo =
        Number(
            reporte.es_anonimo
            ?? reporte.anonimo
            ?? 0
        ) === 1;


    /* =====================================================
       NÚMERO ANÓNIMO
    ===================================================== */

    const numeroAnonimo =
        modal.querySelector(
            '#editar-numero-anonimo'
        );


    if (numeroAnonimo) {

        numeroAnonimo.value =
            String(
                reporte.numero_anonimo
                ?? reporte.no_numerico
                ?? ''
            ).trim();
    }


    /* =====================================================
       CANALIZACIÓN
    ===================================================== */

    const canalizacion =
        modal.querySelector(
            '#editar-canalizacion'
        );


    /*
     * En BD el campo real es canalizacion_area.
     * Conservamos fallbacks para no romper datos previos.
     */

    const valorCanalizacion =
        String(
            reporte.canalizacion_area
            ?? reporte.canalizacion
            ?? ''
        ).trim();


    if (canalizacion) {

        canalizacion.value =
            valorCanalizacion;
    }


    const canalizacionOtro =
        modal.querySelector(
            '#editar-canalizacion-otro'
        );


    if (canalizacionOtro) {

        canalizacionOtro.value =
            String(
                reporte.canalizacion_otro
                ?? ''
            ).trim();
    }


    /* =====================================================
       ACTUALIZAR INTERFAZ
    ===================================================== */

    establecerAnonimoEditar(
        modal,
        esAnonimo
    );


    actualizarCanalizacionEditar(
        modal
    );
}


/* =========================================================
   LIMPIAR
========================================================= */

export function limpiarQuejosoEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       NÚMERO ANÓNIMO
    ===================================================== */

    const numeroAnonimo =
        modal.querySelector(
            '#editar-numero-anonimo'
        );


    if (numeroAnonimo) {

        numeroAnonimo.value =
            '';


        numeroAnonimo.required =
            false;


        numeroAnonimo.disabled =
            true;
    }


    /* =====================================================
       CANALIZACIÓN
    ===================================================== */

    const canalizacion =
        modal.querySelector(
            '#editar-canalizacion'
        );


    if (canalizacion) {

        canalizacion.value =
            '';
    }


    const texto =
        modal.querySelector(
            '#editar-canalizacion-select-texto'
        );


    if (texto) {

        texto.textContent =
            'Sin canalización';
    }


    const canalizacionOtro =
        modal.querySelector(
            '#editar-canalizacion-otro'
        );


    if (canalizacionOtro) {

        canalizacionOtro.value =
            '';


        canalizacionOtro.required =
            false;


        canalizacionOtro.disabled =
            true;
    }


    cerrarCanalizacionEditar(
        modal
    );


    ocultarCanalizacionOtroEditar(
        modal
    );


    /* =====================================================
       RESTABLECER QUEJOSO
    ===================================================== */

    establecerAnonimoEditar(
        modal,
        false
    );


    actualizarCanalizacionEditar(
        modal
    );
}