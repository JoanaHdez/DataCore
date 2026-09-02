/* =========================================================
   SISTEMA DE REPORTES - ASUNTOS INTERNOS
   Listado - Exportar Excel con selección de secciones
========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        inicializarExportacionListado();

    }
);


/* =========================================================
   INICIALIZAR
========================================================= */

function inicializarExportacionListado() {

    const modal =
        document.querySelector(
            '#modal-exportar-listado'
        );

    const botonAbrir =
        document.querySelector(
            '#btn-exportar-reportes'
        );

    const formulario =
        document.querySelector(
            '#form-exportar-listado'
        );

    const seleccionarTodo =
        document.querySelector(
            '#exportar-listado-seleccionar-todo'
        );

    const mensaje =
        document.querySelector(
            '#exportar-listado-mensaje'
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

            prepararModalExportacion(
                formulario,
                seleccionarTodo,
                mensaje
            );


            abrirModalExportacion(
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
                    '[data-cerrar-modal-exportar-listado]'
                );


            if (!botonCerrar) {
                return;
            }


            cerrarModalExportacion(
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


            cerrarModalExportacion(
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
                obtenerOpcionesExportacion(
                    formulario
                );


            opciones.forEach(
                (opcion) => {

                    opcion.checked =
                        seleccionarTodo.checked;

                }
            );


            actualizarEstadoSeleccionarTodo(
                formulario,
                seleccionarTodo
            );


            ocultarMensajeExportacion(
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


            actualizarEstadoSeleccionarTodo(
                formulario,
                seleccionarTodo
            );


            ocultarMensajeExportacion(
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
                obtenerSeccionesSeleccionadas(
                    formulario
                );


            if (
                secciones.length === 0
            ) {

                mostrarMensajeExportacion(
                    mensaje
                );

                return;
            }


            ocultarMensajeExportacion(
                mensaje
            );


            await enviarExportacionListado(
                secciones,
                modal,
                mensaje
            );

        }
    );

}


/* =========================================================
   PREPARAR MODAL
========================================================= */

function prepararModalExportacion(
    formulario,
    seleccionarTodo,
    mensaje
) {

    const opciones =
        obtenerOpcionesExportacion(
            formulario
        );


    actualizarEstadoSeleccionarTodo(
        formulario,
        seleccionarTodo
    );


    if (
        opciones.length === 0
    ) {

        seleccionarTodo.checked =
            false;

        seleccionarTodo.indeterminate =
            false;

    }


    ocultarMensajeExportacion(
        mensaje
    );

}


/* =========================================================
   ABRIR MODAL
========================================================= */

function abrirModalExportacion(
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

function cerrarModalExportacion(
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


    ocultarMensajeExportacion(
        mensaje
    );

}


/* =========================================================
   OBTENER OPCIONES
========================================================= */

function obtenerOpcionesExportacion(
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

function obtenerSeccionesSeleccionadas(
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
   ACTUALIZAR "SELECCIONAR TODO"
========================================================= */

function actualizarEstadoSeleccionarTodo(
    formulario,
    seleccionarTodo
) {

    const opciones =
        obtenerOpcionesExportacion(
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
   MOSTRAR MENSAJE
========================================================= */

function mostrarMensajeExportacion(
    mensaje
) {

    if (!mensaje) {
        return;
    }


    mensaje.textContent =
        'Selecciona al menos una sección para continuar.';


    mensaje.hidden =
        false;

}


/* =========================================================
   OCULTAR MENSAJE
========================================================= */

function ocultarMensajeExportacion(
    mensaje
) {

    if (!mensaje) {
        return;
    }


    mensaje.hidden =
        true;

}


/* =========================================================
   ENVIAR EXPORTACIÓN
========================================================= */

async function enviarExportacionListado(
    secciones,
    modal,
    mensaje
) {

    const botonGenerar =
        document.querySelector(
            '#btn-generar-excel-listado'
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
           SOLICITUD
        ================================================= */

        const respuesta =
            await fetch(
                construirUrlExportacion(),
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


            mostrarErrorExportacion(
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
            obtenerNombreArchivo(
                disposition
            );


        /* =================================================
           DESCARGAR
        ================================================= */

        descargarArchivo(
            archivo,
            nombre
        );


        /* =================================================
           CERRAR MODAL
        ================================================= */

        cerrarModalExportacion(
            modal,
            mensaje
        );


    } catch (error) {

        console.error(
            'Error exportando listado:',
            error
        );


        mostrarErrorExportacion(
            mensaje,
            error.message
            || 'No fue posible generar el archivo de Excel.'
        );


    } finally {

        /* =================================================
           RESTAURAR BOTÓN
        ================================================= */

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

/* function construirUrlExportacion() {

    const base =
        document
            .querySelector(
                'base'
            )
            ?.href;


    if (base) {

        return new URL(
            'asuntos-internos/reportes/listado/exportar',
            base
        ).toString();

    }


    return (
        `${window.location.origin}`
        + '/asuntos-internos/reportes/listado/exportar'
    );

} */

function construirUrlExportacion() {

    return new URL(
        'DataCore/public/asuntos-internos/reportes/listado/exportar',
        `${window.location.origin}/`
    ).toString();

}

/* =========================================================
   DESCARGAR ARCHIVO
========================================================= */

function descargarArchivo(
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

function obtenerNombreArchivo(
    contentDisposition
) {

    const predeterminado =
        'reportes_asuntos_internos.xlsx';


    if (!contentDisposition) {

        return predeterminado;

    }


    /*
     * filename*=UTF-8''archivo.xlsx
     */

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


    /*
     * filename="archivo.xlsx"
     */

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
   MOSTRAR ERROR
========================================================= */

function mostrarErrorExportacion(
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