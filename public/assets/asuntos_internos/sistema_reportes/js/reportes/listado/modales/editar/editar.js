/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Editar reporte - Coordinador
========================================================= */

import {
    reportesTemporales,
    estadoEdicion,
    establecerReporteActual,
    limpiarReporteActual,
} from './estado.js';

import {
    construirFolio,
    asignarTextoEditar,
} from './utilidades.js';

import {
    inicializarEditarPersonal,
} from './personal.js';

import {
    inicializarEditarUnidades,
} from './unidades.js';

import {
    inicializarModalEditar,
    mostrarSeccionEditar,
    abrirModalEditar,
    cerrarModalEditar,
} from './modal.js';

import {
    actualizarHeaderEditar,
} from './header.js';


import {
    inicializarEditarEvidencia,
    obtenerEvidenciasEliminadas,
} from './evidencia.js';

import {
    cargarReporteEnFormulario,
    obtenerReporteDesdeFormulario,
} from './formulario.js';

import {
    inicializarUbicacionEditar,
} from './ubicacion.js';

import {
    mostrarResultado,
} from '../../../notificaciones/resultado.js';

import {
    confirmarAccion,
} from '../../../notificaciones/confirmacion.js';

import {
    inicializarEditarQuejoso,
} from './quejoso.js';

import {
    inicializarEditarClasificacion
} from './clasificacion.js';

import {
    inicializarSancionesEditar
} from './sanciones.js';

import {
    inicializarMotivosEditar,
    obtenerMotivosEditar
} from './motivos.js';


/* =========================================================
   INICIALIZAR
========================================================= */

