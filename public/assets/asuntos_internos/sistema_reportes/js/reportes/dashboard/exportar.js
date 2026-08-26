document.addEventListener('DOMContentLoaded', () => {
    inicializarExportacionDashboard();
});


function inicializarExportacionDashboard() {

    const modal =
        document.querySelector(
            '#modal-exportar-dashboard'
        );

    const botonAbrir =
        document.querySelector(
            '#btn-exportar-dashboard'
        );

    const formulario =
        document.querySelector(
            '#form-exportar-dashboard'
        );

    const seleccionarTodo =
        document.querySelector(
            '#exportar-seleccionar-todo'
        );

    const mensaje =
        document.querySelector(
            '#exportar-dashboard-mensaje'
        );


    if (
        !modal
        || !botonAbrir
        || !formulario
        || !seleccionarTodo
    ) {
        return;
    }


    /* =========================================================
       ABRIR MODAL
    ========================================================= */

    botonAbrir.addEventListener('click', () => {

        prepararModalExportacion(
            formulario,
            seleccionarTodo,
            mensaje
        );

        abrirModalExportacion(
            modal
        );
    });


    /* =========================================================
       CERRAR MODAL
    ========================================================= */

    modal.addEventListener('click', (evento) => {

        const botonCerrar =
            evento.target.closest(
                '[data-cerrar-modal-exportar]'
            );

        if (!botonCerrar) {
            return;
        }

        cerrarModalExportacion(
            modal,
            mensaje
        );
    });


    /* =========================================================
       CERRAR CON ESCAPE
    ========================================================= */

    document.addEventListener('keydown', (evento) => {

        if (
            evento.key === 'Escape'
            && modal.classList.contains(
                'modal-reporte--visible'
            )
        ) {
            cerrarModalExportacion(
                modal,
                mensaje
            );
        }
    });


    /* =========================================================
       SELECCIONAR TODO
    ========================================================= */

    seleccionarTodo.addEventListener('change', () => {

        const opciones =
            obtenerOpcionesExportacion(
                formulario
            );

        opciones.forEach((opcion) => {
            opcion.checked =
                seleccionarTodo.checked;
        });

        actualizarEstadoSeleccionarTodo(
            formulario,
            seleccionarTodo
        );

        ocultarMensajeExportacion(
            mensaje
        );
    });


    /* =========================================================
       OPCIONES INDIVIDUALES
    ========================================================= */

    formulario.addEventListener('change', (evento) => {

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
    });


    /* =========================================================
       GENERAR EXCEL
    ========================================================= */

    formulario.addEventListener('submit', (evento) => {

        evento.preventDefault();


        const secciones =
            obtenerSeccionesSeleccionadas(
                formulario
            );


        /*
         * Debe existir por lo menos
         * una sección seleccionada.
         */
        if (secciones.length === 0) {

            mostrarMensajeExportacion(
                mensaje
            );

            return;
        }


        ocultarMensajeExportacion(
            mensaje
        );


        /*
         * TEMPORAL
         *
         * Aquí conectaremos posteriormente
         * el backend que generará el Excel.
         */
        enviarExportacionDashboard(
            secciones,
            modal,
            mensaje
        );
    });

}


/* =============================================================
   PREPARAR MODAL
============================================================= */

function prepararModalExportacion(
    formulario,
    seleccionarTodo,
    mensaje
) {

    const opciones =
        obtenerOpcionesExportacion(
            formulario
        );


    /*
     * Al abrir nuevamente el modal
     * conservamos las selecciones existentes.
     */
    actualizarEstadoSeleccionarTodo(
        formulario,
        seleccionarTodo
    );


    /*
     * Por seguridad, si no existen opciones
     * no dejamos seleccionado "Seleccionar todo".
     */
    if (opciones.length === 0) {
        seleccionarTodo.checked = false;
        seleccionarTodo.indeterminate = false;
    }


    ocultarMensajeExportacion(
        mensaje
    );
}


/* =============================================================
   ABRIR MODAL
============================================================= */

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


/* =============================================================
   CERRAR MODAL
============================================================= */

