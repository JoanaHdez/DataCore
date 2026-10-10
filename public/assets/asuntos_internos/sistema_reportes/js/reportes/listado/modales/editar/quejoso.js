import {
    asignarValorEditar,
} from './utilidades.js';


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   EDITAR REPORTE
   DATOS DEL QUEJOSO
========================================================= */


/* =========================================================
   INICIALIZAR
========================================================= */

function ajustarAlturaCatalogoCanalizacionEditar(
    resultados
) {

    if (!resultados) {
        return;
    }


    const opciones =
        Array.from(
            resultados.querySelectorAll(
                '.canalizacion-resultados__item'
            )
        )
        .filter(
            (opcion) => !opcion.hidden
        );


    if (
        opciones.length <= 4
    ) {

        resultados.style.maxHeight =
            '';

        resultados.classList.remove(
            'canalizacion-resultados--scroll'
        );

        return;
    }


    const alturaPrimerasOpciones =
        opciones
            .slice(
                0,
                4
            )
            .reduce(
                (total, opcion) => total
                    + opcion.getBoundingClientRect().height,
                0
            );


    resultados.style.maxHeight =
        `${Math.ceil(alturaPrimerasOpciones + 2)}px`;


    resultados.classList.add(
        'canalizacion-resultados--scroll'
    );
}


function seleccionarTextoCanalizacionEditar(
    elemento
) {

    if (!elemento) {
        return;
    }


    const seleccion =
        window.getSelection();


    if (!seleccion) {
        return;
    }


    const rango =
        document.createRange();


    rango.selectNodeContents(
        elemento
    );


    seleccion.removeAllRanges();


    seleccion.addRange(
        rango
    );
}


function obtenerPosicionCursorCanalizacionEditar(
    elemento
) {

    const seleccion =
        window.getSelection();


    if (
        !seleccion
        || seleccion.rangeCount === 0
    ) {
        return 0;
    }


    const rango =
        seleccion.getRangeAt(
            0
        );


    const previo =
        rango.cloneRange();


    previo.selectNodeContents(
        elemento
    );


    previo.setEnd(
        rango.endContainer,
        rango.endOffset
    );


    return previo.toString().length;
}


function restaurarCursorCanalizacionEditar(
    elemento,
    posicion
) {

    const texto =
        elemento.firstChild;


    if (!texto) {
        return;
    }


    const seleccion =
        window.getSelection();


    if (!seleccion) {
        return;
    }


    const rango =
        document.createRange();


    rango.setStart(
        texto,
        Math.min(
            posicion,
            texto.textContent.length
        )
    );


    rango.collapse(
        true
    );


    seleccion.removeAllRanges();


    seleccion.addRange(
        rango
    );
}


