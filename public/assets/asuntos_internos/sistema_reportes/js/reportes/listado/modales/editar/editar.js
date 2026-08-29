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
} from './evidencia.js';

import {
    cargarReporteEnFormulario,
    obtenerReporteDesdeFormulario,
    actualizarFilaDesdeReporte,
} from './formulario.js';


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


            /*
             * Evitamos abrir el modal con información
             * anterior mientras consultamos la BD.
             */

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


                /*
                 * Conservamos temporalmente el objeto porque
                 * el flujo actual de formulario todavía lo usa
                 * al obtener los cambios.
                 */

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
       TEMPORAL POR AHORA
    ===================================================== */

    formulario.addEventListener(
        'submit',
        (evento) => {

            evento.preventDefault();


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


            const reporteEditado =
                obtenerReporteDesdeFormulario(
                    formulario,
                    reporteAnterior
                );


            const nuevoFolio =
                construirFolio(
                    reporteEditado.prefijo,
                    reporteEditado.numero_folio
                );


            reporteEditado.folio =
                nuevoFolio;


            /*
             * IMPORTANTE:
             *
             * Todavía NO enviamos cambios al backend.
             *
             * Conservamos temporalmente el comportamiento
             * existente hasta comprobar que la carga real
             * funciona correctamente.
             */


            /* =================================================
               ACTUALIZAR MAP TEMPORAL
            ================================================= */

            if (
                nuevoFolio
                !== folioActual
            ) {

                reportesTemporales.delete(
                    folioActual
                );


                reportesTemporales.set(
                    nuevoFolio,
                    reporteEditado
                );

            } else {

                reportesTemporales.set(
                    folioActual,
                    reporteEditado
                );

            }


            /* =================================================
               ACTUALIZAR FILA TEMPORAL
            ================================================= */

            actualizarFilaDesdeReporte(
                filaActual,
                reporteEditado
            );


            actualizarHeaderEditar(
                modal,
                reporteEditado
            );


            actualizarListadoRelacionado();


            cerrarModalEditar(
                modal
            );


            limpiarReporteActual();

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


    /*
     * Los input[type="date"] esperan:
     *
     * YYYY-MM-DD
     */

    const coincidencia =
        fecha.match(
            /^(\d{4})-(\d{2})-(\d{2})/
        );


    if (coincidencia) {

        return `${coincidencia[1]}-${coincidencia[2]}-${coincidencia[3]}`;

    }


    /*
     * También aceptamos DD/MM/YYYY
     * por compatibilidad.
     */

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
   ACTUALIZAR LISTADO
========================================================= */

function actualizarListadoRelacionado() {

    const busqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


    if (!busqueda) {
        return;
    }


    busqueda.dispatchEvent(
        new Event(
            'input',
            {
                bubbles: true,
            }
        )
    );

}