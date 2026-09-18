import {
    mostrarResultadoYRedirigir
} from '../notificaciones/resultado.js';

import {
    inicializarDireccionNotificacion
} from './direccion_notificacion.js';

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarFormularioPorPasos();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarFormularioPorPasos() {

    const formulario =
        document.querySelector(
            '#form-nuevo-reporte'
        );


    if (!formulario) {
        return;
    }


    const pasos =
        Array.from(
            formulario.querySelectorAll(
                '.report-step'
            )
        );


    const indicadores =
        Array.from(
            formulario.querySelectorAll(
                '[data-step-indicator]'
            )
        );


    const botonAnterior =
        formulario.querySelector(
            '#btn-step-anterior'
        );


    const botonSiguiente =
        formulario.querySelector(
            '#btn-step-siguiente'
        );


    const botonGuardar =
        formulario.querySelector(
            '#btn-guardar-reporte'
        );


    if (
        !pasos.length
        || !botonAnterior
        || !botonSiguiente
        || !botonGuardar
    ) {
        return;
    }


    let pasoActual = 1;

    let guardando = false;

    let direccionNotificacionInicializada =
        false;


    const totalPasos =
        pasos.length;


    const pasosCompletados =
        new Set();


    /* =====================================================
       MOSTRAR PASO
    ===================================================== */

    function mostrarPaso(numeroPaso) {

        pasoActual =
            numeroPaso;


        /* =================================================
           CONTENIDO
        ================================================= */

        pasos.forEach(
            (paso) => {

                const numero =
                    Number(
                        paso.dataset.step
                    );


                paso.classList.toggle(
                    'report-step--active',
                    numero === pasoActual
                );

            }
        );


        /* =================================================
           DIRECCIÓN PARA NOTIFICACIÓN
    
           El mapa se inicializa únicamente cuando el
           paso 4 ya está visible.
    
           Esto evita que Google Maps intente calcular
           dimensiones mientras el contenedor está oculto.
        ================================================= */

        if (
            pasoActual === 4
            && !direccionNotificacionInicializada
        ) {

            direccionNotificacionInicializada =
                true;


            window.setTimeout(
                () => {

                    inicializarDireccionNotificacion();

                },
                100
            );
        }


        /* =================================================
           INDICADORES
        ================================================= */

        indicadores.forEach(
            (indicador) => {

                const numero =
                    Number(
                        indicador
                            .dataset
                            .stepIndicator
                    );


                indicador.classList.toggle(
                    'report-steps__item--active',
                    numero === pasoActual
                );


                indicador.classList.toggle(
                    'report-steps__item--completed',
                    pasosCompletados.has(
                        numero
                    )
                    && numero !== pasoActual
                );

            }
        );


        /* =================================================
           ANTERIOR
        ================================================= */

        botonAnterior.classList.toggle(
            'report-step-control--hidden',
            pasoActual === 1
        );


        /* =================================================
           SIGUIENTE
        ================================================= */

        botonSiguiente.classList.toggle(
            'report-step-control--hidden',
            pasoActual === totalPasos
        );


        /* =================================================
           GUARDAR
        ================================================= */

        botonGuardar.classList.toggle(
            'report-step-control--hidden',
            pasoActual !== totalPasos
        );


        /* =================================================
           SCROLL
        ================================================= */

        formulario.scrollIntoView({
            behavior:
                'smooth',

            block:
                'start',
        });
    }

    /* =====================================================
       SIGUIENTE
    ===================================================== */

    botonSiguiente.addEventListener(
        'click',
        async () => {

            if (
                guardando
                || pasoActual >= totalPasos
            ) {
                return;
            }


            const paso =
                obtenerPaso(
                    pasos,
                    pasoActual
                );


            if (!paso) {
                return;
            }


            /*
             * Validaciones normales del paso.
             */
            if (
                !validarPasoActual(
                    paso
                )
            ) {
                return;
            }


            /*
             * Validaciones especiales de campos
             * dinámicos.
             */
            if (
                !validarRelacionesDelPaso(
                    pasoActual,
                    formulario
                )
            ) {
                return;
            }

            /* =====================================================
            VALIDAR FOLIOS IP / IMP EN PASO 1
            ===================================================== */

            if (
                pasoActual === 1
            ) {

                const foliosValidos =
                    await validarFoliosPasoUno(
                        formulario
                    );


                if (
                    !foliosValidos
                ) {
                    return;
                }
            }

            pasosCompletados.add(
                pasoActual
            );


            mostrarPaso(
                pasoActual + 1
            );

        }
    );


    /* =====================================================
       ANTERIOR
    ===================================================== */

    botonAnterior.addEventListener(
        'click',
        () => {

            if (
                guardando
                || pasoActual <= 1
            ) {
                return;
            }


            mostrarPaso(
                pasoActual - 1
            );

        }
    );


    /* =====================================================
       GUARDAR REPORTE
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            if (guardando) {
                return;
            }


            /*
             * Antes del envío volvemos a validar
             * TODOS los pasos.
             *
             * Esto es importante porque el usuario
             * puede regresar a un paso anterior y
             * modificar un campo después de haberlo
             * validado.
             */
            const validacion =
                validarFormularioCompleto(
                    pasos,
                    formulario
                );


            if (!validacion.valido) {

                mostrarPaso(
                    validacion.paso
                );

                return;
            }


            guardando =
                true;


            establecerEstadoGuardando(
                botonGuardar,
                true
            );


            try {

                /* =============================================
                   FORM DATA
                ============================================= */

                const datos =
                    new FormData(
                        formulario
                    );

                console.log(
                    'PRUEBA QUEJOSO:',
                    {
                        es_anonimo:
                            datos.get(
                                'es_anonimo'
                            ),

                        numero_anonimo:
                            datos.get(
                                'numero_anonimo'
                            ),

                        canalizacion:
                            datos.get(
                                'canalizacion'
                            ),

                        canalizacion_otro:
                            datos.get(
                                'canalizacion_otro'
                            ),
                    }
                );
                /* =============================================
                   ENDPOINT
                ============================================= */

                const url =
                    construirUrlGuardar();


                /* =============================================
                   REQUEST
                ============================================= */

                const respuesta =
                    await fetch(
                        url,
                        {
                            method: 'POST',

                            body:
                                datos,

                            headers: {
                                Accept:
                                    'application/json',
                            },

                            credentials:
                                'same-origin',
                        }
                    );


                /* =============================================
                   RESPUESTA
                ============================================= */

                let resultado = null;


                try {

                    resultado =
                        await respuesta.json();

                } catch (error) {

                    throw new Error(
                        'El servidor devolvió una respuesta no válida.'
                    );

                }


                if (
                    !respuesta.ok
                    || !resultado?.success
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible guardar el reporte.'
                    );

                }


                /* =============================================
                   MARCAR TODOS COMO COMPLETADOS
                ============================================= */

                for (
                    let numero = 1;
                    numero <= totalPasos;
                    numero++
                ) {

                    pasosCompletados.add(
                        numero
                    );

                }


                actualizarIndicadoresGuardados(
                    indicadores,
                    pasosCompletados
                );


                /* =============================================
                   ÉXITO
                ============================================= */

                mostrarResultadoYRedirigir({

                    tipo:
                        'success',

                    titulo:
                        'Reporte guardado',

                    mensaje:
                        resultado.message
                        || 'El reporte se registró correctamente.',

                    url:
                        '/DataCore/public/asuntos-internos/reportes/listado',

                    duracion:
                        2000,

                });


            } catch (error) {

                console.error(
                    'Error guardando reporte:',
                    error
                );


                mostrarResultadoYRedirigir({

                    tipo:
                        'error',

                    titulo:
                        'No fue posible guardar',

                    mensaje:
                        error.message
                        || 'Ocurrió un error al registrar el reporte.',

                    duracion:
                        3000,

                });


            } finally {

                guardando =
                    false;


                establecerEstadoGuardando(
                    botonGuardar,
                    false
                );

            }

        }
    );


    /* =====================================================
    PREVISUALIZAR FOLIO AUTOMÁTICO
    ===================================================== */

    cargarPrevisualizacionFolio(
        formulario
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    mostrarPaso(
        1
    );

}


