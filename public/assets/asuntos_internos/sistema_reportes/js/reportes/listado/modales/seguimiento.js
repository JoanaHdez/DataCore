import {
    mostrarResultado,
    cerrarResultado,
} from '../../notificaciones/resultado.js';

import {
    confirmarAccion,
} from '../../notificaciones/confirmacion.js';

import {
    cargarHistorialSeguimiento,
} from './seguimiento/historial.js';

import {
    inicializarSancionSeguimiento,
    actualizarCampoOtroSancion,
    validarSancionSeguimiento,
    obtenerSancionSeleccionada,
    normalizarSancion,
    existeCambioRealSancion,
    determinarAccionSancionEdicion,
    obtenerTextoSancion,
    cargarSancionActual,
} from './seguimiento/sanciones.js';

import {
    consultarSeguimientos,
    registrarSeguimiento,
    actualizarSeguimientoBackend,
} from './seguimiento/api.js';

import {
    asignarValor,
    asignarTexto,
    obtenerFechaActual,
    obtenerClaseEstado,
} from './seguimiento/utils.js';

import {
    prepararFormularioSeguimiento,
    actualizarInterfazModoFormulario,
    cargarDatosSeguimiento,
    iniciarEdicionSeguimiento,
    iniciarDetalleSeguimiento,
    inicializarCancelarEdicion,
    cancelarEdicionSeguimiento,
    inicializarMayusculasSeguimiento,
} from './seguimiento/formulario.js';

import {
    normalizarSeguimientos,
} from './seguimiento/normalizadores.js';

import {
    abrirModalSeguimiento,
    cerrarModalSeguimiento,
} from './seguimiento/modal.js';

/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Seguimiento
========================================================= */


/* =========================================================
   ESTADO DEL MÓDULO
========================================================= */

const estadoSeguimiento = {

    idReporte:
        0,

    filaActual:
        null,

    reporte:
        null,

    sancionActual:
        null,

    seguimientos:
        [],

    modoEdicion:
        false,

    modoDetalle:
        false,

    idSeguimientoEdicion:
        0,

    seguimientoEdicion:
        null,

};


