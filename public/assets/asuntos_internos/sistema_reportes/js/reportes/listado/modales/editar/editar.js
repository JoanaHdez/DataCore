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
    inicializarEditarSancion,
    cargarSancionEditar,
    validarSancionEditar,
    sancionFueModificada,
    obtenerSancionEditar,
    obtenerTextoSancionEditar,
} from './sanciones.js';

import {
    mostrarResultado,
} from '../../../notificaciones/resultado.js';

import {
    confirmarAccion,
} from '../../../notificaciones/confirmacion.js';

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


    inicializarEditarSancion(
        modal
    );


    inicializarUbicacionEditar(
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
                !Number.isInteger(idReporte)
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


                /* =================================================
                UBICACIÓN / GOOGLE MAPS
                ================================================= */

                inicializarUbicacionEditar(
                    modal
                );


                /*
                 * La sanción se carga directamente desde
                 * la respuesta real del backend.
                 *
                 * Así evitamos que pueda perderse durante
                 * la adaptación del objeto del reporte.
                 */

                cargarSancionEditar(
                    modal,
                    datos.sancion
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


    /* =====================================================
       GUARDAR CAMBIOS
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            /* =================================================
               REPORTE ACTUAL
            ================================================= */

            const filaActual =
                estadoEdicion.filaActual;


            const folioActual =
                estadoEdicion.folioActual;


            if (
                !filaActual
                || !folioActual
            ) {
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
                !Number.isInteger(idReporte)
                || idReporte <= 0
            ) {

                window.alert(
                    'No fue posible identificar el reporte que deseas actualizar.'
                );

                return;
            }


            /* =================================================
               VALIDACIÓN HTML
            ================================================= */

            if (
                typeof formulario.reportValidity
                === 'function'
                && !formulario.reportValidity()
            ) {
                return;
            }


            /* =================================================
               VALIDAR SANCIÓN
            ================================================= */

            if (
                !validarSancionEditar(
                    modal
                )
            ) {
                return;
            }


            /* =================================================
               DETECTAR CAMBIO DE SANCIÓN
            ================================================= */

            const cambioSancion =
                sancionFueModificada(
                    modal
                );


            if (cambioSancion) {

                const sancionNueva =
                    obtenerSancionEditar(
                        modal
                    );


                const sancionAnterior =
                    reporteAnterior.sancion
                    || null;


                const textoAnterior =
                    obtenerTextoSancionEditar(
                        sancionAnterior
                    );


                const textoNuevo =
                    obtenerTextoSancionEditar(
                        sancionNueva
                    );


                /*
                 * TEMPORAL:
                 *
                 * Esto nos permite probar primero el flujo.
                 *
                 * Posteriormente lo sustituiremos por el modal
                 * institucional con:
                 *
                 * - Cancelar
                 * - Corregir sanción
                 * - Ir a seguimiento
                 */

                const continuar =
                    await confirmarAccion({
                        titulo:
                            'Confirmar cambio de sanción',

                        mensaje:
                            'Estás modificando la sanción disciplinaria registrada actualmente.'
                            + '\n\n'
                            + `Sanción actual: ${textoAnterior}`
                            + '\n'
                            + `Nueva sanción: ${textoNuevo}`
                            + '\n\n'
                            + 'Si se trata de una corrección de captura, puedes continuar desde Editar.'
                            + '\n\n'
                            + 'Si corresponde a una nueva sanción derivada del seguimiento del caso, debe registrarse desde Seguimiento.',

                        textoConfirmar:
                            'Continuar',

                        textoCancelar:
                            'Cancelar',
                    });


                if (!continuar) {
                    return;
                }
            }


            /* =================================================
               OBTENER ESTADO FINAL DEL FORMULARIO
            ================================================= */

            const reporteEditado =
                obtenerReporteDesdeFormulario(
                    formulario,
                    reporteAnterior
                );


            /*
             * Conservamos también en el objeto local
             * la sanción seleccionada.
             */

            reporteEditado.sancion =
                obtenerSancionEditar(
                    modal
                );


            const nuevoFolio =
                construirFolio(
                    reporteEditado.prefijo,
                    reporteEditado.numero_folio
                );


            reporteEditado.folio =
                nuevoFolio;


            reporteEditado.id_reporte =
                idReporte;


            /* =================================================
               FORM DATA REAL
            ================================================= */

            const datos =
                new FormData(
                    formulario
                );


            /*
             * El folio también lo enviamos construido
             * explícitamente.
             */

            datos.set(
                'folio',
                nuevoFolio
            );


            /* =================================================
               SANCIÓN DISCIPLINARIA
            ================================================= */

            datos.set(
                'sancion_modificada',
                cambioSancion
                    ? '1'
                    : '0'
            );


            datos.set(
                'sancion_origen_cambio',
                cambioSancion
                    ? 'edicion'
                    : ''
            );


            /*
             * Forzamos los valores finales por seguridad.
             */

            const sancionFinal =
                obtenerSancionEditar(
                    modal
                );


            datos.set(
                'sancion_disciplinaria',
                sancionFinal.tipo
                || ''
            );


            datos.set(
                'sancion_otro',
                sancionFinal.descripcion_otro
                || ''
            );


            /* =================================================
               PERSONAL
            ================================================= */

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
                (persona, indice) => {

                    agregarValorFormData(
                        datos,
                        `personal[${indice}][plantilla_id]`,
                        persona.plantilla_id
                        ?? persona.id
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][perscod]`,
                        persona.perscod
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][nombre]`,
                        persona.nombre
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][area]`,
                        persona.area
                    );


                    agregarValorFormData(
                        datos,
                        `personal[${indice}][turno]`,
                        persona.turno
                    );

                }
            );


            /* =================================================
               UNIDADES
            ================================================= */

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
                (unidad, indice) => {

                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][parque_vehicular_id]`,
                        unidad.parque_vehicular_id
                        ?? unidad.id
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][no_economico]`,
                        unidad.no_economico
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][placas]`,
                        unidad.placas
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][marca]`,
                        unidad.marca
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][submarca]`,
                        unidad.submarca
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][color]`,
                        unidad.color
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][estatus]`,
                        unidad.estatus
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][servicio]`,
                        unidad.servicio
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][tipo]`,
                        unidad.tipo
                    );


                    agregarValorFormData(
                        datos,
                        `unidades[${indice}][origen]`,
                        unidad.origen
                    );

                }
            );


            /* =================================================
               EVIDENCIAS ELIMINADAS
            ================================================= */

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


            /* =================================================
               BOTÓN GUARDAR
            ================================================= */

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
                   ENVIAR AL BACKEND
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
                   ACTUALIZAR ESTADO LOCAL
                ================================================= */

                const folioGuardado =
                    resultado.folio
                    || nuevoFolio;


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
                   CERRAR MODAL
                ================================================= */

                cerrarModalEditar(
                    modal
                );


                limpiarReporteActual();


                /* =================================================
                RESULTADO
                ================================================= */

                mostrarResultado({
                    tipo: 'success',
                    titulo: 'Reporte actualizado',
                    mensaje: 'Los cambios del reporte se guardaron correctamente.',
                });


                /* =================================================
                RECARGAR
                ================================================= */

                window.setTimeout(
                    () => {

                        window.location.reload();

                    },
                    1800
                );


            } catch (error) {

                console.error(
                    'Error actualizando reporte:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible actualizar el reporte.'
                );


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
   CONSULTAR REPORTE REAL
========================================================= */

async function consultarReporteEditar(
    idReporte
) {

    const baseUrl =
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`;


    const url =
        new URL(
            `asuntos-internos/reportes/detalle/${idReporte}`,
            baseUrl
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
                    })
                )
                : [],


        /* =====================================================
           UNIDADES
        ===================================================== */

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

                        origen:
                            valorEditar(
                                unidad.origen
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
                        datos.sancion.id_seguimiento
                        !== null
                        && datos.sancion.id_seguimiento
                        !== undefined
                            ? Number(
                                datos.sancion.id_seguimiento
                                || 0
                            )
                            : null,

                }
                : null,


        quien_emite_resolucion:
            valorEditar(
                origen.quien_emite_resolucion
            ),

        resolucion:
            valorEditar(
                origen.resolucion
            ),

        estado_actual:
            valorEditar(
                origen.estado_actual
            ),

        motivos:
            valorEditar(
                origen.motivos
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
   HEADER
========================================================= */

function actualizarHeaderEditar(
    modal,
    reporte
) {

    const folio =
        reporte.folio
        || construirFolio(
            reporte.prefijo,
            reporte.numero_folio
        );


    asignarTextoEditar(
        modal,
        '#editar-meta-expediente',
        reporte.expediente
    );


    asignarTextoEditar(
        modal,
        '#editar-meta-estado',
        reporte.estado_actual
        || reporte.resolucion
    );


    const titulo =
        modal.querySelector(
            '#modal-editar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Editar ${folio}`
                : 'Editar reporte';
    }
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

    const baseUrl =
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`;


    const url =
        new URL(
            `asuntos-internos/reportes/actualizar/${idReporte}`,
            baseUrl
        );


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


    let resultado =
        null;


    try {

        resultado =
            await respuesta.json();

    } catch (error) {

        throw new Error(
            'El servidor devolvió una respuesta no válida.'
        );
    }


    if (!respuesta.ok) {

        throw new Error(
            resultado?.message
            || 'No fue posible actualizar el reporte.'
        );
    }


    return resultado;
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