export function inicializarEditarReporte() {

    const modal =
        document.querySelector(
            '#modal-editar-reporte'
        );


    const formulario =
        document.querySelector(
            '#form-editar-reporte'
        );


    if (
        !modal
        || !formulario
    ) {
        return;
    }


    /* =====================================================
       MAYÚSCULAS AUTOMÁTICAS
    ===================================================== */

    inicializarMayusculasEditar(
        formulario
    );


    /* =====================================================
       BLOQUEAR ENTER
    ===================================================== */

    inicializarBloqueoEnterEditar(
        formulario
    );

    /* =====================================================
       MÓDULOS
    ===================================================== */

    inicializarModalEditar(
        modal
    );


    inicializarEditarPersonal(
        modal
    );


    inicializarEditarUnidades(
        modal
    );


    inicializarEditarEvidencia(
        modal,
        formulario
    );


    inicializarUbicacionEditar(
        modal
    );


    inicializarEditarQuejoso(
        modal
    );


    inicializarEditarClasificacion(
        modal
    );


    inicializarSancionesEditar(
        modal
    );


    inicializarMotivosEditar(
        modal
    );


    inicializarCatalogoTipoFolioEditar(
        modal
    );


    inicializarTipoFolioEditar(
        modal
    );

    inicializarCatalogoEstadoEditar(
        modal
    );


    /* =====================================================
       ABRIR EDITAR
    ===================================================== */

    document.addEventListener(
        'click',
        async (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="editar"]'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest(
                    'tr'
                );


            if (!fila) {
                return;
            }


            const idReporte =
                Number(
                    boton.dataset.idReporte
                    || fila.dataset.idReporte
                    || 0
                );


            if (
                !Number.isInteger(
                    idReporte
                )
                || idReporte <= 0
            ) {

                console.error(
                    'El reporte no contiene un id_reporte válido.'
                );


                return;
            }


            boton.disabled =
                true;


            try {

                /* =================================================
                   CONSULTAR REPORTE REAL
                ================================================= */

                const datos =
                    await consultarReporteEditar(
                        idReporte
                    );


                if (
                    !datos
                    || datos.success !== true
                    || !datos.reporte
                ) {

                    throw new Error(
                        datos?.message
                        || 'No fue posible consultar el reporte.'
                    );
                }


                /* =================================================
                   ADAPTAR RESPUESTA
                ================================================= */

                const reporte =
                    construirReporteEditar(
                        datos
                    );


                /* =================================================
                   ESTADO ACTUAL
                ================================================= */

                establecerReporteActual(
                    fila,
                    reporte.folio
                );


                reportesTemporales.set(
                    reporte.folio,
                    reporte
                );


                /* =================================================
                   CARGAR FORMULARIO
                ================================================= */

                cargarReporteEnFormulario(
                    modal,
                    formulario,
                    reporte
                );


                actualizarCatalogoEstadoEditar(
                    modal
                );


                /* =================================================
                ESTADO ORIGINAL DEL TIPO DE FOLIO
                ================================================= */

                const selectTipoFolio =
                    modal.querySelector(
                        '#editar-tipo-folio'
                    );


                if (selectTipoFolio) {

                    selectTipoFolio.dataset
                        .tipoFolioOriginal =
                        String(
                            reporte.prefijo
                            || 'QJ'
                        )
                            .trim()
                            .toUpperCase();


                    selectTipoFolio.dataset
                        .folioOriginal =
                        String(
                            reporte.folio
                            || ''
                        ).trim();


                    selectTipoFolio.dataset
                        .nomenclaturaOriginal =
                        String(
                            reporte.nomenclatura
                            || ''
                        ).trim();
                }


                /* =================================================
                ACTUALIZAR ESTADO QJF
                ================================================= */

                actualizarEstadoQjfEditar(
                    modal
                );

                /* =================================================
                   UBICACIÓN / GOOGLE MAPS
                ================================================= */

                inicializarUbicacionEditar(
                    modal
                );


                /* =================================================
                   HEADER
                ================================================= */

                actualizarHeaderEditar(
                    modal,
                    reporte
                );


                /* =================================================
                   PRIMERA PESTAÑA
                ================================================= */

                mostrarSeccionEditar(
                    modal,
                    'datos'
                );


                /* =================================================
                   ABRIR MODAL
                ================================================= */

                abrirModalEditar(
                    modal
                );


            } catch (error) {

                console.error(
                    'Error cargando reporte para edición:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible cargar el reporte.'
                );


            } finally {

                boton.disabled =
                    false;
            }

        }
    );


    /* =========================================================
    GUARDAR CAMBIOS
    ========================================================= */


    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            /* =====================================================
               REPORTE ACTUAL
            ===================================================== */

            const filaActual =
                estadoEdicion.filaActual;


            const folioActual =
                estadoEdicion.folioActual;


            if (
                !filaActual
                || !folioActual
            ) {

                mostrarResultado({
                    tipo:
                        'error',

                    titulo:
                        'No fue posible guardar',

                    mensaje:
                        'No fue posible identificar el reporte que estás editando.',
                });


                return;
            }


            const reporteAnterior =
                reportesTemporales.get(
                    folioActual
                )
                || {};


            const idReporte =
                Number(
                    reporteAnterior.id_reporte
                    || filaActual.dataset.idReporte
                    || 0
                );


            if (
                !Number.isInteger(
                    idReporte
                )
                || idReporte <= 0
            ) {

                mostrarResultado({
                    tipo:
                        'error',

                    titulo:
                        'No fue posible guardar',

                    mensaje:
                        'No fue posible identificar el reporte que deseas actualizar.',
                });


                return;
            }


            /* =====================================================
               VALIDACIÓN HTML
            ===================================================== */

            if (
                typeof formulario.reportValidity
                === 'function'
                && !formulario.reportValidity()
            ) {

                return;
            }


            /* =====================================================
            VALIDAR FOLIOS IP / IMP
            ===================================================== */

            const foliosValidos =
                await validarFoliosEditar(
                    formulario,
                    idReporte
                );


            if (!foliosValidos) {
                return;
            }


            /* =====================================================
               OBTENER ESTADO FINAL DEL FORMULARIO
            ===================================================== */

            const reporteEditado =
                obtenerReporteDesdeFormulario(
                    formulario,
                    reporteAnterior
                );


            const nuevoFolio =
                String(
                    reporteEditado.folio
                    || reporteAnterior.folio
                    || ''
                ).trim();


            reporteEditado.folio =
                nuevoFolio;


            reporteEditado.id_reporte =
                idReporte;


            /* =====================================================
               FORM DATA
            ===================================================== */

            const datos =
                new FormData(
                    formulario
                );


            datos.set(
                'folio',
                nuevoFolio
            );


            /* =====================================================
               ESTADO ACTUAL
            ===================================================== */

            const estadoActual =
                modal.querySelector(
                    '#editar-estado-actual'
                );


            if (estadoActual) {

                datos.set(
                    'estado_actual',
                    String(
                        estadoActual.value
                        || 'Pendiente'
                    ).trim()
                );
            }


            /* =====================================================
               QUEJOSO / ANÓNIMO
            ===================================================== */

            const anonimoSeleccionado =
                modal.querySelector(
                    'input[name="anonimo"]:checked'
                );


            const esAnonimo =
                String(
                    anonimoSeleccionado?.value
                    || '0'
                ).trim() === '1';


            datos.set(
                'es_anonimo',
                esAnonimo
                    ? '1'
                    : '0'
            );


            const numeroAnonimo =
                modal.querySelector(
                    '#editar-numero-anonimo'
                );


            datos.set(
                'numero_anonimo',
                esAnonimo
                    ? String(
                        numeroAnonimo?.value
                        || ''
                    ).trim()
                    : ''
            );


            /*
             * El backend usa "es_anonimo",
             * no "anonimo".
             */

            datos.delete(
                'anonimo'
            );


            /* =====================================================
            SITUACIÓN DE LA SANCIÓN
            ===================================================== */

            const sinSanciones =
                modal.querySelector(
                    '#editar-sin-sanciones'
                );


            const bajaVoluntaria =
                modal.querySelector(
                    '#editar-baja-voluntaria'
                );


            const desistir =
                modal.querySelector(
                    '#editar-desistir'
                );


            /* =====================================================
            SIN SANCIONES
            ===================================================== */

            datos.set(
                'sin_sanciones',
                sinSanciones?.checked
                    ? '1'
                    : '0'
            );


            /* =====================================================
            BAJA VOLUNTARIA
            ===================================================== */

            datos.set(
                'baja_voluntaria',
                bajaVoluntaria?.checked
                    ? '1'
                    : '0'
            );


            /* =====================================================
            DESISTIR
            ===================================================== */

            datos.set(
                'desistir',
                desistir?.checked
                    ? '1'
                    : '0'
            );


            /* =====================================================
               PERSONAL
            ===================================================== */

            eliminarClavesFormData(
                datos,
                'personal['
            );


            const personal =
                Array.isArray(
                    reporteEditado.personal
                )
                    ? reporteEditado.personal
                    : [];


            personal.forEach(
                (
                    persona,
                    indice
                ) => {

                    agregarValorFormData(
                        datos,
                        `personal[${indice}][plantilla_id]`,
                        persona.plantilla_id
                        ?? persona.id
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][perscod]`,
                        persona.perscod
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][nombre]`,
                        persona.nombre
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][area]`,
                        persona.area
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][turno]`,
                        persona.turno
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][alias]`,
                        persona.alias
                        ?? persona.alias_snapshot
                        ?? ''
                    );

                }
            );


            /* =====================================================
               UNIDADES
            ===================================================== */

            eliminarClavesFormData(
                datos,
                'unidades['
            );


            const unidades =
                Array.isArray(
                    reporteEditado.unidades
                )
                    ? reporteEditado.unidades
                    : [];


            unidades.forEach(
                (
                    unidad,
                    indice
                ) => {

                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][parque_vehicular_id]`,
                        unidad.parque_vehicular_id
                        ?? unidad.id
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][no_economico]`,
                        unidad.no_economico
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][placas]`,
                        unidad.placas
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][marca]`,
                        unidad.marca
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][submarca]`,
                        unidad.submarca
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][color]`,
                        unidad.color
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][estatus]`,
                        unidad.estatus
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][servicio]`,
                        unidad.servicio
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][tipo]`,
                        unidad.tipo
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][modelo]`,
                        unidad.modelo
                        ?? ''
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][serie]`,
                        unidad.serie
                        ?? ''
                    );

                }
            );


            /* =====================================================
               EVIDENCIAS ELIMINADAS
            ===================================================== */

            datos.delete(
                'evidencias_eliminadas[]'
            );


            const evidenciasEliminadas =
                obtenerEvidenciasEliminadas();


            evidenciasEliminadas.forEach(
                (idEvidencia) => {

                    datos.append(
                        'evidencias_eliminadas[]',
                        String(
                            idEvidencia
                        )
                    );

                }
            );

            /* =====================================================
               BOTÓN GUARDAR
            ===================================================== */

            const botonGuardar =
                formulario.querySelector(
                    '[type="submit"]'
                );


            const textoOriginal =
                botonGuardar
                    ? botonGuardar.innerHTML
                    : '';


            if (botonGuardar) {

                botonGuardar.disabled =
                    true;


                botonGuardar.innerHTML =
                    'Guardando...';
            }


            try {

                /* =================================================
                   ENVIAR
                ================================================= */

                const resultado =
                    await actualizarReporteBackend(
                        idReporte,
                        datos
                    );


                if (
                    !resultado
                    || resultado.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible actualizar el reporte.'
                    );
                }


                /* =================================================
                   ESTADO LOCAL
                ================================================= */

                const folioGuardado =
                    String(
                        resultado.folio
                        || nuevoFolio
                    ).trim();


                reporteEditado.folio =
                    folioGuardado;


                if (
                    folioGuardado
                    !== folioActual
                ) {

                    reportesTemporales.delete(
                        folioActual
                    );
                }


                reportesTemporales.set(
                    folioGuardado,
                    reporteEditado
                );


                /* =================================================
                   CERRAR
                ================================================= */

                cerrarModalEditar(
                    modal
                );


                limpiarReporteActual();


                /* =================================================
                   RESULTADO
                ================================================= */

                mostrarResultado({
                    tipo:
                        'success',

                    titulo:
                        'Reporte actualizado',

                    mensaje:
                        'Los cambios del reporte se guardaron correctamente.',
                });


                /* =================================================
                   RECARGAR
                ================================================= */

                window.setTimeout(
                    () => {

                        window.location.reload();

                    },
                    1500
                );


            } catch (error) {

                console.error(
                    'Error actualizando reporte:',
                    error
                );


                mostrarResultado({
                    tipo:
                        'error',

                    titulo:
                        'No fue posible actualizar',

                    mensaje:
                        error.message
                        || 'No fue posible actualizar el reporte.',
                });


            } finally {

                if (botonGuardar) {

                    botonGuardar.disabled =
                        false;


                    botonGuardar.innerHTML =
                        textoOriginal;
                }

            }

        }
    );
}