/* =========================================================
   INICIALIZACIÓN
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarSeguimientoReporte();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarSeguimientoReporte() {

    const modal =
        document.querySelector(
            '#modal-seguimiento-reporte'
        );


    const formulario =
        document.querySelector(
            '#form-seguimiento-reporte'
        );


    if (
        !modal
        || !formulario
    ) {
        return;
    }

    inicializarMayusculasSeguimiento(
        formulario
    );


    inicializarSancionSeguimiento(
        modal
    );


    inicializarCancelarEdicion(
        modal,
        formulario,
        estadoSeguimiento
    );


    /* =====================================================
       ABRIR MODAL
    ===================================================== */

    document.addEventListener(
        'click',
        async (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="seguimiento"]'
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
                    'No fue posible identificar el reporte.'
                );

                return;
            }


            boton.disabled =
                true;


            try {

                const datos =
                    await consultarSeguimientos(
                        idReporte
                    );


                if (
                    !datos
                    || datos.success !== true
                    || !datos.reporte
                ) {

                    throw new Error(
                        datos?.message
                        || 'No fue posible consultar el seguimiento.'
                    );
                }


                /* =================================================
                   ESTADO
                ================================================= */

                estadoSeguimiento.idReporte =
                    idReporte;


                estadoSeguimiento.filaActual =
                    fila;


                estadoSeguimiento.reporte =
                    datos.reporte;


                estadoSeguimiento.sancionActual =
                    normalizarSancion(
                        datos.sancion
                    );


                estadoSeguimiento.seguimientos =
                    normalizarSeguimientos(
                        datos.seguimientos
                    );


                /* =================================================
                   REINICIAR MODO EDICIÓN
                ================================================= */

                limpiarModoEdicion();


                /* =================================================
                   CARGAR
                ================================================= */

                cargarDatosSeguimiento(
                    modal,
                    formulario,
                    datos.reporte
                );


                cargarHistorialSeguimiento(
                    modal,
                    estadoSeguimiento.seguimientos
                );


                actualizarInterfazModoFormulario(
                    modal,
                    estadoSeguimiento.modoEdicion
                );


                abrirModalSeguimiento(
                    modal
                );


            } catch (error) {

                console.error(
                    'Error cargando seguimiento:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible consultar el seguimiento.'
                );


            } finally {

                boton.disabled =
                    false;

            }

        }
    );


    /* =====================================================
    DETALLES DESDE HISTORIAL
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonDetalles =
                evento.target.closest(
                    '[data-detalle-seguimiento]'
                );


            if (!botonDetalles) {
                return;
            }


            const idSeguimiento =
                Number(
                    botonDetalles.dataset
                        .detalleSeguimiento
                    || 0
                );


            if (
                !Number.isInteger(idSeguimiento)
                || idSeguimiento <= 0
            ) {
                return;
            }


            const seguimiento =
                estadoSeguimiento.seguimientos
                    .find(
                        (item) =>
                            item.id_seguimiento
                            === idSeguimiento
                    );


            if (!seguimiento) {

                window.alert(
                    'No fue posible localizar el seguimiento.'
                );

                return;
            }


            iniciarDetalleSeguimiento(
                modal,
                formulario,
                seguimiento,
                estadoSeguimiento
            );

        }
    );


    /* =====================================================
       EDITAR DESDE HISTORIAL
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonEditar =
                evento.target.closest(
                    '[data-editar-seguimiento]'
                );


            if (!botonEditar) {
                return;
            }


            const idSeguimiento =
                Number(
                    botonEditar.dataset
                        .editarSeguimiento
                    || 0
                );


            if (
                !Number.isInteger(idSeguimiento)
                || idSeguimiento <= 0
            ) {
                return;
            }


            const seguimiento =
                estadoSeguimiento.seguimientos
                    .find(
                        (item) =>
                            item.id_seguimiento
                            === idSeguimiento
                    );


            if (!seguimiento) {

                window.alert(
                    'No fue posible localizar el seguimiento.'
                );

                return;
            }


            iniciarEdicionSeguimiento(
                modal,
                formulario,
                seguimiento,
                estadoSeguimiento
            );

        }
    );


    /* =====================================================
       CERRAR MODAL
    ===================================================== */

    modal.addEventListener(
        'click',
        (evento) => {

            const botonCerrar =
                evento.target.closest(
                    '[data-cerrar-modal-seguimiento]'
                );


            if (!botonCerrar) {
                return;
            }


            cerrarModalSeguimiento(
                modal
            );


            limpiarEstadoSeguimiento();

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key === 'Escape'
                && modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModalSeguimiento(
                    modal
                );


                limpiarEstadoSeguimiento();

            }

        }
    );


    /* =====================================================
       SUBMIT
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            /* =================================================
               DETECTAR SI REALMENTE ESTAMOS EDITANDO
            ================================================= */

            const inputIdEdicion =
                formulario.querySelector(
                    '#seguimiento-id-edicion'
                );


            const idSeguimientoEdicion =
                Number(
                    inputIdEdicion?.value
                    || estadoSeguimiento.idSeguimientoEdicion
                    || 0
                );


            const estaEditando =
                Number.isInteger(
                    idSeguimientoEdicion
                )
                && idSeguimientoEdicion > 0;


            /* =================================================
               EDICIÓN
            ================================================= */

            if (estaEditando) {

                estadoSeguimiento.modoEdicion =
                    true;


                estadoSeguimiento.idSeguimientoEdicion =
                    idSeguimientoEdicion;


                if (
                    !estadoSeguimiento.seguimientoEdicion
                    || estadoSeguimiento
                        .seguimientoEdicion
                        .id_seguimiento
                    !== idSeguimientoEdicion
                ) {

                    estadoSeguimiento.seguimientoEdicion =
                        estadoSeguimiento.seguimientos.find(
                            (seguimiento) =>
                                seguimiento.id_seguimiento
                                === idSeguimientoEdicion
                        )
                        || null;
                }


                await procesarEdicionSeguimiento(
                    modal,
                    formulario
                );


                return;
            }


            /* =================================================
               NUEVO
            ================================================= */

            estadoSeguimiento.modoEdicion =
                false;


            estadoSeguimiento.idSeguimientoEdicion =
                0;


            estadoSeguimiento.seguimientoEdicion =
                null;


            await procesarNuevoSeguimiento(
                modal,
                formulario
            );

        }
    );

}


