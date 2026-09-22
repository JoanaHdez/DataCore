import {
    abrirModalEditar,
    cerrarModalEditar,
} from './editar/modal.js';

import {
    inicializarModalidadUnidadEditar,
} from './editar/unidades/modalidad.js';

import {
    inicializarBuscadorUnidadesEditar,
} from './editar/unidades/buscador.js';

import {
    inicializarBuscadorPersonalEditar,
} from './editar/personal/buscador.js';

import {
    inicializarNavegacionEditar,
} from './editar/navegacion.js';

import {
    prepararModalEditar,
} from './editar/preparar.js';

import {
    limpiarModalEditar,
} from './editar/limpiar.js';

import {
    inicializarGuardadoEditar,
} from './editar/guardar.js';


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
       MAYÚSCULAS AUTOMÁTICAS
    ===================================================== */

    inicializarMayusculasEditarFelicitacion(
        modal
    );


    /* =====================================================
       MODALIDAD DE UNIDAD
    ===================================================== */

    inicializarModalidadUnidadEditar(
        modal
    );


    /* =====================================================
       BUSCADOR DE UNIDADES
    ===================================================== */

    inicializarBuscadorUnidadesEditar(
        modal
    );


    /* =====================================================
       BUSCADOR DE PERSONAL
    ===================================================== */

    inicializarBuscadorPersonalEditar(
        modal
    );


    /* =====================================================
       NAVEGACIÓN
    ===================================================== */

    inicializarNavegacionEditar(
        modal
    );


    /* =====================================================
       GUARDADO
    ===================================================== */

    inicializarGuardadoEditar(
        modal
    );


    /* =====================================================
       BLOQUEAR ENTER
    ===================================================== */

    inicializarBloqueoEnterEditarFelicitacion(
        modal
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
                            folio,
                            limpiarModalEditar
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
   MAYÚSCULAS AUTOMÁTICAS
   EDITAR FELICITACIÓN
========================================================= */

function inicializarMayusculasEditarFelicitacion(
    modal
) {

    if (!modal) {
        return;
    }


    const formulario =
        modal.querySelector(
            'form'
        );


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
       CONVERTIR MIENTRAS SE ESCRIBE
    ===================================================== */

    formulario.addEventListener(
        'input',
        (evento) => {

            const campo =
                evento.target;


            if (
                !(
                    campo instanceof HTMLInputElement
                )
                && !(
                    campo instanceof HTMLTextAreaElement
                )
            ) {
                return;
            }


            /* =================================================
               TIPOS QUE NO DEBEN MODIFICARSE
            ================================================= */

            if (
                campo instanceof HTMLInputElement
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
   BLOQUEAR ENTER EN EDITAR FELICITACIÓN
========================================================= */

function inicializarBloqueoEnterEditarFelicitacion(
    modal
) {

    if (!modal) {
        return;
    }


    const formulario =
        modal.querySelector(
            'form'
        );


    if (!formulario) {
        return;
    }


    /* =====================================================
       EVITAR INICIALIZACIÓN DUPLICADA
    ===================================================== */

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


    /* =====================================================
       BLOQUEAR ENTER
    ===================================================== */

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

               Enter se conserva para permitir saltos
               de línea.
            ================================================= */

            if (
                evento.target
                    instanceof HTMLTextAreaElement
            ) {
                return;
            }


            /* =================================================
               EVITAR ENVÍO ACCIDENTAL DEL FORMULARIO
            ================================================= */

            evento.preventDefault();

        }
    );

}