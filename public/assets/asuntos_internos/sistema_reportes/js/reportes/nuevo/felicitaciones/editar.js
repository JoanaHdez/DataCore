import {
    escaparHtml,
    obtenerInicialApellido,
    obtenerFotoPersonal,
} from './editar/utilidades.js';

import {
    abrirModalEditar,
    cerrarModalEditar,
} from './editar/modal.js';

import {
    cargarDatosGeneralesEditar,
} from './editar/cargar.js';

import {
    cargarUnidadesEditar,
} from './editar/unidades/unidades.js';

import {
    inicializarModalidadUnidadEditar,
} from './editar/unidades/modalidad.js';

import {
    inicializarBuscadorUnidadesEditar,
} from './editar/unidades/buscador.js';

import {
    cargarPersonalEditar,
} from './editar/personal/personal.js';

import {
    inicializarBuscadorPersonalEditar,
} from './editar/personal/buscador.js';


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Felicitaciones - Editar
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarEditarFelicitacion();
    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarEditarFelicitacion() {

    const modal =
        document.querySelector(
            '#modal-editar-felicitacion'
        );


    const botonesEditar =
        document.querySelectorAll(
            '[data-accion-felicitacion="editar"]'
        );


    if (
        !modal
        || botonesEditar.length === 0
    ) {
        return;
    }


    /* =====================================================
       MODALIDAD DE UNIDAD
    ===================================================== */

    inicializarModalidadUnidadEditar(
        modal
    );

    inicializarBuscadorUnidadesEditar(
        modal
    );

    inicializarBuscadorPersonalEditar(
        modal
    );

    /* =====================================================
       NAVEGACIÓN ENTRE PESTAÑAS
    ===================================================== */

    const botonesSeccion =
        modal.querySelectorAll(
            '[data-seccion-editar-felicitacion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-panel-editar-felicitacion]'
        );


    botonesSeccion.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                () => {

                    const seccion =
                        String(
                            boton.dataset
                                .seccionEditarFelicitacion
                            || ''
                        ).trim();


                    if (
                        seccion === ''
                    ) {
                        return;
                    }


                    /* =========================================
                       BOTONES
                    ========================================== */

                    botonesSeccion.forEach(
                        (botonActual) => {

                            const activo =
                                botonActual.dataset
                                    .seccionEditarFelicitacion
                                === seccion;


                            botonActual.classList.toggle(
                                'modal-felicitacion-editar__nav-btn--activo',
                                activo
                            );


                            botonActual.setAttribute(
                                'aria-selected',
                                activo
                                    ? 'true'
                                    : 'false'
                            );
                        }
                    );


                    /* =========================================
                       PANELES
                    ========================================== */

                    paneles.forEach(
                        (panel) => {

                            const activo =
                                panel.dataset
                                    .panelEditarFelicitacion
                                === seccion;


                            panel.hidden =
                                !activo;


                            panel.classList.toggle(
                                'modal-felicitacion-editar__panel--activo',
                                activo
                            );
                        }
                    );


                    /* =========================================
                       SUBIR SCROLL DEL BODY
                    ========================================== */

                    const body =
                        modal.querySelector(
                            '.modal-felicitacion-editar__body'
                        );


                    if (body) {

                        body.scrollTop =
                            0;
                    }
                }
            );
        }
    );


    /* =====================================================
       ABRIR DESDE BOTÓN EDITAR
    ===================================================== */

    botonesEditar.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                async () => {

                    const idFelicitacion =
                        Number(
                            boton.dataset.idFelicitacion
                            || 0
                        );


                    const folio =
                        String(
                            boton.dataset.folio
                            || ''
                        ).trim();


                    if (
                        idFelicitacion <= 0
                    ) {
                        return;
                    }


                    const cargado =
                        await prepararModalEditar(
                            modal,
                            idFelicitacion,
                            folio
                        );


                    if (
                        cargado !== true
                    ) {
                        return;
                    }


                    abrirModalEditar(
                        modal
                    );
                }
            );
        }
    );


    /* =====================================================
       CERRAR
    ===================================================== */

    modal
        .querySelectorAll(
            '[data-cerrar-editar-felicitacion]'
        )
        .forEach(
            (elemento) => {

                elemento.addEventListener(
                    'click',
                    () => {

                        cerrarModalEditar(
                            modal
                        );
                    }
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
                evento.key === 'Escape'
                && !modal.hidden
            ) {

                cerrarModalEditar(
                    modal
                );
            }
        }
    );
}

/* =========================================================
   PREPARAR MODAL
========================================================= */