/* =========================================================
   OBTENER PASO
========================================================= */

function obtenerPaso(
    pasos,
    numeroPaso
) {

    return pasos.find(
        (elemento) => {

            return Number(
                elemento.dataset.step
            ) === numeroPaso;

        }
    )
        || null;

}


/* =========================================================
   VALIDAR FORMULARIO COMPLETO
========================================================= */

function validarFormularioCompleto(
    pasos,
    formulario
) {

    for (
        const paso
        of pasos
    ) {

        const numeroPaso =
            Number(
                paso.dataset.step
            );


        /*
         * Primero validamos los campos
         * HTML requeridos.
         */

        if (
            !validarPasoActual(
                paso,
                false
            )
        ) {

            mostrarPrimerCampoInvalido(
                paso
            );


            return {
                valido: false,
                paso: numeroPaso,
            };
        }


        /*
         * Después validamos las relaciones
         * dinámicas:
         *
         * - personal;
         * - modalidad de unidad;
         * - unidades.
         */

        if (
            !validarRelacionesDelPaso(
                numeroPaso,
                formulario
            )
        ) {

            return {
                valido: false,
                paso: numeroPaso,
            };
        }
    }


    return {
        valido: true,
        paso: null,
    };
}


/* =========================================================
   VALIDAR PASO ACTUAL
========================================================= */