function convertirBusquedaCanalizacionEditarAMayusculas(
    elemento
) {

    const texto =
        elemento.textContent
        || '';


    const mayusculas =
        texto.toLocaleUpperCase(
            'es-MX'
        );


    if (
        texto === mayusculas
    ) {
        return mayusculas;
    }


    const posicion =
        obtenerPosicionCursorCanalizacionEditar(
            elemento
        );


    elemento.textContent =
        mayusculas;


    restaurarCursorCanalizacionEditar(
        elemento,
        posicion
    );


    return mayusculas;
}


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


    const textoCanalizacion =
        modal.querySelector(
            '#editar-canalizacion-select-texto'
        );


    const inputCanalizacion =
        modal.querySelector(
            '#editar-canalizacion'
        );


    let busquedaCanalizacionActiva =
        false;


    if (
        botonCanalizacion
        && resultadosCanalizacion
        && textoCanalizacion
        && inputCanalizacion
    ) {

        const opcionesCanalizacion =
            Array.from(
                resultadosCanalizacion.querySelectorAll(
                    '[data-editar-canalizacion-opcion]'
                )
            );


        const mensajeSinCoincidencias =
            document.createElement(
                'div'
            );


        mensajeSinCoincidencias.className =
            'canalizacion-resultados__vacio';


        mensajeSinCoincidencias.textContent =
            'Sin coincidencias';


        mensajeSinCoincidencias.hidden =
            true;


        resultadosCanalizacion.appendChild(
            mensajeSinCoincidencias
        );


        const obtenerTextoSeleccionado = () => {

            const valor =
                String(
                    inputCanalizacion.value
                    || ''
                ).trim();


            return valor !== ''
                ? valor
                : 'Sin canalización';
        };


        const filtrarCanalizaciones = (
            busqueda
        ) => {

            const termino =
                normalizarTextoCanalizacion(
                    busqueda
                );


            let totalVisibles =
                0;


            opcionesCanalizacion.forEach(
                (opcion) => {

                    const nombre =
                        opcion.dataset.canalizacionNombre
                        || opcion.textContent
                        || '';


                    const coincide =
                        termino === ''
                        || normalizarTextoCanalizacion(
                            nombre
                        ).includes(
                            termino
                        );


                    opcion.hidden =
                        !coincide;


                    if (coincide) {
                        totalVisibles += 1;
                    }
                }
            );


            mensajeSinCoincidencias.hidden =
                totalVisibles > 0;


            ajustarAlturaCatalogoCanalizacionEditar(
                resultadosCanalizacion
            );
        };


        const abrirCanalizacion = (
            busqueda = ''
        ) => {

            resultadosCanalizacion.hidden =
                false;


            botonCanalizacion.classList.add(
                'canalizacion-select--activo'
            );


            botonCanalizacion.setAttribute(
                'aria-expanded',
                'true'
            );


            filtrarCanalizaciones(
                busqueda
            );
        };


        const cerrarCanalizacion = (
            restaurarTexto = true
        ) => {

            resultadosCanalizacion.hidden =
                true;


            botonCanalizacion.classList.remove(
                'canalizacion-select--activo'
            );


            botonCanalizacion.setAttribute(
                'aria-expanded',
                'false'
            );


            if (restaurarTexto) {

                textoCanalizacion.textContent =
                    obtenerTextoSeleccionado();


                busquedaCanalizacionActiva =
                    false;


                filtrarCanalizaciones(
                    ''
                );
            }
        };

        botonCanalizacion.addEventListener(
            'click',
            (evento) => {

                if (
                    evento.target === textoCanalizacion
                ) {

                    abrirCanalizacion();

                    requestAnimationFrame(
                        () => seleccionarTextoCanalizacionEditar(
                            textoCanalizacion
                        )
                    );

                    return;
                }

                const estaAbierto =
                    !resultadosCanalizacion.hidden;


                if (estaAbierto) {

                    cerrarCanalizacion(
                        true
                    );

                } else {

                    abrirCanalizacion();
                }
            }
        );


        textoCanalizacion.addEventListener(
            'focus',
            () => {

                abrirCanalizacion();


                requestAnimationFrame(
                    () => seleccionarTextoCanalizacionEditar(
                        textoCanalizacion
                    )
                );
            }
        );


        textoCanalizacion.addEventListener(
            'keydown',
            (evento) => {

                if (
                    evento.key === 'Enter'
                ) {

                    evento.preventDefault();
                }
            }
        );


        textoCanalizacion.addEventListener(
            'click',
            () => {

                requestAnimationFrame(
                    () => seleccionarTextoCanalizacionEditar(
                        textoCanalizacion
                    )
                );
            }
        );


        textoCanalizacion.addEventListener(
            'input',
            () => {

                const busqueda =
                    convertirBusquedaCanalizacionEditarAMayusculas(
                        textoCanalizacion
                    );


                busquedaCanalizacionActiva =
                    true;


                abrirCanalizacion(
                    busqueda
                );


                filtrarCanalizaciones(
                    busqueda
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


                cerrarCanalizacion(
                    false
                );


                busquedaCanalizacionActiva =
                    false;
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


            if (
                textoCanalizacion
                && inputCanalizacion
            ) {

                const valor =
                    String(
                        inputCanalizacion.value
                        || ''
                    ).trim();


                textoCanalizacion.textContent =
                    valor !== ''
                        ? valor
                        : 'Sin canalización';


                busquedaCanalizacionActiva =
                    false;
            }
        }
    );


    /* =====================================================
    CATÁLOGO DE GÉNERO
    ===================================================== */

    inicializarGeneroEditar(
        modal
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarEstadoQuejosoEditar(
        modal
    );
}


/* =========================================================
   CATÁLOGO DE GÉNERO - EDITAR
========================================================= */

function inicializarGeneroEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       ELEMENTOS
    ===================================================== */

    const input =
        modal.querySelector(
            '#editar-genero'
        );


    const boton =
        modal.querySelector(
            '#editar-genero-select'
        );


    const texto =
        modal.querySelector(
            '#editar-genero-select-texto'
        );


    const resultados =
        modal.querySelector(
            '#editar-genero-resultados'
        );


    if (
        !input
        || !boton
        || !texto
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       EVITAR LISTENERS DUPLICADOS
    ===================================================== */

    if (
        boton.dataset
            .generoInicializado
        === '1'
    ) {
        return;
    }


    boton.dataset
        .generoInicializado =
        '1';


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    boton.addEventListener(
        'click',
        () => {

            if (boton.disabled) {
                return;
            }


            const estaAbierto =
                !resultados.hidden;


            resultados.hidden =
                estaAbierto;


            boton.classList.toggle(
                'genero-select--activo',
                !estaAbierto
            );


            boton.setAttribute(
                'aria-expanded',
                !estaAbierto
                    ? 'true'
                    : 'false'
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
                    '[data-editar-genero-opcion]'
                );


            if (!opcion) {
                return;
            }


            const valor =
                String(
                    opcion.dataset.genero
                    || ''
                ).trim();


            input.value =
                valor;


            texto.textContent =
                valor !== ''
                    ? valor
                    : 'Selecciona una opción';


            cerrarGeneroEditar(
                modal
            );


            input.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles:
                            true,
                    }
                )
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
                boton.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarGeneroEditar(
                modal
            );
        }
    );


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key
                !== 'Escape'
            ) {
                return;
            }


            cerrarGeneroEditar(
                modal
            );
        }
    );
}