/* =========================================================
   NUEVO SEGUIMIENTO
========================================================= */

async function procesarNuevoSeguimiento(
    modal,
    formulario
) {

    const idReporte =
        estadoSeguimiento.idReporte;


    if (
        !Number.isInteger(idReporte)
        || idReporte <= 0
    ) {

        mostrarResultado({
            tipo: 'error',
            titulo: 'Reporte no identificado',
            mensaje:
                'No fue posible identificar el reporte.',
        });

        return;
    }


    /* =====================================================
       VALIDACIÓN SANCIÓN
    ===================================================== */

    if (
        !validarSancionSeguimiento(
            modal
        )
    ) {
        return;
    }


    /* =====================================================
       VALIDACIÓN NATIVA
    ===================================================== */

    if (
        !formulario.checkValidity()
    ) {

        formulario.reportValidity();

        return;
    }


    /* =====================================================
       ESTADO ACTUAL DEL REPORTE
    ===================================================== */

    const estadoActual =
        String(
            estadoSeguimiento
                .reporte
                ?.estado_actual
            || 'Pendiente'
        ).trim();


    /* =====================================================
       ESTADO SELECCIONADO EN SEGUIMIENTO
    ===================================================== */

    const inputEstado =
        formulario.querySelector(
            '#seguimiento-estado'
        );


    const estadoNuevo =
        String(
            inputEstado?.value
            || ''
        ).trim();


    /* =====================================================
       DETECTAR CAMBIO REAL DE ESTADO
    ===================================================== */

    const hayCambioEstado =
        estadoNuevo !== ''
        && estadoNuevo !== estadoActual;


    /* =====================================================
       CONFIRMAR CAMBIO DE ESTADO
    ===================================================== */

    if (hayCambioEstado) {

        let mensajeEstado =
            'El estado seleccionado es diferente al estado actual de la queja.'
            + '\n\n'
            + `Estado actual: ${estadoActual}`
            + '\n'
            + `Nuevo estado: ${estadoNuevo}`
            + '\n\n';


        /* -------------------------------------------------
           MENSAJE ESPECIAL AL FINALIZAR
        ------------------------------------------------- */

        if (
            estadoNuevo === 'Finalizado'
        ) {

            mensajeEstado +=
                'Al confirmar, la queja quedará finalizada desde este seguimiento.'
                + '\n\n'
                + '¿Deseas continuar?';

        } else {

            mensajeEstado +=
                'Al confirmar, el estado general de la queja también será actualizado.'
                + '\n\n'
                + '¿Deseas continuar?';
        }


        const confirmado =
            await confirmarAccion({

                titulo:
                    estadoNuevo === 'Finalizado'
                        ? 'Confirmar finalización de la queja'
                        : 'Confirmar cambio de estado',

                mensaje:
                    mensajeEstado,

                textoConfirmar:
                    estadoNuevo === 'Finalizado'
                        ? 'Finalizar'
                        : 'Cambiar estado',

                textoCancelar:
                    'Cancelar',

            });


        if (!confirmado) {
            return;
        }

    }


    /* =====================================================
       SANCIÓN SELECCIONADA
    ===================================================== */

    const sancionSeleccionada =
        obtenerSancionSeleccionada(
            modal
        );


    /* =====================================================
       DETECTAR CAMBIO REAL DE SANCIÓN

       Ya NO genera confirmación.

       Solamente se utiliza para decidir si realmente
       debemos enviar una modificación de sanción.
    ===================================================== */

    const hayCambioSancion =
        existeCambioRealSancion(
            estadoSeguimiento.sancionActual,
            sancionSeleccionada
        );


    /* =====================================================
       FORM DATA
    ===================================================== */

    const datos =
        new FormData(
            formulario
        );


    /*
     * Si seleccionó exactamente la misma sanción,
     * se envía como "Sin cambio".
     */

    if (
        sancionSeleccionada.tipo
        && !hayCambioSancion
    ) {

        datos.set(
            'sancion_disciplinaria',
            ''
        );


        datos.set(
            'sancion_otro',
            ''
        );

    }


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
           REGISTRAR
        ================================================= */

        const resultado =
            await registrarSeguimiento(
                idReporte,
                datos
            );


        if (
            !resultado
            || resultado.success !== true
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible registrar el seguimiento.'
            );
        }


        /* =================================================
           REFRESCAR
        ================================================= */

        await refrescarSeguimientoCompleto(
            modal,
            formulario,
            idReporte
        );


        /* =================================================
           ACTUALIZAR LISTADO
        ================================================= */

        actualizarListadoRelacionado();


        /* =================================================
           EVENTO GLOBAL
        ================================================= */

        document.dispatchEvent(
            new CustomEvent(
                'seguimientoReporteActualizado',
                {
                    detail: {

                        idReporte,

                        estado:
                            estadoSeguimiento
                                .reporte
                                ?.estado_actual
                            || '',

                        estadoAnterior:
                            estadoActual,

                        estadoNuevo:
                            estadoNuevo,

                        cambioEstado:
                            hayCambioEstado,

                        sancion:
                            estadoSeguimiento
                                .sancionActual,

                        seguimiento:
                            resultado.seguimiento
                            || null,

                    },
                }
            )
        );


        /* =================================================
           RESULTADO CORRECTO
        ================================================= */

        mostrarResultado({

            tipo:
                'success',

            titulo:
                hayCambioEstado
                    ? (
                        estadoNuevo === 'Finalizado'
                            ? 'Queja finalizada'
                            : 'Estado actualizado'
                    )
                    : 'Seguimiento registrado',

            mensaje:
                hayCambioEstado
                    ? (
                        estadoNuevo === 'Finalizado'
                            ? 'El seguimiento se registró correctamente y la queja quedó finalizada.'
                            : `El seguimiento se registró correctamente y el estado cambió a ${estadoNuevo}.`
                    )
                    : 'El seguimiento se registró correctamente.',

        });


        /* =================================================
           CERRAR MODAL
        ================================================= */

        cerrarModalSeguimiento(
            modal
        );


        /* =================================================
           LIMPIAR ESTADO
        ================================================= */

        limpiarEstadoSeguimiento();


        /* =================================================
           CERRAR NOTIFICACIÓN
        ================================================= */

        window.setTimeout(
            () => {

                cerrarResultado();

            },
            1800
        );


    } catch (error) {

        console.error(
            'Error registrando seguimiento:',
            error
        );


        const mensaje =
            String(
                error?.message
                || ''
            ).trim();


        /* =================================================
           FOLIO IP REPETIDO
        ================================================= */

        if (
            mensaje
                .toLocaleLowerCase('es-MX')
                .includes(
                    'folio ip'
                )
            &&
            mensaje
                .toLocaleLowerCase('es-MX')
                .includes(
                    'registrado'
                )
        ) {

            mostrarResultado({
                tipo: 'warning',
                titulo: 'Folio IP repetido',
                mensaje:
                    mensaje
                    || 'El Folio IP ya se encuentra registrado. Debes ingresar uno diferente para continuar.',
            });


            const inputFolioIp =
                formulario.querySelector(
                    '#seguimiento-folio-ip'
                );


            inputFolioIp?.focus();


            return;
        }


        /* =================================================
           OTRO ERROR
        ================================================= */

        mostrarResultado({
            tipo: 'error',
            titulo: 'No fue posible guardar',
            mensaje:
                mensaje
                || 'No fue posible registrar el seguimiento.',
        });

    } finally {

        /* =================================================
           RESTAURAR BOTÓN
        ================================================= */

        if (botonGuardar) {

            botonGuardar.disabled =
                false;


            botonGuardar.innerHTML =
                textoOriginal;

        }

    }

}