function validarPasoActual(
    paso,
    mostrarError = true
) {

    const camposValidables =
        Array.from(
            paso.querySelectorAll(
                `
                input[required]:not([data-validacion-dinamica]),
                input[type="email"]:not([data-validacion-dinamica]),
                select[required]:not([data-validacion-dinamica]),
                textarea[required]:not([data-validacion-dinamica])
                `
            )
        );


    for (
        const campo
        of camposValidables
    ) {

        /*
         * Los campos deshabilitados
         * no participan en la validación.
         */
        if (campo.disabled) {
            continue;
        }


        if (
            !campo.checkValidity()
        ) {

            if (mostrarError) {

                campo.reportValidity();

                campo.focus();

            }


            return false;
        }

    }


    return true;

}


/* =========================================================
   MOSTRAR PRIMER CAMPO INVÁLIDO
========================================================= */

function mostrarPrimerCampoInvalido(
    paso
) {

    /*
     * Esperamos a que mostrarPaso()
     * haga visible la sección.
     */
    window.setTimeout(
        () => {

            const campo =
                paso.querySelector(
                    `
                    input:invalid,
                    select:invalid,
                    textarea:invalid
                    `
                );


            if (!campo) {
                return;
            }


            campo.reportValidity();

            campo.focus();

        },
        100
    );

}


/* =========================================================
   VALIDACIONES DINÁMICAS
========================================================= */