/* =========================================================
   MAYÚSCULAS AUTOMÁTICAS
   EDITAR REPORTE
========================================================= */

function inicializarMayusculasEditar(
    formulario
) {

    if (!formulario) {
        return;
    }


    /* =====================================================
       EVITAR LISTENER DUPLICADO
    ===================================================== */

    if (
        formulario.dataset
            .mayusculasInicializadas
        === '1'
    ) {
        return;
    }


    formulario.dataset
        .mayusculasInicializadas =
        '1';


    /* =====================================================
       CONVERTIR MIENTRAS EL USUARIO ESCRIBE
    ===================================================== */

    formulario.addEventListener(
        'input',
        (evento) => {

            const campo =
                evento.target;


            if (
                !(
                    campo
                    instanceof HTMLInputElement
                )
                && !(
                    campo
                    instanceof HTMLTextAreaElement
                )
            ) {
                return;
            }


            /* =================================================
               INPUTS QUE NO DEBEN MODIFICARSE
            ================================================= */

            if (
                campo
                instanceof HTMLInputElement
            ) {

                const tiposIgnorados =
                    [
                        'checkbox',
                        'radio',
                        'file',
                        'hidden',
                        'number',
                        'range',
                        'date',
                        'datetime-local',
                        'time',
                        'month',
                        'week',
                        'email',
                        'password',
                        'url',
                    ];


                if (
                    tiposIgnorados.includes(
                        campo.type
                    )
                ) {
                    return;
                }

            }


            /* =================================================
               EXCLUSIÓN MANUAL

               Si algún campo necesita conservar minúsculas:

               data-sin-mayusculas="1"
            ================================================= */

            if (
                campo.dataset
                    .sinMayusculas
                === '1'
            ) {
                return;
            }


            /* =================================================
               VALOR ACTUAL
            ================================================= */

            const valorActual =
                String(
                    campo.value
                    || ''
                );


            const valorMayusculas =
                valorActual
                    .toLocaleUpperCase(
                        'es-MX'
                    );


            if (
                valorActual
                === valorMayusculas
            ) {
                return;
            }


            /* =================================================
               POSICIÓN DEL CURSOR
            ================================================= */

            const inicioSeleccion =
                campo.selectionStart;


            const finSeleccion =
                campo.selectionEnd;


            /* =================================================
               ASIGNAR MAYÚSCULAS
            ================================================= */

            campo.value =
                valorMayusculas;


            /* =================================================
               RESTAURAR CURSOR
            ================================================= */

            if (
                inicioSeleccion !== null
                && finSeleccion !== null
            ) {

                try {

                    campo.setSelectionRange(
                        inicioSeleccion,
                        finSeleccion
                    );

                } catch (error) {

                    /*
                     * Algunos tipos de input no permiten
                     * controlar manualmente la selección.
                     */

                }

            }

        }
    );

}


/* =========================================================
   BLOQUEAR ENTER EN EDITAR
========================================================= */

function inicializarBloqueoEnterEditar(
    formulario
) {

    if (!formulario) {
        return;
    }


    if (
        formulario.dataset
            .bloqueoEnterInicializado
        === '1'
    ) {
        return;
    }


    formulario.dataset
        .bloqueoEnterInicializado =
        '1';


    formulario.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Enter'
            ) {
                return;
            }


            /* =================================================
               TEXTAREA

               Aquí Enter sí debe funcionar para permitir
               saltos de línea.
            ================================================= */

            if (
                evento.target
                instanceof HTMLTextAreaElement
            ) {
                return;
            }


            /* =================================================
               EVITAR SUBMIT ACCIDENTAL
            ================================================= */

            evento.preventDefault();

        }
    );

}


/* =========================================================
   QJF - PERSONAL Y UNIDADES EN EDITAR
========================================================= */

function actualizarEstadoQjfEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       TIPO DE FOLIO
    ===================================================== */

    const selectTipoFolio =
        modal.querySelector(
            '#editar-tipo-folio'
        );


    const esQjf =
        String(
            selectTipoFolio?.value
            || ''
        )
            .trim()
            .toUpperCase()
        === 'QJF';


    /* =====================================================
       PERSONAL
    ===================================================== */

    const personalContenido =
        modal.querySelector(
            '#editar-personal-contenido'
        );


    const personalVacio =
        modal.querySelector(
            '#editar-personal-qjf-vacio'
        );


    if (personalContenido) {

        personalContenido.hidden =
            esQjf;
    }


    if (personalVacio) {

        personalVacio.hidden =
            !esQjf;
    }


    /* =====================================================
       UNIDADES
    ===================================================== */

    const unidadesContenido =
        modal.querySelector(
            '#editar-unidades-contenido'
        );


    const unidadesVacio =
        modal.querySelector(
            '#editar-unidades-qjf-vacio'
        );


    if (unidadesContenido) {

        unidadesContenido.hidden =
            esQjf;
    }


    if (unidadesVacio) {

        unidadesVacio.hidden =
            !esQjf;
    }
}

/* =========================================================
   CATÁLOGO ESTADO - EDITAR
========================================================= */

function inicializarCatalogoEstadoEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const input =
        modal.querySelector(
            '#editar-estado-actual'
        );


    const selector =
        modal.querySelector(
            '#editar-estado-select'
        );


    const texto =
        modal.querySelector(
            '#editar-estado-select-texto'
        );


    const resultados =
        modal.querySelector(
            '#editar-estado-resultados'
        );


    if (
        !input
        || !selector
        || !texto
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       EVITAR LISTENERS DUPLICADOS
    ===================================================== */

    if (
        selector.dataset
            .estadoInicializado
        === '1'
    ) {
        return;
    }


    selector.dataset
        .estadoInicializado =
        '1';


    /* =====================================================
       ABRIR / CERRAR
    ===================================================== */

    selector.addEventListener(
        'click',
        () => {

            const abierto =
                !resultados.hidden;


            resultados.hidden =
                abierto;


            selector.classList.toggle(
                'estado-select--activo',
                !abierto
            );


            selector.setAttribute(
                'aria-expanded',
                !abierto
                    ? 'true'
                    : 'false'
            );
        }
    );


    /* =====================================================
       SELECCIONAR
    ===================================================== */

    resultados.addEventListener(
        'click',
        (evento) => {

            const opcion =
                evento.target.closest(
                    '[data-editar-estado-opcion]'
                );


            if (!opcion) {
                return;
            }


            const valor =
                String(
                    opcion.dataset.estado
                    || ''
                ).trim();


            if (valor === '') {
                return;
            }


            input.value =
                valor;


            texto.textContent =
                valor;


            cerrarCatalogoEstadoEditar(
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
                selector.contains(
                    evento.target
                )
                || resultados.contains(
                    evento.target
                )
            ) {
                return;
            }


            cerrarCatalogoEstadoEditar(
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
                === 'Escape'
            ) {

                cerrarCatalogoEstadoEditar(
                    modal
                );
            }
        }
    );
}


/* =========================================================
   CERRAR CATÁLOGO ESTADO
========================================================= */

function cerrarCatalogoEstadoEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const selector =
        modal.querySelector(
            '#editar-estado-select'
        );


    const resultados =
        modal.querySelector(
            '#editar-estado-resultados'
        );


    if (resultados) {

        resultados.hidden =
            true;
    }


    if (selector) {

        selector.classList.remove(
            'estado-select--activo'
        );


        selector.setAttribute(
            'aria-expanded',
            'false'
        );
    }
}


/* =========================================================
   SINCRONIZAR TEXTO DEL ESTADO
========================================================= */

function actualizarCatalogoEstadoEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const input =
        modal.querySelector(
            '#editar-estado-actual'
        );


    const texto =
        modal.querySelector(
            '#editar-estado-select-texto'
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
            || 'Pendiente'
        ).trim();


    texto.textContent =
        valor !== ''
            ? valor
            : 'Pendiente';
}


/* =========================================================
   VALIDAR FOLIOS IP / IMP EN EDICIÓN
========================================================= */

