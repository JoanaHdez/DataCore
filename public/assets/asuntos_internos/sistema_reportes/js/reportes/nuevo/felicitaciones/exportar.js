/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   FELICITACIONES - EXPORTAR EXCEL
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarExportacionFelicitaciones();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarExportacionFelicitaciones() {

    const modal =
        document.querySelector(
            '#modal-exportar-felicitaciones'
        );


    const botonAbrir =
        document.querySelector(
            '#btn-exportar-felicitaciones'
        );


    const formulario =
        document.querySelector(
            '#form-exportar-felicitaciones'
        );


    const seleccionarTodo =
        document.querySelector(
            '#exportar-felicitaciones-seleccionar-todo'
        );


    const inputCantidad =
        document.querySelector(
            '#exportar-felicitaciones-cantidad'
        );


    const mensaje =
        document.querySelector(
            '#exportar-felicitaciones-mensaje'
        );


    if (
        !modal
        || !botonAbrir
        || !formulario
        || !seleccionarTodo
    ) {
        return;
    }


    /* =====================================================
       ABRIR MODAL
    ===================================================== */

    botonAbrir.addEventListener(
        'click',
        () => {

            prepararModalExportacionFelicitaciones(
                formulario,
                seleccionarTodo,
                inputCantidad,
                mensaje
            );


            abrirModalExportacionFelicitaciones(
                modal
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
                    '[data-cerrar-modal-exportar-felicitaciones]'
                );


            if (!botonCerrar) {
                return;
            }


            cerrarModalExportacionFelicitaciones(
                modal,
                mensaje
            );

        }
    );


    /* =====================================================
       CERRAR CON ESCAPE
    ===================================================== */

    document.addEventListener(
        'keydown',
        (evento) => {

            if (
                evento.key !== 'Escape'
                || !modal.classList.contains(
                    'modal-reporte--visible'
                )
            ) {
                return;
            }


            cerrarModalExportacionFelicitaciones(
                modal,
                mensaje
            );

        }
    );


    /* =====================================================
       SELECCIONAR TODO
    ===================================================== */

    seleccionarTodo.addEventListener(
        'change',
        () => {

            const opciones =
                obtenerOpcionesExportacionFelicitaciones(
                    formulario
                );


            opciones.forEach(
                (opcion) => {

                    opcion.checked =
                        seleccionarTodo.checked;

                }
            );


            actualizarEstadoSeleccionarTodoFelicitaciones(
                formulario,
                seleccionarTodo
            );


            ocultarMensajeExportacionFelicitaciones(
                mensaje
            );

        }
    );


    /* =====================================================
       OPCIONES INDIVIDUALES
    ===================================================== */

    formulario.addEventListener(
        'change',
        (evento) => {

            if (
                !evento.target.matches(
                    'input[name="secciones[]"]'
                )
            ) {
                return;
            }


            actualizarEstadoSeleccionarTodoFelicitaciones(
                formulario,
                seleccionarTodo
            );


            ocultarMensajeExportacionFelicitaciones(
                mensaje
            );

        }
    );


    /* =====================================================
       GENERAR EXCEL
    ===================================================== */

    formulario.addEventListener(
        'submit',
        async (evento) => {

            evento.preventDefault();


            const secciones =
                obtenerSeccionesSeleccionadasFelicitaciones(
                    formulario
                );


            if (
                secciones.length === 0
            ) {

                mostrarMensajeExportacionFelicitaciones(
                    mensaje,
                    'Selecciona al menos una sección para continuar.'
                );

                return;
            }


            const cantidad =
                obtenerCantidadExportacionFelicitaciones(
                    inputCantidad
                );


            if (
                cantidad === false
            ) {

                mostrarMensajeExportacionFelicitaciones(
                    mensaje,
                    'La cantidad debe ser un número entero mayor a 0.'
                );

                inputCantidad?.focus();

                return;
            }


            ocultarMensajeExportacionFelicitaciones(
                mensaje
            );


            await enviarExportacionFelicitaciones(
                secciones,
                cantidad,
                modal,
                mensaje
            );

        }
    );

}


/* =========================================================
   PREPARAR MODAL
========================================================= */

