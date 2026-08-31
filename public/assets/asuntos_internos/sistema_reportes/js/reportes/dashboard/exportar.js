document.addEventListener(
    'DOMContentLoaded',
    () => {
        inicializarExportacionDashboard();
    }
);


/* =============================================================
   INICIALIZAR EXPORTACIÓN
============================================================= */

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


    /*
     * Este archivo puede cargarse en vistas donde
     * el Dashboard no está presente.
     */

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


    /* =========================================================
       CERRAR MODAL
    ========================================================= */

    modal.addEventListener(
        'click',
        (evento) => {

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

        }
    );


    /* =========================================================
       CERRAR CON ESCAPE
    ========================================================= */

    document.addEventListener(
        'keydown',
        (evento) => {

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

        }
    );


    /* =========================================================
       SELECCIONAR TODO
    ========================================================= */

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


    /* =========================================================
       OPCIONES INDIVIDUALES
    ========================================================= */

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


    /* =========================================================
       GENERAR EXCEL
    ========================================================= */

    formulario.addEventListener(
        'submit',
        (evento) => {

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


            enviarExportacionDashboard(
                secciones,
                modal,
                mensaje
            );

        }
    );

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


/* =============================================================
   OBTENER CHECKBOXES ACTIVOS

   IMPORTANTE:
   Ignoramos los checkbox deshabilitados.

   Actualmente:
   - zonas
   - sanciones

   no forman parte de "Seleccionar todo".
============================================================= */

function obtenerOpcionesExportacion(
    formulario
) {

    return Array.from(
        formulario.querySelectorAll(
            'input[name="secciones[]"]:not(:disabled)'
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
            'input[name="secciones[]"]:checked:not(:disabled)'
        )
    ).map(
        (opcion) =>
            opcion.value
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
            (opcion) =>
                opcion.checked
        );


    /* =========================================================
       NO EXISTEN OPCIONES ACTIVAS
    ========================================================= */

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


    /* =========================================================
       NINGUNA SELECCIONADA
    ========================================================= */

    if (
        seleccionadas.length === 0
    ) {

        seleccionarTodo.checked =
            false;

        seleccionarTodo.indeterminate =
            false;

        return;
    }


    /* =========================================================
       TODAS SELECCIONADAS
    ========================================================= */

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


    /* =========================================================
       SOLO ALGUNAS
    ========================================================= */

    seleccionarTodo.checked =
        false;

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


    mensaje.textContent =
        'Selecciona al menos una sección para continuar.';


    mensaje.hidden =
        false;

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


    mensaje.hidden =
        true;

}


/* =============================================================
   OBTENER FILTROS ACTIVOS DEL DASHBOARD

   Tomamos los filtros directamente de la URL.

   Así el Excel utilizará exactamente la misma consulta
   que actualmente está viendo el usuario.
============================================================= */

function obtenerFiltrosDashboard() {

    const parametros =
        new URLSearchParams(
            window.location.search
        );


    const nombresPermitidos = [

        /* Fechas */
        'fecha_inicio',
        'fecha_fin',
        'periodo',
        'tipo_fecha',

        /* Reporte */
        'estado_actual',
        'seguimiento',
        'evidencia',

        /* Personal */
        'area_personal',
        'turno',

        /* Quejoso */
        'genero',

        /* Unidad */
        'unidad',

    ];


    const filtros =
        {};


    nombresPermitidos.forEach(
        (nombre) => {

            const valor =
                parametros.get(
                    nombre
                );


            if (
                valor !== null
                && valor.trim() !== ''
            ) {

                filtros[nombre] =
                    valor.trim();

            }

        }
    );


    return filtros;

}


/* =============================================================
   ENVIAR EXPORTACIÓN AL BACKEND
============================================================= */

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

        /* =========================================================
           BLOQUEAR BOTÓN
        ========================================================= */

        if (botonGenerar) {

            botonGenerar.disabled =
                true;


            botonGenerar.textContent =
                'Generando...';

        }


        /* =========================================================
           FORM DATA
        ========================================================= */

        const datos =
            new FormData();


        /* =========================================================
           SECCIONES
        ========================================================= */

        secciones.forEach(
            (seccion) => {

                datos.append(
                    'secciones[]',
                    seccion
                );

            }
        );


        /* =========================================================
           FILTROS ACTIVOS
        ========================================================= */

        const filtros =
            obtenerFiltrosDashboard();


        Object.entries(
            filtros
        ).forEach(
            ([nombre, valor]) => {

                datos.append(
                    nombre,
                    valor
                );

            }
        );


        /* =========================================================
           SOLICITUD
        ========================================================= */

        const respuesta =
            await fetch(
                '/asuntos-internos/reportes/dashboard/exportar',
                {
                    method:
                        'POST',

                    body:
                        datos,
                }
            );


        /* =========================================================
           ERROR DEL BACKEND
        ========================================================= */

        if (
            !respuesta.ok
        ) {

            let resultado =
                null;


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


        /* =========================================================
           ARCHIVO XLSX
        ========================================================= */

        const archivo =
            await respuesta.blob();


        if (
            archivo.size === 0
        ) {

            throw new Error(
                'El archivo generado está vacío.'
            );

        }


        /* =========================================================
           NOMBRE DEL ARCHIVO
        ========================================================= */

        const disposition =
            respuesta.headers.get(
                'Content-Disposition'
            );


        const nombreArchivo =
            obtenerNombreArchivo(
                disposition
            );


        /* =========================================================
           DESCARGAR
        ========================================================= */

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
            nombreArchivo;


        document.body.appendChild(
            enlace
        );


        enlace.click();


        enlace.remove();


        window.URL.revokeObjectURL(
            url
        );


        /* =========================================================
           CERRAR MODAL
        ========================================================= */

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

        /* =========================================================
           RESTAURAR BOTÓN
        ========================================================= */

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


    if (
        !contentDisposition
    ) {

        return nombrePredeterminado;

    }


    /*
     * Primero intentamos formato RFC 5987:
     *
     * filename*=UTF-8''archivo.xlsx
     */

    const coincidenciaUtf8 =
        contentDisposition.match(
            /filename\*=UTF-8''([^;]+)/i
        );


    if (
        coincidenciaUtf8
        && coincidenciaUtf8[1]
    ) {

        try {

            return decodeURIComponent(
                coincidenciaUtf8[1]
            );

        } catch (error) {

            return coincidenciaUtf8[1];

        }

    }


    /*
     * Formato tradicional:
     *
     * filename="archivo.xlsx"
     * filename=archivo.xlsx
     */

    const coincidencia =
        contentDisposition.match(
            /filename="?([^";]+)"?/i
        );


    if (
        !coincidencia
        || !coincidencia[1]
    ) {

        return nombrePredeterminado;

    }


    return coincidencia[1]
        .trim();

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


    mensaje.hidden =
        false;

}