async function validarFoliosEditar(
    formulario,
    idReporte
) {

    const inputFolioIp =
        formulario.querySelector(
            '#editar-folio-ip'
        );


    const inputFolioImp =
        formulario.querySelector(
            '#editar-folio-imp'
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
       SIN FOLIOS QUE VALIDAR
    ===================================================== */

    if (
        folioIp === ''
        && folioImp === ''
    ) {
        return true;
    }


    try {

        const url =
            new URL(
                'DataCore/public/asuntos-internos/reportes/validar-folio',
                `${window.location.origin}/`
            );


        url.searchParams.set(
            'id_reporte',
            String(idReporte)
        );


        if (folioIp !== '') {

            url.searchParams.set(
                'folio_ip',
                folioIp
            );
        }


        if (folioImp !== '') {

            url.searchParams.set(
                'folio_imp',
                folioImp
            );
        }


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

            mostrarResultado({

                tipo:
                    'warning',

                titulo:
                    'Folio IP repetido',

                mensaje:
                    'El Folio IP ya se encuentra registrado. Debes ingresar uno diferente para continuar.',

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

            mostrarResultado({

                tipo:
                    'warning',

                titulo:
                    'Folio IMP repetido',

                mensaje:
                    'El Folio IMP ya se encuentra registrado. Debes ingresar uno diferente para continuar.',

            });


            inputFolioImp?.focus();


            return false;
        }


        return true;


    } catch (error) {

        console.error(
            'Error validando Folio IP / IMP en edición:',
            error
        );


        mostrarResultado({

            tipo:
                'error',

            titulo:
                'No fue posible validar',

            mensaje:
                error.message
                || 'No fue posible validar los folios.',

        });


        return false;
    }
}

/* =========================================================
   CONSULTAR REPORTE REAL
========================================================= */

async function consultarReporteEditar(
    idReporte
) {

    /* const baseUrl =
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`;


    const url =
        new URL(
            `asuntos-internos/reportes/detalle/${idReporte}`,
            baseUrl
        );
 */

    const url =
        new URL(
            `DataCore/public/asuntos-internos/reportes/detalle/${idReporte}`,
            `${window.location.origin}/`
        );

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


    let datos =
        null;


    try {

        datos =
            await respuesta.json();

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );
    }


    if (!respuesta.ok) {

        throw new Error(
            datos?.message
            || 'No fue posible consultar el reporte.'
        );
    }


    return datos;
}


/* =========================================================
   CONSTRUIR REPORTE PARA EL FORMULARIO
========================================================= */

function construirReporteEditar(
    datos
) {

    const origen =
        datos.reporte
        || {};


    const direccionNotificacion =
        datos.direccion_notificacion
            && typeof datos.direccion_notificacion === 'object'
            ? datos.direccion_notificacion
            : {};


    const folio =
        String(
            origen.folio
            || ''
        ).trim();


    return {

        /* =====================================================
           IDENTIFICADOR REAL
        ===================================================== */

        id_reporte:
            Number(
                origen.id_reporte
                || 0
            ),


        /* =====================================================
           DATOS DEL REPORTE
        ===================================================== */

        folio,

        prefijo:
            obtenerPrefijoEditar(
                folio
            ),

        numero_folio:
            obtenerNumeroFolioEditar(
                folio
            ),

        fecha_registro:
            convertirFechaEditar(
                origen.fecha_registro
            ),


        /* =====================================================
           IDENTIFICACIÓN
        ===================================================== */

        folio_ip:
            valorEditar(
                origen.folio_ip
            ),

        folio_imp:
            valorEditar(
                origen.folio_imp
            ),

        fecha_queja:
            convertirFechaEditar(
                origen.fecha_queja
            ),

        fecha_acuerdo:
            convertirFechaEditar(
                origen.fecha_acuerdo
            ),

        expediente:
            valorEditar(
                origen.expediente
            ),

        nomenclatura:
            valorEditar(
                origen.nomenclatura
            ),

        no_oficio:
            valorEditar(
                origen.numero_oficio
            ),


        /* =====================================================
           HECHOS
        ===================================================== */

        fecha_hechos:
            convertirFechaEditar(
                origen.fecha_hechos
            ),

        hora_hechos:
            convertirHoraEditar(
                origen.hora_hechos
            ),

        descripcion:
            valorEditar(
                origen.descripcion_hechos
            ),


        /* =====================================================
           UBICACIÓN
        ===================================================== */

        calle:
            valorEditar(
                origen.calle
            ),

        numero:
            valorEditar(
                origen.numero_exterior
            ),

        colonia:
            valorEditar(
                origen.colonia
            ),

        entre_calle:
            valorEditar(
                origen.entre_calle
            ),

        y_calle:
            valorEditar(
                origen.y_calle
            ),

        municipio:
            valorEditar(
                origen.municipio
            ),

        estado:
            valorEditar(
                origen.estado
            ),

        sector:
            valorEditar(
                origen.sector
            ),

        cuadrante:
            valorEditar(
                origen.cuadrante
            ),

        id_cuadra:
            valorEditar(
                origen.id_cuadra
            ),

        latitud:
            valorEditar(
                origen.latitud
            ),

        longitud:
            valorEditar(
                origen.longitud
            ),

        origen_ubicacion:
            valorEditar(
                origen.origen_ubicacion
            ),


        /* =====================================================
           DIRECCIÓN PARA NOTIFICACIÓN
        ===================================================== */

        direccion_notificacion: {

            pertenece_neza:
                direccionNotificacion.pertenece_neza !== null
                    && direccionNotificacion.pertenece_neza !== undefined
                    ? Number(
                        direccionNotificacion.pertenece_neza
                    )
                    : null,

            calle:
                valorEditar(
                    direccionNotificacion.calle
                ),

            numero_exterior:
                valorEditar(
                    direccionNotificacion.numero_exterior
                ),

            colonia:
                valorEditar(
                    direccionNotificacion.colonia
                ),

            entre_calle:
                valorEditar(
                    direccionNotificacion.entre_calle
                ),

            y_calle:
                valorEditar(
                    direccionNotificacion.y_calle
                ),

            municipio:
                valorEditar(
                    direccionNotificacion.municipio
                ),

            estado:
                valorEditar(
                    direccionNotificacion.estado
                ),

            sector:
                valorEditar(
                    direccionNotificacion.sector
                ),

            cuadrante:
                valorEditar(
                    direccionNotificacion.cuadrante
                ),

            id_cuadra:
                valorEditar(
                    direccionNotificacion.id_cuadra
                ),

            latitud:
                valorEditar(
                    direccionNotificacion.latitud
                ),

            longitud:
                valorEditar(
                    direccionNotificacion.longitud
                ),

            origen_ubicacion:
                valorEditar(
                    direccionNotificacion.origen_ubicacion
                ),
        },


        /* =====================================================
           PERSONAL
        ===================================================== */

        personal:
            Array.isArray(
                datos.personal
            )
                ? datos.personal.map(
                    (persona) => ({

                        ...persona,

                        nombre:
                            valorEditar(
                                persona.nombre
                            ),

                        nomina:
                            valorEditar(
                                persona.nomina
                            ),

                        area:
                            valorEditar(
                                persona.area
                            ),

                        turno:
                            valorEditar(
                                persona.turno
                            ),

                        alias:
                            valorEditar(
                                persona.alias
                                ?? persona.alias_snapshot
                            ),
                    })
                )
                : [],


        /* =====================================================
           UNIDADES
        ===================================================== */

        modalidad_unidad:
            valorEditar(
                origen.modalidad_unidad
                || 'CON_UNIDAD'
            ),

        unidades:
            Array.isArray(
                datos.unidades
            )
                ? datos.unidades.map(
                    (unidad) => ({

                        ...unidad,

                        no_economico:
                            valorEditar(
                                unidad.no_economico
                            ),

                        placas:
                            valorEditar(
                                unidad.placas
                            ),

                        marca:
                            valorEditar(
                                unidad.marca
                            ),

                        submarca:
                            valorEditar(
                                unidad.submarca
                            ),

                        color:
                            valorEditar(
                                unidad.color
                            ),

                        estatus:
                            valorEditar(
                                unidad.estatus
                            ),

                        servicio:
                            valorEditar(
                                unidad.servicio
                            ),

                        tipo:
                            valorEditar(
                                unidad.tipo
                            ),

                        modelo:
                            valorEditar(
                                unidad.modelo
                            ),

                        serie:
                            valorEditar(
                                unidad.serie
                            ),
                    })
                )
                : [],


        /* =====================================================
           QUEJOSO
        ===================================================== */

        quejoso:
            valorEditar(
                origen.nombre_quejoso
            ),

        edad:
            valorEditar(
                origen.edad_quejoso
            ),

        genero:
            valorEditar(
                origen.genero_quejoso
            ),

        telefono:
            valorEditar(
                origen.telefono_quejoso
            ),

        correo:
            valorEditar(
                origen.correo_quejoso
            ),

        direccion_quejoso:
            valorEditar(
                origen.direccion_quejoso
            ),

        /* =====================================================
           QUEJA ANÓNIMA
        ===================================================== */

        es_anonimo:
            Number(
                origen.es_anonimo
                ?? 0
            ),

        numero_anonimo:
            valorEditar(
                origen.numero_anonimo
            ),


        /* =====================================================
           CANALIZACIÓN
        ===================================================== */

        canalizacion_area:
            valorEditar(
                origen.canalizacion_area
            ),

        canalizacion_otro:
            valorEditar(
                origen.canalizacion_otro
            ),


        /* =====================================================
           CLASIFICACIÓN
        ===================================================== */

        clasificacion:
            valorEditar(
                origen.clasificacion
            ),

        inspector:
            valorEditar(
                origen.inspector
            ),

        investigador:
            valorEditar(
                origen.investigador
            ),


        /* =====================================================
           ESTADO ACTUAL
        ===================================================== */

        estado_actual:
            valorEditar(
                origen.estado_actual
            ),


        /* =====================================================
        SITUACIÓN DE LA SANCIÓN
        ===================================================== */

        sin_sanciones:
            Number(
                origen.sin_sanciones
                ?? 0
            ),


        baja_voluntaria:
            Number(
                origen.baja_voluntaria
                ?? 0
            ),


        desistir:
            Number(
                origen.desistir
                ?? 0
            ),


        /* =====================================================
           MOTIVOS
        ===================================================== */

        motivos:
            Array.isArray(
                datos.motivos
            )
                ? datos.motivos.map(
                    (motivo) => ({

                        ...motivo,

                        id_motivo:
                            Number(
                                motivo.id_motivo
                                ?? 0
                            ),

                        motivo:
                            valorEditar(
                                motivo.motivo
                            ),

                        sancion:
                            valorEditar(
                                motivo.sancion
                                ?? motivo.tipo_sancion
                            ),

                        folio_sancion:
                            valorEditar(
                                motivo.folio_sancion
                            ),
                    })
                )
                : [],


        /* =====================================================
           SANCIÓN DISCIPLINARIA
        ===================================================== */

        sancion:
            datos.sancion
                && typeof datos.sancion === 'object'
                ? {

                    ...datos.sancion,

                    tipo:
                        valorEditar(
                            datos.sancion.tipo
                        ),

                    descripcion_otro:
                        valorEditar(
                            datos.sancion.descripcion_otro
                        ),

                    origen:
                        valorEditar(
                            datos.sancion.origen
                        ),

                    fecha_actualizacion:
                        valorEditar(
                            datos.sancion.fecha_actualizacion
                        ),

                    actualizada_desde_seguimiento:
                        datos.sancion
                            .actualizada_desde_seguimiento
                        === true,

                    id_sancion:
                        Number(
                            datos.sancion.id_sancion
                            || 0
                        ),

                    id_seguimiento:
                        datos.sancion.id_seguimiento !== null
                            && datos.sancion.id_seguimiento !== undefined
                            ? Number(
                                datos.sancion.id_seguimiento
                                || 0
                            )
                            : null,

                }
                : null,


        /* =====================================================
           RESOLUCIÓN
        ===================================================== */

        quien_emite_resolucion:
            valorEditar(
                origen.quien_emite_resolucion
            ),

        resolucion:
            valorEditar(
                origen.resolucion
            ),


        /* =====================================================
           ADICIONAL
        ===================================================== */

        observaciones:
            valorEditar(
                origen.observaciones
            ),


        /* =====================================================
           EVIDENCIAS EXISTENTES
        ===================================================== */

        evidencias:
            Array.isArray(
                datos.evidencias
            )
                ? datos.evidencias.map(
                    (evidencia) => ({
                        ...evidencia,
                    })
                )
                : [],
    };
}

/* =========================================================
   FOLIO
========================================================= */

function obtenerPrefijoEditar(
    folio
) {

    const valor =
        String(
            folio || ''
        ).trim();


    if (!valor) {
        return 'QJ';
    }


    const partes =
        valor.split('-');


    return partes.length > 1
        ? partes[0]
        : 'QJ';
}


function obtenerNumeroFolioEditar(
    folio
) {

    const valor =
        String(
            folio || ''
        ).trim();


    if (!valor) {
        return '';
    }


    const partes =
        valor.split('-');


    if (
        partes.length <= 1
    ) {
        return valor;
    }


    return partes
        .slice(1)
        .join('-');
}


/* =========================================================
   FECHA
========================================================= */

function convertirFechaEditar(
    valor
) {

    const fecha =
        String(
            valor || ''
        ).trim();


    if (!fecha) {
        return '';
    }


    const coincidencia =
        fecha.match(
            /^(\d{4})-(\d{2})-(\d{2})/
        );


    if (coincidencia) {

        return `${coincidencia[1]}-${coincidencia[2]}-${coincidencia[3]}`;
    }


    const fechaVisual =
        fecha.match(
            /^(\d{2})\/(\d{2})\/(\d{4})$/
        );


    if (fechaVisual) {

        return `${fechaVisual[3]}-${fechaVisual[2]}-${fechaVisual[1]}`;
    }


    return '';
}


/* =========================================================
   HORA
========================================================= */

function convertirHoraEditar(
    valor
) {

    const hora =
        String(
            valor || ''
        ).trim();


    if (!hora) {
        return '';
    }


    return hora.length >= 5
        ? hora.substring(
            0,
            5
        )
        : hora;
}


/* =========================================================
   VALOR SEGURO
========================================================= */

function valorEditar(
    valor
) {

    if (
        valor === null
        || valor === undefined
    ) {
        return '';
    }


    return String(
        valor
    ).trim();
}


/* =========================================================
   ACTUALIZAR REPORTE EN BACKEND
========================================================= */

async function actualizarReporteBackend(
    idReporte,
    datos
) {

    /* =====================================================
       SINCRONIZAR MOTIVOS
    ===================================================== */

    sincronizarMotivosFormDataEditar(
        datos
    );


    /* =====================================================
       URL
    ===================================================== */

    const url =
        new URL(
            `DataCore/public/asuntos-internos/reportes/actualizar/${idReporte}`,
            `${window.location.origin}/`
        );


    /* =====================================================
       PETICIÓN
    ===================================================== */

    const respuesta =
        await fetch(
            url.toString(),
            {

                method:
                    'POST',

                headers: {

                    Accept:
                        'application/json',

                },

                credentials:
                    'same-origin',

                body:
                    datos,

            }
        );


    /* =====================================================
       RESPUESTA
    ===================================================== */

    const texto =
        await respuesta.text();


    let resultado =
        null;


    try {

        resultado =
            texto
                ? JSON.parse(
                    texto
                )
                : null;

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );
    }


    /* =====================================================
       VALIDAR RESPUESTA
    ===================================================== */

    if (!respuesta.ok) {

        throw new Error(
            resultado?.message
            || `No fue posible actualizar el reporte. Código ${respuesta.status}.`
        );
    }


    return resultado;
}


