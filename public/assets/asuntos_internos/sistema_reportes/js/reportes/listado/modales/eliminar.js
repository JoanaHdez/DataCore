import {
    mostrarResultado,
    cerrarResultado
} from '../../notificaciones/resultado.js';


/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Eliminar reporte
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarEliminarReporte();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarEliminarReporte() {

    const pagina =
        document.querySelector(
            '.reportes-page[data-usuario-rol]'
        );


    const modalUsuario =
        document.querySelector(
            '#modal-eliminar-reporte'
        );


    const formularioUsuario =
        document.querySelector(
            '#form-eliminar-reporte'
        );


    const inputPassword =
        document.querySelector(
            '#eliminar-reporte-password'
        );


    const mensajeUsuario =
        document.querySelector(
            '#eliminar-reporte-mensaje'
        );


    const modalAdmin =
        document.querySelector(
            '#modal-confirmar-eliminacion-reporte'
        );


    const formularioAdmin =
        document.querySelector(
            '#form-confirmar-eliminacion-reporte'
        );


    if (
        !pagina
        || !modalUsuario
        || !formularioUsuario
        || !inputPassword
        || !mensajeUsuario
        || !modalAdmin
        || !formularioAdmin
    ) {
        return;
    }


    const rolUsuario =
        String(
            pagina.dataset.usuarioRol
            ?? 'usuario'
        ).trim();


    let filaActual =
        null;


    let folioActual =
        '';


    let idReporteActual =
        0;


    /* =====================================================
       ABRIR MODAL SEGÚN ROL
    ===================================================== */

    document.addEventListener(
        'click',
        (evento) => {

            const boton =
                evento.target.closest(
                    '[data-accion="eliminar"]'
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
                    'No fue posible identificar el reporte a eliminar.'
                );

                return;
            }


            filaActual =
                fila;


            idReporteActual =
                idReporte;


            folioActual =
                String(
                    boton.dataset.folio
                    || fila
                        .querySelector('td')
                        ?.textContent
                    || ''
                ).trim();


            /* =============================================
               ADMIN
            ============================================== */

            if (
                rolUsuario === 'admin'
            ) {

                prepararModalConfirmacionAdmin(
                    modalAdmin,
                    folioActual
                );


                abrirModal(
                    modalAdmin
                );


                return;
            }


            /* =============================================
               USUARIO NORMAL
            ============================================== */

            prepararModalUsuario(
                modalUsuario,
                inputPassword,
                mensajeUsuario,
                folioActual
            );


            abrirModal(
                modalUsuario
            );

        }
    );


    /* =====================================================
       CERRAR MODAL USUARIO
    ===================================================== */

    modalUsuario.addEventListener(
        'click',
        (evento) => {

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal-eliminar]'
                );


            if (!cerrar) {
                return;
            }


            cerrarModal(
                modalUsuario
            );


            limpiarModalUsuario(
                inputPassword,
                mensajeUsuario
            );


            limpiarEstado();

        }
    );


    /* =====================================================
       CERRAR MODAL ADMIN
    ===================================================== */

    modalAdmin.addEventListener(
        'click',
        (evento) => {

            const cerrar =
                evento.target.closest(
                    '[data-cerrar-modal-confirmar-eliminacion]'
                );


            if (!cerrar) {
                return;
            }


            cerrarModal(
                modalAdmin
            );


            limpiarEstado();

        }
    );


    /* =====================================================
       ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Escape'
            ) {
                return;
            }


            if (
                modalUsuario.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModal(
                    modalUsuario
                );


                limpiarModalUsuario(
                    inputPassword,
                    mensajeUsuario
                );


                limpiarEstado();


                return;
            }


            if (
                modalAdmin.classList.contains(
                    'modal-reporte--visible'
                )
            ) {

                cerrarModal(
                    modalAdmin
                );


                limpiarEstado();

            }

        }
    );


    /* =====================================================
       USUARIO NORMAL
       AUTORIZAR + ELIMINAR REAL
    ===================================================== */

    formularioUsuario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            if (
                !filaActual
                || !folioActual
                || idReporteActual <= 0
            ) {
                return;
            }


            const password =
                inputPassword.value
                    .trim();


            if (!password) {

                mostrarMensajeUsuario(
                    mensajeUsuario,
                    'Ingresa la contraseña del administrador.',
                    'error'
                );


                inputPassword.focus();


                return;
            }


            const botonSubmit =
                formularioUsuario.querySelector(
                    'button[type="submit"]'
                );


            const textoOriginal =
                botonSubmit
                    ? botonSubmit.textContent
                    : 'Eliminar reporte';


            if (botonSubmit) {

                botonSubmit.disabled =
                    true;


                botonSubmit.textContent =
                    'Eliminando...';

            }


            mensajeUsuario.hidden =
                true;


            try {

                /*
                 * Ya no necesitamos hacer primero una
                 * autorización separada.
                 *
                 * El propio endpoint eliminarReporte()
                 * vuelve a validar la contraseña del admin
                 * antes de hacer el borrado lógico.
                 */

                const formData =
                    new FormData();


                formData.append(
                    'password_admin',
                    password
                );


                agregarCsrf(
                    formularioUsuario,
                    formData
                );


                const resultado =
                    await eliminarReporteBackend(
                        idReporteActual,
                        formData
                    );


                if (
                    !resultado
                    || resultado.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible eliminar el reporte.'
                    );
                }


                const folioEliminado =
                    resultado.folio
                    || folioActual;


                eliminarFilaTemporal(
                    filaActual
                );


                actualizarListadoDespuesEliminar();


                cerrarModal(
                    modalUsuario
                );


                limpiarModalUsuario(
                    inputPassword,
                    mensajeUsuario
                );


                limpiarEstado();


                mostrarNotificacionEliminado(
                    folioEliminado
                );


            } catch (error) {

                console.error(
                    'Error eliminando reporte:',
                    error
                );


                mostrarMensajeUsuario(
                    mensajeUsuario,
                    error.message
                    || 'No fue posible eliminar el reporte.',
                    'error'
                );


                inputPassword.value =
                    '';


                inputPassword.focus();


            } finally {

                if (botonSubmit) {

                    botonSubmit.disabled =
                        false;


                    botonSubmit.textContent =
                        textoOriginal;

                }

            }

        }
    );


    /* =====================================================
       ADMIN
       ELIMINACIÓN DIRECTA REAL
    ===================================================== */

    formularioAdmin.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            if (
                !filaActual
                || !folioActual
                || idReporteActual <= 0
            ) {
                return;
            }


            const botonSubmit =
                formularioAdmin.querySelector(
                    'button[type="submit"]'
                );


            const textoOriginal =
                botonSubmit
                    ? botonSubmit.textContent
                    : 'Eliminar reporte';


            if (botonSubmit) {

                botonSubmit.disabled =
                    true;


                botonSubmit.textContent =
                    'Eliminando...';

            }


            try {

                const formData =
                    new FormData();


                agregarCsrf(
                    formularioAdmin,
                    formData
                );


                const resultado =
                    await eliminarReporteBackend(
                        idReporteActual,
                        formData
                    );


                if (
                    !resultado
                    || resultado.success !== true
                ) {

                    throw new Error(
                        resultado?.message
                        || 'No fue posible eliminar el reporte.'
                    );
                }


                const folioEliminado =
                    resultado.folio
                    || folioActual;


                eliminarFilaTemporal(
                    filaActual
                );


                actualizarListadoDespuesEliminar();


                cerrarModal(
                    modalAdmin
                );


                limpiarEstado();


                mostrarNotificacionEliminado(
                    folioEliminado
                );


            } catch (error) {

                console.error(
                    'Error eliminando reporte:',
                    error
                );


                window.alert(
                    error.message
                    || 'No fue posible eliminar el reporte.'
                );


            } finally {

                if (botonSubmit) {

                    botonSubmit.disabled =
                        false;


                    botonSubmit.textContent =
                        textoOriginal;

                }

            }

        }
    );


    /* =====================================================
       LIMPIAR ESTADO
    ===================================================== */

    function limpiarEstado() {

        filaActual =
            null;


        folioActual =
            '';


        idReporteActual =
            0;

    }

}