/* =========================================================
   PROCESAR EDICIÓN
========================================================= */

async function procesarEdicionSeguimiento(
    modal,
    formulario
) {

    const seguimientoAnterior =
        estadoSeguimiento.seguimientoEdicion;


    const idSeguimiento =
        estadoSeguimiento.idSeguimientoEdicion;


    if (
        !seguimientoAnterior
        || !Number.isInteger(idSeguimiento)
        || idSeguimiento <= 0
    ) {

        mostrarResultado({
            tipo: 'error',
            titulo: 'Seguimiento no identificado',
            mensaje:
                'No fue posible identificar el seguimiento que deseas editar.',
        });

        return;
    }


    /* =====================================================
       VALIDACIÓN SANCIÓN
    ===================================================== */

    if (
        !validarSancionSeguimiento(
            modal
        )
    ) {
        return;
    }


    /* =====================================================
       VALIDACIÓN NATIVA
    ===================================================== */

    if (
        !formulario.checkValidity()
    ) {

        formulario.reportValidity();

        return;
    }


    /* =====================================================
       ESTADO REGISTRADO EN EL SEGUIMIENTO
    ===================================================== */

    const estadoAnterior =
        String(
            seguimientoAnterior.estado
            || ''
        ).trim();


    /* =====================================================
       ESTADO SELECCIONADO
    ===================================================== */

    const estadoNuevo =
        String(
            formulario.querySelector(
                '#seguimiento-estado'
            )?.value
            || ''
        ).trim();


    /* =====================================================
       DETECTAR CAMBIO REAL DE ESTADO
    ===================================================== */

    const hayCambioEstado =
        estadoNuevo !== ''
        && estadoNuevo !== estadoAnterior;


    /* =====================================================
       CONFIRMAR CAMBIO DE ESTADO

       Esta es la ÚNICA confirmación especial de la edición.
    ===================================================== */

    if (hayCambioEstado) {

        let mensajeEstado =
            'Estás cambiando el estado registrado en este seguimiento.'
            + '\n\n'
            + `Estado registrado: ${estadoAnterior}`
            + '\n'
            + `Nuevo estado: ${estadoNuevo}`
            + '\n\n';


        if (
            estadoNuevo === 'Finalizado'
        ) {

            mensajeEstado +=
                'El seguimiento quedará registrado con estado Finalizado.'
                + '\n\n'
                + '¿Deseas continuar?';

        } else {

            mensajeEstado +=
                'El seguimiento se actualizará con el nuevo estado.'
                + '\n\n'
                + '¿Deseas continuar?';
        }


        const confirmado =
            await confirmarAccion({

                titulo:
                    estadoNuevo === 'Finalizado'
                        ? 'Confirmar cambio a Finalizado'
                        : 'Confirmar cambio de estado',

                mensaje:
                    mensajeEstado,

                textoConfirmar:
                    estadoNuevo === 'Finalizado'
                        ? 'Finalizar'
                        : 'Cambiar estado',

                textoCancelar:
                    'Cancelar',

            });


        if (!confirmado) {
            return;
        }

    }


    /* =====================================================
       SANCIÓN
    ===================================================== */

    const sancionAnterior =
        seguimientoAnterior.sancion;


    const sancionNueva =
        obtenerSancionSeleccionada(
            modal
        );


    /*
     * La sanción sigue funcionando exactamente igual.
     *
     * Solamente quitamos las confirmaciones antiguas.
     */

    const accionSancion =
        determinarAccionSancionEdicion(
            sancionAnterior,
            sancionNueva
        );


    console.log(
        'SANCIÓN EDICIÓN',
        {
            anterior:
                sancionAnterior,

            nueva:
                sancionNueva,

            accion:
                accionSancion,
        }
    );


    /* =====================================================
       DATOS
    ===================================================== */

    const datos =
        new URLSearchParams();


    datos.set(
        'fecha',
        formulario.querySelector(
            '#seguimiento-fecha'
        )?.value
        || ''
    );


    datos.set(
        'tipo',
        formulario.querySelector(
            '#seguimiento-tipo'
        )?.value
        || ''
    );


    datos.set(
        'estado',
        estadoNuevo
    );


    datos.set(
        'observaciones',
        formulario.querySelector(
            '#seguimiento-observaciones'
        )?.value
        || ''
    );


    /* =====================================================
       FOLIO IP
    ===================================================== */

    datos.set(
        'folio_ip',
        formulario.querySelector(
            '#seguimiento-folio-ip'
        )?.value
        || ''
    );


    /* =====================================================
       SANCIÓN
    ===================================================== */

    datos.set(
        'sancion_accion',
        accionSancion
    );


    datos.set(
        'sancion_disciplinaria',
        sancionNueva.tipo
        || ''
    );


    datos.set(
        'sancion_otro',
        sancionNueva.descripcion_otro
        || ''
    );


    /* =====================================================
       BOTÓN
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
            'Guardando cambios...';

    }


    try {

        console.log(
            'DATOS PUT SEGUIMIENTO',
            {
                idSeguimiento,

                fecha:
                    datos.get('fecha'),

                tipo:
                    datos.get('tipo'),

                estado:
                    datos.get('estado'),

                folio_ip:
                    datos.get('folio_ip'),

                sancion_accion:
                    datos.get('sancion_accion'),

                sancion_disciplinaria:
                    datos.get('sancion_disciplinaria'),

                sancion_otro:
                    datos.get('sancion_otro'),
            }
        );


        console.log(
            'EDITANDO SEGUIMIENTO',
            {
                modoEdicion:
                    estadoSeguimiento.modoEdicion,

                idEstado:
                    estadoSeguimiento.idSeguimientoEdicion,

                idFormulario:
                    formulario.querySelector(
                        '#seguimiento-id-edicion'
                    )?.value,

                idEnviado:
                    idSeguimiento,
            }
        );


        /* =================================================
           ACTUALIZAR SEGUIMIENTO
        ================================================= */

        const resultado =
            await actualizarSeguimientoBackend(
                idSeguimiento,
                datos
            );


        if (
            !resultado
            || resultado.success !== true
        ) {

            throw new Error(
                resultado?.message
                || 'No fue posible actualizar el seguimiento.'
            );
        }


        /* =================================================
           SALIR DE EDICIÓN
        ================================================= */

        limpiarModoEdicion();


        /* =================================================
           RECARGAR TODO

           Importante:
           primero refrescamos para que estadoSeguimiento tenga
           los datos actuales antes de cerrar el modal.
        ================================================= */

        await refrescarSeguimientoCompleto(
            modal,
            formulario,
            estadoSeguimiento.idReporte
        );


        /* =================================================
           ACTUALIZAR LISTADO
        ================================================= */

        actualizarListadoRelacionado();


        /* =================================================
           EVENTO GLOBAL
        ================================================= */

        document.dispatchEvent(
            new CustomEvent(
                'seguimientoReporteActualizado',
                {
                    detail: {

                        idReporte:
                            estadoSeguimiento
                                .idReporte,

                        estado:
                            estadoSeguimiento
                                .reporte
                                ?.estado_actual
                            || '',

                        estadoAnterior:
                            estadoAnterior,

                        estadoNuevo:
                            estadoNuevo,

                        cambioEstado:
                            hayCambioEstado,

                        sancion:
                            estadoSeguimiento
                                .sancionActual,

                        seguimientoEditado:
                            idSeguimiento,

                    },
                }
            )
        );


        /* =================================================
           RESULTADO
        ================================================= */

        mostrarResultado({

            tipo:
                'success',

            titulo:
                hayCambioEstado
                    ? (
                        estadoNuevo === 'Finalizado'
                            ? 'Estado actualizado a Finalizado'
                            : 'Estado actualizado'
                    )
                    : 'Seguimiento actualizado',

            mensaje:
                hayCambioEstado
                    ? `Los cambios del seguimiento se guardaron correctamente y el estado registrado cambió de ${estadoAnterior} a ${estadoNuevo}.`
                    : 'Los cambios del seguimiento se guardaron correctamente.',

        });


        /* =================================================
           CERRAR MODAL
        ================================================= */

        cerrarModalSeguimiento(
            modal
        );


        /* =================================================
           LIMPIAR ESTADO DEL MÓDULO
        ================================================= */

        limpiarEstadoSeguimiento();


        /* =================================================
           CERRAR NOTIFICACIÓN
        ================================================= */

        window.setTimeout(
            () => {

                cerrarResultado();

            },
            1800
        );


    } catch (error) {

        console.error(
            'Error actualizando seguimiento:',
            error
        );


        const mensaje =
            String(
                error?.message
                || ''
            ).trim();


        /* =================================================
           FOLIO IP REPETIDO
        ================================================= */

        if (
            mensaje
                .toLocaleLowerCase('es-MX')
                .includes(
                    'folio ip'
                )
            &&
            mensaje
                .toLocaleLowerCase('es-MX')
                .includes(
                    'registrado'
                )
        ) {

            mostrarResultado({
                tipo: 'warning',
                titulo: 'Folio IP repetido',
                mensaje:
                    mensaje
                    || 'El Folio IP ya se encuentra registrado. Debes ingresar uno diferente para continuar.',
            });


            const inputFolioIp =
                formulario.querySelector(
                    '#seguimiento-folio-ip'
                );


            inputFolioIp?.focus();


            return;
        }


        /* =================================================
           OTRO ERROR
        ================================================= */

        mostrarResultado({
            tipo: 'error',
            titulo: 'No fue posible actualizar',
            mensaje:
                mensaje
                || 'No fue posible actualizar el seguimiento.',
        });

    } finally {

        /* =================================================
           RESTAURAR BOTÓN
        ================================================= */

        if (botonGuardar) {

            botonGuardar.disabled =
                false;


            botonGuardar.innerHTML =
                textoOriginal;

        }

    }

}