function validarRelacionesDelPaso(
    numeroPaso,
    formulario
) {

    /*
     * PASO 3:
     * Personal y unidades.
     *
     * Los registros dinámicos se representan
     * mediante los hidden inputs generados por
     * personal.js y unidades.js.
     */
    if (
        numeroPaso !== 3
    ) {
        return true;
    }


    /* =====================================================
       PERSONAL-------------
    ===================================================== */

    const personal =
        formulario.querySelectorAll(
            `
            #personal-hidden-inputs
            input[name^="personal["],

            #editar-personal-hidden-inputs
            input[name^="personal["]
            `
        );


    /*
     * Buscamos directamente cualquier input
     * personal[...] dentro del formulario para
     * no depender del nombre exacto del contenedor.
     */
    const personalReal =
        formulario.querySelectorAll(
            'input[name^="personal["]'
        );


    if (
        personalReal.length === 0
    ) {

        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'Personal requerido',

            mensaje:
                'Agrega al menos una persona relacionada con el reporte.',

            duracion:
                3000,

        });


        return false;
    }


    /*
     * La variable anterior solo se conserva
     * para dejar explícita la intención.
     */
    void personal;


    /* =====================================================
    MODALIDAD DE UNIDAD
    ===================================================== */

    const modalidadUnidad =
        formulario.querySelector(
            'input[name="modalidad_unidad"]:checked'
        );


    if (
        !modalidadUnidad
    ) {

        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'Modalidad requerida',

            mensaje:
                'Selecciona si el personal cuenta con unidad o se encuentra en oficina.',

            duracion:
                3000,

        });


        return false;
    }


    /* =====================================================
    SIN UNIDAD / OFICINA
    ===================================================== */

    if (
        modalidadUnidad.value ===
        'SIN_UNIDAD_OFICINA'
    ) {

        /*
        * En esta modalidad no se requiere ninguna
        * unidad vehicular relacionada.
        */

        return true;
    }


    /* =====================================================
    CON UNIDAD
    ===================================================== */

    if (
        modalidadUnidad.value !==
        'CON_UNIDAD'
    ) {

        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'Modalidad no válida',

            mensaje:
                'La modalidad de unidad seleccionada no es válida.',

            duracion:
                3000,

        });


        return false;
    }


    const unidades =
        formulario.querySelectorAll(
            'input[name^="unidades["]'
        );


    if (
        unidades.length === 0
    ) {

        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'Unidad requerida',

            mensaje:
                'Agrega al menos una unidad relacionada con el reporte.',

            duracion:
                3000,

        });


        return false;
    }


    return true;

}


/* =========================================================
   INICIALIZAR NOMENCLATURA MANUAL
========================================================= */

function inicializarNomenclaturaManual(
    formulario
) {

    if (!formulario) {
        return;
    }


    const inputParte =
        formulario.querySelector(
            '#nomenclatura_parte'
        );


    const inputCompleto =
        formulario.querySelector(
            '#nomenclatura'
        );


    if (
        !inputParte
        || !inputCompleto
    ) {

        return;
    }


    const PREFIJO =
        'CGSC/CAI/QJ/';


    /* =====================================================
       CONSTRUIR NOMENCLATURA COMPLETA
    ===================================================== */

    function actualizarNomenclatura() {

        let parte =
            String(
                inputParte.value
                || ''
            ).trim();


        /*
         * Evitar que el usuario genere:
         *
         * CGSC/CAI/QJ//1295/2026
         *
         * si escribe "/" al principio.
         */

        parte =
            parte.replace(
                /^\/+/,
                ''
            );


        inputParte.value =
            parte;


        inputCompleto.value =
            parte !== ''
                ? `${PREFIJO}${parte}`
                : '';
    }


    /* =====================================================
       EVITAR LISTENER DUPLICADO
    ===================================================== */

    if (
        inputParte.dataset
            .nomenclaturaInicializada
        !== '1'
    ) {

        inputParte.dataset
            .nomenclaturaInicializada =
            '1';


        inputParte.addEventListener(
            'input',
            actualizarNomenclatura
        );


        inputParte.addEventListener(
            'change',
            actualizarNomenclatura
        );
    }


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarNomenclatura();
}


/* =========================================================
   PREVISUALIZAR FOLIO Y NOMENCLATURA
========================================================= */

