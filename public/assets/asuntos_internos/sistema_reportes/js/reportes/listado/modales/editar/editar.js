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
    obtenerFolioFila,
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
    crearReporteTemporalDesdeFila,
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
        (evento) => {

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


            const folio =
                obtenerFolioFila(
                    fila
                );


            if (!folio) {
                return;
            }


            establecerReporteActual(
                fila,
                folio
            );


            /*
             * Si aún no tenemos una copia temporal
             * del reporte, la construimos desde la fila.
             */
            if (
                !reportesTemporales.has(
                    folio
                )
            ) {

                const reporteInicial =
                    crearReporteTemporalDesdeFila(
                        fila
                    );


                reportesTemporales.set(
                    folio,
                    reporteInicial
                );

            }


            const reporte =
                reportesTemporales.get(
                    folio
                );


            cargarReporteEnFormulario(
                modal,
                formulario,
                reporte
            );


            actualizarHeaderEditar(
                modal,
                reporte
            );


            /*
             * Siempre abrir en la primera pestaña.
             */
            mostrarSeccionEditar(
                modal,
                'datos'
            );


            abrirModalEditar(
                modal
            );

        }
    );


    /* =====================================================
       GUARDAR CAMBIOS
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


            /* =================================================
               ACTUALIZAR MAP
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
               ACTUALIZAR FILA
            ================================================= */

            actualizarFilaDesdeReporte(
                filaActual,
                reporteEditado
            );


            /* =================================================
               ACTUALIZAR HEADER
            ================================================= */

            actualizarHeaderEditar(
                modal,
                reporteEditado
            );


            /* =================================================
               ACTUALIZAR LISTADO RELACIONADO
            ================================================= */

            actualizarListadoRelacionado();


            cerrarModalEditar(
                modal
            );


            limpiarReporteActual();

        }
    );

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
        reporte.resolucion
    );


    const titulo =
        modal.querySelector(
            '#modal-editar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            `Editar ${folio}`;

    }

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