/* =========================================================
   CERRAR CATÁLOGO DE GÉNERO
========================================================= */

function cerrarGeneroEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const resultados =
        modal.querySelector(
            '#editar-genero-resultados'
        );


    const boton =
        modal.querySelector(
            '#editar-genero-select'
        );


    if (resultados) {

        resultados.hidden =
            true;
    }


    if (boton) {

        boton.classList.remove(
            'genero-select--activo'
        );


        boton.setAttribute(
            'aria-expanded',
            'false'
        );
    }
}


/* =========================================================
   ACTUALIZAR INTERFAZ DE GÉNERO
========================================================= */

function actualizarGeneroEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const input =
        modal.querySelector(
            '#editar-genero'
        );


    const texto =
        modal.querySelector(
            '#editar-genero-select-texto'
        );


    if (
        !input
        || !texto
    ) {
        return;
    }


    const valor =
        String(
            input.value
            || ''
        ).trim();


    texto.textContent =
        valor !== ''
            ? valor
            : 'Selecciona una opción';
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

        {
            selector:
                '#editar-calle-quejoso',

            requerido:
                false,
        },

        {
            selector:
                '#editar-numero-quejoso',

            requerido:
                false,
        },

        {
            selector:
                '#editar-colonia-quejoso',

            requerido:
                false,
        },

        {
            selector:
                '#editar-municipio-quejoso',

            requerido:
                false,
        },

        {
            selector:
                '#editar-estado-quejoso',

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
       SELECTOR VISUAL DE GÉNERO
    ===================================================== */

    const botonGenero =
        modal.querySelector(
            '#editar-genero-select'
        );


    if (botonGenero) {

        botonGenero.disabled =
            esAnonimo;


        if (esAnonimo) {

            cerrarGeneroEditar(
                modal
            );
        }
    }


    /* =====================================================
       DIRECCIÓN PARA NOTIFICACIÓN
    ===================================================== */

    const seccionNotificacion =
        modal.querySelector(
            '#editar-seccion-direccion-notificacion'
        );


    const advertenciaForaneo =
        modal.querySelector(
            '#editar-notificacion-advertencia-foraneo'
        );


    const mapaNotificacion =
        modal.querySelector(
            '#editar-notificacion-mapa-ubicacion'
        );


    /*
     * IMPORTANTE:
     *
     * Ya NO agregamos ninguna clase visual al contenedor
     * completo de Dirección para notificación.
     *
     * La sección conserva:
     * - fondo blanco
     * - header normal
     * - borde normal
     * - sombra normal
     *
     * Solamente se deshabilitan sus controles.
     */

    const controlesNotificacion =
        seccionNotificacion
            ? Array.from(
                seccionNotificacion.querySelectorAll(
                    'input, select, textarea, button'
                )
            )
            : [];


    /* =====================================================
       CONTROLES DE DIRECCIÓN PARA NOTIFICACIÓN
    ===================================================== */

    controlesNotificacion.forEach(
        (control) => {

            if (!control) {
                return;
            }


            /*
             * Los controles internos de Google Maps
             * no se modifican individualmente.
             *
             * El mapa se bloquea después mediante
             * pointer-events.
             */

            if (
                mapaNotificacion
                && mapaNotificacion.contains(
                    control
                )
            ) {

                return;
            }


            /* =================================================
               GUARDAR ESTADO ORIGINAL
            ================================================= */

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


            /* =================================================
               ANÓNIMO
            ================================================= */

            if (esAnonimo) {

                control.disabled =
                    true;


                control.required =
                    false;

            } else {

                /* =================================================
                   RESTAURAR ESTADO ORIGINAL
                ================================================= */

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


    /* =====================================================
       MAPA DE DIRECCIÓN PARA NOTIFICACIÓN
    ===================================================== */

    if (mapaNotificacion) {

        if (esAnonimo) {

            mapaNotificacion.style.pointerEvents =
                'none';


            mapaNotificacion.setAttribute(
                'aria-disabled',
                'true'
            );

        } else {

            mapaNotificacion.style.removeProperty(
                'pointer-events'
            );


            mapaNotificacion.removeAttribute(
                'aria-disabled'
            );
        }
    }


    /* =====================================================
       OCULTAR ADVERTENCIA SI ES ANÓNIMO
    ===================================================== */

    if (
        advertenciaForaneo
        && esAnonimo
    ) {

        advertenciaForaneo.hidden =
            true;
    }


    /* =====================================================
       CANALIZACIÓN

       La canalización es independiente de que
       la queja sea anónima o no.
    ===================================================== */

    const botonCanalizacion =
        modal.querySelector(
            '#editar-canalizacion-select'
        );


    if (botonCanalizacion) {

        botonCanalizacion.disabled =
            false;
    }


    /*
     * Conservamos y mostramos la canalización
     * que ya tenga registrada el reporte.
     */

    actualizarCanalizacionEditar(
        modal
    );
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
       DATOS PERSONALES
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-quejoso',
        reporte.quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-edad',
        reporte.edad
    );


    /* =====================================================
       GÉNERO
    ===================================================== */

    const genero =
        modal.querySelector(
            '#editar-genero'
        );


    if (genero) {

        genero.value =
            String(
                reporte.genero
                || ''
            ).trim();
    }


    actualizarGeneroEditar(
        modal
    );


    asignarValorEditar(
        modal,
        '#editar-telefono',
        reporte.telefono
    );


    asignarValorEditar(
        modal,
        '#editar-correo',
        reporte.correo
    );


    /* =====================================================
       DIRECCIÓN DEL QUEJOSO
    ===================================================== */

    asignarValorEditar(
        modal,
        '#editar-calle-quejoso',
        reporte.calle_quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-numero-quejoso',
        reporte.numero_quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-colonia-quejoso',
        reporte.colonia_quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-municipio-quejoso',
        reporte.municipio_quejoso
    );


    asignarValorEditar(
        modal,
        '#editar-estado-quejoso',
        reporte.estado_quejoso
    );


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
       DIRECCIÓN DEL QUEJOSO
    ===================================================== */

    const camposDireccionQuejoso = [

        '#editar-calle-quejoso',
        '#editar-numero-quejoso',
        '#editar-colonia-quejoso',
        '#editar-municipio-quejoso',
        '#editar-estado-quejoso',

    ];


    camposDireccionQuejoso.forEach(
        (selector) => {

            const campo =
                modal.querySelector(
                    selector
                );


            if (!campo) {
                return;
            }


            campo.value =
                '';
        }
    );


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