function prepararModalExportacionFelicitaciones(
    formulario,
    seleccionarTodo,
    inputCantidad,
    mensaje
) {

    const opciones =
        obtenerOpcionesExportacionFelicitaciones(
            formulario
        );


    opciones.forEach(
        (opcion) => {

            opcion.checked =
                true;

        }
    );


    seleccionarTodo.checked =
        true;


    seleccionarTodo.indeterminate =
        false;


    if (inputCantidad) {

        inputCantidad.value =
            '';

    }


    actualizarEstadoSeleccionarTodoFelicitaciones(
        formulario,
        seleccionarTodo
    );


    ocultarMensajeExportacionFelicitaciones(
        mensaje
    );

}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModalExportacionFelicitaciones(
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

function cerrarModalExportacionFelicitaciones(
    modal,
    mensaje
) {

    const elementoActivo =
        document.activeElement;


    if (
        elementoActivo
        && modal.contains(
            elementoActivo
        )
    ) {

        elementoActivo.blur();

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


    ocultarMensajeExportacionFelicitaciones(
        mensaje
    );

}


/* =========================================================
   OBTENER OPCIONES
========================================================= */

function obtenerOpcionesExportacionFelicitaciones(
    formulario
) {

    return Array.from(
        formulario.querySelectorAll(
            'input[name="secciones[]"]:not(:disabled)'
        )
    );

}


/* =========================================================
   OBTENER SECCIONES SELECCIONADAS
========================================================= */

function obtenerSeccionesSeleccionadasFelicitaciones(
    formulario
) {

    return Array.from(
        formulario.querySelectorAll(
            'input[name="secciones[]"]:checked:not(:disabled)'
        )
    ).map(
        (opcion) =>
            opcion.value
    );

}


/* =========================================================
   OBTENER CANTIDAD
========================================================= */

function obtenerCantidadExportacionFelicitaciones(
    inputCantidad
) {

    if (!inputCantidad) {
        return null;
    }


    const valor =
        String(
            inputCantidad.value
            || ''
        ).trim();


    /*
     * Vacío = exportar todos.
     */

    if (
        valor === ''
    ) {
        return null;
    }


    const cantidad =
        Number(
            valor
        );


    if (
        !Number.isInteger(
            cantidad
        )
        || cantidad <= 0
    ) {

        return false;

    }


    return cantidad;

}


/* =========================================================
   ACTUALIZAR "SELECCIONAR TODO"
========================================================= */

function actualizarEstadoSeleccionarTodoFelicitaciones(
    formulario,
    seleccionarTodo
) {

    const opciones =
        obtenerOpcionesExportacionFelicitaciones(
            formulario
        );


    const seleccionadas =
        opciones.filter(
            (opcion) =>
                opcion.checked
        );


    if (
        opciones.length === 0
    ) {

        seleccionarTodo.checked =
            false;


        seleccionarTodo.indeterminate =
            false;


        seleccionarTodo.disabled =
            true;


        return;
    }


    seleccionarTodo.disabled =
        false;


    if (
        seleccionadas.length === 0
    ) {

        seleccionarTodo.checked =
            false;


        seleccionarTodo.indeterminate =
            false;


        return;
    }


    if (
        seleccionadas.length
        === opciones.length
    ) {

        seleccionarTodo.checked =
            true;


        seleccionarTodo.indeterminate =
            false;


        return;
    }


    seleccionarTodo.checked =
        false;


    seleccionarTodo.indeterminate =
        true;

}


/* =========================================================
   ENVIAR EXPORTACIÓN
========================================================= */

async function enviarExportacionFelicitaciones(
    secciones,
    cantidad,
    modal,
    mensaje
) {

    const botonGenerar =
        document.querySelector(
            '#btn-generar-excel-felicitaciones'
        );


    const textoOriginal =
        botonGenerar?.textContent
            ?.trim()
        || 'Generar Excel';


    try {

        /* =================================================
           BLOQUEAR BOTÓN
        ================================================= */

        if (botonGenerar) {

            botonGenerar.disabled =
                true;


            botonGenerar.textContent =
                'Generando...';

        }


        /* =================================================
           FORM DATA
        ================================================= */

        const datos =
            new FormData();


        secciones.forEach(
            (seccion) => {

                datos.append(
                    'secciones[]',
                    seccion
                );

            }
        );


        /* =================================================
           CANTIDAD

           Solo se envía si el usuario indicó un valor.
        ================================================= */

        if (
            cantidad !== null
        ) {

            datos.append(
                'cantidad',
                String(
                    cantidad
                )
            );

        }


        /* =================================================
           SOLICITUD
        ================================================= */

        const respuesta =
            await fetch(
                construirUrlExportacionFelicitaciones(),
                {
                    method:
                        'POST',

                    headers: {
                        Accept:
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/json',
                    },

                    credentials:
                        'same-origin',

                    body:
                        datos,
                }
            );


        /* =================================================
           ERROR DEL BACKEND
        ================================================= */

        if (!respuesta.ok) {

            let texto =
                'No fue posible generar el archivo de Excel.';


            try {

                const resultado =
                    await respuesta.json();


                if (
                    resultado?.message
                ) {

                    texto =
                        resultado.message;

                }

            } catch (error) {

                /*
                 * La respuesta no era JSON.
                 */

            }


            mostrarMensajeExportacionFelicitaciones(
                mensaje,
                texto
            );


            return;
        }


        /* =================================================
           ARCHIVO
        ================================================= */

        const archivo =
            await respuesta.blob();


        if (
            !archivo
            || archivo.size === 0
        ) {

            throw new Error(
                'El archivo generado está vacío.'
            );

        }


        /* =================================================
           NOMBRE DEL ARCHIVO
        ================================================= */

        const disposition =
            respuesta.headers.get(
                'Content-Disposition'
            );


        const nombre =
            obtenerNombreArchivoFelicitaciones(
                disposition
            );


        /* =================================================
           DESCARGAR
        ================================================= */

        descargarArchivoFelicitaciones(
            archivo,
            nombre
        );


        /* =================================================
           CERRAR MODAL
        ================================================= */

        cerrarModalExportacionFelicitaciones(
            modal,
            mensaje
        );


    } catch (error) {

        console.error(
            'Error exportando felicitaciones:',
            error
        );


        mostrarMensajeExportacionFelicitaciones(
            mensaje,
            error.message
            || 'No fue posible generar el archivo de Excel.'
        );


    } finally {

        if (botonGenerar) {

            botonGenerar.disabled =
                false;


            botonGenerar.textContent =
                textoOriginal;

        }

    }

}


/* =========================================================
   URL DE EXPORTACIÓN
========================================================= */

function construirUrlExportacionFelicitaciones() {

    return new URL(
        'DataCore/public/asuntos-internos/reportes/felicitaciones/exportar',
        `${window.location.origin}/`
    ).toString();

}


/* =========================================================
   DESCARGAR ARCHIVO
========================================================= */

function descargarArchivoFelicitaciones(
    archivo,
    nombre
) {

    const url =
        window.URL.createObjectURL(
            archivo
        );


    const enlace =
        document.createElement(
            'a'
        );


    enlace.href =
        url;


    enlace.download =
        nombre;


    document.body.appendChild(
        enlace
    );


    enlace.click();


    enlace.remove();


    window.URL.revokeObjectURL(
        url
    );

}


/* =========================================================
   OBTENER NOMBRE DEL ARCHIVO
========================================================= */

function obtenerNombreArchivoFelicitaciones(
    contentDisposition
) {

    const predeterminado =
        'felicitaciones_asuntos_internos.xlsx';


    if (!contentDisposition) {
        return predeterminado;
    }


    /* =====================================================
       filename*=UTF-8''archivo.xlsx
    ===================================================== */

    const utf8 =
        contentDisposition.match(
            /filename\*=UTF-8''([^;]+)/i
        );


    if (
        utf8
        && utf8[1]
    ) {

        try {

            return decodeURIComponent(
                utf8[1]
            );

        } catch (error) {

            return utf8[1];

        }

    }


    /* =====================================================
       filename="archivo.xlsx"
    ===================================================== */

    const normal =
        contentDisposition.match(
            /filename="?([^";]+)"?/i
        );


    if (
        normal
        && normal[1]
    ) {

        return normal[1]
            .trim();

    }


    return predeterminado;

}


/* =========================================================
   MOSTRAR MENSAJE
========================================================= */

function mostrarMensajeExportacionFelicitaciones(
    mensaje,
    texto
) {

    if (!mensaje) {
        return;
    }


    mensaje.textContent =
        texto
        || 'No fue posible generar el archivo de Excel.';


    mensaje.hidden =
        false;

}


/* =========================================================
   OCULTAR MENSAJE
========================================================= */

function ocultarMensajeExportacionFelicitaciones(
    mensaje
) {

    if (!mensaje) {
        return;
    }


    mensaje.hidden =
        true;

}