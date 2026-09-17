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


            datos.set(
                'sin_sanciones',
                sinSanciones?.checked
                    ? '1'
                    : '0'
            );


            datos.set(
                'baja_voluntaria',
                bajaVoluntaria?.checked
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