async function cargarPrevisualizacionFolio(
    formulario
) {

    if (!formulario) {
        return;
    }


    /* =====================================================
       CAMPOS
    ===================================================== */

    const inputFolio =
        formulario.querySelector(
            '#folio_visual'
        );


    const inputNomenclatura =
        formulario.querySelector(
            '#nomenclatura_visual'
        );


    const selectTipoFolio =
        formulario.querySelector(
            '#tipo_folio'
        );


    const inputFechaRegistro =
        formulario.querySelector(
            '#fecha_registro'
        );


    if (!inputFolio) {

        console.warn(
            'No se encontró el campo visual del folio automático.'
        );


        return;
    }


    /* =====================================================
       OBTENER AÑO DE REGISTRO
    ===================================================== */

    function obtenerAnioRegistro() {

        const fecha =
            String(
                inputFechaRegistro?.value
                || ''
            ).trim();


        /*
         * El campo fecha_registro actualmente se muestra:
         * dd/mm/aaaa
         */

        const coincidencia =
            fecha.match(
                /^(\d{2})\/(\d{2})\/(\d{4})$/
            );


        if (coincidencia) {

            return coincidencia[3];
        }


        /*
         * Respaldo por si en algún momento
         * se utiliza formato yyyy-mm-dd.
         */

        const coincidenciaIso =
            fecha.match(
                /^(\d{4})-(\d{2})-(\d{2})$/
            );


        if (coincidenciaIso) {

            return coincidenciaIso[1];
        }


        return String(
            new Date().getFullYear()
        );
    }


    /* =====================================================
       ACTUALIZAR PREVISUALIZACIÓN
    ===================================================== */

    async function actualizarPrevisualizacion() {

        const claveFolio =
            String(
                selectTipoFolio?.value
                || 'QJ'
            )
                .trim()
                .toUpperCase();


        try {

            /* =================================================
               ENDPOINT
            ================================================= */

            const url =
                new URL(
                    'DataCore/public/asuntos-internos/reportes/previsualizar-folio',
                    `${window.location.origin}/`
                );


            url.searchParams.set(
                'clave_folio',
                claveFolio
            );


            /* =================================================
               CONSULTAR
            ================================================= */

            const respuesta =
                await fetch(
                    url.toString(),
                    {
                        method:
                            'GET',

                        headers: {
                            Accept:
                                'application/json',
                        },

                        credentials:
                            'same-origin',
                    }
                );


            let resultado =
                null;


            try {

                resultado =
                    await respuesta.json();

            } catch (error) {

                throw new Error(
                    'El servidor devolvió una respuesta no válida al consultar el folio.'
                );
            }


            if (
                !respuesta.ok
                || resultado?.success !== true
            ) {

                throw new Error(
                    resultado?.message
                    || 'No fue posible consultar el siguiente folio.'
                );
            }


            /* =================================================
               FOLIO
            ================================================= */

            const folio =
                String(
                    resultado.folio
                    || ''
                ).trim();


            const numeroFolio =
                Number(
                    resultado.numero_folio
                    || 0
                );


            if (
                folio === ''
                || numeroFolio <= 0
            ) {

                throw new Error(
                    'El servidor no devolvió un folio válido.'
                );
            }


            inputFolio.value =
                folio;


            /* =================================================
               NOMENCLATURA
            ================================================= */

            if (inputNomenclatura) {

                const anioRegistro =
                    obtenerAnioRegistro();


                inputNomenclatura.value =
                    `CGSC/CAI/${claveFolio}/${numeroFolio}/${anioRegistro}`;
            }


        } catch (error) {

            console.error(
                'Error previsualizando folio:',
                error
            );


            inputFolio.value =
                `${claveFolio}- — No disponible`;


            if (inputNomenclatura) {

                inputNomenclatura.value =
                    'No disponible';
            }
        }
    }


    /* =====================================================
       CAMBIO DE TIPO DE FOLIO
    ===================================================== */

    if (
        selectTipoFolio
        && selectTipoFolio.dataset
            .folioInicializado
        !== '1'
    ) {

        selectTipoFolio.dataset
            .folioInicializado =
            '1';


        selectTipoFolio.addEventListener(
            'change',
            () => {

                actualizarPrevisualizacion();
            }
        );
    }


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    await actualizarPrevisualizacion();
}

/* =========================================================
   VALIDAR FOLIOS IP / IMP
========================================================= */