/* =========================================================
   SINCRONIZAR MOTIVOS EN FORM DATA
========================================================= */

function sincronizarMotivosFormDataEditar(
    datos
) {

    if (
        !(datos instanceof FormData)
    ) {

        return;
    }


    /* =====================================================
       ELIMINAR MOTIVOS QUE YA ESTUVIERAN EN FORMDATA
    ===================================================== */

    eliminarClavesFormData(
        datos,
        'motivos_seleccionados['
    );


    /* =====================================================
       OBTENER INPUTS DINÁMICOS DE MOTIVOS
    ===================================================== */

    const contenedor =
        document.querySelector(
            '#editar-motivos-inputs'
        );


    if (!contenedor) {

        return;
    }


    const inputs =
        contenedor.querySelectorAll(
            'input[name^="motivos_seleccionados["]'
        );


    /* =====================================================
       RECONSTRUIR MOTIVOS
    ===================================================== */

    inputs.forEach(
        (input) => {

            const nombre =
                String(
                    input.name
                    || ''
                ).trim();


            if (!nombre) {

                return;
            }


            datos.append(
                nombre,
                String(
                    input.value
                    ?? ''
                )
            );
        }
    );
}


/* =========================================================
   ELIMINAR GRUPO DE FORM DATA
========================================================= */