async function prepararModalEditar(
    modal,
    idFelicitacion,
    folio
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


        console.log(
            'Consultando felicitación para editar:',
            {
                idFelicitacion,
                folio,
                url: url.toString()
            }
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


        /* =================================================
           RESPUESTA JSON
        ================================================= */

        const resultado =
            await respuesta.json();


        /* =================================================
           DEPURACIÓN TEMPORAL

           Esto nos permitirá revisar exactamente qué
           está enviando el backend.
        ================================================= */

        console.log(
            'DETALLE FELICITACIÓN:',
            resultado
        );


        console.log(
            'FELICITACIÓN:',
            resultado?.felicitacion
        );


        console.log(
            'PERSONAL:',
            resultado?.personal
        );


        console.log(
            'UNIDADES:',
            resultado?.unidades
        );


        console.log(
            'PRIMERA UNIDAD COMPLETA:',
            resultado?.unidades?.[0]
        );


        console.table(
            resultado?.unidades
        );


        /* =================================================
           VALIDAR RESPUESTA
        ================================================= */

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


        console.log(
            'PERSONAL QUE SE ENVIARÁ AL MODAL:',
            personal
        );


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


        console.log(
            'UNIDADES QUE SE ENVIARÁN AL MODAL:',
            unidades
        );


        cargarUnidadesEditar(
            modal,
            unidades
        );


        /* =================================================
           CONSULTA COMPLETADA
        ================================================= */

        return true;


    } catch (error) {

        console.error(
            'Error consultando felicitación para editar:',
            error
        );


        return false;
    }
}


/* =========================================================
   LIMPIAR MODAL
========================================================= */

function limpiarModalEditar(
    modal
) {

    /* =====================================================
       ID
    ===================================================== */

    const inputId =
        modal.querySelector(
            '#editar-felicitacion-id'
        );


    if (inputId) {

        inputId.value =
            '';
    }


    /* =====================================================
       TÍTULO
    ===================================================== */

    const tituloFolio =
        modal.querySelector(
            '#editar-felicitacion-titulo-folio'
        );


    if (tituloFolio) {

        tituloFolio.textContent =
            '';
    }


    /* =====================================================
       FOLIO
    ===================================================== */

    const inputFolio =
        modal.querySelector(
            '#editar-felicitacion-folio'
        );


    if (inputFolio) {

        inputFolio.value =
            '';
    }


    /* =====================================================
       FECHA
    ===================================================== */

    const inputFecha =
        modal.querySelector(
            '#editar-felicitacion-fecha'
        );


    if (inputFecha) {

        inputFecha.value =
            '';
    }


    /* =====================================================
       FELICITANTE
    ===================================================== */

    const inputFelicitante =
        modal.querySelector(
            '#editar-felicitacion-felicitante'
        );


    if (inputFelicitante) {

        inputFelicitante.value =
            '';
    }


    /* =====================================================
       RAZÓN DE LA FELICITACIÓN
    ===================================================== */

    const inputRazon =
        modal.querySelector(
            '#editar-felicitacion-razon'
        );


    if (inputRazon) {

        inputRazon.value =
            '';
    }


    /* =====================================================
       PERSONAL
    ===================================================== */

    const tbodyPersonal =
        modal.querySelector(
            '#editar-felicitacion-personal'
        );


    if (tbodyPersonal) {

        tbodyPersonal.innerHTML = `
            <tr>
                <td colspan="6">
                    Sin personal relacionado
                </td>
            </tr>
        `;
    }


    /* =====================================================
    UNIDADES
    ===================================================== */

    const tbodyUnidades =
        modal.querySelector(
            '#editar-felicitacion-unidades'
        );


    if (tbodyUnidades) {

        tbodyUnidades.innerHTML = `
            <tr>
                <td colspan="7">
                    Sin unidades relacionadas
                </td>
            </tr>
        `;
    }


    const radioConUnidad =
        modal.querySelector(
            '#editar-felicitacion-con-unidad'
        );


    const radioSinUnidad =
        modal.querySelector(
            '#editar-felicitacion-sin-unidad'
        );


    if (radioConUnidad) {
        radioConUnidad.checked = false;
    }


    if (radioSinUnidad) {
        radioSinUnidad.checked = false;
    }

    /* =====================================================
       REGRESAR A PRIMERA PESTAÑA
    ===================================================== */

    const botones =
        modal.querySelectorAll(
            '[data-seccion-editar-felicitacion]'
        );


    const paneles =
        modal.querySelectorAll(
            '[data-panel-editar-felicitacion]'
        );


    botones.forEach(
        (boton) => {

            const activo =
                boton.dataset
                    .seccionEditarFelicitacion
                === 'datos';


            boton.classList.toggle(
                'modal-felicitacion-editar__nav-btn--activo',
                activo
            );
        }
    );


    paneles.forEach(
        (panel) => {

            const activo =
                panel.dataset
                    .panelEditarFelicitacion
                === 'datos';


            panel.hidden =
                !activo;


            panel.classList.toggle(
                'modal-felicitacion-editar__panel--activo',
                activo
            );
        }
    );
}