function cerrarModalExportacion(
    modal,
    mensaje
) {

    /*
     * Si algún elemento dentro del modal
     * conserva el foco, lo quitamos antes
     * de ocultar el modal.
     */
    const elementoActivo =
        document.activeElement;

    if (
        elementoActivo
        && modal.contains(elementoActivo)
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

/* =============================================================
   OBTENER CHECKBOXES
============================================================= */

function obtenerOpcionesExportacion(
    formulario
) {

    return Array.from(
        formulario.querySelectorAll(
            'input[name="secciones[]"]'
        )
    );
}


/* =============================================================
   OBTENER SECCIONES SELECCIONADAS
============================================================= */

function obtenerSeccionesSeleccionadas(
    formulario
) {

    return Array.from(
        formulario.querySelectorAll(
            'input[name="secciones[]"]:checked'
        )
    ).map(
        (opcion) => opcion.value
    );
}


/* =============================================================
   ACTUALIZAR "SELECCIONAR TODO"
============================================================= */

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
            (opcion) => opcion.checked
        );


    /*
     * Ninguna seleccionada
     */
    if (seleccionadas.length === 0) {

        seleccionarTodo.checked = false;

        seleccionarTodo.indeterminate =
            false;

        return;
    }


    /*
     * Todas seleccionadas
     */
    if (
        seleccionadas.length
        === opciones.length
    ) {

        seleccionarTodo.checked = true;

        seleccionarTodo.indeterminate =
            false;

        return;
    }


    /*
     * Solo algunas seleccionadas
     */
    seleccionarTodo.checked = false;

    seleccionarTodo.indeterminate =
        true;
}


/* =============================================================
   MOSTRAR MENSAJE
============================================================= */

function mostrarMensajeExportacion(
    mensaje
) {

    if (!mensaje) {
        return;
    }

    mensaje.hidden = false;
}


/* =============================================================
   OCULTAR MENSAJE
============================================================= */

function ocultarMensajeExportacion(
    mensaje
) {

    if (!mensaje) {
        return;
    }

    mensaje.hidden = true;
}

async function enviarExportacionDashboard(
    secciones,
    modal,
    mensaje
) {

    const botonGenerar =
        document.querySelector(
            '#btn-generar-excel'
        );

    const textoOriginal =
        botonGenerar?.textContent
            ?.trim()
        || 'Generar Excel';


    try {

        /*
         * Evitamos múltiples exportaciones
         * mientras se genera el archivo.
         */
        if (botonGenerar) {

            botonGenerar.disabled = true;

            botonGenerar.textContent =
                'Generando...';
        }


        /*
         * Construimos los datos que recibirá
         * el Controller.
         */
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


        /*
         * Enviamos la solicitud al backend.
         */
        const respuesta =
            await fetch(
                '/asuntos-internos/reportes/dashboard/exportar',
                {
                    method: 'POST',
                    body: datos,
                }
            );


        /*
         * Si PHP devuelve un error,
         * intentamos recuperar el JSON.
         */
        if (!respuesta.ok) {

            let resultado = null;

            try {

                resultado =
                    await respuesta.json();

            } catch (error) {

                resultado = {
                    message:
                        'No fue posible generar el archivo de Excel.',
                };

            }


            console.error(
                'Error exportando Dashboard:',
                resultado
            );

            mostrarErrorExportacion(
                mensaje,
                resultado.message
            );

            return;
        }


        /*
         * El Controller devuelve un archivo
         * binario .xlsx, no JSON.
         */
        const archivo =
            await respuesta.blob();


        /*
         * Intentamos recuperar el nombre
         * enviado por CodeIgniter.
         */
        const disposition =
            respuesta.headers.get(
                'Content-Disposition'
            );


        const nombreArchivo =
            obtenerNombreArchivo(
                disposition
            );


        /*
         * Creamos temporalmente una URL
         * para descargar el Blob.
         */
        const url =
            window.URL.createObjectURL(
                archivo
            );


        const enlace =
            document.createElement('a');


        enlace.href = url;

        enlace.download =
            nombreArchivo;


        document.body.appendChild(
            enlace
        );


        enlace.click();

        enlace.remove();


        /*
         * Liberamos la URL temporal.
         */
        window.URL.revokeObjectURL(
            url
        );


        /*
         * Cerramos el modal únicamente
         * cuando la exportación fue correcta.
         */
        cerrarModalExportacion(
            modal,
            mensaje
        );


    } catch (error) {

        console.error(
            'Error enviando exportación:',
            error
        );


        mostrarErrorExportacion(
            mensaje,
            'Ocurrió un error al generar el archivo de Excel.'
        );


    } finally {

        /*
         * Restauramos el botón.
         */
        if (botonGenerar) {

            botonGenerar.disabled =
                false;

            botonGenerar.textContent =
                textoOriginal;
        }

    }
}


/* =============================================================
   OBTENER NOMBRE DEL ARCHIVO
============================================================= */

function obtenerNombreArchivo(
    contentDisposition
) {

    const nombrePredeterminado =
        'dashboard_reportes.xlsx';


    if (!contentDisposition) {
        return nombrePredeterminado;
    }


    /*
     * Soporte para:
     *
     * filename="archivo.xlsx"
     * filename=archivo.xlsx
     */
    const coincidencia =
        contentDisposition.match(
            /filename="?([^"]+)"?/i
        );


    if (
        !coincidencia
        || !coincidencia[1]
    ) {
        return nombrePredeterminado;
    }


    return coincidencia[1].trim();
}


/* =============================================================
   MOSTRAR ERROR DE EXPORTACIÓN
============================================================= */

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


    mensaje.hidden = false;
}