/* =========================================================
   LIMPIAR MODO EDICIÓN
========================================================= */

function limpiarModoEdicion() {

    estadoSeguimiento.modoEdicion =
        false;

    estadoSeguimiento.modoDetalle =
        false;

    estadoSeguimiento.idSeguimientoEdicion =
        0;

    estadoSeguimiento.seguimientoEdicion =
        null;

}


/* =========================================================
   REFRESCAR TODO EL MODAL
========================================================= */

async function refrescarSeguimientoCompleto(
    modal,
    formulario,
    idReporte
) {

    const datos =
        await consultarSeguimientos(
            idReporte
        );


    if (
        !datos
        || datos.success !== true
        || !datos.reporte
    ) {

        throw new Error(
            datos?.message
            || 'No fue posible actualizar la información del seguimiento.'
        );
    }


    estadoSeguimiento.reporte =
        datos.reporte;


    estadoSeguimiento.sancionActual =
        normalizarSancion(
            datos.sancion
        );


    estadoSeguimiento.seguimientos =
        normalizarSeguimientos(
            datos.seguimientos
        );


    /* =====================================================
       HEADER
    ===================================================== */

    asignarTexto(
        modal,
        '#seguimiento-estado-actual',
        estadoSeguimiento.reporte
            ?.estado_actual
        || 'Pendiente'
    );


    cargarSancionActual(
        modal,
        estadoSeguimiento.sancionActual
    );


    /* =====================================================
       TABLA
    ===================================================== */

    if (
        estadoSeguimiento.filaActual
    ) {

        actualizarEstadoReporte(
            estadoSeguimiento.filaActual,
            estadoSeguimiento.reporte
                ?.estado_actual
            || 'Pendiente'
        );
    }


    /* =====================================================
       HISTORIAL
    ===================================================== */

    cargarHistorialSeguimiento(
        modal,
        estadoSeguimiento.seguimientos
    );


    /* =====================================================
       FORMULARIO
    ===================================================== */

    prepararFormularioSeguimiento(
        formulario,
        estadoSeguimiento.reporte
            ?.estado_actual
        || 'Pendiente'
    );


    actualizarCampoOtroSancion(
        modal
    );


    actualizarInterfazModoFormulario(
        modal,
        estadoSeguimiento.modoEdicion
    );

}


/* =========================================================
   ACTUALIZAR ESTADO LISTADO
========================================================= */

function actualizarEstadoReporte(
    fila,
    estado
) {

    if (!fila) {
        return;
    }


    const celdas =
        fila.querySelectorAll(
            'td'
        );


    if (
        celdas.length < 7
    ) {
        return;
    }


    const celdaEstado =
        celdas[5];


    celdaEstado.innerHTML =
        '';


    const etiqueta =
        document.createElement(
            'span'
        );


    etiqueta.className =
        `reportes-tabla__estado ${obtenerClaseEstado(
            estado
        )}`;


    etiqueta.textContent =
        estado
        || 'Pendiente';


    celdaEstado.appendChild(
        etiqueta
    );

}


/* =========================================================
   ACTUALIZAR FILTROS
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
                bubbles:
                    true,
            }
        )
    );

}


/* =========================================================
   LIMPIAR ESTADO
========================================================= */

function limpiarEstadoSeguimiento() {

    estadoSeguimiento.idReporte =
        0;


    estadoSeguimiento.filaActual =
        null;


    estadoSeguimiento.reporte =
        null;


    estadoSeguimiento.sancionActual =
        null;


    estadoSeguimiento.seguimientos =
        [];


    limpiarModoEdicion();

}