/* =========================================================
   ELIMINAR REPORTE EN BACKEND
========================================================= */

async function eliminarReporteBackend(
    idReporte,
    formData
) {

    const baseUrl =
        obtenerBaseUrlEliminar();


    const url =
        new URL(
            `asuntos-internos/reportes/listado/eliminar/${idReporte}`,
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

                    'X-Requested-With':
                        'XMLHttpRequest',
                },

                credentials:
                    'same-origin',

                body:
                    formData,
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
            || 'No fue posible eliminar el reporte.'
        );

    }


    return datos;

}


/* =========================================================
   AGREGAR CSRF
========================================================= */

function agregarCsrf(
    formulario,
    formData
) {

    if (
        !formulario
        || !(formData instanceof FormData)
    ) {
        return;
    }


    /*
     * Buscamos únicamente un hidden con nombre.
     * Así conservamos el comportamiento que ya
     * tenía este módulo.
     */

    const csrf =
        formulario.querySelector(
            'input[type="hidden"][name]'
        );


    if (
        !csrf
        || !csrf.name
        || !csrf.value
    ) {
        return;
    }


    formData.append(
        csrf.name,
        csrf.value
    );

}


/* =========================================================
   PREPARAR MODAL USUARIO
========================================================= */

function prepararModalUsuario(
    modal,
    inputPassword,
    mensaje,
    folio
) {

    limpiarModalUsuario(
        inputPassword,
        mensaje
    );


    const folioElemento =
        modal.querySelector(
            '#eliminar-reporte-folio'
        );


    if (folioElemento) {

        folioElemento.textContent =
            folio || '—';

    }


    const titulo =
        modal.querySelector(
            '#modal-eliminar-titulo'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Eliminar ${folio}`
                : 'Eliminar reporte';

    }


    window.setTimeout(
        () => {

            inputPassword.focus();

        },
        100
    );

}


/* =========================================================
   PREPARAR MODAL ADMIN
========================================================= */

function prepararModalConfirmacionAdmin(
    modal,
    folio
) {

    const folioElemento =
        modal.querySelector(
            '#confirmar-eliminacion-folio'
        );


    if (folioElemento) {

        folioElemento.textContent =
            folio || '—';

    }


    const titulo =
        modal.querySelector(
            '#titulo-confirmar-eliminacion'
        );


    if (titulo) {

        titulo.textContent =
            folio
                ? `Eliminar ${folio}`
                : 'Eliminar reporte';

    }

}


/* =========================================================
   LIMPIAR MODAL USUARIO
========================================================= */

function limpiarModalUsuario(
    inputPassword,
    mensaje
) {

    inputPassword.value =
        '';


    mensaje.textContent =
        '';


    mensaje.hidden =
        true;


    mensaje.classList.remove(
        'eliminar-reporte__mensaje--error',
        'eliminar-reporte__mensaje--info',
        'eliminar-reporte__mensaje--success'
    );

}


/* =========================================================
   MENSAJE USUARIO
========================================================= */

function mostrarMensajeUsuario(
    mensaje,
    texto,
    tipo
) {

    mensaje.textContent =
        texto;


    mensaje.hidden =
        false;


    mensaje.classList.remove(
        'eliminar-reporte__mensaje--error',
        'eliminar-reporte__mensaje--info',
        'eliminar-reporte__mensaje--success'
    );


    mensaje.classList.add(
        `eliminar-reporte__mensaje--${tipo}`
    );

}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModal(
    modal
) {

    modal.classList.add(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'false'
    );


    document.body.classList.add(
        'modal-abierto'
    );

}


/* =========================================================
   CERRAR MODAL
========================================================= */

function cerrarModal(
    modal
) {

    const activo =
        document.activeElement;


    if (
        activo
        && modal.contains(
            activo
        )
    ) {

        activo.blur();

    }


    modal.classList.remove(
        'modal-reporte--visible'
    );


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.classList.remove(
        'modal-abierto'
    );

}


/* =========================================================
   QUITAR FILA DESPUÉS DE CONFIRMACIÓN REAL
========================================================= */

function eliminarFilaTemporal(
    fila
) {

    if (!fila) {
        return;
    }


    /*
     * Ojo:
     *
     * A estas alturas el backend ya hizo el
     * borrado lógico real.
     *
     * Aquí solamente retiramos la fila de la
     * interfaz para evitar recargar toda la página.
     */

    fila.remove();

}


/* =========================================================
   ACTUALIZAR LISTADO
========================================================= */

function actualizarListadoDespuesEliminar() {

    const busqueda =
        document.querySelector(
            '#filtro_busqueda'
        );


    if (busqueda) {

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


    actualizarTablaVacia();

}


/* =========================================================
   TABLA VACÍA
========================================================= */

function actualizarTablaVacia() {

    const tbody =
        document.querySelector(
            '#tabla-reportes-body'
        );


    if (!tbody) {
        return;
    }


    const filas =
        Array.from(
            tbody.querySelectorAll(
                'tr'
            )
        )
            .filter(
                (fila) => {

                    return !fila
                        .classList
                        .contains(
                            'reportes-tabla__empty'
                        );

                }
            );


    if (
        filas.length > 0
    ) {
        return;
    }


    if (
        tbody.querySelector(
            '.reportes-tabla__empty'
        )
    ) {
        return;
    }


    const fila =
        document.createElement(
            'tr'
        );


    fila.className =
        'reportes-tabla__empty';


    fila.innerHTML = `
        <td colspan="9">

            <div class="reportes-tabla__empty-content">

                <strong>
                    No hay reportes para mostrar
                </strong>

                <span>
                    Los registros aparecerán aquí cuando existan reportes disponibles.
                </span>

            </div>

        </td>
    `;


    tbody.appendChild(
        fila
    );

}


/* =========================================================
   NOTIFICACIÓN
========================================================= */

function mostrarNotificacionEliminado(
    folio
) {

    mostrarResultado({
        tipo:
            'success',

        titulo:
            'Reporte eliminado',

        mensaje:
            `El reporte ${folio} fue eliminado correctamente.`,
    });


    window.setTimeout(
        () => {

            cerrarResultado();

        },
        2000
    );

}


/* =========================================================
   BASE URL
========================================================= */

function obtenerBaseUrlEliminar() {

    return (
        document
            .querySelector('base')
            ?.href
        || `${window.location.origin}/`
    );

}