function eliminarClavesFormData(
    datos,
    prefijo
) {

    const claves =
        [];


    for (
        const clave
        of datos.keys()
    ) {

        if (
            clave.startsWith(
                prefijo
            )
        ) {

            claves.push(
                clave
            );
        }
    }


    claves.forEach(
        (clave) => {

            datos.delete(
                clave
            );

        }
    );
}


/* =========================================================
   AGREGAR VALOR A FORM DATA
========================================================= */

function agregarValorFormData(
    datos,
    nombre,
    valor
) {

    if (
        valor === null
        || valor === undefined
    ) {

        datos.append(
            nombre,
            ''
        );

        return;
    }


    datos.append(
        nombre,
        String(
            valor
        )
    );
}

/* =========================================================
   CATÁLOGO TIPO DE FOLIO - EDITAR
========================================================= */

function inicializarCatalogoTipoFolioEditar(
    modal
) {

    if (!modal) {
        return;
    }


    /* =====================================================
       ELEMENTOS
    ===================================================== */

    const inputTipoFolio =
        modal.querySelector(
            '#editar-tipo-folio'
        );


    const selector =
        modal.querySelector(
            '#editar-tipo-folio-select'
        );


    const textoSelector =
        modal.querySelector(
            '#editar-tipo-folio-select-texto'
        );


    const resultados =
        modal.querySelector(
            '#editar-tipo-folio-resultados'
        );


    const opciones =
        modal.querySelectorAll(
            '[data-editar-tipo-folio-opcion]'
        );


    if (
        !inputTipoFolio
        || !selector
        || !textoSelector
        || !resultados
    ) {
        return;
    }


    /* =====================================================
       EVITAR LISTENERS DUPLICADOS
    ===================================================== */

    if (
        selector.dataset
            .tipoFolioCatalogoInicializado
        === '1'
    ) {
        return;
    }


    selector.dataset
        .tipoFolioCatalogoInicializado =
        '1';


    /* =====================================================
       ABRIR CATÁLOGO
    ===================================================== */

    function abrirCatalogo() {

        resultados.hidden =
            false;


        selector.setAttribute(
            'aria-expanded',
            'true'
        );


        selector.classList.add(
            'tipo-folio-select--activo'
        );
    }


    /* =====================================================
       CERRAR CATÁLOGO
    ===================================================== */

    function cerrarCatalogo() {

        resultados.hidden =
            true;


        selector.setAttribute(
            'aria-expanded',
            'false'
        );


        selector.classList.remove(
            'tipo-folio-select--activo'
        );
    }


    /* =====================================================
       ACTUALIZAR TEXTO VISUAL
    ===================================================== */

    function obtenerNombreTipoFolio(
        clave
    ) {

        switch (
        String(
            clave
            || ''
        )
            .trim()
            .toUpperCase()
        ) {

            case 'QJV':

                return 'QJV - Queja verbal';


            case 'QJF':

                return 'QJF - Queja foránea';


            case 'QJ':
            default:

                return 'QJ - Queja';
        }
    }


    /* =====================================================
       SELECCIONAR OPCIÓN
    ===================================================== */

    function seleccionarTipoFolio(
        valor,
        nombre
    ) {

        const clave =
            String(
                valor
                || ''
            )
                .trim()
                .toUpperCase();


        if (
            ![
                'QJ',
                'QJV',
                'QJF',
            ].includes(
                clave
            )
        ) {
            return;
        }


        inputTipoFolio.value =
            clave;


        textoSelector.textContent =
            String(
                nombre
                || obtenerNombreTipoFolio(
                    clave
                )
            ).trim();


        cerrarCatalogo();


        /*
         * Disparamos CHANGE porque la función
         * inicializarTipoFolioEditar() ya escucha
         * este evento y actualiza:
         *
         * - folio
         * - nomenclatura
         * - estado QJF
         * - personal
         * - unidades
         */

        inputTipoFolio.dispatchEvent(
            new Event(
                'change',
                {
                    bubbles:
                        true,
                }
            )
        );
    }


    /* =====================================================
       CLICK EN SELECTOR
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
       CLICK EN OPCIONES
    ===================================================== */

    opciones.forEach(
        (opcion) => {

            opcion.addEventListener(
                'click',
                () => {

                    seleccionarTipoFolio(
                        opcion.dataset.tipoFolio,
                        opcion.dataset.tipoFolioNombre
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
       ESTADO VISUAL INICIAL
    ===================================================== */

    textoSelector.textContent =
        obtenerNombreTipoFolio(
            inputTipoFolio.value
        );
}


/* =========================================================
   TIPO DE FOLIO EN EDITAR
========================================================= */

function inicializarTipoFolioEditar(
    modal
) {

    if (!modal) {
        return;
    }


    const selectTipoFolio =
        modal.querySelector(
            '#editar-tipo-folio'
        );


    const inputFolio =
        modal.querySelector(
            '#editar-folio'
        );


    const inputNomenclatura =
        modal.querySelector(
            '#editar-nomenclatura'
        );


    const inputFechaRegistro =
        modal.querySelector(
            '#editar-fecha-registro'
        );


    if (
        !selectTipoFolio
        || !inputFolio
        || !inputNomenclatura
    ) {
        return;
    }


    /* =====================================================
       EVITAR LISTENER DUPLICADO
    ===================================================== */

    if (
        selectTipoFolio.dataset
            .tipoFolioInicializado
        === '1'
    ) {

        actualizarEstadoQjfEditar(
            modal
        );

        return;
    }


    selectTipoFolio.dataset
        .tipoFolioInicializado =
        '1';


    /* =====================================================
       OBTENER AÑO
    ===================================================== */

    function obtenerAnioRegistro() {

        const fecha =
            String(
                inputFechaRegistro?.value
                || ''
            ).trim();


        const coincidenciaIso =
            fecha.match(
                /^(\d{4})-(\d{2})-(\d{2})$/
            );


        if (coincidenciaIso) {

            return coincidenciaIso[1];
        }


        const coincidenciaVisual =
            fecha.match(
                /^(\d{2})\/(\d{2})\/(\d{4})$/
            );


        if (coincidenciaVisual) {

            return coincidenciaVisual[3];
        }


        return String(
            new Date().getFullYear()
        );
    }


    /* =====================================================
       PREVISUALIZAR NUEVA FAMILIA
    ===================================================== */

    async function previsualizarNuevoFolio(
        claveFolio
    ) {

        const url =
            new URL(
                'DataCore/public/asuntos-internos/reportes/previsualizar-folio',
                `${window.location.origin}/`
            );


        url.searchParams.set(
            'clave_folio',
            claveFolio
        );


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
                || 'No fue posible consultar el siguiente folio.'
            );
        }


        const numeroFolio =
            Number(
                resultado.numero_folio
                || 0
            );


        const folio =
            String(
                resultado.folio
                || ''
            ).trim();


        if (
            numeroFolio <= 0
            || folio === ''
        ) {

            throw new Error(
                'El servidor no devolvió un folio válido.'
            );
        }


        inputFolio.value =
            folio;


        inputNomenclatura.value =
            `CGSC/CAI/${claveFolio}/${numeroFolio}/${obtenerAnioRegistro()}`;
    }


    /* =====================================================
       CAMBIO DE TIPO
    ===================================================== */

    selectTipoFolio.addEventListener(
        'change',
        async () => {

            const claveSeleccionada =
                String(
                    selectTipoFolio.value
                    || 'QJ'
                )
                    .trim()
                    .toUpperCase();


            /*
             * Primero actualizamos el estado visual
             * de Personal y Unidades.
             */
            actualizarEstadoQjfEditar(
                modal
            );


            const claveOriginal =
                String(
                    selectTipoFolio.dataset
                        .tipoFolioOriginal
                    || ''
                )
                    .trim()
                    .toUpperCase();


            /* =================================================
               VOLVIÓ AL TIPO ORIGINAL
            ================================================= */

            if (
                claveOriginal !== ''
                && claveSeleccionada
                === claveOriginal
            ) {

                inputFolio.value =
                    String(
                        selectTipoFolio.dataset
                            .folioOriginal
                        || ''
                    );


                inputNomenclatura.value =
                    String(
                        selectTipoFolio.dataset
                            .nomenclaturaOriginal
                        || ''
                    );


                return;
            }


            /* =================================================
               CAMBIÓ DE FAMILIA
            ================================================= */

            try {

                await previsualizarNuevoFolio(
                    claveSeleccionada
                );

            } catch (error) {

                console.error(
                    'Error previsualizando folio en edición:',
                    error
                );


                mostrarResultado({

                    tipo:
                        'error',

                    titulo:
                        'No fue posible cambiar el tipo de folio',

                    mensaje:
                        error.message
                        || 'No fue posible consultar el siguiente consecutivo.',

                });


                /*
                 * Si falla, regresamos al tipo original.
                 */

                if (claveOriginal !== '') {

                    selectTipoFolio.value =
                        claveOriginal;


                    inputFolio.value =
                        String(
                            selectTipoFolio.dataset
                                .folioOriginal
                            || ''
                        );


                    inputNomenclatura.value =
                        String(
                            selectTipoFolio.dataset
                                .nomenclaturaOriginal
                            || ''
                        );


                    /*
                     * Como regresamos al tipo original,
                     * restauramos también Personal y Unidades.
                     */
                    actualizarEstadoQjfEditar(
                        modal
                    );
                }
            }
        }
    );


    /* =====================================================
       ESTADO INICIAL
    ===================================================== */

    actualizarEstadoQjfEditar(
        modal
    );
}
