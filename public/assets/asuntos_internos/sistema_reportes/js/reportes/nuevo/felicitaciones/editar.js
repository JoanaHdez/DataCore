
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

    inicializarNavegacionEditar(
        modal
    );

    inicializarGuardadoEditar(
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
