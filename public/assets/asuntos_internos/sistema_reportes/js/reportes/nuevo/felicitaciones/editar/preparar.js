/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EDITAR
   PREPARAR MODAL
========================================================= */

import {
    cargarDatosGeneralesEditar,
} from './cargar.js';

import {
    cargarPersonalEditar,
} from './personal/personal.js';

import {
    cargarUnidadesEditar,
} from './unidades/unidades.js';


/* =========================================================
   PREPARAR MODAL
========================================================= */

export async function prepararModalEditar(
    modal,
    idFelicitacion,
    folio,
    limpiarModalEditar
) {

    /* =====================================================
       LIMPIAR DATOS ANTERIORES
    ===================================================== */

    limpiarModalEditar(
        modal
    );


    /* =====================================================
       ID
    ===================================================== */

    const inputId =
        modal.querySelector(
            '#editar-felicitacion-id'
        );


    if (inputId) {

        inputId.value =
            String(
                idFelicitacion
            );
    }


    /* =====================================================
       FOLIO DEL TÍTULO
    ===================================================== */

    const tituloFolio =
        modal.querySelector(
            '#editar-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            folio !== ''
                ? ` ${folio}`
                : '';
    }


    /* =====================================================
       CONSULTAR DATOS
    ===================================================== */

    try {

        const url =
            new URL(
                `DataCore/public/asuntos-internos/reportes/felicitaciones/detalle/${idFelicitacion}`,
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


        const resultado =
            await respuesta.json();


        if (
            !respuesta.ok
            || resultado?.success !== true
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible consultar la felicitación.'
            );
        }


        /* =================================================
           DATOS GENERALES
        ================================================= */

        cargarDatosGeneralesEditar(
            modal,
            resultado.felicitacion
            || {}
        );


        /* =================================================
           PERSONAL
        ================================================= */

        const personal =
            Array.isArray(
                resultado.personal
            )
                ? resultado.personal
                : [];


        cargarPersonalEditar(
            modal,
            personal
        );


        /* =================================================
           UNIDADES
        ================================================= */

        const unidades =
            Array.isArray(
                resultado.unidades
            )
                ? resultado.unidades
                : [];


        cargarUnidadesEditar(
            modal,
            unidades
        );


        return true;


    } catch (error) {

        console.error(
            'Error consultando felicitación para editar:',
            error
        );


        return false;
    }
}