async function validarFoliosPasoUno(
    formulario
) {

    const inputFolioIp =
        formulario.querySelector(
            '#folio_ip'
        );


    const inputFolioImp =
        formulario.querySelector(
            '#folio_imp'
        );


    const folioIp =
        String(
            inputFolioIp?.value
            || ''
        ).trim();


    const folioImp =
        String(
            inputFolioImp?.value
            || ''
        ).trim();


    /* =====================================================
       AMBOS VACÍOS
    ===================================================== */

    if (
        folioIp === ''
        && folioImp === ''
    ) {
        return true;
    }


    try {

        /* =================================================
           URL
        ================================================= */

        const url =
            new URL(
                'DataCore/public/asuntos-internos/reportes/validar-folio',
                `${window.location.origin}/`
            );


        if (
            folioIp !== ''
        ) {

            url.searchParams.set(
                'folio_ip',
                folioIp
            );
        }


        if (
            folioImp !== ''
        ) {

            url.searchParams.set(
                'folio_imp',
                folioImp
            );
        }


        /* =================================================
           REQUEST
        ================================================= */

        const respuesta =
            await fetch(
                url.toString(),
                {
                    method:
                        'GET',

                    headers: {
                        Accept:
                            'application/json',
                    },

                    credentials:
                        'same-origin',
                }
            );


        const resultado =
            await respuesta.json();


        if (
            !respuesta.ok
            || resultado?.success !== true
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible validar los folios.'
            );
        }


        /* =================================================
           FOLIO IP REPETIDO
        ================================================= */

        if (
            resultado?.folio_ip?.existe
        ) {

            mostrarResultadoYRedirigir({

                tipo:
                    'warning',

                titulo:
                    'Folio IP repetido',

                mensaje:
                    'El Folio IP ya se encuentra registrado. Debes ingresar uno diferente para continuar.',

                duracion:
                    3000,

            });


            inputFolioIp?.focus();


            return false;
        }


        /* =================================================
           FOLIO IMP REPETIDO
        ================================================= */

        if (
            resultado?.folio_imp?.existe
        ) {

            mostrarResultadoYRedirigir({

                tipo:
                    'warning',

                titulo:
                    'Folio IMP repetido',

                mensaje:
                    'El Folio IMP ya se encuentra registrado. Debes ingresar uno diferente para continuar.',

                duracion:
                    3000,

            });


            inputFolioImp?.focus();


            return false;
        }


        return true;


    } catch (error) {

        console.error(
            'Error validando Folio IP / IMP:',
            error
        );


        mostrarResultadoYRedirigir({

            tipo:
                'error',

            titulo:
                'No fue posible validar',

            mensaje:
                error.message
                || 'No fue posible validar los folios.',

            duracion:
                3000,

        });


        return false;
    }
}


/* =========================================================
   URL GUARDAR
========================================================= */

function construirUrlGuardar() {

    return new URL(
        'DataCore/public/asuntos-internos/reportes/guardar',
        `${window.location.origin}/`
    ).toString();

}

/* =========================================================
   ESTADO BOTÓN GUARDAR
========================================================= */

function establecerEstadoGuardando(
    boton,
    guardando
) {

    boton.disabled =
        guardando;


    if (guardando) {

        boton.dataset.textoOriginal =
            boton.textContent;


        boton.textContent =
            'Guardando...';


        return;
    }


    boton.textContent =
        boton.dataset.textoOriginal
        || 'Guardar reporte';


    delete boton.dataset.textoOriginal;

}


/* =========================================================
   INDICADORES COMPLETADOS
========================================================= */

function actualizarIndicadoresGuardados(
    indicadores,
    pasosCompletados
) {

    indicadores.forEach(
        (indicador) => {

            const numero =
                Number(
                    indicador
                        .dataset
                        .stepIndicator
                );


            indicador.classList.remove(
                'report-steps__item--active'
            );


            indicador.classList.toggle(
                'report-steps__item--completed',
                pasosCompletados.has(
                    numero
                )
            );

        }
    );


}
